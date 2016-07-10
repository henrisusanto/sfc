<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class bawaanproduk extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'bawaanproduk';
  }

  function update ($data, $waktu, $reason, $outlet) {
    $previous = $this->findOne($data['id']);
    $data['id'] = parent::save($data);
    if ($data['qty'] > $previous['qty']) {
      $this->sirkulasiProduk ($waktu, $data['produk'], 'KELUAR', $reason, $data['id'], $data['qty'] - $previous['qty']);
      $this->sirkulasiProdukOutlet ($waktu, $data['produk'], 'MASUK', $reason, $data['id'], $data['qty'] - $previous['qty'], $outlet);
    }
    if ($data['qty'] < $previous['qty']) {
      $this->sirkulasiProduk ($waktu, $data['produk'], 'MASUK', $reason, $data['id'], $previous['qty'] - $data['qty']);
      $this->sirkulasiProdukOutlet ($waktu, $data['produk'], 'KELUAR', $reason, $data['id'], $previous['qty'] - $data['qty'], $outlet);
    }
  }

  function save ($data, $waktu, $reason, $outlet) {
    $data['id'] = parent::save($data);
    $this->sirkulasiProduk ($waktu, $data['produk'], 'KELUAR', $reason, $data['id'], $data['qty']);
    $this->sirkulasiProdukOutlet ($waktu, $data['produk'], 'MASUK', $reason, $data['id'], $data['qty'], $outlet);
  }

  function delete ($data, $waktu, $reason, $outlet) {
    $this->sirkulasiProduk ($waktu, $data->produk, 'MASUK', $reason, $data->id, $data->qty);
    $this->sirkulasiProdukOutlet ($waktu, $data->produk, 'KELUAR', $reason, $data->id, $data->qty, $outlet);
  }
}