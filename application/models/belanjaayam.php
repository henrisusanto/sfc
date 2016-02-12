<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class belanjaayam extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'belanja';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('karyawan','PEMBELI'),
      array('nama','PENJUAL'),
      array('qtyEkor','JUMLAH'),
      array('qtyKg','BERAT'),
      array('total','HARGA')
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('karyawan', 'PENANGGUNG JAWAB'),
      2 => array('distributor', 'DISTRIBUTOR'),
      3 => array('qtyEkor', 'JUMLAH EKOR'),
      4 => array('qtyKg', 'BERAT TOTAL'),
      5 => array('total', 'HARGA TOTAL'),
    );

    $this->inputFields[1][2][0] = '';
    foreach ($this->findAnother('karyawan') as $item)
      $this->inputFields[1][2][$item->id] = $item->nama;
    $this->inputFields[2][2][0] = '';
    foreach ($this->findAnother('distributor') as $item)
      $this->inputFields[2][2][$item->id] = $item->nama;
  }

  function find ($where = array()) {
    $ayam = $this->db->get_where('baranggudang', array('nama' => 'AYAM HIDUP'))->row_array();
    if ($ayam == array()) return $ayam;
    $this->db->where('belanjadetail.barang', $ayam['id']);
    $this->db
      ->select('belanja.id')
      ->select("DATE_FORMAT(belanja.waktu,'%d %b %Y %T') AS waktu", false)
      ->select('karyawan.nama as karyawan')
      ->select('distributor.nama')
      ->select("CONCAT(belanjadetail.qtyEkor, ' EKOR') AS qtyEkor", false)
      ->select("CONCAT(belanjadetail.qtyKg, ' KG') AS qtyKg", false)
      ->select("CONCAT('Rp ', FORMAT(belanja.total, 2)) AS total", false)
      
      ->join('belanjadetail', 'belanjadetail.belanja = belanja.id', 'left')
      ->join('distributor', 'distributor.id = belanjadetail.distributor', 'left')
      ->join('karyawan', 'karyawan.id = belanja.karyawan', 'left');
    return parent::find($where);
  }

  function save ($data) {
    if (isset($data['id'])) die('durung tak pikir');
    $ayam = $this->db->get_where('baranggudang', array('nama' => 'AYAM HIDUP'))->row_array();
    if ($ayam == array()) return $ayam;
    $data['barang'] = (int)$ayam['id'];
    $id = time();
    $this->db->insert('belanja', array(
      'id' => $id,
      'waktu' => $data['waktu'],
      'karyawan' => $data['karyawan'],
      'total' => $data['total'],
    ));
    $this->db->insert('belanjadetail', array(
      'id' => $id,
      'belanja' => $id,
      'barang' => $data['barang'],
      'qty' => 0,
      'qtyEkor' => $data['qtyEkor'],
      'qtyKg' => $data['qtyKg'],
      'total' => $data['total'],
      'distributor' => $data['distributor'],
    ));
    $this->db
      ->where('id', $data['barang'])
      ->set('ayamPCs', "ayamPCs + " . $data['qtyEkor'], false)
      ->set('ayamKg', "ayamKg + " . $data['qtyKg'], false)
      ->update('baranggudang');
    $sirkulasi = array(
      'id' => $id,
      'waktu' => $data['waktu'],
      'barang' => $data['barang'],
      'type' => 'MASUK',
      'transaksi' => 'BELANJA',
      'fkey' => $id,
      'qtyEkor' => $data['qtyEkor'],
      'qtyKg' => $data['qtyKg']
    );
    $this->db->insert('sirkulasibarang', $sirkulasi);
    $this->sirkulasiKeuangan ('KELUAR', 'BELANJA', $data['total'], $id, $data['waktu']);
    return $id;
  }
}
