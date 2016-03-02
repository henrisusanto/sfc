<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class laporanpenjualanoutlet extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'setoran';
    $this->thead = array(
      array('outlet','OUTLET'),
      array('produk','PRODUK'),
      // array('type','JENIS'),
      array('waktu','TANGGAL'),
      array('harga','HARGA SATUAN'),
      array('qty','TERJUAL'),
      array('total','TOTAL'),
    );
    $this->filters = array(
      0 => array('outlet', 'FILTER OUTLET'),
      1 => array('produk', 'FILTER PRODUK'),
      2 => array('since', 'SEJAK TANGGAL'),
      3 => array('until', 'HINGGA TANGGAL'),
    );
    $this->buildRelation($this->filters[0][2], 'outlet');
    $this->buildRelation($this->filters[1][2], 'produk');
    $this->buildTFoot();
  }

  function getTFoot ($tbody) {
    $qty = 0;
    $total = 0;
    $satuan = 'PCs';
    foreach ($tbody as $tb) {
      $qty += $tb->qty;
      $total += preg_replace("/([^0-9\\.])/i", "", $tb->total);
    }
    $this->tfoot[4] = 'TOTAL';
    $this->tfoot[5] = "$qty $satuan";
    $this->tfoot[6] = $this->toRp ($total);
    return parent::getTFoot($tbody);
  }

  function find ($where = array()) {
    $this->translateDateRange($where);
    $this->db
      ->select('setoran.*')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select('produk.nama as produk', FALSE)
      ->select('produk.type as type', FALSE)
      ->select('outlet.nama as outlet', FALSE)
      ->select("CONCAT('Rp ', FORMAT(produk.harga, 2)) AS harga", false)
      ->select("CONCAT (setoranpenjualan.qty, ' PCs') as qty", false)
      ->select("CONCAT('Rp ', FORMAT(qty * harga, 2)) AS total", false)
      ->join('setoranpenjualan', 'setoranpenjualan.setoran=setoran.id', 'LEFT')
      ->join('outlet', 'setoran.outlet=outlet.id', 'LEFT')
      ->join('produk', 'setoranpenjualan.produk=produk.id', 'LEFT')
      ;
    return 
    parent::find($where);
  }
}
