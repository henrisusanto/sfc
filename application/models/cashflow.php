<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class cashflow extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'cashflow';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('transaksi','TRANSAKSI'),
      array('type','FLOW'),
      array('nominal','CASH'),
      array('saldo','SALDO'),
    );
    $this->inputFields = null;
    // array(
      // 0 => array('waktu', 'TANGGAL'),
      // 1 => array('type', 'FLOW', array(
        // 'MASUK' => 'MASUK',
        // 'KELUAR' => 'KELUAR'
      // )),
      // 2 => array('transaksi', 'TRANSAKSI'),
      // 3 => array('nominal', 'NOMINAL'),
    // );
  }

  function save ($data) {
    $data['saldo'] = parent::cashFlowGetSaldo($data['nominal'], $data['type']);
    return parent::save($data);
  }

  function find ($where = array()) {
    $this->db
      ->select('*')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT('Rp ', FORMAT(nominal, 2)) AS nominal", false)
      ->select("CONCAT('Rp ', FORMAT(saldo, 2)) AS saldo", false)
      ->order_by('id', 'DESC');
    return parent::find($where);
  }
}
