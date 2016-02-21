<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class produksigudang extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'produksigudang';
    $this->thead = array(
      array('waktu','TANGGAL PRODUKSI'),
      array('produk','NAMA PRODUK'),
      array('qty','JUMLAH')
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('produk', 'NAMA PRODUK'),
      2 => array('qty', 'JUMLAH'),
    );
    $this->buildRelation($this->inputFields[1][2], 'produk');
  }

  function save ($data) {
    if (isset($data['id'])) die('x');
    $waktu = $data['waktu'];
    $produk= $data['produk'];
    $qty = $data['qty'];
    $fkey = parent::save($data);
    $komposisi = $this->db->get_where('komposisi', array('produk' => $produk))->result();
    foreach ($komposisi as $k) {
      if ($k->barang > 0) $this->sirkulasiBarang ($waktu, $k->barang, 'KELUAR', 'PRODUKSI', $fkey, $k->qty * $qty);
      if ($k->ayam > 0) $this->sirkulasiAyam ($waktu, $k->ayam, 'KELUAR', 'PRODUKSI', $fkey, $k->qty * $qty, 0/*INI GIMANA?*/);
    }
    $this->sirkulasiProduk ($waktu, $produk, 'MASUK', 'PRODUKSI', $fkey, $qty);
    return $fkey;
  }

  function find ($where = array()) {
    $this->db
      ->select('produksigudang.*')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select('produk.nama as produk', false)
      ->select("CONCAT(qty, ' PCs') as qty", false)
      ->join('produk', 'produksigudang.produk=produk.id');
    return parent::find($where);
  }
}
