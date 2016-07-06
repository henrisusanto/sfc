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
    $total = 0;
    $waktu = $data['waktu'];
    $transaksi = 'PESANAN';
    $produks = array();
    foreach ($this->db->get('produk')->result() as $p) $produks[$p->id] = $p->harga;
    foreach ($data['pesananproduk']['produk'] as $index => $produk)  
      $total += $produks[$produk] * $data['pesananproduk']['qty'][$index];

    $record = array(
      'waktu' => $waktu,
      'outlet' => $data['outlet'],
      'customer' => $data['customer'],
      'total' => $total
    );
    if (isset($data['id'])) {
      $message = $this->delete($data['id']);
      if (strlen($message) > 0) return $message;
      $record['id'] = $data['id'];
    }
    $this->db->insert('pesanan', $record);
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

  function delete ($id) {
    $dibayar = $this->db->get_where('pesananbayar', array('pesanan' => $id))->result();
    if (count ($dibayar) > 0) return 'UNTUK MENJAGA KONSISTENSI DATA, PESANAN YANG TELAH DIBAYAR TIDAK DAPAT DIHAPUS ATAU DI-EDIT';
    $transaksi = 'PESANAN BATAL';
    $waktu = date('Y-m-d H:i:s',time());
    $fkey = $id;
    foreach ($this->db->get_where('pesananproduk', array('pesanan' => $id))->result() as $child) {
      $this->sirkulasiProduk ($waktu, $child->produk, 'KELUAR', $transaksi, $fkey, $child->qty);
      $this->sirkulasiProduk ($waktu, $child->produk, 'MASUK', $transaksi, $fkey, $child->qty);
    }
    foreach ($this->db->get_where('pesananbarang', array('pesanan' => $id))->result() as $child)
      $this->sirkulasiBarang ($waktu, $child->barang, 'MASUK', $transaksi, $fkey, $child->qty);
    foreach ($this->db->get_where('pesananayam', array('pesanan' => $id))->result() as $child)
      $this->sirkulasiAyam ($waktu, $child->ayam, 'MASUK', $transaksi, $fkey, $child->pcs, $child->kg);
    parent::delete($id);
    return '';
  }

  function find ($where = array()) {
    $this->db
      ->select('pesanan.*, outlet.nama as outlet', false)
      ->select("DATE_FORMAT(pesanan.waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT('Rp ', FORMAT(total, 2)) AS total", false)
      ->select("CONCAT('Rp ', FORMAT(SUM(pesananbayar.nominal), 2)) as dibayar", false)
      ->join('pesananbayar', 'pesananbayar.pesanan = pesanan.id', 'LEFT')
      ->join('outlet', 'outlet.id = pesanan.outlet', 'LEFT')
      ->group_by('pesanan.id');
    return 
    parent::find($where);
  }
}
