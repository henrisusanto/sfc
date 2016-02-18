<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class ayamoutlet extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'ayamoutlet';
    $this->thead = array(
      array('outlet','OUTLET'),
      array('ayam','AYAM'),
      array('pcs','STOK'),
    );
    $this->inputFields = array(
      0 => array('outlet', 'NAMA OUTLET'),
      1 => array('ayam', 'AYAM')
    );

  }

  function find ($where = array()) {
    $this->db
      ->select('ayamoutlet.*')
      ->select("CONCAT(ayamoutlet.pcs, ' PCs') as stock", false)
      ->select('outlet.nama as outlet')
      ->join('outlet','ayamoutlet.outlet=outlet.id')
      ->select('ayam.nama as ayam')
      ->join('ayam','ayamoutlet.ayam=ayam.id', 'LEFT');
    return parent::find($where);
  }
}
