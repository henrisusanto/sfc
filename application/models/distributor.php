<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class distributor extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'distributor';
    $this->thead = array(
      array('nama','NAMA DISTRIBUTOR')
    );
    $this->inputFields = array(
      0 => array('nama', 'NAMA DISTRIBUTOR')
    );
  }

}
