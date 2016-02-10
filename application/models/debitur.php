<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class debitur extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'debitur';
    $this->thead = array(
      array('nama','NAMA DEBITUR'),
      array('saldo','SISA PINJAMAN')
    );
    $this->inputFields = array(
      0 => array('nama', 'NAMA DEBITUR')
    );
  }

  function find ($where = array()) {
    $this->db
      ->select('*')
      ->select("CONCAT('Rp ', FORMAT(saldo, 2)) AS saldo", false);
    return parent::find($where);
  }
}
