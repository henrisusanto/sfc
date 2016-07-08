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
    $this->strings = array('item');
    $this->required = array('item', 'nominal');
    $this->buildRelation($this->inputFields[1][2], 'karyawan');
  }

  function update ($data) {
    $reason = 'PEMASUKAN';
    $previous = $this->findOne($data['id']);
    if ($data['nominal'] > $previous['nominal'])
      $this->sirkulasiKeuangan ('MASUK', $reason, $data['nominal'] - $previous['nominal'], $data['id'], $data['waktu']);
    if ($data['nominal'] < $previous['nominal'])
      $this->sirkulasiKeuangan ('KELUAR', $reason, $previous['nominal'] - $data['nominal'], $data['id'], $data['waktu']);
    return parent::save ($data);    
  }

  function save ($data) {
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
