<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class belanja extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'belanja';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('nama','PENANGGUNG JAWAB'),
      array('total','TOTAL')
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('karyawan', 'PENANGGUNG JAWAB'),
    );

    foreach ($this->findAnother('karyawan') as $karyawan)
      $this->inputFields[1][2][$karyawan->id] = $karyawan->nama;

    $this->subFields = array (
      0 => array('barang[]', 'NAMA BARANG'),
      1 => array('distributor[]', 'TOKO / PENJUAL'),
      2 => array('qty[]', 'JUMLAH'),
      3 => array('total[]', 'HARGA TOTAL'),
    );

    $this->subFields[0][2][0] = '';
    foreach ($this->findAnother('baranggudang') as $item)
      $this->subFields[0][2][$item->id] = $item->nama;

    $this->subFields[1][2][0] = '';
    foreach ($this->findAnother('distributor') as $item)
      $this->subFields[1][2][$item->id] = $item->nama;
  }

  function insert ($data) {
    $belanjadetails = array ();
    $baranggudangs = array ();
    $total = 0;
    foreach ($data['barang'] as $index => $value) {
      if ($data['qty'][$index] <= 0 || $data['total'][$index] <= 0) continue;
      $belanjadetails[] = array(
        'id' => time() + $index,
        'belanja' => time(),
        'barang' => $data['barang'][$index],
        'qty' => $data['qty'][$index],
        'total' => $data['total'][$index],
        'distributor' => $data['distributor'][$index],
      );
      $baranggudangs[] = array (
        'id' => $data['barang'][$index],
        'stock' => $data['qty'][$index]
      );
      $total += $data['total'][$index];
    }
    
    $belanja = array (
      'id' => time(),
      'waktu' => $data['waktu'],
      'karyawan' => $data['karyawan'],
      'total' => $total,
    );
    
    $cashflow = array (
      'id' => time(),
      'waktu' => $data['waktu'],
      'type' => 'KELUAR',
      'transaksi' => 'BELANJA',
      'foreignKey' => time(),
      'nominal' => $total,
      'saldo' => parent::cashFlowGetSaldo($total, 'KELUAR'),
    );

    foreach ($belanjadetails as $belanjadetail)
    $this->db->insert('belanjadetail', $belanjadetail);

    foreach ($baranggudangs as $gudang)
    $this->db
      ->where('id', $gudang['id'])
      ->set('stock', "stock+" . $gudang['stock'], false)
      ->update('baranggudang');

    $this->db->insert('belanja', $belanja);
    $this->db->insert('cashflow', $cashflow);
    return time();
  }

  function update ($data) {
    die('too many possibilities, but low priority');
  }

  function delete ($id) {
    $total = 0;
    foreach ($this->findAnother('belanjadetail', array('belanja' => $id)) as $detail) {
      $this->db
        ->where('id', $detail->barang)
        ->set('stock', "stock-" . $detail->qty, false)
        ->update('baranggudang');
      $total += $detail->total;
    }
    $this->db->where('belanja', $id)->delete('belanjadetail');
    $cashflow = array (
      'id' => time(),
      'waktu' => date('Y-m-d H:i:s'),
      'type' => 'MASUK',
      'transaksi' => 'BELANJA BATAL',
      'nominal' => $total,
      'saldo' => parent::cashFlowGetSaldo($total, 'MASUK'),
    );
    $this->db->insert('cashflow', $cashflow);
    return parent::delete($id);
  }

  function find ($where = array()) {
    $this->db
      ->select('belanja.*, karyawan.nama')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT('Rp ', FORMAT(total, 2)) AS total", false)
      ->join('karyawan', 'karyawan.id = belanja.karyawan');
    return parent::find($where);
  }
}
