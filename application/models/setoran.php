<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class setoran extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'setoran';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('outlet','OUTLET'),
      array('nominal','SETORAN'),
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('outlet', 'OUTLET'),
    );
    $this->buildRelation($this->inputFields[1][2], 'outlet');

    $this->expandables = array();
    $this->expandables[0] = array(
      'label' => 'LAPORAN PENJUALAN',
      'fields' => array (
        0 => array('setoranpenjualan[produk][]', 'PRODUK'),
        1 => array('setoranpenjualan[qty][]', 'JUMLAH'),
      )
    );
    $this->buildRelation($this->expandables[0]['fields'][0][2], 'produk');

    $this->expandables[1] = array(
      'label' => 'LAPORAN PENGELUARAN',
      'fields' => array (
        0 => array('setoranpengeluaran[item][]', 'ITEM'),
        1 => array('setoranpengeluaran[nominal][]', 'NOMINAL'),
      )
    );

    $this->expandables[2] = array(
      'label' => 'LAPORAN SISA PRODUK',
      'fields' => array (
        0 => array('setoransisaproduk[produk][]', 'PRODUK'),
        1 => array('setoransisaproduk[qty][]', 'QTY'),
      )
    );
    $this->buildRelation($this->expandables[2]['fields'][0][2], 'produk');

    $this->expandables[3] = array(
      'label' => 'LAPORAN SISA BAHAN',
      'fields' => array (
        0 => array('setoransisabarang[barang][]', 'BAHAN'),
        1 => array('setoransisabarang[qty][]', 'QTY'),
      )
    );
    $this->buildRelation($this->expandables[3]['fields'][0][2], 'baranggudang');

  }

  function save ($data) {
    if (isset($data['id'])) die('x');

    $waktu = $data['waktu'];
    $outlet = $data['outlet'];
    $transaksi = 'SETORAN';
    $total = 0;
    $prices = array();

    // HITUNG TOTAL UANG YANG DISETOR
    foreach ($this->db->get('produk')->result() as $product) 
      $prices[$product->id] = $product->harga;
    foreach ($data['setoranpenjualan']['produk'] as $index => $produk)
      $total += $prices[$produk] * $data['setoranpenjualan']['qty'][$index];
    foreach ($data['setoranpengeluaran']['nominal'] as $index => $nominal) 
      $total -= $nominal;

    $this->db->insert('setoran', array(
      'outlet' => $outlet,
      'waktu' => $waktu,
      'nominal' => $total
    ));
    $setoranId = $this->db->insert_id();
    $this->sirkulasiKeuangan ('MASUK', 'SETORAN', $total, $setoranId, $waktu);

    // HITUNG HASIL PRODUKSI OUTLET
    $produksi = array();
    foreach ($data['setoranpenjualan']['produk'] as $index => $produk)
      $produksi[$produk] = $data['setoranpenjualan']['qty'][$index];
    foreach ($data['setoransisaproduk']['produk'] as $index => $produk) {
      if (!isset($produksi[$produk])) $produksi[$produk] = $data['setoranpenjualan']['qty'][$index];
      else $produksi[$produk] += $data['setoransisaproduk']['qty'][$index];  
    }
    foreach ($produksi as $produk => $qty) {
      $this->db->insert('produksioutlet', array(
        'outlet'=> $outlet,
        'waktu' => $waktu,
        'produk'=> $produk,
        'qty' => $qty
      ));
      $fkey = $this->db->insert_id();
      $komposisi = $this->db->get_where('komposisi', array('produk' => $produk))->result();
      foreach ($komposisi as $k) {
        if ($k->barang > 0) $this->sirkulasiBarangOutlet ($waktu, $k->barang, 'KELUAR', 'PRODUKSI OUTLET', $fkey, $k->qty * $qty, $outlet);
        if ($k->ayam > 0) $this->sirkulasiAyamOutlet ($waktu, $k->ayam, 'KELUAR', 'PRODUKSI OUTLET', $fkey, $k->qty * $qty, 0, $outlet);
      }
      $this->sirkulasiProdukOutlet ($waktu, $produk, 'MASUK', 'PRODUKSI OUTLET', $fkey, $qty, $outlet);
    }

    // penjualan
    foreach ($data['setoranpenjualan']['produk'] as $index => $produk) {
      $qty = $data['setoranpenjualan']['qty'][$index];
      $this->db->insert('setoranpenjualan', array(
        'setoran' => $setoranId,
        'produk' => $produk,
        'qty' => $qty
      ));
      $fkey = $this->db->insert_id();
      $this->sirkulasiProdukOutlet ($waktu, $produk, 'KELUAR', 'PENJUALAN OUTLET', $fkey, $qty, $outlet);
    }

    // pengeluaran
    foreach ($data['setoranpengeluaran']['item'] as $index => $item)
      $this->db->insert('setoranpengeluaran', array(
        'setoran' => $setoranId,
        'item' => $item,
        'nominal' => $data['setoranpengeluaran']['nominal'][$index]
      ));

    // sisa produk
    foreach ($data['setoransisaproduk']['produk'] as $index => $produk) {
      $qty = $data['setoransisaproduk']['qty'][$index];
      $this->db->insert('setoransisaproduk', array(
        'setoran' => $setoranId,
        'produk' => $produk,
        'qty' => $qty
      ));
      $fkey = $this->db->insert_id();
      $this->sirkulasiProdukOutlet ($waktu, $produk, 'KELUAR', 'SETORAN SISA', $fkey, $qty, $outlet);
      $this->sirkulasiProduk ($waktu, $produk, 'MASUK', 'SETORAN SISA', $fkey, $qty);
    }

    // sisa bahan
    foreach ($data['setoransisabarang']['barang'] as $index => $barang) {
      $qty = $data['setoransisabarang']['qty'][$index];
      $this->db->insert('setoransisabarang', array(
        'setoran' => $setoranId,
        'barang' => $barang,
        'qty' => $qty
      ));
      $fkey = $this->db->insert_id();
      $this->sirkulasiBarangOutlet ($waktu, $barang, 'KELUAR', 'SETORAN SISA', $fkey, $qty, $outlet);
      $this->sirkulasiBarang ($waktu, $barang, 'MASUK', 'SETORAN SISA', $fkey, $qty);
    }
  }

  function find ($where = array()) {
    $this->db
      ->select('setoran.*, outlet.nama as outlet')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT('Rp ', FORMAT(nominal, 2)) AS nominal", false)
      ->join('outlet', 'outlet.id = setoran.outlet');
    return parent::find($where);
  }
}
