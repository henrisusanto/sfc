<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class belanjaayam extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'belanjaayam';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('karyawan','PENANGGUNG JAWAB'),
      array('distributor','DISTRIBUTOR'),
      array('ekor','JUMLAH'),
      array('kg','BERAT'),
      array('total','HARGA TOTAL')
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('karyawan', 'PENANGGUNG JAWAB'),
      2 => array('distributor', 'DISTRIBUTOR'),
      3 => array('ekor', 'JUMLAH (EKOR)'),
      4 => array('kg', 'BERAT TOTAL'),
      5 => array('total', 'HARGA TOTAL'),
    );

    $this->buildRelation($this->inputFields[1][2], 'karyawan');
    $this->buildRelation($this->inputFields[2][2], 'distributor');
  }

  function save ($data) {
    if (isset($data['id'])) $this->delete ($data['id']);
    $ayam = $this->db->get_where('ayam', array('nama' => 'AYAM HIDUP'))->row_array();
    if (!isset($ayam['id'])) die('AYAM HIDUP tidak ditemukan dalam tabel AYAM');
    $belanjaayam = array(
      'waktu' => $data['waktu'],
      'karyawan' => $data['karyawan'],
      'distributor' => $data['distributor'],
      'ayam' => $ayam['id'],
      'ekor' => $data['ekor'],
      'kg' => $data['kg'],
      'total' => $data['total'],
    );
    if (isset($data['id'])) $belanjaayam['id'] = $data['id'];
    $this->db->insert('belanjaayam', $belanjaayam);
    $fkey = $this->db->insert_id();
    $this->sirkulasiAyam ($data['waktu'], $ayam['id'], 'MASUK', 'BELANJA AYAM', $fkey, $data['ekor'], $data['kg']);
    $this->sirkulasiKeuangan ('KELUAR', 'BELANJA AYAM', $data['total'], $fkey, $data['waktu']);
  }

  function delete ($id) {
    $waktu = date('Y-m-d H:i:s',time());
    $ayam = $this->db->get_where('ayam', array('nama' => 'AYAM HIDUP'))->row_array();
    $belanjaayam = $this->findOne ($id);
    $this->sirkulasiAyam ($waktu, $ayam['id'], 'KELUAR', 'PEMBATALAN BELANJA AYAM', $id, $belanjaayam['ekor'], $belanjaayam['kg']);
    $this->sirkulasiKeuangan ('MASUK', 'PEMBATALAN BELANJA AYAM', $belanjaayam['total'], $id, $waktu);
    return parent::delete($id);
  }

  function find ($where = array()) {
    $this->db
      ->select('belanjaayam.*')
      ->select('karyawan.nama as karyawan')
      ->select('distributor.nama as distributor')
      ->select("CONCAT(ekor, ' EKOR') as ekor", false)
      ->select("CONCAT(kg, ' KG') as kg", false)
      ->select("CONCAT('Rp ', FORMAT(total, 2)) AS total", false)
      ->join('karyawan', 'karyawan.id = belanjaayam.karyawan' ,'LEFT')
      ->join('distributor', 'distributor.id = belanjaayam.distributor', 'LEFT');
    return parent::find($where);
  }
}
