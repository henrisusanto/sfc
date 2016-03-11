<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class debitur extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'debitur';
    $this->thead = array(
      array('nama','NAMA DEBITUR'),
    );
    $this->inputFields = array(
      0 => array('nama', 'NAMA DEBITUR')
    );
  }

  function find ($where = array()) {
    return parent::find($where);
  }
}
