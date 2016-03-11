<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class laporanlabarugi extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'cashflow';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('transaksi','TRANSAKSI'),
      array('masuk','MASUK'),
      array('keluar','KELUAR'),
      array('saldo','SALDO'),
    );
    $this->filters = array (
      1 => array('since', 'SEJAK TANGGAL'),
      2 => array('until', 'HINGGA TANGGAL'),
    );
    $this->buildTFoot();
  }

  function getTFoot ($tbody) {
    $masuk = 0;
    $keluar= 0;
    $saldo = 0;
    foreach ($tbody as $tb) {
      $masuk += preg_replace("/([^0-9\\.])/i", "", $tb->masuk);
      $keluar += preg_replace("/([^0-9\\.])/i", "", $tb->keluar);
    }
    $saldo = $masuk - $keluar;
    $this->tfoot[2] = 'TOTAL';
    $this->tfoot[3] = $this->toRp($masuk);
    $this->tfoot[4] = $this->toRp($keluar);
    $this->tfoot[5] = $this->toRp($saldo);
    $this->tfoot[5] = $this->tfoot[5] < 0 ? "RUGI ".$this->tfoot[5]*-1 : "LABA ".$this->tfoot[5];
    return parent::getTFoot($tbody);
  }

  function find ($where = array()) {
    $this->translateDateRange($where);
    $this->db
      ->select('*')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT('Rp ', FORMAT(IF (type = 'MASUK', nominal, 0), 2)) AS masuk", false)
      ->select("CONCAT('Rp ', FORMAT(IF (type = 'KELUAR', nominal, 0), 2)) AS keluar", false)
      ->select("CONCAT('Rp ', FORMAT(saldo, 2)) AS saldo", false)
      ->where($where);
    return 
    parent::find($where);
    // die($this->db->last_query());
  }
}
