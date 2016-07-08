<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class pemotongandetail extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'pemotongandetail';
  }

  function update ($data, $waktu, $reason) {
    $previous = $this->findOne($data['id']);
    $data['id'] = parent::save($data);

    if ($data['pcs'] > $previous['pcs'] && $data['kg'] > $previous['kg'])
      $this->sirkulasiAyam ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], 
      $data['pcs'] - $previous['pcs'], $data['kg'] - $previous['kg']);
    else if ($data['pcs'] < $previous['pcs'] && $data['kg'] < $previous['kg'])
      $this->sirkulasiAyam ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], 
      $previous['pcs'] - $data['pcs'], $previous['kg'] - $data['kg']);
    else {
      if ($data['pcs'] > $previous['pcs'])
        $this->sirkulasiAyam ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], 
        $data['pcs'] - $previous['pcs'], 0);      
      if ($data['kg'] > $previous['kg'])
        $this->sirkulasiAyam ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], 
        0, $data['kg'] - $previous['kg']);
      if ($data['pcs'] < $previous['pcs'])
        $this->sirkulasiAyam ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], 
        $previous['pcs'] - $data['pcs'], 0);
      if ($data['kg'] < $previous['kg'])
        $this->sirkulasiAyam ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], 
        0, $previous['kg'] - $data['kg']);
    }

    return $data['id'];    
  }

  function save ($data, $waktu, $reason) {
    $data['id'] = parent::save($data);
    $this->sirkulasiAyam ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], $data['pcs'], $data['kg']);
    return $data['id'];
  }

  function delete ($id, $waktu, $reason) {
    $data = $this->findOne($id);
    $this->sirkulasiAyam ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], $data['pcs'], $data['kg']);
    return parent::delete($id);
  }
}