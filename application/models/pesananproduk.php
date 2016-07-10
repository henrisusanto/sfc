<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class pesananproduk extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'pesananproduk';
  }

  function update ($data, $waktu, $reason) {
    $previous = $this->findOne($data['id']);
    $data['id'] = parent::save($data);
    $qty = $data['qty'] > $previous['qty'] ? $data['qty'] - $previous['qty'] : $previous['qty'] - $data['qty'];
    $this->sirkulasiProduk ($waktu, $data['produk'], 'MASUK', $reason, $data['id'], $data['qty']);
    $this->sirkulasiProduk ($waktu, $data['produk'], 'KELUAR', $reason, $data['id'], $data['qty']);      
  }

  function save ($data, $waktu, $reason) {
    $data['id'] = parent::save($data);
    $this->sirkulasiProduk ($waktu, $data['produk'], 'MASUK', $reason, $data['id'], $data['qty']);
    $this->sirkulasiProduk ($waktu, $data['produk'], 'KELUAR', $reason, $data['id'], $data['qty']);
  }

  function delete ($data, $waktu, $reason) {
    $this->sirkulasiProduk ($waktu, $data->produk, 'KELUAR', $reason, $data->id, $data->qty);
    $this->sirkulasiProduk ($waktu, $data->produk, 'MASUK', $reason, $data->id, $data->qty);
  }
}