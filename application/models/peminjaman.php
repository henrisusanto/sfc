<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class peminjaman extends my_model {

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

  function debitur ($debitur, $nominal, $plusminus) {
    $CI =& get_instance();
    $CI->load->model('debitur');
    return $this->debitur->saldo($debitur, $nominal, $plusminus);
  }

  function validate ($data) {
    if (0 == $data['debitur']) return array('DEBITUR HARUS DIISI', 'error');
    return parent::validate($data);
  }

  function update ($data) {
    $reason = 'EDIT PEMINJAMAN';
    $data['type'] = 'PEMINJAMAN';
    $previous = $this->findOne ($data['id']);

    if ($data['debitur'] != $previous['debitur']) {
      $this->debitur ($previous['debitur'], $previous['nominal'], '-');
      $this->debitur ($data['debitur'], $data['nominal'], '+');
    } else {
      if ($data['nominal'] > $previous['nominal']) {
        $this->sirkulasiKeuangan ('MASUK', $reason, $data['nominal'] - $previous['nominal'], $data['id'], $data['waktu']);
        $this->debitur ($data['debitur'], $data['nominal'] - $previous['nominal'], '+');
      }
      if ($data['nominal'] < $previous['nominal']) {
        $this->sirkulasiKeuangan ('KELUAR', $reason, $previous['nominal'] - $data['nominal'], $data['id'], $data['waktu']);
        $this->debitur ($data['debitur'], $previous['nominal'] - $data['nominal'], '-');
      }
    }
    return parent::save($data);
  }

  function save ($data) {
    $data['type'] = 'PEMINJAMAN';
    $data['id'] = parent::save($data);
    $this->sirkulasiKeuangan ('MASUK','PEMINJAMAN',$data['nominal'],$data['id'],$data['waktu']);
    $this->debitur ($data['debitur'], $data['nominal'], '+');
  }

  function delete ($id) {
    $data = $this->findOne($id);
    $this->sirkulasiKeuangan ('KELUAR','PEMINJAMAN BATAL',$data['nominal'],$data['id'],date('Y-m-d H:i:s',time()));
    $this->debitur ($data['debitur'], $data['nominal'], '-');
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
      ->where('type', 'PEMINJAMAN');
    return parent::find($where);
  }
}
