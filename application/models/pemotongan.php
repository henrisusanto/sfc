<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class pemotongan extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'pemotongan';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('bahanpcs','AYAM EKOR'),
      array('bahankg','AYAM KG'),
      array('hasilpcs','POTONGAN PCS'),
      array('hasilkg','POTONGAN KG'),
      array('avg','RATA-RATA'),
      array('susud','SUSUD'),
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('karyawan', 'PENANGGUNG JAWAB'),
      2 => array('bahanpcs', 'JUMLAH AYAM'),
      3 => array('bahankg', 'BERAT TOTAL'),
      4 => array('pasar', 'KE PASAR (KG)'),
    );

    $this->buildRelation($this->inputFields[1][2], 'karyawan');

    $this->expandables = array();
    $this->expandables[0] = array(
      'label' => 'HASIL PEMOTONGAN AYAM',
      'fields' => array (
        0 => array('pemotongandetail[ayam][]', 'JENIS POTONGAN AYAM'),
        1 => array('pemotongandetail[pcs][]', 'PCs'),
        2 => array('pemotongandetail[kg][]', 'KG'),
      )
    );

    $this->db->where('nama <>', 'AYAM HIDUP');
    $this->buildRelation($this->expandables[0]['fields'][0][2], 'ayam');
  }

  function save ($data) {
    if (isset($data['id'])) die('durung tak pikir');

    $ayamhidup = $this->db->get_where('ayam', array('nama' => 'AYAM HIDUP'))->row_array();
    if (!isset($ayamhidup['id'])) die('AYAM HIDUP not found');
    $atiayam = $this->db->get_where('ayam', array('nama' => 'ATI'))->row_array();
    if (!isset($atiayam['id'])) die('ATI not found');

    $id = time();
    $hasilpcs = 0;
    $hasilkg = 0;
    $atikg = 0;
    foreach ($data['pemotongandetail']['ayam'] as $index => $value) {
      $this->db->insert('pemotongandetail', array(
        'id' => $id + $index,
        'pemotongan' => time(),
        'ayam' => $data['pemotongandetail']['ayam'][$index],
        'pcs' => $data['pemotongandetail']['pcs'][$index],
        'kg' => $data['pemotongandetail']['kg'][$index],
      ));
      $hasilpcs += $data['pemotongandetail']['pcs'][$index];
      $hasilkg += $data['pemotongandetail']['kg'][$index];
      if ($data['pemotongandetail']['ayam'][$index] == $atiayam['id']) 
        $atikg = $data['pemotongandetail']['kg'][$index]; 
      $this->sirkulasiAyam (
        $id + $index, 
        $data['waktu'], 
        $data['pemotongandetail']['ayam'][$index], 
        'MASUK', 
        'PEMOTONGAN', 
        $id + $index, 
        $data['pemotongandetail']['pcs'][$index],
        $data['pemotongandetail']['kg'][$index]
      );
    }
    $this->db->insert('pemotongan', array(
      'id' => $id,
      'waktu' => $data['waktu'],
      'karyawan' => $data['karyawan'],
      'bahanpcs' => $data['bahanpcs'],
      'bahankg' => $data['bahankg'],
      'hasilpcs' => $hasilpcs,
      'hasilkg' => $hasilkg,
      'avg' => $data['bahanpcs'] / $data['bahankg'],
      'per5kg' => $hasilpcs / $hasilkg * 5,
      'susud' => $data['bahanpcs']  - $hasilpcs - $data['pasar'] - $atikg,
      'pasar' => $data['pasar'],
    ));
    $this->sirkulasiAyam (
      null,
      $data['waktu'], 
      $ayamhidup['id'], 
      'KELUAR', 
      'PEMOTONGAN', 
      $id, 
      $data['bahanpcs'], 
      $data['bahankg']
    );
  }


  function find ($where = array()) {
    return parent::find($where);
  }
}
