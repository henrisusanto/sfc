<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class pesananbarang extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'pesananbarang';
  }

  function update ($data, $waktu, $reason) {
    $previous = $this->findOne($data['id']);
    $data['id'] = parent::save($data);

    if ($data['qty'] > $previous['qty'])
      $this->sirkulasiBarang ($waktu, $data['barang'], 'KELUAR', $reason, $data['id'], $data['qty'] - $previous['qty']);
    if ($data['qty'] < $previous['qty'])
      $this->sirkulasiBarang ($waktu, $data['barang'], 'MASUK', $reason, $data['id'], $previous['qty'] - $data['qty']);
  }

  function save ($data, $waktu, $reason) {
    $data['id'] = parent::save($data);
    $this->sirkulasiBarang ($waktu, $data['barang'], 'KELUAR', $reason, $data['id'], $data['qty']);
  }

  function delete ($data, $waktu, $reason) {
    $this->sirkulasiBarang ($waktu, $data->barang, 'MASUK', $reason, $data->id, $data->qty);
  }
}