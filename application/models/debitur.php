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

  function saldo ($debitur, $nominal, $plusminus) {
    $debitur = $this->findOne($debitur);
    $debitur['saldo'] = $plusminus == '+' ? $debitur['saldo'] + $nominal : $debitur['saldo'] - $nominal;
    return $this->save($debitur);
  }
}
