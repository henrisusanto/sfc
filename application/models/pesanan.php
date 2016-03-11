<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class pesanan extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'pesanan';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('outlet','OUTLET'),
      array('nama','PENANGGUNG JAWAB'),
      array('total','TOTAL'),
      array('dibayar','TELAH DIBAYAR'),
      array('kekurangan','KEKURANGAN')
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('outlet', 'OUTLET'),
      2 => array('karyawan', 'PENANGGUNG JAWAB'),
    );

    $this->buildRelation($this->inputFields[1][2], 'outlet');
    $this->buildRelation($this->inputFields[2][2], 'karyawan');

    $this->expandables = array();
    $this->expandables[0] = array(
      'label' => 'DAFTAR PRODUK PESANAN',
      'fields' => array (
        0 => array('pesanandetail[produk][]', 'NAMA PRODUK'),
        1 => array('pesanandetail[qty][]', 'JUMLAH'),
      )
    );

    $this->buildRelation($this->expandables[0]['fields'][0][2], 'produk');
  }

  function save ($data) {
    if (isset($data['id'])) die('durung tak pikir');
    $total = 0;
    foreach ($data['pesanandetail']['total'] as $hargatotal) $total += $hargatotal;
    $this->db->insert('pesanan', array(
      'waktu' => $data['waktu'],
      'karyawan' => $data['karyawan'],
      'total' => $total
    ));
    $pesanan = $this->db->insert_id();
    foreach ($data['pesanandetail']['barang'] as $key => $value) {
      if ($value == 0) continue;
      $this->db->insert('pesanandetail', array(
        'pesanan' => $pesanan,
        'distributor' => $data['pesanandetail']['distributor'][$key],
        'barang' => $data['pesanandetail']['barang'][$key],
        'qty' => $data['pesanandetail']['qty'][$key],
        'hargasatuan' => $data['pesanandetail']['total'][$key] / $data['pesanandetail']['qty'][$key],
        'total' => $data['pesanandetail']['total'][$key],
      ));
      $id = $this->db->insert_id();
      $this->sirkulasiBarang ($data['waktu'], $data['pesanandetail']['barang'][$key], 'MASUK', 'pesanan', $id, $data['pesanandetail']['qty'][$key]);
    }
    $this->sirkulasiKeuangan ('KELUAR', 'pesanan', $total, $pesanan, $data['waktu']);
  }


  function find ($where = array()) {
    $this->db
      ->select('pesanan.*, karyawan.nama as karyawan, outlet.nama as outlet', false)
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT('Rp ', FORMAT(total, 2)) AS total", false)
      ->select("SUM(pesanan.bayar)")
      ->join('karyawan', 'karyawan.id = pesanan.karyawan', 'LEFT')
      ->join('outlet', 'outlet.id = pesanan.outlet', 'LEFT');
    return parent::find($where);
  }
}
