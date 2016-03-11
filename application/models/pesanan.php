<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class pesanan extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'pesanan';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('outlet','OUTLET'),
      array('customer','CUSTOMER'),
      array('total','TOTAL PESANAN'),
      array('dibayar','TELAH DIBAYAR'),
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('outlet', 'OUTLET'),
      2 => array('customer', 'NAMA CUSTOMER'),
    );

    $this->buildRelation($this->inputFields[1][2], 'outlet');

    $this->expandables = array();
    $this->expandables[0] = array(
      'label' => 'DAFTAR PRODUK YANG DIPESAN',
      'fields' => array (
        0 => array('pesananproduk[produk][]', 'NAMA PRODUK'),
        1 => array('pesananproduk[qty][]', 'JUMLAH'),
      )
    );
    $this->buildRelation($this->expandables[0]['fields'][0][2], 'produk');

    $this->expandables[1] = array(
      'label' => 'DAFTAR BAHAN YANG DIGUNAKAN',
      'fields' => array (
        0 => array('pesananbarang[barang][]', 'NAMA BAHAN'),
        1 => array('pesananbarang[qty][]', 'JUMLAH'),
      )
    );
    $this->buildRelation($this->expandables[1]['fields'][0][2], 'baranggudang');

    $this->expandables[2] = array(
      'label' => 'DAFTAR AYAM YANG DIPAKAI',
      'fields' => array (
        0 => array('pesananayam[ayam][]', 'AYAM'),
        1 => array('pesananayam[pcs][]', 'JUMLAH'),
        2 => array('pesananayam[kg][]', 'BERAT'),
      )
    );
    $this->buildRelation($this->expandables[2]['fields'][0][2], 'ayam', array('nama <>' => 'AYAM HIDUP'));
  }

  function save ($data) {
    if (isset($data['id'])) die('durung tak pikir');
    $total = 0;
    $waktu = $data['waktu'];
    $transaksi = 'PESANAN';
    $produks = array();
    foreach ($this->db->get('produk')->result() as $p) $produks[$p->id] = $p->harga;
    foreach ($data['pesananproduk']['produk'] as $pesan)  $total += $produks[$pesan];
    $this->db->insert('pesanan', array(
      'waktu' => $waktu,
      'outlet' => $data['outlet'],
      'customer' => $data['customer'],
      'total' => $total
    ));
    $pid = $this->db->insert_id();

    foreach ($data['pesananproduk']['produk'] as $index => $produk) {
      $qty = $data['pesananproduk']['qty'][$index];
      $this->db->insert('pesananproduk', array(
        'pesanan' => $pid,
        'produk' => $produk,
        'qty' => $qty
      ));
      $fkey = $this->db->insert_id();
      $this->sirkulasiProduk ($waktu, $produk, 'MASUK', $transaksi, $fkey, $qty);
      $this->sirkulasiProduk ($waktu, $produk, 'KELUAR', $transaksi, $fkey, $qty);
    }

    foreach ($data['pesananbarang']['barang'] as $index => $barang) {
      $qty = $data['pesananbarang']['qty'][$index];
      $this->db->insert('pesananbarang', array(
        'pesanan' => $pid,
        'barang' => $barang,
        'qty' => $qty
      ));
      $this->sirkulasiBarang ($waktu, $barang, 'KELUAR', $transaksi, $this->db->insert_id(), $qty);
    }

    foreach ($data['pesananayam']['ayam'] as $index => $ayam) {
      $pcs = $data['pesananayam']['pcs'][$index];
      $kg = $data['pesananayam']['kg'][$index];
      $this->db->insert('pesananayam', array(
        'pesanan' => $pid,
        'ayam' => $ayam,
        'pcs' => $pcs,
        'kg' => $kg
      ));
      $this->sirkulasiAyam ($waktu, $ayam, 'KELUAR', $transaksi, $this->db->insert_id(), $pcs, $kg);
    }
  }


  function find ($where = array()) {
    $this->db
      ->select('pesanan.*, outlet.nama as outlet', false)
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT('Rp ', FORMAT(total, 2)) AS total", false)
      ->select("CONCAT('Rp ', FORMAT(IFNULL (SUM(pesananbayar.nominal), 0), 2)) as dibayar", false)
      ->join('pesananbayar', 'pesananbayar.pesanan = pesanan.id', 'LEFT')
      ->join('outlet', 'outlet.id = pesanan.outlet', 'LEFT');
    return 
    parent::find($where);
    // die($this->db->last_query());
  }
}
