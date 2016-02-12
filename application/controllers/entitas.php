<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class entitas extends my_controller {

  public function debitur ($tpl='table', $id=null) {
    $this->crud ('debitur', $tpl, $id);
  }

  public function karyawan ($tpl='table', $id=null) {
    $this->crud ('karyawan', $tpl, $id);
  }

  public function outlet ($tpl='table', $id=null) {
    $this->crud ('outlet', $tpl, $id);
  }

  public function distributor ($tpl='table', $id=null) {
    $this->crud ('distributor', $tpl, $id);
  }

  public function baranggudang ($tpl='table', $id=null) {
    $this->crud ('baranggudang', $tpl, $id);
  }

  public function ayam ($tpl='table', $id=null) {
    $this->crud ('ayam', $tpl, $id);
  }

  public function produk ($tpl='table', $id=null) {
    $this->crud ('produk', $tpl, $id);
  }

  public function komposisi ($tpl='table', $id=null) {
    $this->crud ('komposisi', $tpl, $id);
  }

  public function produkoutlet ($tpl='table', $id=null) {
    $this->crud ('produkoutlet', $tpl, $id);
  }
}
