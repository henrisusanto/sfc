<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class produksi extends my_controller {

  public function pemotongan ($tpl='table', $id=null) {
    $this->crud ('pemotongan', $tpl, $id);
  }

  public function bawaan ($tpl='table', $id=null) {
    $this->crud ('bawaan', $tpl, $id);
  }
}