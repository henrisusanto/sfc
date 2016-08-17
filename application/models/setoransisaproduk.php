<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class setoransisaproduk extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'setoransisaproduk';
  }

  function update ($data, $waktu, $reason, $outlet) {
    $previous = $this->findOne($data['id']);
    $data['id'] = parent::save($data);
    if ($data['qty'] > $previous['qty']) {
      $this->sirkulasiProdukOutlet ($waktu, $data['produk'], 'KELUAR', $reason, $data['id'], $data['qty'] - $previous['qty'], $outlet);
      $this->sirkulasiProduk ($waktu, $data['produk'], 'MASUK', $reason, $data['id'], $data['qty'] - $previous['qty']);      
    }
    if ($previous['qty'] > $data['qty']) {
      $this->sirkulasiProdukOutlet ($waktu, $data['produk'], 'MASUK', $reason, $data['id'], $previous['qty'] - $data['qty'], $outlet);
      $this->sirkulasiProduk ($waktu, $data['produk'], 'KELUAR', $reason, $data['id'], $previous['qty'] - $data['qty']);      
    }
    return $data['id'];
  }

  function save ($data, $waktu, $reason, $outlet) {
    $data['id'] = parent::save($data);
    $this->sirkulasiProdukOutlet ($waktu, $data['produk'], 'KELUAR', $reason, $data['id'], $data['qty'], $outlet);
    $this->sirkulasiProduk ($waktu, $data['produk'], 'MASUK', $reason, $data['id'], $data['qty']);
    return $data['id'];
  }

  function delete ($data, $waktu, $reason, $outlet) {
    $this->sirkulasiProdukOutlet ($waktu, $data->produk, 'MASUK', $reason, $data->id, $data->qty, $outlet);
    $this->sirkulasiProduk ($waktu, $data->produk, 'KELUAR', $reason, $data->id, $data->qty);
    return parent::delete($data->id);
  }
}