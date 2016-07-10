<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class bawaan extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'bawaan';
    $this->submodel = array('bawaanbarang', 'bawaanproduk', 'bawaanayam');
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('outlet','OUTLET')
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('outlet', 'OUTLET'),
      2 => array('modal', 'MODAL / RECEH')
    );
    $this->required = array('outlet');
    $this->buildRelation($this->inputFields[1][2], 'outlet');
    $this->expandables = array();
    $this->expandables[0] = array(
      'label' => 'DAFTAR BAWAAN BARANG',
      'required' => array('barang', 'qty'),
      'fields' => array (
        0 => array('bawaanbarang[barang][]', 'NAMA BARANG'),
        1 => array('bawaanbarang[qty][]', 'JUMLAH'),
      )
    );
    $this->buildRelation($this->expandables[0]['fields'][0][2], 'baranggudang');
    $this->expandables[1] = array(
      'label' => 'DAFTAR BAWAAN AYAM',
      'required' => array('ayam', 'pcs', 'kg'),
      'fields' => array (
        0 => array('bawaanayam[ayam][]', 'AYAM'),
        1 => array('bawaanayam[pcs][]', 'JUMLAH'),
        2 => array('bawaanayam[kg][]', 'BERAT'),
      )
    );
    $this->buildRelation($this->expandables[1]['fields'][0][2], 'ayam', array('nama <>' => 'AYAM HIDUP'));
    $this->expandables[2] = array(
      'label' => 'DAFTAR BAWAAN PRODUK',
      'required' => array('produk', 'qty'),
      'fields' => array (
        0 => array('bawaanproduk[produk][]', 'PRODUK'),
        1 => array('bawaanproduk[qty][]', 'JUMLAH'),
      )
    );
    $this->buildRelation($this->expandables[2]['fields'][0][2], 'produk');
  }

  function submodel ($data) {
    $CI =& get_instance();
    foreach ($this->submodel as $submodel) $CI->load->model($submodel);

    $excepted = array();
    foreach ($data['bawaanbarang']['barang'] as $key => $barang) {
      if ($barang == 0) continue;
      $record = array(
        'bawaan' => $data['id'],
        'barang' => $barang,
        'qty' => $data['bawaanbarang']['qty'][$key]
      );
      if (!empty ($data['bawaanbarang']['id'][$key])) {
        $record['id'] = $data['bawaanbarang']['id'][$key];
        $excepted[] = $this->bawaanbarang->update($record, $data['waktu'], $data['reason'], $data['outlet']);
      } else $excepted[] = $this->bawaanbarang->save($record, $data['waktu'], $data['reason'], $data['outlet']);
    }
    foreach ($this->bawaanbarang->find(array('bawaan' => $data['id']), array('id' => $excepted)) as $delete)
      $this->bawaanbarang->delete($delete->id, $data['waktu'], $data['reason'], $data['outlet']);

    $excepted = array();
    foreach ($data['bawaanayam']['ayam'] as $index => $ayam) {
      if ($ayam == 0) continue;
      $record = array(
        'bawaan' => $data['id'],
        'ayam' => $ayam,
        'pcs' => $data['bawaanayam']['pcs'][$index],
        'kg' => $data['bawaanayam']['kg'][$index]
      );
      if (!empty ($data['bawaanayam']['id'][$key])) {
        $record['id'] = $data['bawaanayam']['id'][$key];
        $excepted[] = $this->bawaanayam->update($record, $data['waktu'], $data['reason'], $data['outlet']);
      } else $excepted[] = $this->bawaanayam->save($record, $data['waktu'], $data['reason'], $data['outlet']);
    }
    foreach ($this->bawaanayam->find(array('bawaan' => $data['id']), array('id' => $excepted)) as $delete)
      $this->bawaanayam->delete($delete->id, $data['waktu'], $data['reason'], $data['outlet']);

    $excepted = array();
    foreach ($data['bawaanproduk']['produk'] as $index => $produk) {
      if ($produk == 0) continue;
      $record = array(
        'bawaan' => $data['id'],
        'produk' => $produk,
        'qty' => $data['bawaanproduk']['qty'][$index]
      );
      if (!empty ($data['bawaanproduk']['id'][$key])) {
        $record['id'] = $data['bawaanproduk']['id'][$key];
        $excepted[] = $this->bawaanproduk->update($record, $data['waktu'], $data['reason'], $data['outlet']);
      } else $excepted[] = $this->bawaanproduk->save($record, $data['waktu'], $data['reason'], $data['outlet']);
    }
    foreach ($this->bawaanproduk->find(array('bawaan' => $data['id']), array('id' => $excepted)) as $delete)
      $this->bawaanproduk->delete($delete->id, $data['waktu'], $data['reason'], $data['outlet']); 
  }

  function bawaanmodal ($data) {
    if (empty ($data['modal'])) return true;
    if (!isset ($data['previous'])) {
      $this->sirkulasiKeuangan ('KELUAR', $data['reason'], $data['modal'], $data['id'], $data['waktu']);
      $this->sirkulasiKeuanganOutlet ('MASUK', $data['reason'], $data['modal'], $data['id'], $data['waktu'], $data['outlet']);      
    } else {
      if ($data['modal'] > $data['previous']['modal']) {
        $this->sirkulasiKeuangan ('KELUAR', $data['reason'], $data['modal'] - $data['previous']['modal'], $data['id'], $data['waktu']);
        $this->sirkulasiKeuanganOutlet ('MASUK', $data['reason'], $data['modal'] - $data['previous']['modal'], $data['id'], $data['waktu'], $data['outlet']);
      }
      if ($data['modal'] < $data['previous']['modal']) {
        $this->sirkulasiKeuangan ('KELUAR', $data['reason'], $data['previous']['modal'] - $data['modal'], $data['id'], $data['waktu']);
        $this->sirkulasiKeuanganOutlet ('MASUK', $data['reason'], $data['previous']['modal'] - $data['modal'], $data['id'], $data['waktu'], $data['outlet']);
      }
    }
  }

  function prepare ($data) {
    $outlet = $data['outlet'];
    $waktu = $data['waktu'];
    $modal = $data['modal'];
    $outlet = $data['outlet'];
    $waktu = $data['waktu'];
    $modal = $data['modal'];

    $record = array(
      'outlet' => $outlet,
      'waktu' => $waktu,
      'modal' => $modal,
    );
    if (isset ($data['id'])) $record['id'] = $data['id'];
    return $record;    
  }

  function update ($data) {
    $record = $this->prepare($data);
    $data['reason'] = 'EDIT BAWAAN';
    $data['id'] = parent::save($record);
    $this->submodel($data);
    $this->bawaanmodal($data);
  }

  function save ($data) {
    $record = $this->prepare($data);
    $data['reason'] = 'BAWAAN';
    $data['id'] = parent::save($record);
    $this->submodel($data);
    $this->bawaanmodal($data);
  }

  function delete ($id) {
    $previous = parent::findOne($id);
    $data['reason'] = 'BAWAAN BATAL';
    $waktu = date('Y-m-d H:i:s',time());
    if ($previous['modal'] > 0) {
      $this->sirkulasiKeuangan ('MASUK', $data['reason'], $previous['modal'], $previous['id'], $waktu);
      $this->sirkulasiKeuanganOutlet ('KELUAR', $data['reason'], $previous['modal'], $previous['id'], $waktu, $previous['outlet']);
    }
    $CI =& get_instance();
    foreach ($this->submodel as $submodel) {
      $CI->load->model($submodel);
      foreach ($this->$submodel->find(array('bawaan' => $id)) as $delete)
        $this->$submodel->delete($delete, $waktu, $data['reason'], $data['outlet']);
    }
    return parent::delete($id);
  }

  function find ($where = array()) {
    $this->db
      ->select('bawaan.*')
      ->select('outlet.nama as outlet')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->join('outlet', 'bawaan.outlet = outlet.id');
    return parent::find($where);
  }

}
