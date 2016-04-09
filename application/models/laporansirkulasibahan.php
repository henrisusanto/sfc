<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class laporansirkulasibahan extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'sirkulasibarang';
    $this->thead = array(
      array('barang','BARANG GUDANG'),
      array('waktu','TANGGAL'),
      array('transaksi','TRANSAKSI'),
      array('masuk','MASUK'),
      array('keluar','KELUAR'),
      array('stock','STOK'),
    );
    $this->buildFilters('baranggudang', 'barang');
    $this->buildTFoot();
  }

  function getTFoot ($tbody) {
    $masuk = 0;
    $keluar= 0;
    $stock = 0;
    $satuan= '';
    foreach ($tbody as $tb) {
      $satuan = $tb->satuan;
      $masuk += $tb->masuk;
      $keluar += $tb->keluar;
    }
    $stock = $masuk - $keluar;
    $this->tfoot[3] = 'TOTAL';
    $this->tfoot[4] = "$masuk $satuan";
    $this->tfoot[5] = "$keluar $satuan";
    $this->tfoot[6] = "$stock $satuan";
    return parent::getTFoot($tbody);
  }

  function find ($where = array()) {
    $this->translateDateRange($where);
    $this->db
      ->select('sirkulasibarang.*')
      ->select('baranggudang.satuan')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select('baranggudang.nama as barang')
      ->select("CONCAT (IF (sirkulasibarang.type = 'MASUK', qty, 0), ' ', baranggudang.satuan) AS masuk", false)
      ->select("CONCAT (IF (sirkulasibarang.type = 'KELUAR', qty, 0), ' ', baranggudang.satuan) AS keluar", false)
      ->select("CONCAT (sirkulasibarang.stock, ' ', baranggudang.satuan) AS stock", false)
      ->join('baranggudang', 'baranggudang.id=sirkulasibarang.barang')
      // ->order_by('id', 'DESC')
      ;
    return 
    parent::find($where);
  }
}
