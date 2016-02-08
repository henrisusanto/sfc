<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class setoran extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'setoran';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('outlet','OUTLET'),
      array('nominal','SETORAN'),
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('outlet', 'OUTLET'),
      2 => array('nominal', 'SETORAN'),
    );

    foreach ($this->findAnother('outlet') as $item)
      $this->inputFields[1][2][$item->id] = $item->nama;
  }

  function save ($data) {
    if (!isset($data['id'])) {
      $cashflow = array (
        'id' => time(),
        'waktu' => date('Y-m-d H:i:s'),
        'type' => 'MASUK',
        'transaksi' => 'SETORAN',
        'nominal' => $data['nominal'],
        'saldo' => parent::cashFlowGetSaldo($data['nominal'], 'MASUK'),
      );
      $this->db->insert('cashflow', $cashflow);
    } else {
      $old = $this->findOne($data['id']);
      $nominal_lama = $old['nominal'];
      $nominal_baru = $data['nominal'];
      $selisih = $nominal_lama - $nominal_baru;
      if ($selisih != 0) {
        $type = $selisih < 0 ? 'MASUK': 'KELUAR';
        $selisih = abs($selisih);
        $cashflow = array (
          'id' => time(),
          'waktu' => date('Y-m-d H:i:s'),
          'type' => $type,
          'transaksi' => 'SETORAN EDIT',
          'nominal' => $selisih,
          'saldo' => parent::cashFlowGetSaldo($selisih, $type),
        );
        $this->db->insert('cashflow', $cashflow);
      }
    }
    return parent::save($data);
  }

  function delete ($id) {
    $setoran = $this->findOne($id);
    $cashflow = array (
      'id' => time(),
      'waktu' => date('Y-m-d H:i:s'),
      'type' => 'KELUAR',
      'transaksi' => 'SETORAN BATAL',
      'nominal' => $setoran['nominal'],
      'saldo' => parent::cashFlowGetSaldo($setoran['nominal'], 'KELUAR'),
    );
    $this->db->insert('cashflow', $cashflow);
    return parent::delete($id);
  }

  function find ($where = array()) {
    $this->db
      ->select('setoran.*, outlet.nama as outlet')
      ->select("CONCAT('Rp ', FORMAT(nominal, 2)) AS nominal", false)
      ->join('outlet', 'outlet.id = setoran.outlet');
    return parent::find($where);
  }
}
