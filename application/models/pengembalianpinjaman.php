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

    $this->buildRelation($this->inputFields[1][2], 'debitur', array('saldo > ' => 0));
  }

  function validate ($data) {
    if (0 == $data['debitur']) return array('DEBITUR HARUS DIISI', 'error');
    return parent::validate($data);
  }

  function update ($data) {
    $reason = 'EDIT PENGEMBALIAN PINJAMAN';
    $data['type'] = 'PENGEMBALIAN';
    $previous = $this->findOne($data['id']);
    parent::save($data);
    $CI =& get_instance();
    $CI->load->model('debitur');
    if ($data['debitur'] != $previous['debitur']) {
      $this->debitur->saldo($previous['debitur'], $previous['nominal'], '+');
      $this->debitur->saldo($data['debitur'], $data['nominal'], '-');
    } else {
      if ($data['nominal'] > $previous['nominal']) {
        $this->sirkulasiKeuangan ('KELUAR', $reason, $data['nominal'] - $previous['nominal'], $data['id'], $data['waktu']);
        $this->debitur->saldo($data['debitur'], $data['nominal'] - $previous['nominal'], '-');
      }
      if ($data['nominal'] < $previous['nominal']) {
        $this->sirkulasiKeuangan ('MASUK', $reason, $previous['nominal'] - $data['nominal'], $data['id'], $data['waktu']);
        $this->debitur->saldo($data['debitur'], $previous['nominal'] - $data['nominal'], '+');
      }
    }
  }

  function save ($data) {
    $data['type'] = 'PENGEMBALIAN';
    $data['id'] = parent::save($data);
    $this->sirkulasiKeuangan ('KELUAR','PENGEMBALIAN PINJAMAN',$data['nominal'],$data['id'],$data['waktu']);
    $CI =& get_instance();
    $CI->load->model('debitur');
    return $this->debitur->saldo($data['debitur'], $data['nominal'], '-');
  }

  function delete ($id) {
    $data = $this->findOne($id);
    $this->sirkulasiKeuangan ('MASUK','PENGEMBALIAN BATAL',$data['nominal'],$data['id'],date('Y-m-d H:i:s',time()));
    $CI =& get_instance();
    $CI->load->model('debitur');
    $this->debitur->saldo($data['debitur'], $data['nominal'], '+');
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
