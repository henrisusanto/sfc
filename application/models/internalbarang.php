<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class internalbarang extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'internalbarang';
  }

  function update ($data, $waktu, $reason, $source, $destination) {
    $previous = $this->findOne($data['id']);
    $data['id'] = parent::save($data);

    if ($data['qty'] > $previous['qty']) {
      $this->sirkulasiBarangOutlet ($waktu, $data['barang'], 'KELUAR', $reason, $data['id'], $data['qty'] - $previous['qty'], $source);
      $this->sirkulasiBarangOutlet ($waktu, $data['barang'], 'MASUK', $reason, $data['id'], $data['qty'] - $previous['qty'], $destination);
    }
    if ($data['qty'] < $previous['qty']) {
      $this->sirkulasiBarangOutlet ($waktu, $data['barang'], 'MASUK', $reason, $data['id'], $previous['qty'] - $data['qty'], $source);
      $this->sirkulasiBarangOutlet ($waktu, $data['barang'], 'KELUAR', $reason, $data['id'], $previous['qty'] - $data['qty'], $destination);
    }
    return $data['id'];
  }

  function save ($data, $waktu, $reason, $source, $destination) {
    $data['id'] = parent::save($data);
    $this->sirkulasiBarangOutlet ($waktu, $data['barang'], 'KELUAR', $reason, $data['id'], $data['qty'], $source);
    $this->sirkulasiBarangOutlet ($waktu, $data['barang'], 'MASUK', $reason, $data['id'], $data['qty'], $destination);
    return $data['id'];
  }

  function delete ($data, $waktu, $reason, $source, $destination) {
    $this->sirkulasiBarangOutlet ($waktu, $data->barang, 'MASUK', $reason, $data->id, $data->qty, $source);
    $this->sirkulasiBarangOutlet ($waktu, $data->barang, 'KELUAR', $reason, $data->id, $data->qty, $destination);
    return parent::delete($data->id);
  }
}