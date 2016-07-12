<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class internalproduk extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'internalproduk';
  }

  function update ($data, $waktu, $reason, $source, $destination) {
    $previous = $this->findOne($data['id']);
    $data['id'] = parent::save($data);
    if ($data['qty'] > $previous['qty']) {
      $this->sirkulasiProdukOutlet ($waktu, $data['produk'], 'KELUAR', $reason, $data['id'], $data['qty'] - $previous['qty'], $source);
      $this->sirkulasiprodukOutlet ($waktu, $data['produk'], 'MASUK', $reason, $data['id'], $data['qty'] - $previous['qty'], $destination);      
    }
    if ($previous['qty'] > $data['qty']) {
      $this->sirkulasiProdukOutlet ($waktu, $data['produk'], 'MASUK', $reason, $data['id'], $previous['qty'] - $data['qty'], $source);
      $this->sirkulasiprodukOutlet ($waktu, $data['produk'], 'KELUAR', $reason, $data['id'], $previous['qty'] - $data['qty'], $destination);      
    }
    return $data['id'];
  }

  function save ($data, $waktu, $reason, $source, $destination) {
    $data['id'] = parent::save($data);
    $this->sirkulasiProdukOutlet ($waktu, $data['produk'], 'KELUAR', $reason, $data['id'], $data['qty'], $source);
    $this->sirkulasiprodukOutlet ($waktu, $data['produk'], 'MASUK', $reason, $data['id'], $data['qty'], $destination);
    return $data['id'];
  }

  function delete ($data, $waktu, $reason, $source, $destination) {
    $this->sirkulasiProdukOutlet ($waktu, $data->produk, 'MASUK', $reason, $data->id, $data->qty, $source);
    $this->sirkulasiprodukOutlet ($waktu, $data->produk, 'KELUAR', $reason, $data->id, $data->qty, $destination);
    return parent::delete($data->id);
  }
}