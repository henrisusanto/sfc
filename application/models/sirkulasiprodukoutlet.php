<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class sirkulasiprodukoutlet extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'sirkulasiprodukoutlet';
    $this->thead = array(
      array('outlet','OUTLET'),
      array('waktu','TANGGAL'),
      array('produk','NAMA PRODUK'),
      array('type','FLOW'),
      array('transaksi','TRANSAKSI'),
      array('qty','QTY'),
      array('stock','STOK'),
    );
    $this->inputFields = null;
  }

  function find ($where = array()) {
    $this->db
      ->select('sirkulasiprodukoutlet.*')
      ->select('outlet.nama as outlet', false)
      ->select('produk.nama as produk')
      ->select("CONCAT (sirkulasiprodukoutlet.qty, ' PCs') AS qty", false)
      ->select("CONCAT (sirkulasiprodukoutlet.stock, ' PCs') AS stock", false)
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->join('produk', 'produk.id=sirkulasiprodukoutlet.produk')
      ->join('outlet', 'sirkulasiprodukoutlet.outlet=outlet.id')
      ->order_by('id', 'DESC');
    return parent::find($where);
  }
}
