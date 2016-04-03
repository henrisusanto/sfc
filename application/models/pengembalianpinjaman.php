<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class pengembalianpinjaman extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'pinjaman';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('debitur','DEBITUR'),
      array('nominal','NOMINAL'),
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('debitur', 'DEBITUR'),
      2 => array('nominal', 'NOMINAL')
    );

    $this->buildRelation($this->inputFields[1][2], 'debitur');
  }

  function save ($data) {
    $data['type'] = 'PENGEMBALIAN';
    if (isset($data['id'])) {
      $record = $this->findOne ($data['id']);
      $this->sirkulasiKeuangan ('MASUK','PENGEMBALIAN BATAL',$record['nominal'],$record['id'],date('Y-m-d H:i:s',time()));
      $this->db
        ->where('id', $data['debitur'])
        ->set('saldo', 'saldo+'.$record['nominal'],false)
        ->update('debitur');      
    }
    $data['id'] = parent::save($data);
    $this->sirkulasiKeuangan ('KELUAR','PENGEMBALIAN PINJAMAN',$data['nominal'],$data['id'],$data['waktu']);
    $this->db
      ->where('id', $data['debitur'])
      ->set('saldo', 'saldo-'.$data['nominal'],false)
      ->update('debitur');
  }

  function delete ($id) {
    $data = $this->findOne($id);
    $this->sirkulasiKeuangan ('MASUK','PENGEMBALIAN BATAL',$data['nominal'],$data['id'],date('Y-m-d H:i:s',time()));
    $this->db
      ->where('id', $data['debitur'])
      ->set('saldo', 'saldo+'.$data['nominal'],false)
      ->update('debitur');
    return parent::delete($id);
  }

  function find ($where = array()) {
    $this->db
      ->select('pinjaman.*')
      ->select('debitur.nama as debitur')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT('Rp ', FORMAT(nominal, 2)) AS nominal", false)
      ->join('debitur', 'debitur.id = pinjaman.debitur')
      ->order_by('id', 'DESC')
      ->where('type', 'PENGEMBALIAN');
    return parent::find($where);
  }
}
