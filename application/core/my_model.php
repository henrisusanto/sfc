<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class my_model extends CI_Model {

  function __construct () {
    parent::__construct();
    $this->load->database();
    date_default_timezone_set('Asia/Jakarta');
  }

  function toRp ($int) {
    return 'Rp. ' . number_format( $int, 0 , '' , '.' ) . ',00';
  }

  function getTHead () {
    return $this->thead;
  }

  function getTFoot ($tbody) {
    return $this->tfoot;
  }

  function getInputFields () {
    return $this->inputFields;
  }

  function getInputStrings () {
    return isset ($this->strings) ? $this->strings : array();
  }

  function buildFilters ($table, $fkey = null, $label = null) {
    $fkey = is_null($fkey) ? $table : $fkey;
    $label = is_null($label) ? $fkey : $label;
    $this->filters = array(
      0 => array($fkey, strtoupper("FILTER $label")),
      1 => array('since', 'SEJAK TANGGAL'),
      2 => array('until', 'HINGGA TANGGAL'),
    );
    $this->buildRelation($this->filters[0][2], $table, array(), 'TAMPILKAN SEMUA');
  }

  function getFilters () {
    return $this->filters;
  }

  function buildTFoot () {
    $this->tfoot = array();
    foreach ($this->thead as $index => $th) $this->tfoot[$index] = '';
    $this->tfoot[] = '';
  }

  function getExpandables ($id) {
    if (!isset($this->expandables)) return array();
    foreach ($this->expandables as &$exp) $exp['subform'] = array();
    if (is_null($id)) return $this->expandables;
    else if (count ($this->expandables == 1) && in_array($this->table . 'detail', $this->db->list_tables()))
      $this->expandables[0]['subform'] = $this->db->order_by('id', 'DESC')->get_where($this->table . 'detail', array($this->table => $id))->result();
    else {
      foreach ($this->expandables as &$exp) {
        $table = explode('[', $exp['fields'][0][0]);
        $table = reset($table);
        $exp['subform'] = $this->db->get_where($table, array($this->table => $id))->result();
      }
    }
    return $this->expandables;
  }

  function getTablePage ($id) {
    $goto = current_url();
    if (strpos($goto, 'form')) $goto = str_replace('form', '', $goto);
    if (strpos($goto, 'delete')) $goto = str_replace('delete', '', $goto);
    if (!is_null($id)) $goto = str_replace("/$id", '', $goto);
    return $goto;  
  }

  function translateDateRange (&$where, $field = 'waktu') {
    if (empty($where)) return;
    foreach (array ('since', 'until') as $between) {
      $operator = $between == 'since' ? ' >=' : ' <=';
      if (isset ($where[$between])) {
        $where["DATE_FORMAT(`$field`, '%m/%d/%Y') $operator"] = $where[$between];
        unset($where[$between]);
      }      
    }
  }

  function find ($where = array(), $where_not_in = array()) {
    $this->db->where($where);
    if (!empty ($where_not_in))
      foreach ($where_not_in as $field => $values)
        $this->db->where_not_in($field, $values);
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
      $this->db->where('id', $data['id'])->update($this->table, $data);
    }
    return $data['id'];
  }

  function update ($data) {
    return $this->save($data);
  }

  function delete ($id) {
    if (isset($this->expandables))
      foreach ($this->expandables as &$exp) {
        $table = explode('[', $exp['fields'][0][0]);
        $table = reset($table);
        $this->db->where($this->table, $id)->delete($table);
      }
    return $this->db->where('id', $id)->delete($this->table);
  }

  function buildRelation (&$dropdown, $table, $where = array(), $default = '') {
    $dropdown[0] = $default;
    $this->db->where($where);
    foreach ($this->db->get($table)->result() as $item)
      $dropdown[$item->id] = $item->nama;
  }

  function sirkulasiKeuangan ($type, $transaksi, $nominal, $fkey, $waktu) {
    if ($nominal <= 0) return true;
    $cashflow = $this->db->get('cashflow')->result();
    $last = end($cashflow);
    $saldo = isset($last->saldo) ? $last->saldo : 0;
    $saldo = $type == 'MASUK' ? $saldo + $nominal : $saldo - $nominal;
    $cashflow = array (
      'waktu' => $waktu,
      'type' => $type,
      'transaksi' => $transaksi,
      'fkey' => $fkey,
      'nominal' => $nominal,
      'saldo' => $saldo,
    );
    $this->db->insert('cashflow', $cashflow);
  }

  function sirkulasiBarang ($waktu, $barang, $type, $transaksi, $fkey, $qty) {
    if ($qty <= 0) return true;
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
    if ($pcs <= 0 && $kg <= 0) return true;
    $operator = $type == 'MASUK' ? '+' : '-';
    $this->db
      ->where('id', $ayam)
      ->set('pcs', "pcs $operator " . $pcs, false)
      ->set('kg', "kg $operator " . $kg, false)
      ->update('ayam');
    // die ($this->db->last_query());
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
    if ($qty <= 0) return true;
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
    if ($nominal <= 0) return true;
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
    if ($qty <= 0) return true;
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
    if ($pcs <= 0 && $kg <= 0) return true;
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
    if ($qty <= 0) return true;
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

  function validate ($data) {
    if (!empty ($this->expandables))
      foreach ($this->expandables as $exp) {
        $detail = $this->getExpDetail($exp);
        /*  VALIDATE EMPTY SUBFORM  */
        if (!isset ($data[$detail['table']])) return array($exp['label'] . ' TIDAK BOLEH KOSONG', 'error');
  
        /*  VALIDATE REQUIRED  */
        if (!isset ($exp['required'])) continue;
        else foreach ($data[$detail['table']][$detail['fields'][0]] as $key => $value) {
          foreach ($exp['required'] as $input_name) {

            $user_input = $data[$detail['table']][$input_name][$key];
            $input_label = strtoupper($input_name);
            if (empty ($user_input)) return array($input_label . ' TIDAK BOLEH KOSONG', 'error');

            /*  VALIDATE INTEGER  */
            if (!is_numeric($user_input) && (!isset ($exp['strings']) || !in_array($input_name, $exp['strings'])))
              return array($input_label . ' HARUS BERUPA ANGKA', 'error');
          }
        }
      }

    if (!isset ($this->strings)) $this->strings = array();
    $this->strings[] = 'waktu';
    foreach ($this->inputFields as $mainfield) {
      if (isset ($this->required) && in_array ($mainfield[0], $this->required) && empty ($data[$mainfield[0]]))
        return array($mainfield[1] . ' TIDAK BOLEH KOSONG', 'error');
      if (!in_array($mainfield[0], $this->strings) && !is_numeric($data[$mainfield[0]]))
        return array($mainfield[1] . ' HARUS BERUPA ANGKA', 'error');
    }
    return true;
  }

  /*  KEMBALIKAN ISIAN FORMULIR USER SAAT GAGAL VALIDASI  */
  function prePopulate ($entity, $data) {
    $data['form'] = $entity;

    if (isset ($this->expandables))
      foreach ($this->expandables as $exp) {
        $detail = $this->getExpDetail($exp);
        $table = $detail['table'];
  
        foreach ($data['expandables'] as &$dex) {
          if ($dex['label'] != $exp['label']) continue;
          foreach ($entity[$table] as $field => $values) {
            foreach ($values as $index => $value) {
              if (!isset ($dex['subform'][$index])) {
                $dex['subform'][$index] = new stdClass();
                $dex['subform'][$index]->id = 0;
              } 
              $dex['subform'][$index]->$field = $value;            
            }
          }
        }
      }
    
    return $data;
  }

  function getExpDetail ($exp) {
    $detail = array('table' => '', 'fields' => array());
    $field0 = $exp['fields'][0][0];
    $split = explode('[', $field0);
    $detail['table'] = $split[0];
    foreach ($exp['fields'][0] as $field) {
      $f = str_replace($detail['table'] . '[', '', $field);
      $f = str_replace('][]', '', $f);
      $detail['fields'][] = $f;
    }
    return $detail;
  }
}