<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class pengeluarangudang extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'pengeluarandetail';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('karyawan','PENANGGUNG JAWAB'),
      array('item','PENGELUARAN'),
      array('nominal','TOTAL'),
    );
    $this->inputFields = array(
      0 => array('nama', 'NAMA DEBITUR')
    );
  }

}
