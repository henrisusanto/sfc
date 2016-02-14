<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class sirkulasiayam extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'sirkulasiayam';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('ayam','JENIS'),
      array('type','FLOW'),
      array('transaksi','TRANSAKSI'),
      array('pcs','PCs'),
      array('kg','KG'),
      array('stockpcs','STOK PCs'),
      array('stockkg','STOK KG')
    );
    $this->inputFields = null;
  }

  function find ($where = array()) {
    $this->db
      ->select('sirkulasiayam.*')
      ->select('ayam.nama as ayam')
      ->select("CONCAT(sirkulasiayam.pcs, ' PCs') as pcs", false)
      ->select("CONCAT(sirkulasiayam.kg, ' KG') as kg", false)
      ->select("CONCAT (stockpcs, ' PCs') AS stockpcs", false)
      ->select("CONCAT (stockkg, ' KG') AS stockkg", false)
      ->join('ayam', 'ayam.id=sirkulasiayam.ayam');
    return parent::find($where);
  }
}
