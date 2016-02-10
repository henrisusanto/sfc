<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class produk extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'produk';
    $this->thead = array(
      array('nama','NAMA PRODUK'),
      array('type','JENIS PRODUK'),
      array('stock','STOK GUDANG'),
      array('harga','HARGA')
    );
    $this->inputFields = array(
      0 => array('nama', 'NAMA PRODUK'),
      1 => array('type', 'JENIS PRODUK', array ('ECERAN'=>'ECERAN', 'PAKET'=>'PAKET')),
      2 => array('harga', 'HARGA JUAL'),
    );
  }

  function find ($where = array()) {
    $this->db
      ->select('*')
      ->select("CONCAT(stock, ' PCs') AS stock", false)
      ->select("CONCAT('Rp ', FORMAT(harga, 2)) AS harga", false);
    return parent::find($where);
  }
}
