<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class komposisi extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'komposisi';
    $this->thead = array(
      array('produk','NAMA PRODUK /  PAKET'),
      // array('ayam','AYAM'),
      array('barang','BAHAN'),
    );
    $this->inputFields = array(
      0 => array('produk', 'NAMA PRODUK'),
    );

    $this->buildRelation($this->inputFields[0][2], 'produk');
    $this->expandables = array();
    $this->expandables[0] = array(
      'label' => 'AYAM',
      'fields' => array (
        0 => array('komposisiayam[ayam][]', 'AYAM'),
        1 => array('komposisiayam[qty][]', 'JUMLAH'),
      )
    );
    $this->buildRelation($this->expandables[0]['fields'][0][2], 'ayam', array('nama <>' => 'AYAM HIDUP'));
    $this->expandables[1] = array(
      'label' => 'BAHAN',
      'fields' => array (
        0 => array('komposisibahan[barang][]', 'BAHAN'),
        1 => array('komposisibahan[qty][]', 'UKURAN'),
      )
    );
    $this->buildRelation($this->expandables[1]['fields'][0][2], 'baranggudang');
  }

  function save ($data) {
    if (isset($data['id'])) die('x');
    $produk = $data['produk'];
    foreach ($data['komposisiayam']['ayam'] as $index => $ayam) {
      if ($ayam == 0) continue;
      $this->db->insert('komposisi', array(
        'produk' => $produk,
        'ayam' => $ayam, 
        'qty' => $data['komposisiayam']['qty'][$index]
      ));
    }
    foreach ($data['komposisibahan']['barang'] as $index => $barang) {
      if ($barang == 0) continue;
      $this->db->insert('komposisi', array(
        'produk' => $produk,
        'barang' => $barang,
        'qty' => $data['komposisibahan']['qty'][$index]
      ));
    }
  }

  function find ($where = array()) {
    $this->db
      ->select('komposisi.*, produk.nama as produk')
      ->select("IFNULL(CONCAT(qty, ' PCs ', ayam.nama), CONCAT(qty, baranggudang.satuan, ' ', baranggudang.nama)) as barang", false)
      // ->select("CONCAT(qty, ' PCs ', ayam.nama) as ayam", false)
      // ->select("CONCAT(qty, baranggudang.satuan, ' ', baranggudang.nama) AS barang", false)
      ->join('baranggudang', 'baranggudang.id = komposisi.barang', 'LEFT')
      ->join('ayam', 'ayam.id = komposisi.ayam', 'LEFT')
      ->join('produk', 'produk.id = komposisi.produk', 'LEFT');
    return parent::find($where);
  }
}
