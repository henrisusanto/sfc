<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class pengeluaran extends my_controller {

  public function gudang ($tpl='table', $id=null) {
    $this->crud ('pengeluarangudang', $tpl, $id);
  }

  public function belanjaayam ($tpl='table', $id=null) {
    $this->crud ('belanjaayam', $tpl, $id);
  }

  public function belanja ($tpl='table', $id=null) {
    $this->crud ('belanja', $tpl, $id);
  }

}
