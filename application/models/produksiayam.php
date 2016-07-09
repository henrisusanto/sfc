<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class produksiayam extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'produksiayam';
  }

  function update ($data, $waktu, $reason, $outlet) {
    $previous = $this->findOne($data['id']);
    $data['id'] = parent::save($data);
    if ($data['ekor'] > $previous['ekor'] && $data['kg'] > $previous['kg'])
      $this->sirkulasiAyam ($data['waktu'], $data['ayam'], 'KELUAR', $reason, $data['id'], 
      $data['ekor'] - $previous['ekor'], $data['kg'] - $previous['kg']);
    else if ($data['ekor'] < $previous['ekor'] && $data['kg'] < $previous['kg'])
      $this->sirkulasiAyam ($data['waktu'], $data['ayam'], 'MASUK', $reason, $data['id'], 
      $previous['ekor'] - $data['ekor'], $previous['kg'] - $data['kg']);
    else {
      if ($data['ekor'] > $previous['ekor'])
        $this->sirkulasiAyam ($data['waktu'], $data['ayam'], 'KELUAR', $reason, $data['id'], 
        $data['ekor'] - $previous['ekor'], 0);      
      if ($data['kg'] > $previous['kg'])
        $this->sirkulasiAyam ($data['waktu'], $data['ayam'], 'KELUAR', $reason, $data['id'], 
        0, $data['kg'] - $previous['kg']);
      if ($data['ekor'] < $previous['ekor'])
        $this->sirkulasiAyam ($data['waktu'], $data['ayam'], 'MASUK', $reason, $data['id'], 
        $previous['ekor'] - $data['ekor'], 0);
      if ($data['kg'] < $previous['kg'])
        $this->sirkulasiAyam ($data['waktu'], $data['ayam'], 'MASUK', $reason, $data['id'], 
        0, $previous['kg'] - $data['kg']);
    }
  }

  function save ($data, $waktu, $reason, $outlet) {
    if ($outlet==0) $this->sirkulasiAyam ($waktu, $ayam, 'KELUAR', $reason, $fkey, $pcs, $kg);
    else $this->sirkulasiAyamOutlet ($waktu, $ayam, 'KELUAR', $reason, $fkey, $pcs, $kg, $outlet);
  }

  function delete ($id, $reason, $waktu, $outlet) {
    $data = $this->findOne($id);
    parent::delete($id);
    return 0 == $outlet ? $this->sirkulasiAyam ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], $data['pcs'], $data['kg']):
    $this->sirkulasiAyamOutlet ($waktu, $data['ayam'], 'MASUK', $reason, $data['id'], $data['pcs'], $data['kg'], $outlet);    
  }

}
