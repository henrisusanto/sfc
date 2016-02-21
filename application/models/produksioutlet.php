<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class produksioutlet extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'produksioutlet';
    $this->thead = array(
      array('outlet','NAMA OUTLET'),
      array('waktu','TANGGAL PRODUKSI'),
      array('produk','NAMA PRODUK'),
      array('qty','JUMLAH')
    );
    $this->inputFields = array(
      0 => array('nama', 'JENIS produksioutlet'),
    );
  }

  function find ($where = array()) {
    $this->db
      ->select('produksioutlet.*')
      ->select('outlet.nama as outlet', false)
      ->select('produk.nama as produk', false)
      ->select("CONCAT(qty, ' PCs') as qty", false)
      ->join('outlet', 'produksioutlet.outlet=outlet.id')
      ->join('produk', 'produksioutlet.produk=produk.id');
    return parent::find($where);
  }
}
