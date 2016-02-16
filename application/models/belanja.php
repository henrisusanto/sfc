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

    $this->buildRelation($this->inputFields[1][2], 'karyawan');

    $this->expandables = array();
    $this->expandables[0] = array(
      'label' => 'DAFTAR BARANG BELANJA',
      'fields' => array (
        0 => array('belanjadetail[barang][]', 'NAMA BARANG'),
        1 => array('belanjadetail[distributor][]', 'TOKO / PENJUAL'),
        2 => array('belanjadetail[qty][]', 'JUMLAH'),
        3 => array('belanjadetail[total][]', 'HARGA TOTAL'),
      )
    );

    $this->buildRelation($this->expandables[0]['fields'][0][2], 'baranggudang');
    $this->buildRelation($this->expandables[0]['fields'][1][2], 'distributor');
  }

  function save ($data) {
    if (isset($data['id'])) die('durung tak pikir');
    $total = 0;
    foreach ($data['belanjadetail']['total'] as $hargatotal) $total += $hargatotal;
    $this->db->insert('belanja', array(
      'waktu' => $data['waktu'],
      'karyawan' => $data['karyawan'],
      'total' => $total
    ));
    $belanja = $this->db->insert_id();
    foreach ($data['belanjadetail']['barang'] as $key => $value) {
      $this->db->insert('belanjadetail', array(
        'belanja' => $belanja,
        'distributor' => $data['belanjadetail']['distributor'][$key],
        'barang' => $data['belanjadetail']['barang'][$key],
        'qty' => $data['belanjadetail']['qty'][$key],
        'hargasatuan' => $data['belanjadetail']['total'][$key] / $data['belanjadetail']['qty'][$key],
        'total' => $data['belanjadetail']['total'][$key],
      ));
      $id = $this->db->insert_id();
      $this->sirkulasiBarang ($data['waktu'], $data['belanjadetail']['barang'][$key], 'MASUK', 'BELANJA', $id, $data['belanjadetail']['qty'][$key]);
    }
    $this->sirkulasiKeuangan ('KELUAR', 'BELANJA', $total, $belanja, $data['waktu']);
  }


  function find ($where = array()) {
    $this->db
      ->select('belanja.*, karyawan.nama')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT('Rp ', FORMAT(total, 2)) AS total", false)
      ->join('karyawan', 'karyawan.id = belanja.karyawan', 'LEFT');
    return parent::find($where);
  }
}
