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
    if (isset($data['id'])) die('x');
    $fkey = isset($data['id']) ? $data['id'] : null;
    $this->sirkulasiKeuangan ('MASUK', 'PEMASUKAN', $data['nominal'], $fkey, $data['waktu']);
    return parent::save ($data);
  }

  function find ($where = array()) {
    $this->db
      ->select('pemasukan.*, karyawan.nama as karyawan')
      ->select("CONCAT('Rp ', FORMAT(nominal, 2)) AS nominal", false)
      ->join('karyawan', 'karyawan.id = pemasukan.karyawan', 'LEFT');
    return parent::find($where);
  }

}
