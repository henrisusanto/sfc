<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class setoransisabarang extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'setoransisabarang';
  }

  function update ($data, $waktu, $reason, $outlet) {
    $previous = $this->findOne($data['id']);
    $data['id'] = parent::save($data);
    if ($data['qty'] > $previous['qty']) {
      $this->sirkulasiBarangOutlet ($waktu, $data['barang'], 'KELUAR', $reason, $data['id'], $data['qty'] - $previous['qty'], $outlet);
      $this->sirkulasiBarang ($waktu, $data['barang'], 'MASUK', $reason, $data['id'], $data['qty'] - $previous['qty']);
    }
    if ($previous['qty'] > $data['qty']) {
      $this->sirkulasiBarangOutlet ($waktu, $data['barang'], 'MASUK', $reason, $data['id'], $previous['qty'] - $data['qty'], $outlet);
      $this->sirkulasiBarang ($waktu, $data['barang'], 'KELUAR', $reason, $data['id'], $previous['qty'] - $data['qty']);
    }
    return $data['id'];
  }

  function save ($data, $waktu, $reason, $outlet) {
    $data['id'] = parent::save($data);
    $this->sirkulasiBarangOutlet ($waktu, $data['barang'], 'KELUAR', $reason, $data['id'], $data['qty'], $outlet);
    $this->sirkulasiBarang ($waktu, $data['barang'], 'MASUK', $reason, $data['id'], $data['qty']);
    return $data['id'];
  }

  function delete ($data, $waktu, $reason, $outlet) {
    $this->sirkulasiBarangOutlet ($waktu, $data->barang, 'MASUK', $reason, $data->id, $data->qty, $outlet);
    $this->sirkulasiBarang ($waktu, $data->barang, 'KELUAR', $reason, $data->id, $data->qty);
    return parent::delete($data->id);
  }
}