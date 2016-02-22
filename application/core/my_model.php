<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class my_model extends CI_Model {

  function __construct () {
    parent::__construct();
    $this->load->database();
    date_default_timezone_set('Asia/Jakarta');
  }

  function getTHead () {
    return $this->thead;
  }

  function getInputFields () {
    return $this->inputFields;
  }

  function getExpandables () {
    return isset($this->expandables) ? $this->expandables: array();
  }

  function getTablePage ($id) {
    $goto = current_url();
    if (strpos($goto, 'form')) $goto = str_replace('form', '', $goto);
    if (strpos($goto, 'delete')) $goto = str_replace('delete', '', $goto);
    if (!is_null($id)) $goto = str_replace("/$id", '', $goto);
    return $goto;  
  }

  function find ($where = array()) {
    $this->db->where($where);
    return $this->db->get($this->table)->result();
  }

  function findOne ($id) {
    return $this->db->get_where($this->table, array('id'=>$id))->row_array();
  }

  function save ($data) {
    if (!isset($data['id'])) {
      $this->db->insert($this->table, $data);
      $data['id'] = $this->db->insert_id();
    } else {
      return $this->db->where('id', $data['id'])->update($this->table, $data);
    }
    return $data['id'];
  }

  function delete ($id) {
    return $this->db->where('id', $id)->delete($this->table);
  }

  function buildRelation (&$dropdown, $table, $where = array()) {
    $dropdown[0] = '';
    $this->db->where($where);
    foreach ($this->db->get($table)->result() as $item)
      $dropdown[$item->id] = $item->nama;
  }

  function sirkulasiKeuangan ($type, $transaksi, $nominal, $fkey, $waktu) {
    $cashflow = $this->db->get('cashflow')->result();
    $last = end($cashflow);
    $saldo = isset($last->saldo) ? $last->saldo : 0;
    $saldo = $type == 'MASUK' ? $saldo + $nominal : $saldo - $nominal;
    $cashflow = array (
      'waktu' => $waktu,
      'type' => $type,
      'transaksi' => $transaksi,
      'nominal' => $nominal,
      'saldo' => $saldo,
    );
    $this->db->insert('cashflow', $cashflow);
  }

  function sirkulasiBarang ($waktu, $barang, $type, $transaksi, $fkey, $qty) {
    $operator = $type == 'MASUK' ? '+' : '-';
    $this->db
      ->where('id', $barang)
      ->set('stock', "stock $operator " . $qty, false)
      ->update('baranggudang');
    $sir = $this->db->get_where('sirkulasibarang', array('barang' => $barang))->result();
    $last = end($sir);
    $stock = isset($last->stock) ? $last->stock : 0;
    $sirkulasi = array(
      'waktu' => $waktu,
      'barang' => $barang,
      'type' => $type,
      'transaksi' => $transaksi,
      'fkey' => $fkey,
      'qty' => $qty,
      'stock' => $type == 'MASUK' ? $stock + $qty : $stock - $qty
    );
    $this->db->insert('sirkulasibarang', $sirkulasi);
  }

  function sirkulasiAyam ($waktu, $ayam, $type, $transaksi, $fkey, $pcs, $kg) {
    $operator = $type == 'MASUK' ? '+' : '-';
    $this->db
      ->where('id', $ayam)
      ->set('pcs', "pcs $operator " . $pcs, false)
      ->set('kg', "kg $operator " . $kg, false)
      ->update('ayam');
    $sir = $this->db->get_where('sirkulasiayam', array('ayam' => $ayam))->result();
    $last = end($sir);
    $stockpcs = isset($last->stockpcs) ? $last->stockpcs : 0;
    $stockkg = isset($last->stockkg) ? $last->stockkg : 0;
    $sirkulasi = array(
      'waktu' => $waktu,
      'ayam' => $ayam,
      'type' => $type,
      'transaksi' => $transaksi,
      'fkey' => $fkey,
      'pcs' => $pcs,
      'kg' => $kg,
      'stockpcs' => $type == 'MASUK' ? $stockpcs + $pcs : $stockpcs - $pcs,
      'stockkg' => $type == 'MASUK' ? $stockkg + $kg : $stockkg - $kg
    );
    $this->db->insert('sirkulasiayam', $sirkulasi);
  }

  function sirkulasiProduk ($waktu, $produk, $type, $transaksi, $fkey, $qty) {
    $operator = $type == 'MASUK' ? '+' : '-';
    $this->db
      ->where('id', $produk)
      ->set('stock', "stock $operator " . $qty, false)
      ->update('produk');
    $sir = $this->db->get_where('sirkulasiproduk', array('produk' => $produk))->result();
    $last = end($sir);
    $stock = isset($last->stock) ? $last->stock : 0;
    $sirkulasi = array(
      'waktu' => $waktu,
      'produk' => $produk,
      'type' => $type,
      'transaksi' => $transaksi,
      'fkey' => $fkey,
      'qty' => $qty,
      'stock' => $type == 'MASUK' ? $stock + $qty : $stock - $qty
    );
    $this->db->insert('sirkulasiproduk', $sirkulasi);
  }

  function sirkulasiKeuanganOutlet ($type, $transaksi, $nominal, $fkey, $waktu, $outlet) {
    $cashflow = $this->db->get_where('cashflowoutlet', array('outlet' => $outlet))->result();
    $last = end($cashflow);
    $saldo = isset($last->saldo) ? $last->saldo : 0;
    $saldo = $type == 'MASUK' ? $saldo + $nominal : $saldo - $nominal;
    $cashflow = array (
      'outlet' => $outlet,
      'waktu' => $waktu,
      'type' => $type,
      'transaksi' => $transaksi,
      'nominal' => $nominal,
      'saldo' => $saldo,
    );
    $this->db->insert('cashflowoutlet', $cashflow);
    $this->db->where('id', $outlet)->set('saldo', $saldo)->update('outlet');
  }

  function sirkulasiBarangOutlet ($waktu, $barang, $type, $transaksi, $fkey, $qty, $outlet) {
    $operator = $type == 'MASUK' ? '+' : '-';
    $barangOutlet = $this->db
      ->where('outlet', $outlet)
      ->where('barang', $barang)
      ->get('barangoutlet')->row_array();
    if (!isset($barangOutlet['id'])) {
      $this->db->insert('barangoutlet', array(
        'barang' => $barang,
        'outlet' => $outlet,
        'stock' => 0
      ));
      $barangOutlet['id'] = $this->db->insert_id();
    }
    $this->db
      ->where('id', $barangOutlet['id'])
      ->set('stock', "stock $operator " . $qty, false)
      ->update('barangoutlet');
    $sir = $this->db->get_where('sirkulasibarangoutlet', array('barang' => $barang, 'outlet' => $outlet))->result();
    $last = end($sir);
    $stock = isset($last->stock) ? $last->stock : 0;
    $sirkulasi = array(
      'outlet' => $outlet,
      'waktu' => $waktu,
      'barang' => $barang,
      'type' => $type,
      'transaksi' => $transaksi,
      'fkey' => $fkey,
      'qty' => $qty,
      'stock' => $type == 'MASUK' ? $stock + $qty : $stock - $qty
    );
    $this->db->insert('sirkulasibarangoutlet', $sirkulasi);
  }

  function sirkulasiAyamOutlet ($waktu, $ayam, $type, $transaksi, $fkey, $pcs, $kg, $outlet) {
    $operator = $type == 'MASUK' ? '+' : '-';
    $ayamoutlet = $this->db
      ->where('outlet', $outlet)
      ->where('ayam', $ayam)
      ->get('ayamoutlet')->row_array();
    if (!isset($ayamoutlet['id'])) {
      $this->db->insert('ayamoutlet', array(
        'ayam' => $ayam,
        'outlet' => $outlet,
        'pcs' => 0,
        'kg'  => 0
      ));
      $ayamoutlet['id'] = $this->db->insert_id();
    }
    $this->db
      ->where('id', $ayamoutlet['id'])
      ->set('pcs', "pcs $operator " . $pcs, false)
      ->set('kg', "kg $operator " . $kg, false)
      ->update('ayamoutlet');
    $sir = $this->db->get_where('sirkulasiayamoutlet', array('ayam' => $ayam, 'outlet' => $outlet))->result();
    $last = end($sir);
    $stockpcs = isset($last->stockpcs) ? $last->stockpcs : 0;
    $stockkg = isset($last->stockkg) ? $last->stockkg : 0;
    $sirkulasi = array(
      'outlet' => $outlet,
      'waktu' => $waktu,
      'ayam' => $ayam,
      'type' => $type,
      'transaksi' => $transaksi,
      'fkey' => $fkey,
      'pcs' => $pcs,
      'kg' => $kg,
      'stockpcs' => $type == 'MASUK' ? $stockpcs + $pcs : $stockpcs - $pcs,
      'stockkg' => $type == 'MASUK' ? $stockkg + $kg : $stockkg - $kg
    );
    $this->db->insert('sirkulasiayamoutlet', $sirkulasi);
  }

  function sirkulasiProdukOutlet ($waktu, $produk, $type, $transaksi, $fkey, $qty, $outlet) {
    $operator = $type == 'MASUK' ? '+' : '-';
    $produkoutlet = $this->db
      ->where('outlet', $outlet)
      ->where('produk', $produk)
      ->get('produkoutlet')->row_array();
    if (!isset($produkoutlet['id'])) {
      $this->db->insert('produkoutlet', array(
        'produk' => $produk,
        'outlet' => $outlet,
        'stock' => 0
      ));
      $produkoutlet['id'] = $this->db->insert_id();
    }
    $this->db
      ->where('id', $produkoutlet['id'])
      ->set('stock', "stock $operator " . $qty, false)
      ->update('produkoutlet');
    $sir = $this->db->get_where('sirkulasiprodukoutlet', array('produk' => $produk, 'outlet' => $outlet))->result();
    $last = end($sir);
    $stock = isset($last->stock) ? $last->stock : 0;
    $sirkulasi = array(
      'outlet' => $outlet,
      'waktu' => $waktu,
      'produk' => $produk,
      'type' => $type,
      'transaksi' => $transaksi,
      'fkey' => $fkey,
      'qty' => $qty,
      'stock' => $type == 'MASUK' ? $stock + $qty : $stock - $qty
    );
    $this->db->insert('sirkulasiprodukoutlet', $sirkulasi);
  }
}
