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
    $record = array (
      'waktu' => $data['waktu'],
      'type' => 'PINJAMAN',
      'debitur' => $data['debitur'],
      'nominal' => $data['nominal']
    );
    if (isset($data['id'])) {
      $record['id'] = $data['id'];
      $this->delete($data['id']);
    }
    $fkey = parent::save($record);
    $this->sirkulasiKeuangan ('MASUK', 'PEMINJAMAN', $data['nominal'], $fkey, $data['waktu']);
    $this->db->where('id', $data['debitur'])->set('saldo', 'saldo+'.$data['nominal'], false)->update('debitur');
    return $fkey;
  }

  function delete ($id) {
    $record = parent::findOne($id);
    $this->sirkulasiKeuangan ('KELUAR', 'PEMINJAMAN BATAL', $record['nominal'], $id, date('Y-m-d H:i:s',time()));
    $this->db->where('id', $record['debitur'])->set('saldo', 'saldo-'.$record['nominal'], false)->update('debitur');    
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
