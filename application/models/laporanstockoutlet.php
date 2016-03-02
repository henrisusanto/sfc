<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class laporanstockoutlet extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'baranggudang';
    $this->thead = array();
    $this->thead[0] = array(
      array('outlet','OUTLET'),
      array('barang','BAHAN'),
      array('stock','STOK OUTLET'),
    );
    $this->thead[1] = array(
      array('outlet','OUTLET'),
      array('ayam','AYAM MENTAH DI OUTLET'),
      array('pcs','STOK ( PCs )'),
      array('kg','STOK ( Kg )'),
    );
    $this->thead[2] = array(
      array('outlet','OUTLET'),
      array('produk','PRODUK'),
      array('type','JENIS'),
      array('stock','STOK OUTLET'),
    );
    $this->filters = array(
      0 => array('outlet', 'FILTER OUTLET'),
    );
    $this->buildRelation($this->filters[0][2], 'outlet', array(), 'TAMPILKAN SEMUA');
    $this->tfoot = array();
    foreach ($this->thead as $index => $th) {
      foreach ($th as $t => $h) {
        $this->tfoot[$index][$t] = '';
      }
      $this->tfoot[$index][] = '';
    }
  }

  function find ($where = array()) {
    $tbody = array ();

    $this->db
      ->select('outlet.nama as outlet', FALSE)
      ->select('baranggudang.nama as barang', FALSE)
      ->select("CONCAT (barangoutlet.stock, ' ', baranggudang.satuan) as stock", FALSE)
      ->join('baranggudang', 'baranggudang.id = barangoutlet.barang')
      ->join('outlet', 'outlet.id = barangoutlet.outlet')
      ->where($where);
    $tbody[0] = $this->db->get('barangoutlet')->result();

    $this->db
      ->select('outlet.nama as outlet', FALSE)
      ->select('ayam.nama as ayam', FALSE)
      ->select("CONCAT (ayamoutlet.pcs, ' PCs') as pcs", FALSE)
      ->select("CONCAT (ayamoutlet.kg, ' Kg') as kg", FALSE)
      ->join('ayam', 'ayam.id = ayamoutlet.ayam')
      ->join('outlet', 'outlet.id = ayamoutlet.outlet')
      ->where($where);
    $tbody[1] = $this->db->get('ayamoutlet')->result();

    $this->db
      ->select('outlet.nama as outlet', FALSE)
      ->select('produk.nama as produk', FALSE)
      ->select('produk.type')
      ->select("CONCAT (produkoutlet.stock, ' PCs') as stock", FALSE)
      ->join('produk', 'produk.id = produkoutlet.produk')
      ->join('outlet', 'outlet.id = produkoutlet.outlet')
      ->where($where);
    $tbody[2] = $this->db->get('produkoutlet')->result();
    return $tbody;
  }
}
