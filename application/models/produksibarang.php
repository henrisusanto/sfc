<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class produksibarang extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'produksibarang';
  }

  function update ($data, $waktu, $reason, $outlet) {
    $previous = $this->findOne($data['id']);
    $data['id'] = parent::save($data);
    if ($data['qty'] > $previous['qty']) {
      return 0 == $outlet ? $this->sirkulasiBarang ($waktu, $data['barang'], 'KELUAR', $reason, $data['id'], $data['qty'] - $previous['qty']):
      $this->sirkulasiBarangOutlet ($waktu, $data['barang'], 'KELUAR', $reason, $data['id'], $data['qty'] - $previous['qty'], $outlet);
    }
    if ($data['qty'] < $previous['qty']) {
      return 0 == $outlet ? $this->sirkulasiBarang ($waktu, $data['barang'], 'MASUK', $reason, $data['id'], $previous['qty'] - $data['qty']):
      $this->sirkulasiBarangOutlet ($waktu, $data['barang'], 'MASUK', $reason, $data['id'], $previous['qty'] - $data['qty'], $outlet);
    }
    return $data['id'];
  }

  function save ($data, $waktu, $reason, $outlet) {
    $data['id'] = parent::save($data);
    if ($outlet==0) $this->sirkulasiBarang ($waktu, $data['barang'], 'KELUAR', $reason, $data['id'], $data['qty']);
    else $this->sirkulasiBarangOutlet ($waktu, $data['barang'], 'KELUAR', $reason, $data['id'], $data['qty'], $outlet);
    return $data['id'];
  }

  function delete ($id, $reason, $waktu, $outlet) {
    $data = $this->findOne($id);
    parent::delete($id);
    return 0 == $outlet ? $this->sirkulasiBarang ($waktu, $data['barang'], 'MASUK', $reason, $id, $data['qty']):
    $this->sirkulasiBarangOutlet ($waktu, $data['barang'], 'MASUK', $reason, $id, $data['qty'], $outlet);
  }

}
