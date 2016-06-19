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
      2 => array('karyawan', 'PENANGGUNG JAWAB'),
    );
    $this->buildRelation($this->inputFields[1][2], 'outlet');
    $this->buildRelation($this->inputFields[2][2], 'karyawan');

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
      'label' => 'LAPORAN SISA BAHAN YANG DIKEMBALIKAN KE GUDANG',
      'fields' => array (
        0 => array('setoransisabarang[barang][]', 'BAHAN'),
        1 => array('setoransisabarang[qty][]', 'QTY'),
      )
    );
    $this->buildRelation($this->expandables[3]['fields'][0][2], 'baranggudang');

    $this->expandables[4] = array(
      'label' => 'LAPORAN SISA BAHAN DI OUTLET',
      'fields' => array (
        0 => array('stockoutlet[barang][]', 'BAHAN'),
        1 => array('stockoutlet[qty][]', 'QTY'),
      )
    );
    $this->buildRelation($this->expandables[4]['fields'][0][2], 'baranggudang');

    $this->expandables[5] = array(
      'label' => 'LAPORAN PEMBAYARAN PESANAN',
      'fields' => array (
        0 => array('pesanan[id][]', 'PESANAN ATAS NAMA'),
        1 => array('pesanan[nominal][]', 'JUMLAH'),
      )
    );
    $this->expandables[5]['fields'][0][2][0] = '';
    foreach ($this->db->get_where('pesanan', array('lunas' => 0))->result() as $pesanan)
      $this->expandables[5]['fields'][0][2][$pesanan->id] = $pesanan->customer;
  }

  function save ($data) {
    if (isset($data['id'])) die('x');

    $message = '';
    $waktu = $data['waktu'];
    $outlet = $data['outlet'];
    $transaksi = 'SETORAN';
    $prices = array();
    $pemasukan = 0;
    $pengeluaran = 0;
    $produkDihasilkan = array();
    $bahanTerpakai = array();

    // COLLECT STOCK BARANG DI OUTLET UTK HITUNG PRODUKSI
    $stockBahanOutlet = array();
    foreach ($this->db->get_where('barangoutlet', array('outlet' => $outlet))->result() as $barang)
      $stockBahanOutlet [$barang->barang] = $barang->stock;
    // VALIDASI SISA BAHAN
    foreach ($data['setoransisabarang']['barang'] as $index => $barang)
      if (!isset ($stockBahanOutlet[$barang])) {
        $message = 'PROSES SUKSES, NAMUN SETORAN SISA BARANG DIBATALKAN KARENA TIDAK DITEMUKAN DI OUTLET';
        unset ($data['setoransisabarang']['barang'][$index]);
      }

    // HITUNG TOTAL UANG YANG DISETOR
    foreach ($this->db->get('produk')->result() as $product) 
      $prices[$product->id] = $product->harga;
    foreach ($data['setoranpenjualan']['produk'] as $index => $produk)
      if (isset($prices[$produk])) $pemasukan += $prices[$produk] * $data['setoranpenjualan']['qty'][$index];
    foreach ($data['setoranpengeluaran']['nominal'] as $index => $nominal) 
      $pengeluaran += $nominal;
    foreach ($data['pesanan']['nominal'] as $pesanan) $pemasukan += $pesanan;

    $setoranId = parent::save(array(
      'outlet' => $outlet,
      'waktu' => $waktu,
      'nominal' => $pemasukan - $pengeluaran
    ));
    $this->sirkulasiKeuanganOutlet ('MASUK', 'PENJUALAN', $pemasukan, $setoranId, $waktu, $outlet);
    $this->sirkulasiKeuanganOutlet ('KELUAR', 'PENGELUARAN', $pengeluaran, $setoranId, $waktu, $outlet);
    $this->sirkulasiKeuanganOutlet ('KELUAR', 'SETORAN', $pemasukan - $pengeluaran, $setoranId, $waktu, $outlet);
    $this->sirkulasiKeuangan ('MASUK', 'SETORAN', $pemasukan - $pengeluaran, $setoranId, $waktu);

    // penjualan
    foreach ($data['setoranpenjualan']['produk'] as $index => $produk) {
      if ($produk == 0) continue;
      $qty = $data['setoranpenjualan']['qty'][$index];
      $this->db->insert('setoranpenjualan', array(
        'setoran' => $setoranId,
        'produk' => $produk,
        'qty' => $qty
      ));
      $fkey = $this->db->insert_id();
      $this->sirkulasiProdukOutlet ($waktu, $produk, 'KELUAR', 'PENJUALAN OUTLET', $fkey, $qty, $outlet);
      $produkDihasilkan[$produk] = $qty;
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
      if ($produk == 0) continue;
      $qty = $data['setoransisaproduk']['qty'][$index];
      $this->db->insert('setoransisaproduk', array(
        'setoran' => $setoranId,
        'produk' => $produk,
        'qty' => $qty
      ));
      $fkey = $this->db->insert_id();
      $this->sirkulasiProdukOutlet ($waktu, $produk, 'KELUAR', 'SETORAN SISA', $fkey, $qty, $outlet);
      $this->sirkulasiProduk ($waktu, $produk, 'MASUK', 'SETORAN SISA', $fkey, $qty);
      if (isset($produkDihasilkan[$produk])) $produkDihasilkan[$produk] += $qty;
      else $produkDihasilkan[$produk] = $qty;
    }

    // sisa bahan
    foreach ($data['setoransisabarang']['barang'] as $index => $barang) {
      if ($barang == 0) continue;
      $qty = $data['setoransisabarang']['qty'][$index];
      $this->db->insert('setoransisabarang', array(
        'setoran' => $setoranId,
        'barang' => $barang,
        'qty' => $qty
      ));
      $fkey = $this->db->insert_id();
      $this->sirkulasiBarangOutlet ($waktu, $barang, 'KELUAR', 'SETORAN SISA', $fkey, $qty, $outlet);
      $this->sirkulasiBarang ($waktu, $barang, 'MASUK', 'SETORAN SISA', $fkey, $qty);
      $bahanTerpakai[$barang] = $stockBahanOutlet[$barang] - $qty;
    }

    foreach ($data['stockoutlet']['barang'] as $index => $barang) {
      if ($barang == 0) continue;
      $qty = $data['stockoutlet']['qty'][$index];
      $this->db
        ->where('outlet', $outlet)
        ->where('barang', $barang)
        ->set('stock', $qty)
        ->update('barangoutlet');
      if (isset($bahanTerpakai[$barang])) $bahanTerpakai[$barang] -= $qty;
      else $bahanTerpakai[$barang] = $qty;
    }

    // HITUNG PRODUKSI OUTLET
    $this->db->insert('produksi', array(
      'waktu' => $waktu,
      'karyawan' => $data['karyawan'],
      'outlet' => $outlet
    ));
    $produksiId = $this->db->insert_id();

    // SELURUH AYAM DI OUTLET PASTI HABIS DI PRODUKSI
    $seluruhAyam = $this->db->get_where('ayamoutlet', array('outlet'=>$outlet))->result();
    foreach ($seluruhAyam as $ayam) {
      $this->db->insert('produksiayam', array(
        'produksi' => $produksiId,
        'ayam' => $ayam->ayam,
        'pcs' => $ayam->pcs,
        'kg' => $ayam->kg
      ));
      $fkey = $this->db->insert_id();
      $this->sirkulasiAyamOutlet ($waktu, $ayam->ayam, 'KELUAR', 'PRODUKSI', $fkey, $ayam->pcs, $ayam->kg, $outlet);
    }

    foreach ($bahanTerpakai as $barang => $qty) {
      $this->db->insert('produksibarang', array(
        'produksi' => $produksiId,
        'barang' => $barang,
        'qty' => $qty
      ));
      $fkey = $this->db->insert_id();
      $this->sirkulasiBarangOutlet ($waktu, $barang, 'KELUAR', 'PRODUKSI', $fkey, $qty, $outlet);
    }

    foreach ($produkDihasilkan as $produk => $qty) {
      $this->db->insert('produksiproduk', array(
        'produksi' => $produksiId,
        'produk' => $produk,
        'qty' => $qty
      ));
      $fkey = $this->db->insert_id();
      $this->sirkulasiProdukOutlet ($waktu, $produk, 'MASUK', 'PRODUKSI', $fkey, $qty, $outlet);
    }

    if ( $data['pesanan']['id'][0] > 0)
    foreach ($data['pesanan']['id'] as $index => $pesanan) {
      $nominal = $data['pesanan']['nominal'][$index];
      $this->db->insert('pesananbayar', array(
        'setoran' => $setoranId,
        'pesanan' => $pesanan,
        'nominal' => $nominal,
      ));
      $fkey = $this->db->insert_id();
      
      $master = $this->db->get_where('pesanan', array('id' => $pesanan))->row_array();
      $total = $master['total'];
      foreach ($this->db->get_where('pesananbayar', array ('pesanan' => $pesanan))->result() as $bayar)
        $total -= $bayar->nominal;
      
      if ($total <= 0) $this->db->where('id', $pesanan)->set('lunas', 1)->update('pesanan');
      $this->sirkulasiKeuangan ('MASUK', 'PESANAN', $nominal, $fkey, $waktu);
    }

    return $message;
  }

  function delete ($id) {
    $this->sirkulasiKeuanganOutlet ('MASUK', 'PENJUALAN', $pemasukan, $setoranId, $waktu, $outlet);
    $this->sirkulasiKeuanganOutlet ('KELUAR', 'PENGELUARAN', $pengeluaran, $setoranId, $waktu, $outlet);
    $this->sirkulasiKeuanganOutlet ('KELUAR', 'SETORAN', $pemasukan - $pengeluaran, $setoranId, $waktu, $outlet);
    $this->sirkulasiKeuangan ('MASUK', 'SETORAN', $pemasukan - $pengeluaran, $setoranId, $waktu);

    $this->sirkulasiProdukOutlet ($waktu, $produk, 'KELUAR', 'PENJUALAN OUTLET', $fkey, $qty, $outlet);
    // 'setoranpenjualan'
    // 'setoranpengeluaran'

    $this->sirkulasiProdukOutlet ($waktu, $produk, 'KELUAR', 'SETORAN SISA', $fkey, $qty, $outlet);
    $this->sirkulasiProduk ($waktu, $produk, 'MASUK', 'SETORAN SISA', $fkey, $qty);
  }

  function find ($where = array()) {
    $this->db
      ->select('setoran.*, outlet.nama as outlet')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT('Rp ', FORMAT(nominal, 2)) AS nominal", false)
      ->join('outlet', 'outlet.id = setoran.outlet', 'LEFT');
    return parent::find($where);
  }
}
