<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class laporanbelanja extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'belanja';
    $this->thead = array(
      array('barang','NAMA BAHAN'),
      array('waktu','TANGGAL'),
      array('karyawan','PEMBELI'),
      array('distributor','PENJUAL'),
      array('hargasatuan','HARGA SATUAN'),
      array('qty','JUMLAH'),
      array('total','HARGA TOTAL'),
    );
    $this->buildFilters('baranggudang', 'barang');
    $this->buildTFoot();
  }

  function getTFoot ($tbody) {
    $qty = 0;
    $total = 0;
    $satuan = '';
    foreach ($tbody as $tb) {
      $satuan = $tb->satuan;
      $qty += $tb->qty;
      $total += preg_replace("/([^0-9\\.])/i", "", $tb->total);
    }
    $this->tfoot[5] = 'TOTAL';
    $this->tfoot[6] = "$qty $satuan";
    $this->tfoot[7] = $this->toRp ($total);
    return parent::getTFoot($tbody);
  }

  function find ($where = array()) {
    $this->translateDateRange($where);
    $this->db
      ->select('belanja.*')
      ->select('baranggudang.satuan as satuan', false)
      ->select('baranggudang.nama as barang', false)
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select('karyawan.nama as karyawan', false)
      ->select('distributor.nama as distributor', false)
      ->select("CONCAT(belanjadetail.qty, ' ', baranggudang.satuan) as qty", false)
      ->select("CONCAT('Rp ', FORMAT(hargasatuan, 2)) AS hargasatuan", false)
      ->select("CONCAT('Rp ', FORMAT(belanjadetail.total, 2)) AS total", false)
      ->join('belanjadetail', 'belanja.id = belanjadetail.belanja', 'LEFT')
      ->join('baranggudang', 'baranggudang.id = belanjadetail.barang', 'LEFT')
      ->join('karyawan', 'karyawan.id = belanja.karyawan', 'LEFT')
      ->join('distributor', 'distributor.id = belanjadetail.distributor', 'LEFT');
    return 
    parent::find($where);
    // die($this->db->last_query());
  }
}
