<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class sirkulasibarangoutlet extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'sirkulasibarangoutlet';
    $this->thead = array(
      array('outlet','OUTLET'),
      array('waktu','TANGGAL'),
      array('barang','BAHAN'),
      array('type','FLOW'),
      array('transaksi','TRANSAKSI'),
      array('qty','QTY'),
      array('stock','STOK'),
    );
    $this->inputFields = null;
  }

  function find ($where = array()) {
    $this->db
      ->select('sirkulasibarangoutlet.*')
      ->select('outlet.nama as outlet', false)
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select('baranggudang.nama as barang')
      ->select("CONCAT (sirkulasibarangoutlet.qty, ' ', baranggudang.satuan) AS qty", false)
      ->select("CONCAT (sirkulasibarangoutlet.stock, ' ', baranggudang.satuan) AS stock", false)
      ->join('baranggudang', 'baranggudang.id=sirkulasibarangoutlet.barang')
      ->join('outlet', 'sirkulasibarangoutlet.outlet=outlet.id')
      ->order_by('id', 'DESC');
    return parent::find($where);
  }
}
