<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class bawaanayam extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'bawaanayam';
  }

  function update ($data, $waktu, $reason, $outlet) {
    $previous = $this->findOne($data['id']);
    $data['id'] = parent::save($data);

    if ($data['pcs'] > $previous['pcs'] && $data['kg'] > $previous['kg']) {
      $this->sirkulasiAyam ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], $data['pcs'] - $previous['pcs'], $data['kg'] - $previous['kg']);
      $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], $data['pcs'] - $previous['pcs'], $data['kg'] - $previous['kg'], $outlet);      
    }
    else if ($data['pcs'] < $previous['pcs'] && $data['kg'] < $previous['kg']) {
      $this->sirkulasiAyam ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], $previous['pcs'] - $data['pcs'], $previous['kg'] - $data['kg']);
      $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], $previous['pcs'] - $data['pcs'], $previous['kg'] - $data['kg'], $outlet);
    }
    else {
      if ($data['pcs'] > $previous['pcs']) {
        $this->sirkulasiAyam ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], $data['pcs'] - $previous['pcs'], 0);
        $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], $data['pcs'] - $previous['pcs'], 0, $outlet);
      }
      if ($data['kg'] > $previous['kg']) {
        $this->sirkulasiAyam ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], 0, $data['kg'] - $previous['kg']);
        $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], 0, $data['kg'] - $previous['kg'], $outlet);
      }
      if ($data['pcs'] < $previous['pcs']) {
        $this->sirkulasiAyam ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], $previous['kg'] - $data['kg'], 0);
        $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], $previous['kg'] - $data['kg'], 0, $outlet);
      }
      if ($data['kg'] < $previous['kg']) {
        $this->sirkulasiAyam ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], 0, $previous['kg'] - $data['kg']);
        $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], 0, $previous['kg'] - $data['kg'], $outlet);
      }
    }
  }

  function save ($data, $waktu, $reason, $outlet) {
    $data['id'] = parent::save($data);
    $this->sirkulasiAyam ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], $data['pcs'], $data['kg']);
    $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], $data['pcs'], $data['kg'], $outlet);
  }

  function delete ($data, $waktu, $reason, $outlet) {
    $this->sirkulasiAyam ($waktu, $data->ayam, 'MASUK', $reason, $data->id, $data->pcs, $data->kg);
    $this->sirkulasiAyamOutlet ($waktu, $data->ayam, 'KELUAR', $reason, $data->id, $data->pcs, $data->kg, $outlet);
  }
}