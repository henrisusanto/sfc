<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class outlet extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'outlet';
    $this->thead = array(
      array('nama','NAMA OUTLET')
    );
    $this->inputFields = array(
      0 => array('nama', 'NAMA OUTLET')
    );
  }

}
