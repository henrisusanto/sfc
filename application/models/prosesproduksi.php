<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class prosesproduksi extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'produksi';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('karyawan','PENANGGUNG JAWAB'),
      array('outlet', 'OUTLET')
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('karyawan', 'PENANGGUNG JAWAB'),
      2 => array('outlet', 'OUTLET / GUDANG'),
    );
    $this->buildRelation($this->inputFields[1][2], 'karyawan');
    $this->buildRelation($this->inputFields[2][2], 'outlet');
    $this->inputFields[2][2][0] = 'GUDANG';

    $this->expandables = array();
    $this->expandables[0] = array(
      'label' => 'PRODUK YANG DIHASILKAN',
      'fields' => array (
        0 => array('produksiproduk[produk][]', 'NAMA PRODUK'),
        1 => array('produksiproduk[qty][]', 'JUMLAH PCS'),
      ),
      'required' => array('produk', 'qty')
    );
    $this->buildRelation($this->expandables[0]['fields'][0][2], 'produk');

    $this->expandables[1] = array(
      'label' => 'BAHAN YANG DIGUNAKAN',
      'fields' => array (
        0 => array('produksibarang[barang][]', 'BAHAN'),
        1 => array('produksibarang[qty][]', 'JUMLAH SATUAN'),
      ),
      'required' => array('barang', 'qty')
    );
    $this->buildRelation($this->expandables[1]['fields'][0][2], 'baranggudang');

    $this->expandables[2] = array(
      'label' => 'AYAM YANG DIGUNAKAN ( JIKA ADA )',
      'fields' => array (
        0 => array('produksiayam[ayam][]', 'AYAM'),
        1 => array('produksiayam[pcs][]', 'JUMLAH PCS'),
        2 => array('produksiayam[kg][]', 'BERAT'),
      )
    );
    $this->buildRelation($this->expandables[2]['fields'][0][2], 'ayam');
    $this->submodel = array('produksibarang', 'produksiayam', 'produksiproduk');
  }

  function prepare ($data) {
    $prepared = array();
    $prepared['record'] = array(
      'waktu' => $data['waktu'],
      'karyawan' => $data['karyawan'],
      'outlet' => $data['outlet']
    );
    if (isset ($data['id'])) $prepared['record']['id'] = $data['id'];
    return $prepared;
  }

  function submodel ($data) {
    $CI =& get_instance();
    $CI->load->model($this->submodel);

    $excepted = array();
    foreach ($data['produksiproduk']['produk'] as $key => $produk) {
      if ($produk == 0) continue;
      $produksiproduk = array(
        'produksi' => $data['id'],
        'produk' => $produk,
        'qty' => $data['produksiproduk']['qty'][$key]
      );
      if (!empty ($data['produksiproduk']['id'][$key])) {
        $produksiproduk['id'] = $data['produksiproduk']['id'][$key];
        $excepted[] = $this->produksiproduk->update($produksiproduk, $data['waktu'], $data['reason'], $data['outlet']);
      }
      else $excepted[] = $this->produksiproduk->save($produksiproduk, $data['waktu'], $data['reason'], $data['outlet']);
    }
    if (!empty ($excepted))
      foreach ($this->produksiproduk->find(array('produksi' => $data['id']), array('id' => $excepted)) as $delete)
        $this->produksiproduk->delete($delete->id, $data['reason'], $data['waktu'], $data['outlet']);

    $excepted = array();
    foreach ($data['produksibarang']['barang'] as $key => $barang) {
      if ($barang == 0) continue;
      $produksibarang = array(
        'produksi' => $data['id'],
        'barang' => $barang,
        'qty' => $data['produksibarang']['qty'][$key]
      );
      if (!empty ($data['produksibarang']['id'][$key])) {
        $produksibarang['id'] = $data['produksibarang']['id'][$key];
        $excepted[] = $this->produksibarang->update($produksibarang, $data['waktu'], $data['reason'], $data['outlet']);
      }
      else $excepted[] = $this->produksibarang->save($produksibarang, $data['waktu'], $data['reason'], $data['outlet']);
    }
    if (!empty ($excepted))
      foreach ($this->produksibarang->find(array('produksi' => $data['id']), array('id' => $excepted)) as $delete)
        $this->produksibarang->delete($delete->id, $data['reason'], $data['waktu'], $data['outlet']);

    $excepted = array();
    foreach ($data['produksiayam']['ayam'] as $key => $ayam) {
      if ($ayam == 0) continue;
      $produksiayam = array(
        'produksi' => $data['id'],
        'ayam' => $ayam,
        'pcs' => $data['produksiayam']['pcs'][$key],
        'kg' => $data['produksiayam']['kg'][$key]
      );
      if (!empty ($data['produksiayam']['id'][$key])) {
        $produksiayam['id'] = $data['produksiayam']['id'][$key];
        $excepted[] = $this->produksiayam->update($produksiayam, $data['waktu'], $data['reason'], $data['outlet']);
      }
      else $excepted[] = $this->produksiayam->save($produksiayam, $data['waktu'], $data['reason'], $data['outlet']);
    }
    if (!empty ($excepted))
      foreach ($this->produksiayam->find(array('produksi' => $data['id']), array('id' => $excepted)) as $delete)
        $this->produksiayam->delete($delete->id, $data['reason'], $data['waktu'], $data['outlet']);

  }

  function update ($data) {
    $previous = $this->findOne($data['id']);
    $prepared = $this->prepare($data);
    $data['reason'] = 'EDIT PRODUKSI';
    $data['id'] = parent::save($prepared['record']);
    $this->submodel($data);
    return $data['id'];
  }

  function save ($data) {
    $prepared = $this->prepare($data);
    $data['reason'] = 'PRODUKSI';
    $data['id'] = parent::save($prepared['record']);
    $this->submodel($data);
    return $data['id'];
  }

  function delete ($id) {
    $CI =& get_instance();
    $CI->load->model($this->submodel);

    $data = $this->findOne($id);
    foreach ($this->submodel as $submodel) {
      foreach ($this->$submodel->find(array('produksi' => $id)) as $subrecord) {
        $this->$submodel->delete ($subrecord->id, 'PRODUKSI BATAL', date('Y-m-d H:i:s',time()), $data['outlet']);
      }
    }

    return parent::delete($id);
  }

  function find ($where = array()) {
    $this->db
      ->select('produksi.*')
      ->select('karyawan.nama as karyawan', false)
      ->select("IFNULL(outlet.nama, 'GUDANG') as outlet", false)
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->join('outlet', 'outlet.id = produksi.outlet', 'LEFT')
      ->join('karyawan', 'karyawan.id = produksi.karyawan', 'LEFT');
    return parent::find($where);
  }
}
