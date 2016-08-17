<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class setoranbarangoutlet extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'setoranbarangoutlet';
  }

  function update ($data, $waktu, $reason, $outlet) {
    $previous = $this->findOne($data['id']);
    $data['id'] = parent::save($data);

    return $data['id'];
  }

  function save ($data, $waktu, $reason, $outlet) {
    $data['id'] = parent::save($data);
      $this->db
        ->where('outlet', $outlet)
        ->where('barang', $barang)
        ->set('stock', $qty)
        ->update('barangoutlet');
    return $data['id'];
  }

  function delete ($data, $waktu, $reason, $outlet) {
    return parent::delete($data->id);
  }
}