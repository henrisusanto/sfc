<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class sirkulasiayam extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'sirkulasiayam';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('ayam','JENIS'),
      array('transaksi','TRANSAKSI'),
      array('masuk','MASUK'),
      array('keluar','KELUAR'),
      array('stock','STOK GUDANG'),
    );
    $this->inputFields = null;
  }

  function find ($where = array()) {
    $this->db
      ->select('sirkulasiayam.*')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select('ayam.nama as ayam')

      ->select("IF(type = 'MASUK', CONCAT (sirkulasiayam.pcs, ' PCS / ', sirkulasiayam.kg, ' KG'), '') as masuk", false)
      ->select("IF(type = 'KELUAR', CONCAT (sirkulasiayam.pcs, ' PCS / ', sirkulasiayam.kg, ' KG'), '') as keluar", false)
      ->select("CONCAT (stockpcs, ' PCS / ', stockkg, ' KG') as stock", false)

      ->join('ayam', 'ayam.id=sirkulasiayam.ayam')
      ->order_by('id', 'ASC');
    return parent::find($where);
  }
}
