<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class pinjaman extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'pinjaman';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('type','TRANSAKSI'),
      array('debitur','DEBITUR'),
      array('nominal','NOMINAL'),
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('type', 'TRANSAKSI', array (
        'PEMINJAMAN' => 'PEMINJAMAN',
        'PENGEMBALIAN' => 'PENGEMBALIAN'
      )),
      2 => array('debitur', 'DEBITUR'),
      3 => array('nominal', 'NOMINAL')
    );

    $this->buildRelation($this->inputFields[2][2], 'debitur');
  }

  function save ($data) {
    if (isset($data['id'])) die('durung tak pikir');
    $this->sirkulasiKeuangan (
      $data['type'] == 'PEMINJAMAN' ? 'MASUK' : 'KELUAR', 
      $data['type'] == 'PEMINJAMAN' ? 'PEMINJAMAN' : 'PENGEMBALIAN PINJAMAN',
      $data['nominal'], 
      isset($data['id']) ? $data['id'] : null, 
      $data['waktu']
    );
    $this->db->where('id', $data['debitur']);
    if ($data['type'] == 'PEMINJAMAN') $this->db->set('saldo', 'saldo+'.$data['nominal'],false);
    else if ($data['type'] == 'PENGEMBALIAN') $this->db->set('saldo', 'saldo-'.$data['nominal'],false);
    $this->db->update('debitur');
    return parent::save($data);
  }

  function find ($where = array()) {
    $this->db
      ->select('pinjaman.*')
      ->select('debitur.nama as debitur')
      ->select("CONCAT('Rp ', FORMAT(nominal, 2)) AS nominal", false)
      ->join('debitur', 'debitur.id = pinjaman.debitur')
      ->order_by('id', 'DESC');
    return parent::find($where);
  }
}
