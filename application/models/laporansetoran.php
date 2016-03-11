<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class laporansetoran extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'setoran';
    $this->thead = array();
    $this->thead[0] = array(
      array('outlet','OUTLET'),
      array('waktu','TANGGAL'),
      array('karyawan','KARYAWAN'),
      array('nominal','SETORAN'),
    );
    $this->thead[1] = array(
      array('outlet','OUTLET'),
      array('waktu','TANGGAL'),
      array('produk','PRODUK'),
      array('qty','JUMLAH'),
    );
    $this->thead[2] = array(
      array('outlet','OUTLET'),
      array('waktu','TANGGAL'),
      array('barang','BAHAN'),
      array('qty','JUMLAH'),
    );

    $this->filters = array(
      0 => array('outlet', 'FILTER OUTLET'),
      1 => array('barang', 'FILTER BAHAN'),
      2 => array('produk', 'FILTER PRODUK'),
      3 => array('since', 'SEJAK TANGGAL'),
      4 => array('until', 'HINGGA TANGGAL'),
    );
    $this->buildRelation($this->filters[0][2], 'outlet', array(), 'TAMPILKAN SEMUA');
    $this->buildRelation($this->filters[1][2], 'baranggudang', array(), 'TAMPILKAN SEMUA');
    $this->buildRelation($this->filters[2][2], 'produk', array(), 'TAMPILKAN SEMUA');

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
    foreach ($tbody[0] as $tb) $total += preg_replace("/([^0-9\\.])/i", "", $tb->nominal);
    $this->tfoot[0][3] = 'TOTAL';
    $this->tfoot[0][4] = $this->toRp ($total);
    return parent::getTFoot($tbody);
  }

  function find ($where = array()) {
    $tbody = array();

    $this->db
      ->select('setoran.*')
      ->select('outlet.nama as outlet', false)
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT('Rp ', FORMAT(nominal, 2)) AS nominal", false)
      ->select('karyawan.nama as karyawan', false)
      ->join('karyawan', 'karyawan.id=setoran.karyawan', 'LEFT')
      ->join('outlet', 'outlet.id=setoran.outlet', 'LEFT');
    if (isset ($where['outlet'])) $this->db->where('setoran.outlet', $where['outlet']);
    if (isset($where['since'])) $this->db->where("DATE_FORMAT(`waktu`, '%m/%d/%Y') >=", $where['since']);
    if (isset($where['until'])) $this->db->where("DATE_FORMAT(`waktu`, '%m/%d/%Y') <=", $where['until']);
    $tbody[0] = $this->db->get('setoran')->result();

    $this->db
      ->select('setoran.*')
      ->select('produk.nama as produk', false)
      ->select("CONCAT (setoransisaproduk.qty, ' PCs') as qty", false)
      ->select('outlet.nama as outlet', false)
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->join('outlet', 'outlet.id=setoran.outlet', 'LEFT')
      ->join('setoransisaproduk', 'setoran.id=setoransisaproduk.setoran')
      ->join('produk', 'produk.id=setoransisaproduk.produk', 'LEFT');
    if (isset ($where['outlet'])) $this->db->where('setoran.outlet', $where['outlet']);
    if (isset ($where['produk'])) $this->db->where('setoransisaproduk.produk', $where['produk']);
    if (isset($where['since'])) $this->db->where("DATE_FORMAT(`waktu`, '%m/%d/%Y') >=", $where['since']);
    if (isset($where['until'])) $this->db->where("DATE_FORMAT(`waktu`, '%m/%d/%Y') <=", $where['until']);
    $tbody[1] = $this->db->get('setoran')->result();

    $this->db
      ->select('setoran.*')
      ->select('baranggudang.nama as barang', false)
      ->select('baranggudang.satuan as satuan', false)
      ->select("CONCAT (setoransisabarang.qty, ' ', baranggudang.satuan) as qty", false)
      ->select('outlet.nama as outlet', false)
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->join('outlet', 'outlet.id=setoran.outlet', 'LEFT')
      ->join('setoransisabarang', 'setoran.id=setoransisabarang.setoran', 'LEFT')
      ->join('baranggudang', 'baranggudang.id=setoransisabarang.barang');
    if (isset ($where['outlet'])) $this->db->where('setoran.outlet', $where['outlet']);
    if (isset ($where['barang'])) $this->db->where('setoransisabarang.barang', $where['barang']);
    if (isset($where['since'])) $this->db->where("DATE_FORMAT(`waktu`, '%m/%d/%Y') >=", $where['since']);
    if (isset($where['until'])) $this->db->where("DATE_FORMAT(`waktu`, '%m/%d/%Y') <=", $where['until']);
    $tbody[2] = $this->db->get('setoran')->result();

    return $tbody;
  }
}
