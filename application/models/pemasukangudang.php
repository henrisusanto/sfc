<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class pemasukangudang extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'pemasukan';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('karyawan','PENANGGUNG JAWAB'),
      array('item','PEMASUKAN'),
      array('nominal','NOMINAL'),
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('karyawan', 'PENANGGUNG JAWAB'),
      2 => array('item', 'ITEM'),
      3 => array('nominal', 'NOMINAL'),
    );

    $this->buildRelation($this->inputFields[1][2], 'karyawan');
  }

  function save ($data) {
    if (isset($data['id'])) {
      $record = $this->findOne($data['id']);
      $this->sirkulasiKeuangan ('KELUAR', 'PEMASUKAN BATAL', $record['nominal'], $data['id'], date ('Y-m-d H:i:s', time()));
    } 
    parent::save ($data);
    $this->sirkulasiKeuangan ('MASUK', 'PEMASUKAN', $data['nominal'], $this->db->insert_id(), $data['waktu']);
  }

  function delete ($id) {
    $record = $this->findOne($id);
    $this->sirkulasiKeuangan ('KELUAR', 'PEMASUKAN BATAL', $record['nominal'], $id, date ('Y-m-d H:i:s', time()));
    return parent::delete ($id);
  }

  function find ($where = array()) {
    $this->db
      ->select('pemasukan.*, karyawan.nama as karyawan')
      ->select("CONCAT('Rp ', FORMAT(nominal, 2)) AS nominal", false)
      ->join('karyawan', 'karyawan.id = pemasukan.karyawan', 'LEFT');
    return parent::find($where);
  }

}
