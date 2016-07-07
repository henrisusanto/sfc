<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class belanjadetail extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'belanjadetail';
  }

  function save ($data, $waktu, $reason) {
    $old= isset ($data['id']) ? $this->findOne($data['id']) : array();
    $data['id'] = parent::save($data);
    if (empty ($old)) $this->sirkulasiBarang ($waktu, $data['barang'], 'MASUK', $reason, $data['id'], $data['qty']);      
    else {
      if ($old['qty'] > $data['qty'])
        $this->sirkulasiBarang ($waktu, $data['barang'], 'KELUAR', $reason, $data['id'], $old['qty'] - $data['qty']);
      else if ($old['qty'] < $data['qty'])
        $this->sirkulasiBarang ($waktu, $data['barang'], 'MASUK', $reason, $data['id'], $data['qty'] - $old['qty']);
    }
    return $data['id'];
  }

  function delete ($id, $waktu, $reason) {
    $data = $this->findOne($id);
    $this->sirkulasiBarang ($waktu, $data['barang'], 'KELUAR', $reason, $data['id'], $data['qty']);
    return parent::delete($id);
  }
}