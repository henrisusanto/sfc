<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class pesanan extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'pesanan';
    $this->submodel = array('pesananbarang', 'pesananproduk', 'pesananayam');
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
    $this->required = array('customer', 'outlet');
    $this->strings = array('customer');
    $this->buildRelation($this->inputFields[1][2], 'outlet');

    $this->expandables = array();
    $this->expandables[0] = array(
      'label' => 'DAFTAR PRODUK YANG DIPESAN',
      'required' => array('produk', 'qty'),
      'fields' => array (
        0 => array('pesananproduk[produk][]', 'NAMA PRODUK'),
        1 => array('pesananproduk[qty][]', 'JUMLAH'),
      )
    );
    $this->buildRelation($this->expandables[0]['fields'][0][2], 'produk');

    $this->expandables[1] = array(
      'label' => 'DAFTAR BAHAN YANG DIGUNAKAN',
      'required' => array('barang', 'qty'),
      'fields' => array (
        0 => array('pesananbarang[barang][]', 'NAMA BAHAN'),
        1 => array('pesananbarang[qty][]', 'JUMLAH'),
      )
    );
    $this->buildRelation($this->expandables[1]['fields'][0][2], 'baranggudang');

    $this->expandables[2] = array(
      'label' => 'DAFTAR AYAM YANG DIPAKAI',
      'required' => array('ayam', 'pcs', 'kg'),
      'fields' => array (
        0 => array('pesananayam[ayam][]', 'AYAM'),
        1 => array('pesananayam[pcs][]', 'JUMLAH'),
        2 => array('pesananayam[kg][]', 'BERAT'),
      )
    );
    $this->buildRelation($this->expandables[2]['fields'][0][2], 'ayam', array('nama <>' => 'AYAM HIDUP'));
  }

  function prepare ($data) {
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
    if (isset ($data['id'])) $record['id'] = $data['id'];
    return $record;
  }

  function submodel ($data) {
    $CI =& get_instance();
    foreach ($this->submodel as $submodel) $CI->load->model($submodel);

    $excepted = array();
    foreach ($data['pesananproduk']['produk'] as $index => $produk) {
      $record = array(
        'pesanan' => $data['id'],
        'produk' => $produk,
        'qty' => $data['pesananproduk']['qty'][$index]
      );
      if (!empty ($data['pesananproduk']['id'][$index])) {
        $record['id'] = $data['pesananproduk']['id'][$index];
        $excepted[] = $this->pesananproduk->update($record, $data['waktu'], $data['reason']);
      } else $excepted[] = $this->pesananproduk->save($record, $data['waktu'], $data['reason']);
    }
    foreach ($this->pesananproduk->find(array('pesanan' => $data['id']), array('id' => $excepted)) as $delete)
      $this->pesananproduk->delete($delete, $data['waktu'], $data['reason']);

    $excepted = array();
    foreach ($data['pesananbarang']['barang'] as $index => $barang) {
      $record = array(
        'pesanan' => $data['id'],
        'barang' => $barang,
        'qty' => $data['pesananbarang']['qty'][$index]
      );
      if (!empty ($data['pesananbarang']['id'][$index])) {
        $record['id'] = $data['pesananbarang']['id'][$index];
        $excepted[] = $this->pesananbarang->update($record, $data['waktu'], $data['reason']);
      } else $excepted[] = $this->pesananbarang->save($record, $data['waktu'], $data['reason']);
    }
    foreach ($this->pesananbarang->find(array('pesanan' => $data['id']), array('id' => $excepted)) as $delete)
      $this->pesananbarang->delete($delete, $data['waktu'], $data['reason']);

    $excepted = array();
    foreach ($data['pesananayam']['ayam'] as $index => $ayam) {
      $record = array(
        'pesanan' => $data['id'],
        'ayam' => $ayam,
        'pcs' => $data['pesananayam']['pcs'][$index],
        'kg' => $data['pesananayam']['kg'][$index]
      );
      if (!empty ($data['pesananayam']['id'][$index])) {
        $record['id'] = $data['pesananayam']['id'][$index];
        $excepted[] = $this->pesananayam->update($record, $data['waktu'], $data['reason']);
      } else $excepted[] = $this->pesananayam->save($record, $data['waktu'], $data['reason']);
    }
    foreach ($this->pesananayam->find(array('pesanan' => $data['id']), array('id' => $excepted)) as $delete)
      $this->pesananayam->delete($delete, $data['waktu'], $data['reason']);
  }

  function update ($data) {
    $data['reason'] = 'EDIT PESANAN';
    $record = $this->prepare($data);
    $data['id'] = parent::save($record);
    $this->submodel($data);
  }

  function save ($data) {
    $data['reason'] = 'PESANAN';
    $record = $this->prepare($data);
    $data['id'] = parent::save($record);
    $this->submodel($data);
  }

  function delete ($id) {
    $dibayar = $this->db->get_where('pesananbayar', array('pesanan' => $id))->result();
    if (count ($dibayar) > 0) return 'UNTUK MENJAGA KONSISTENSI DATA, PESANAN YANG TELAH DIBAYAR TIDAK DAPAT DIHAPUS ATAU DI-EDIT';

    $reason = 'PESANAN BATAL';
    $waktu = date('Y-m-d H:i:s',time());
    $CI =& get_instance();
    foreach ($this->submodel as $submodel) {
      $CI->load->model($submodel);
      foreach ($this->$submodel->find(array('pesanan' => $id)) as $delete)
        $this->$submodel->delete($delete, $waktu, $reason);
    }

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
