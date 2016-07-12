<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class internalayam extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'internalayam';
  }

  function update ($data, $waktu, $reason, $source, $destination) {
    $previous = $this->findOne($data['id']);
    $data['id'] = parent::save($data);
      if ($data['pcs'] > $previous['pcs'] && $data['kg'] > $previous['kg']) {
        $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], $data['pcs'] - $previous['pcs'], $data['kg'] - $previous['kg'], $source);
        $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], $data['pcs'] - $previous['pcs'], $data['kg'] - $previous['kg'], $destination);
      }
      else if ($data['pcs'] < $previous['pcs'] && $data['kg'] < $previous['kg']) {
        $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], $previous['pcs'] - $data['pcs'], $previous['kg'] - $data['kg'], $source);
        $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], $previous['pcs'] - $data['pcs'], $previous['kg'] - $data['kg'], $destination);        
      }
      else {
        if ($data['pcs'] > $previous['pcs']) {
          $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], $data['pcs'] - $previous['pcs'], 0, $source);
          $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], $data['pcs'] - $previous['pcs'], 0, $destination);
        }
        if ($data['kg'] > $previous['kg']) {
          $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], 0, $data['kg'] - $previous['kg'], $source);
          $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], 0, $data['kg'] - $previous['kg'], $destination);
        }
        if ($data['pcs'] < $previous['pcs']) {
          $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], $previous['pcs'] - $data['pcs'], 0, $source);
          $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], $previous['pcs'] - $data['pcs'], 0, $destination);
        }
        if ($data['kg'] < $previous['kg']) {
          $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], 0, $previous['kg'] - $data['kg'], $source);
          $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], 0, $previous['kg'] - $data['kg'], $destination);
        }
      }
      return $data['id'];
  }

  function save ($data, $waktu, $reason, $source, $destination) {
    $data['id'] = parent::save($data);
    $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], $data['pcs'], $data['kg'], $source);
    $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], $data['pcs'], $data['kg'], $destination);
    return $data['id'];
  }

  function delete ($data, $reason, $waktu, $source, $destination) {
    $this->sirkulasiAyamOutlet ($waktu, $data->ayam, 'MASUK', $reason, $data->id, $data->pcs, $data->kg, $source);
    $this->sirkulasiAyamOutlet ($waktu, $data->ayam, 'KELUAR', $reason, $data->id, $data->pcs, $data->kg, $destination);
    return parent::delete($data->id);
  }

}
