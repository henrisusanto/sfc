<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class transaksiinternal extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'internal';
    $this->submodel = array('internalbarang', 'internalproduk', 'internalayam');
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('source','OUTLET ASAL'),
      array('destination','OUTLET TUJUAN')
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('source', 'OUTLET ASAL'),
      2 => array('destination', 'OUTLET TUJUAN'),
      3 => array('receh', 'RECEH ( JIKA ADA )'),
    );
    $this->required = array('source', 'destination');
    $this->buildRelation($this->inputFields[1][2], 'outlet');
    $this->buildRelation($this->inputFields[2][2], 'outlet');

    $this->expandables = array();
    $this->expandables[0] = array(
      'label' => 'BAHAN YANG DIKIRIM',
      'required' => array('barang', 'qty'),
      'fields' => array (
        0 => array('internalbarang[barang][]', 'NAMA BARANG'),
        1 => array('internalbarang[qty][]', 'JUMLAH'),
      )
    );
    $this->buildRelation($this->expandables[0]['fields'][0][2], 'baranggudang');

    $this->expandables[1] = array(
      'label' => 'AYAM YANG DIKIRIM',
      'required' => array('ayam', 'pcs', 'kg'),
      'fields' => array (
        0 => array('internalayam[ayam][]', 'AYAM'),
        1 => array('internalayam[pcs][]', 'JUMLAH'),
        2 => array('internalayam[kg][]', 'BERAT'),
      )
    );
    $this->buildRelation($this->expandables[1]['fields'][0][2], 'ayam', array('nama <>' => 'AYAM HIDUP'));

    $this->expandables[2] = array(
      'label' => 'PRODUK YANG DIKIRIM',
      'required' => array('produk', 'qty'),
      'fields' => array (
        0 => array('internalproduk[produk][]', 'NAMA PRODUK'),
        1 => array('internalproduk[qty][]', 'JUMLAH'),
      )
    );
    $this->buildRelation($this->expandables[2]['fields'][0][2], 'produk');
  }

  function validate ($data) {
    if (empty ($data['receh'])) $data['receh'] = 0;
    return parent::validate($data);
  }

  function internalreceh ($data) {
    if (empty ($data['receh'])) return true;
    if (!isset ($data['previous'])) {
      $this->sirkulasiKeuanganOutlet ('KELUAR', $data['reason'], $data['receh'], $data['id'], $data['waktu'], $data['source']);
      $this->sirkulasiKeuanganOutlet ('MASUK', $data['reason'], $data['receh'], $data['id'], $data['waktu'], $data['destination']);      
    } else {
      if ($data['modal'] > $data['previous']['modal']) {
        $this->sirkulasiKeuanganOutlet ('KELUAR', $data['reason'], $data['modal'] - $data['previous']['modal'], $data['id'], $data['waktu'], $data['source']);
        $this->sirkulasiKeuanganOutlet ('MASUK', $data['reason'], $data['modal'] - $data['previous']['modal'], $data['id'], $data['waktu'], $data['destination']);        
      }
      if ($data['modal'] < $data['previous']['modal']) {
        $this->sirkulasiKeuanganOutlet ('MASUK', $data['reason'], $data['previous']['modal'] - $data['modal'], $data['id'], $data['waktu'], $data['source']);
        $this->sirkulasiKeuanganOutlet ('KELUAR', $data['reason'], $data['previous']['modal'] - $data['modal'], $data['id'], $data['waktu'], $data['destination']);        
      }      
    }
  }

  function submodel ($data) {
    $excepted = array();
    foreach ($data['internalbarang']['barang'] as $index => $barang) {
      if ($barang == 0) continue;
      $internalbarang = array(
        'internal' => $data['id'],
        'barang' => $barang,
        'qty' => $data['internalbarang']['qty'][$index]
      );
      if (!empty ($data['internalbarang']['id'][$key])) {
        $internalbarang['id'] = $data['internalbarang']['id'][$key];
        $excepted[] = $this->internalbarang->update($internalbarang, $data['waktu'], $data['reason'], $data['source'], $data['destination']);
      } else $excepted[] = $this->internalbarang->save($internalbarang, $data['waktu'], $data['reason'], $data['source'], $data['destination']);
    }
    if (!empty ($excepted))
      foreach ($this->internalbarang->find(array('internal' => $data['id']), array('id' => $excepted)) as $delete)
        $this->internalbarang->delete($delete, $data['reason'], $data['waktu'], $data['source'], $data['destination']);

    $excepted = array();
    foreach ($data['internalayam']['ayam'] as $index => $ayam) {
      if ($ayam == 0) continue;
      $internalayam = array(
        'internal' => $data['id'],
        'ayam' => $ayam,
        'pcs' => $data['internalayam']['pcs'][$index],
        'kg' => $data['internalayam']['kg'][$index]
      );
      if (!empty ($data['internalayam']['id'][$key])) {
        $internalayam['id'] = $data['internalayam']['id'][$key];
        $excepted[] = $this->internalayam->update($internalayam, $data['waktu'], $data['reason'], $data['source'], $data['destination']);
      } else $excepted[] = $this->internalayam->save($internalayam, $data['waktu'], $data['reason'], $data['source'], $data['destination']);
    }
    if (!empty ($excepted))
      foreach ($this->internalayam->find(array('internal' => $data['id']), array('id' => $excepted)) as $delete)
        $this->internalayam->delete($delete, $data['reason'], $data['waktu'], $data['source'], $data['destination']);

    $excepted = array();
    foreach ($data['internalproduk']['produk'] as $index => $produk) {
      if ($produk == 0) continue;
      $internalproduk = array(
        'internal' => $data['id'],
        'produk' => $produk,
        'qty' => $data['internalproduk']['qty'][$index]
      );
      if (!empty ($data['internalproduk']['id'][$key])) {
        $internalproduk['id'] = $data['internalproduk']['id'][$key];
        $excepted[] = $this->internalproduk->update($internalproduk, $data['waktu'], $data['reason'], $data['source'], $data['destination']);
      } else $excepted[] = $this->internalproduk->save($internalproduk, $data['waktu'], $data['reason'], $data['source'], $data['destination']);
    }
    if (!empty ($excepted))
      foreach ($this->internalproduk->find(array('internal' => $data['id']), array('id' => $excepted)) as $delete)
        $this->internalproduk->delete($delete, $data['reason'], $data['waktu'], $data['source'], $data['destination']);
  }

  function prepare ($data) {
    $waktu = $data['waktu'];
    $source = $data['source'];
    $destination = $data['destination'];
    $receh = $data['receh'];
    $record = array( 
      'waktu' => $waktu,
      'source'=> $source,
      'destination' => $destination,
      'receh' => $receh
    );
    if (isset ($data['id'])) $record['id'] = $data['id'];
    return $record;
  }

  function update ($data) {
    $data['reason'] = 'EDIT TRANSAKSI ANTAR OUTLET';
    $record = $this->prepare($data);
    $data['previous'] = $this->findOne($data['id']);
    $data['id'] = parent::save($record);
    $this->internalreceh($data);
    $this->submodel($data);
  }

  function save ($data) {
    $data['reason'] = 'TRANSAKSI ANTAR OUTLET';
    $record = $this->prepare($data);
    $data['id'] = parent::save($record);
    $this->internalreceh($data);
    $this->submodel($data);
  }

  function delete ($id) {
    $reason = 'INTERNAL BATAL';
    $record = parent::findOne($id);
    $receh = $record['receh'];
    $data['id'] = $record['id'];
    $waktu = date('Y-m-d H:i:s',time());
    $source = $record['source'];
    $destination = $record['destination'];
    if ($receh > 0) {
      $this->sirkulasiKeuanganOutlet ('MASUK', $reason, $receh, $data['id'], $waktu, $source);
      $this->sirkulasiKeuanganOutlet ('KELUAR', $reason, $receh, $data['id'], $waktu, $destination);
    }
    $CI =& get_instance();
    $CI->load->model($this->submodel);
    foreach ($this->submodel as $submodel) {
      foreach ($this->$submodel->find(array ('internal' => $data['id'])) as $delete)
        $this->$submodel->delete($delete, $reason, $waktu, $source, $destination);      
    }
    return parent::delete($id);
  }

  function find ($where = array()) {
    $this->db
      ->select('internal.*')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select('source.nama as source', false)
      ->select('destination.nama as destination', false)
      ->join('outlet as source', 'internal.source = source.id', 'LEFT')
      ->join('outlet as destination', 'internal.destination = destination.id', 'LEFT');
    return parent::find($where);
  }
}
