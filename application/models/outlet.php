<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class outlet extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'outlet';
    $this->thead = array(
      array('nama','NAMA OUTLET'),
      array('saldo','SALDO OUTLET')
    );
    $this->inputFields = array(
      0 => array('nama', 'NAMA OUTLET')
    );
    $this->strings = array('nama');
    $this->required = array('nama');
  }

  function find ($where = array()) {
    $this->db
      ->select('outlet.*')
      ->select("CONCAT('Rp ', FORMAT(saldo, 2)) AS saldo", false);
    return parent::find($where);
  }
}
