<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class bawaan extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'bawaan';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('outlet','OUTLET')
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('outlet', 'OUTLET')
    );
    $this->subFields = array (
      0 => array('item[]', 'BAWAAN'),
      1 => array('qty[]', 'JUMLAH'),
    );

    $this->inputFields[1][2][0] = '';
    foreach ($this->findAnother('outlet') as $item)
      $this->inputFields[1][2][$item->id] = $item->nama;

    $this->subFields[0][2][0] = '';
    foreach ($this->findAnother('baranggudang') as $item)
      $this->subFields[0][2][$item->id] = $item->nama;

  }

  function getSubFields ($index) {
    if ($index == 1) return parent::getSubFields();

    $products = array (
      0 => array('item[]', 'BAWAAN'),
      1 => array('qty[]', 'JUMLAH'),
    );

    $products[0][2][0] = '';
    foreach ($this->findAnother('produk') as $item)
      $products[0][2][$item->id] = $item->nama;

    return $products;
  }

}
