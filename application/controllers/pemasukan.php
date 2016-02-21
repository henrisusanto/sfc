<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class pemasukan extends my_controller {

  public function gudang ($tpl='table', $id=null) {
    $this->crud ('pemasukangudang', $tpl, $id);
  }

  public function setoran ($tpl='table', $id=null) {
    $this->crud ('setoran', $tpl, $id);
  }
}
