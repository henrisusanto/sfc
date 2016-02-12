<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class sirkulasi extends my_controller {

  public function keuangan ($tpl='table', $id=null) {
    $this->crud ('cashflow', $tpl, $id);
  }

  public function barang ($tpl='table', $id=null) {
    $this->crud ('sirkulasibarang', $tpl, $id);
  }

  public function ayam ($tpl='table', $id=null) {
    $this->crud ('sirkulasiayam', $tpl, $id);
  }

  public function produk ($tpl='table', $id=null) {
    $this->crud ('sirkulasiproduk', $tpl, $id);
  }

  public function pinjaman ($tpl='table', $id=null) {
    $this->crud ('pinjaman', $tpl, $id);
  }
}
