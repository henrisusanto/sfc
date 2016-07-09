<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class produksiayam extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'produksiayam';
  }

  function update ($data, $waktu, $reason, $outlet) {
    $previous = $this->findOne($data['id']);
    $data['id'] = parent::save($data);
    if (0 == $outlet) {
      if ($data['pcs'] > $previous['pcs'] && $data['kg'] > $previous['kg'])
        $this->sirkulasiAyam ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], 
        $data['pcs'] - $previous['pcs'], $data['kg'] - $previous['kg']);
      else if ($data['pcs'] < $previous['pcs'] && $data['kg'] < $previous['kg'])
        $this->sirkulasiAyam ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], 
        $previous['pcs'] - $data['pcs'], $previous['kg'] - $data['kg']);
      else {
        if ($data['pcs'] > $previous['pcs'])
          $this->sirkulasiAyam ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], 
          $data['pcs'] - $previous['pcs'], 0);      
        if ($data['kg'] > $previous['kg'])
          $this->sirkulasiAyam ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], 
          0, $data['kg'] - $previous['kg']);
        if ($data['pcs'] < $previous['pcs'])
          $this->sirkulasiAyam ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], 
          $previous['pcs'] - $data['pcs'], 0);
        if ($data['kg'] < $previous['kg'])
          $this->sirkulasiAyam ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], 
          0, $previous['kg'] - $data['kg']);
      }
    } else {
      if ($data['pcs'] > $previous['pcs'] && $data['kg'] > $previous['kg'])
        $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], 
        $data['pcs'] - $previous['pcs'], $data['kg'] - $previous['kg'], $outlet);
      else if ($data['pcs'] < $previous['pcs'] && $data['kg'] < $previous['kg'])
        $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], 
        $previous['pcs'] - $data['pcs'], $previous['kg'] - $data['kg'], $outlet);
      else {
        if ($data['pcs'] > $previous['pcs'])
          $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], 
          $data['pcs'] - $previous['pcs'], 0, $outlet);      
        if ($data['kg'] > $previous['kg'])
          $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], 
          0, $data['kg'] - $previous['kg'], $outlet);
        if ($data['pcs'] < $previous['pcs'])
          $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], 
          $previous['pcs'] - $data['pcs'], 0, $outlet);
        if ($data['kg'] < $previous['kg'])
          $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], 
          0, $previous['kg'] - $data['kg'], $outlet);
      }
    }
  }

  function save ($data, $waktu, $reason, $outlet) {
    $data['id'] = parent::save($data);
    if ($outlet==0) $this->sirkulasiAyam ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], $data['pcs'], $data['kg']);
    else $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'KELUAR', $reason, $data['id'], $data['pcs'], $data['kg'], $outlet);
  }

  function delete ($id, $reason, $waktu, $outlet) {
    $data = $this->findOne($id);
    parent::delete($id);
    return 0 == $outlet ? $this->sirkulasiAyam ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], $data['pcs'], $data['kg']):
    $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], $data['pcs'], $data['kg'], $outlet);    
  }

}
