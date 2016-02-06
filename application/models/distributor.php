<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class distributor extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'distributor';
  }

}
