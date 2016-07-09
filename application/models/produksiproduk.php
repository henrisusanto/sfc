<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class produksiproduk extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'produksiproduk';
  }

  function update ($data, $waktu, $reason, $outlet) {
    $previous = $this->findOne($data['id']);
    $data['id'] = parent::save($data);
    if ($data['qty'] > $previous['qty']) {
      return 0 == $outlet ? $this->sirkulasiProduk ($waktu, $data['produk'], 'MASUK', $reason, $data['id'], $data['qty'] - $previous['qty']):
      $this->sirkulasiProdukOutlet ($waktu, $data['produk'], 'MASUK', $reason, $data['id'], $data['qty'] - $previous['qty'], $outlet);
    }
    if ($data['qty'] < $previous['qty']) {
      return 0 == $outlet ? $this->sirkulasiProduk ($waktu, $data['produk'], 'KELUAR', $reason, $data['id'], $previous['qty'] - $data['qty']):
      $this->sirkulasiProdukOutlet ($waktu, $data['produk'], 'KELUAR', $reason, $data['id'], $previous['qty'] - $data['qty'], $outlet);
    }
  }

  function save ($data, $waktu, $reason, $outlet) {
    if ($outlet==0) $this->sirkulasiProduk ($waktu, $data['produk'], 'MASUK', $reason, $data['id'], $data['qty']);
    else $this->sirkulasiProdukOutlet ($waktu, $data['produk'], 'MASUK', $reason, $data['id'], $data['qty'], $outlet);    
  }

  function delete ($id, $reason, $waktu, $outlet) {
    $data = $this->findOne($id);
    parent::delete($id);
    return 0 == $outlet ? $this->sirkulasiProduk ($waktu, $data['produk'], 'KELUAR', $reason, $data['id'], $data['qty']):
    $this->sirkulasiProdukOutlet ($waktu, $data['produk'], 'KELUAR', $reason, $data['id'], $data['qty'], $outlet);    
  }

}
