<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class cashflowoutlet extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'cashflowoutlet';
    $this->thead = array(
      array('outlet','OUTLET'),
      array('waktu','TANGGAL'),
      array('transaksi','TRANSAKSI'),
      array('type','FLOW'),
      array('nominal','CASH'),
      array('saldo','SALDO'),
    );
    $this->inputFields = null;
  }

  function save ($data) {
  }

  function find ($where = array()) {
    $this->db
      ->select('cashflowoutlet.*')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT('Rp ', FORMAT(nominal, 2)) AS nominal", false)
      ->select("CONCAT('Rp ', FORMAT(cashflowoutlet.saldo, 2)) AS saldo", false)
      ->select('outlet.nama as outlet')
      ->join('outlet', 'cashflowoutlet.outlet=outlet.id', 'LEFT')
      ->order_by('id', 'DESC');
    return parent::find($where);
  }
}
