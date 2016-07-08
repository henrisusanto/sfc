<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class karyawan extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'karyawan';
    $this->thead = array(
      array('nama','NAMA KARYAWAN')
    );
    $this->inputFields = array(
      0 => array('nama', 'NAMA KARYAWAN')
    );
    $this->strings = array('nama');
    $this->required = array('nama');
  }

}
