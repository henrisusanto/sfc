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
      $data['id'] = time();
      $this->db->insert($this->table, $data);
    } else {
      return $this->db->where('id', $data['id'])->update($this->table, $data);
    }
    return $data['id'];
  }

  function delete ($id) {
    return $this->db->where('id', $id)->delete($this->table);
  }

  function findAnother ($table, $where = array()) {
    $this->db->where($where);
    return $this->db->get($table)->result();
  }

  function buildRelation (&$dropdown, $table, $where = array()) {
    $dropdown[0] = '';
    $this->db->where($where);
    foreach ($this->db->get($table)->result() as $item)
      $dropdown[$item->id] = $item->nama;
  }

  function sirkulasiKeuangan ($type, $transaksi, $nominal, $foreignKey = null, $waktu = null) {
    $waktu = null === $waktu ? date('Y-m-d H:i:s') : $waktu;
    $foreignKey = null === $foreignKey ? time() : $foreignKey;
    $cashflow = $this->db->get('cashflow')->result();
    $last = end($cashflow);
    $saldo = isset($last->saldo) ? $last->saldo : 0;
    $saldo = $type == 'MASUK' ? $saldo + $nominal : $saldo - $nominal;
    $cashflow = array (
      'id' => time(),
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
      'id' => $fkey,
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

  function sirkulasiProduk () {
    
  }
}
