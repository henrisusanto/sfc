<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class laporanpesanan extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'pesanan';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('outlet','OUTLET'),
      array('customer','CUSTOMER'),
      array('total','TOTAL PESANAN'),
      array('dibayar','TELAH DIBAYAR'),
    );
    $this->buildFilters('outlet');
    $this->buildTFoot();
  }

  function find ($where = array()) {
    $this->translateDateRange($where);
    if (isset ($where['outlet'])) {
      $where['pesanan.outlet'] = $where['outlet'];
      unset($where['outlet']);
    }
    $this->db
      ->select('pesanan.*, outlet.nama as outlet', false)
      ->select("DATE_FORMAT(pesanan.waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT('Rp ', FORMAT(total, 2)) AS total", false)
      ->select("CONCAT('Rp ', FORMAT(SUM(pesananbayar.nominal), 2)) as dibayar", false)
      ->join('pesananbayar', 'pesananbayar.pesanan = pesanan.id', 'LEFT')
      ->join('outlet', 'outlet.id = pesanan.outlet', 'LEFT')
      ->group_by('pesanan.id');
    return 
    parent::find($where);
  }

  function getTFoot ($tbody) {
    $total = 0;
    $dibayar = 0;
    foreach ($tbody as $tb) {
      $total += preg_replace("/([^0-9\\.])/i", "", $tb->total);
      $dibayar += preg_replace("/([^0-9\\.])/i", "", $tb->dibayar);
    }
    $this->tfoot[3] = 'TOTAL';
    $this->tfoot[4] = $this->toRp ($total);
    $this->tfoot[5] = $this->toRp ($dibayar);
    return parent::getTFoot($tbody);
  }

}
