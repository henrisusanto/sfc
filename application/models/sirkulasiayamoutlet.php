<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class sirkulasiayamoutlet extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'sirkulasiayamoutlet';
    $this->thead = array(
      array('outlet','OUTLET'),
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
      ->select('sirkulasiayamoutlet.*')
      ->select('outlet.nama as outlet', false)
      ->select('ayam.nama as ayam')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT(sirkulasiayamoutlet.pcs, ' PCs') as pcs", false)
      ->select("CONCAT(sirkulasiayamoutlet.kg, ' KG') as kg", false)
      ->select("CONCAT (stockpcs, ' PCs') AS stockpcs", false)
      ->select("CONCAT (stockkg, ' KG') AS stockkg", false)
      ->join('ayam', 'ayam.id=sirkulasiayamoutlet.ayam')
      ->join('outlet', 'sirkulasiayamoutlet.outlet=outlet.id')
      ->order_by('id', 'DESC');
    return parent::find($where);
  }
}
