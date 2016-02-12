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
      array('qtyPcs','PCS'),
      array('qtyKg','KG'),
      array('pcs','STOK PCs'),
      array('kg','STOK KG')
    );
    $this->inputFields = null;
  }

  function find ($where = array()) {
    $this->db
      ->select('sirkulasiayam.*')
      ->select('ayam.nama as ayam')
      ->select("CONCAT(qtyPcs, ' PCs') as qtyPcs", false)
      ->select("CONCAT(qtyKg, ' KG') as qtyKg", false)
      ->select("CONCAT (ayam.pcs, ' PCs') AS pcs", false)
      ->select("CONCAT (ayam.kg, ' KG') AS kg", false)
      ->join('ayam', 'ayam.id=sirkulasiayam.ayam');
    return parent::find($where);
  }
}
