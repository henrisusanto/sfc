<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class pesananbayar extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'pesananbayar';
  }

  function update ($data, $waktu, $reason, $outlet) {
    $previous = $this->findOne($data['id']);
    $data['id'] = parent::save($data);
    if ($data['nominal'] > $previous['nominal'])
      $this->sirkulasiKeuangan ('MASUK', 'EDIT BAYAR PESANAN', $data['nominal'] - $previous['nominal'], $data['id'], $waktu);
    if ($previous['nominal'] > $data['nominal'])
      $this->sirkulasiKeuangan ('KELUAR', 'EDIT BAYAR PESANAN', $previous['nominal'] - $data['nominal'], $data['id'], $waktu);
    $this->isLunas($data);
    return $data['id'];
  }

  function save ($data, $waktu, $reason, $outlet) {
    return true;
    $data['id'] = parent::save($data);
    $this->sirkulasiKeuangan ('MASUK', 'BAYAR PESANAN', $data['nominal'], $data['id'], $waktu);
    $this->isLunas($data);
    return $data['id'];
  }

  function delete ($data, $waktu, $reason, $outlet) {
    $this->sirkulasiKeuangan ('KELUAR', 'BATAL BAYAR PESANAN', $pesanan->nominal, $data->id, $waktu);
    $this->isLunas((array)$data);
    return parent::delete($data->id);
  }

  function isLunas ($data) {
    $master = $this->db->get_where('pesanan', array('id' => $data['id']))->row_array();
    $total = $master['total'];
    foreach ($this->db->get_where('pesananbayar', array ('pesanan' => $data['id']))->result() as $bayar)
      $total -= $bayar->nominal;
    if ($total <= 0) $this->db->where('id', $data['id'])->set('lunas', 1)->update('pesanan');
    if ($total > 0) $this->db->where('id', $data['id'])->set('lunas', 0)->update('pesanan');
  }
}