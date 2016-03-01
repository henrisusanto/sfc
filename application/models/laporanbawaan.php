<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class laporanbawaan extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'bawaan';
    $this->thead = array();
    $this->thead[0] = array(
      array('outlet','OUTLET'),
      array('waktu','TANGGAL'),
      array('modal','RECEH'),
    );
    $this->thead[1] = array(
      array('outlet','OUTLET'),
      array('waktu','TANGGAL'),
      array('ayam','AYAM MENTAH'),
      array('pcs','JUMLAH'),
      array('kg','BERAT'),
    );
    $this->thead[2] = array(
      array('outlet','OUTLET'),
      array('waktu','TANGGAL'),
      array('barang','BAHAN'),
      array('qty','JUMLAH'),
    );
    $this->thead[3] = array(
      array('outlet','OUTLET'),
      array('waktu','TANGGAL'),
      array('produk','PRODUK'),
      array('qty','JUMLAH'),
    );

    $this->filters = array(
      0 => array('outlet', 'FILTER OUTLET'),
      1 => array('ayam', 'FILTER AYAM MENTAH'),
      2 => array('barang', 'FILTER BAHAN'),
      3 => array('produk', 'FILTER PRODUK'),
      4 => array('since', 'SEJAK TANGGAL'),
      5 => array('until', 'HINGGA TANGGAL'),
    );
    $this->buildRelation($this->filters[0][2], 'outlet', array(), 'TAMPILKAN SEMUA');
    $this->buildRelation($this->filters[1][2], 'ayam', array(), 'TAMPILKAN SEMUA');
    $this->buildRelation($this->filters[2][2], 'baranggudang', array(), 'TAMPILKAN SEMUA');
    $this->buildRelation($this->filters[3][2], 'produk', array(), 'TAMPILKAN SEMUA');

    $this->tfoot = array();
    foreach ($this->thead as $index => $th) {
      foreach ($th as $t => $h) {
        $this->tfoot[$index][$t] = '';
      }
      $this->tfoot[$index][] = '';
    }
  }

  function getTFoot ($tbody) {
    $total = 0;
    foreach ($tbody[0] as $tb) $total += preg_replace("/([^0-9\\.])/i", "", $tb->modal);
    $this->tfoot[0][2] = 'TOTAL';
    $this->tfoot[0][3] = $this->toRp ($total);

    $pcs = 0;
    $kg = 0;
    foreach ($tbody[1] as $tb) {
      $pcs += $tb->pcs;
      $kg += $tb->kg;
    }
    $this->tfoot[1][3] = 'TOTAL';
    $this->tfoot[1][4] = "$pcs PCs";
    $this->tfoot[1][5] = "$kg Kg";

    $total = 0;
    $satuan = '';
    foreach ($tbody[2] as $tb) {
      $total += $tb->qty;
      $satuan = $tb->satuan;
    }
    $this->tfoot[2][3] = 'TOTAL';
    $this->tfoot[2][4] = "$total $satuan";

    $total = 0;
    foreach ($tbody[3] as $tb) $total += $tb->qty;
    $this->tfoot[3][3] = 'TOTAL';
    $this->tfoot[3][4] = "$total PCs";

    return parent::getTFoot($tbody);
  }

  function find ($where = array()) {
    $tbody = array();

    $this->db
      ->select('bawaan.*')
      ->select('outlet.nama as outlet', false)
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT('Rp ', FORMAT(modal, 2)) AS modal", false)
      ->join('outlet', 'outlet.id=bawaan.outlet', 'LEFT');
    if (isset ($where['outlet'])) $this->db->where('bawaan.outlet', $where['outlet']);
    if (isset($where['since'])) $this->db->where("DATE_FORMAT(`waktu`, '%m/%d/%Y') >=", $where['since']);
    if (isset($where['until'])) $this->db->where("DATE_FORMAT(`waktu`, '%m/%d/%Y') <=", $where['until']);
    $tbody[0] = $this->db->get('bawaan')->result();

    $this->db
      ->select('bawaan.*')
      ->select('ayam.nama as ayam', false)
      ->select("CONCAT (bawaanayam.pcs, ' PCs') as pcs", false)
      ->select("CONCAT (bawaanayam.kg, ' KG') as kg", false)
      ->select('outlet.nama as outlet', false)
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->join('outlet', 'outlet.id=bawaan.outlet', 'LEFT')
      ->join('bawaanayam', 'bawaan.id=bawaanayam.bawaan')
      ->join('ayam', 'ayam.id=bawaanayam.ayam', 'LEFT');
    if (isset ($where['outlet'])) $this->db->where('bawaan.outlet', $where['outlet']);
    if (isset ($where['ayam'])) $this->db->where('bawaanayam.ayam', $where['ayam']);
    if (isset($where['since'])) $this->db->where("DATE_FORMAT(`waktu`, '%m/%d/%Y') >=", $where['since']);
    if (isset($where['until'])) $this->db->where("DATE_FORMAT(`waktu`, '%m/%d/%Y') <=", $where['until']);
    $tbody[1] = $this->db->get('bawaan')->result();

    $this->db
      ->select('bawaan.*')
      ->select('baranggudang.nama as barang', false)
      ->select('baranggudang.satuan as satuan', false)
      ->select("CONCAT (bawaanbarang.qty, ' ', baranggudang.satuan) as qty", false)
      ->select('outlet.nama as outlet', false)
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->join('outlet', 'outlet.id=bawaan.outlet', 'LEFT')
      ->join('bawaanbarang', 'bawaan.id=bawaanbarang.bawaan', 'LEFT')
      ->join('baranggudang', 'baranggudang.id=bawaanbarang.barang');
    if (isset ($where['outlet'])) $this->db->where('bawaan.outlet', $where['outlet']);
    if (isset ($where['barang'])) $this->db->where('bawaanbarang.barang', $where['barang']);
    if (isset($where['since'])) $this->db->where("DATE_FORMAT(`waktu`, '%m/%d/%Y') >=", $where['since']);
    if (isset($where['until'])) $this->db->where("DATE_FORMAT(`waktu`, '%m/%d/%Y') <=", $where['until']);
    $tbody[2] = $this->db->get('bawaan')->result();

    $this->db
      ->select('bawaan.*')
      ->select('produk.nama as produk', false)
      ->select("CONCAT (bawaanproduk.qty, ' PCs') as qty", false)
      ->select('outlet.nama as outlet', false)
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->join('outlet', 'outlet.id=bawaan.outlet', 'LEFT')
      ->join('bawaanproduk', 'bawaan.id=bawaanproduk.bawaan')
      ->join('produk', 'produk.id=bawaanproduk.produk', 'LEFT');
    if (isset ($where['outlet'])) $this->db->where('bawaan.outlet', $where['outlet']);
    if (isset ($where['produk'])) $this->db->where('bawaanproduk.produk', $where['produk']);
    if (isset($where['since'])) $this->db->where("DATE_FORMAT(`waktu`, '%m/%d/%Y') >=", $where['since']);
    if (isset($where['until'])) $this->db->where("DATE_FORMAT(`waktu`, '%m/%d/%Y') <=", $where['until']);
    $tbody[3] = $this->db->get('bawaan')->result();

    return $tbody;
  }
}
