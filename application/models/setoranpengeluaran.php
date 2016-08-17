<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class setoranpengeluaran extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'setoranpengeluaran';
  }

  function update ($data, $waktu, $reason, $outlet) {
    $previous = $this->findOne($data['id']);
    $data['id'] = parent::save($data);
    if ($data['nominal'] > $previous['nominal'])
      $this->sirkulasiKeuanganOutlet ('KELUAR', $reason, $data['nominal'] - $previous['nominal'], $data['id'], $waktu, $outlet);
    if ($previous['nominal'] > $data['nominal'])
      $this->sirkulasiKeuanganOutlet ('MASUK', $reason, $previous['nominal'] - $data['nominal'], $data['id'], $waktu, $outlet);
    return $data['id'];
  }

  function save ($data, $waktu, $reason, $outlet) {
    $data['id'] = parent::save($data);
    $this->sirkulasiKeuanganOutlet ('KELUAR', $reason, $data['nominal'], $data['id'], $waktu, $outlet);
    return $data['id'];
  }

  function delete ($data, $waktu, $reason, $outlet) {
    $this->sirkulasiKeuanganOutlet ('MASUK', $reason, $data->nominal, $data->id, $waktu, $outlet);
    return parent::delete($data->id);
  }
}