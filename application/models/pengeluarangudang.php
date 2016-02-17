<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class pengeluarangudang extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'pengeluaran';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('karyawan','PENANGGUNG JAWAB'),
      array('total','TOTAL'),
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('karyawan', 'PENANGGUNG JAWAB'),
    );

    $this->buildRelation($this->inputFields[1][2], 'karyawan');

    $this->expandables = array();
    $this->expandables[0] = array(
      'label' => 'DAFTAR PENGELUARAN',
      'fields' => array (
        0 => array('pengeluarandetail[item][]', 'ITEM'),
        1 => array('pengeluarandetail[nominal][]', 'NOMINAL'),
      )
    );
  }

  function save ($data) {
    if (isset($data['id'])) die('rung tak pikir');
    $total = 0;
    foreach ($data['pengeluarandetail']['nominal'] as $nominal) $total += $nominal;
    $this->db->insert('pengeluaran', array(
      'waktu' => $data['waktu'],
      'karyawan' => $data['karyawan'],
      'total' => $total,
    ));
    $pengeluaran_id = $this->db->insert_id();
    
    foreach ($data['pengeluarandetail']['item'] as $index => $pd) {
      $this->db->insert('pengeluarandetail', array(
        'pengeluaran' => $pengeluaran_id,
        'item' => $data['pengeluarandetail']['item'][$index],
        'nominal' => $data['pengeluarandetail']['nominal'][$index],
      ));
    }

    $this->sirkulasiKeuangan ('KELUAR', 'PENGELUARAN', $total, $pengeluaran_id, $data['waktu']);
  }

  function find ($where = array()) {
    $this->db
      ->select('pengeluaran.*, karyawan.nama as karyawan')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT('Rp ', FORMAT(total, 2)) AS total", false)
      ->join('karyawan', 'karyawan.id = pengeluaran.karyawan', 'LEFT');
    return parent::find($where);
  }
}
