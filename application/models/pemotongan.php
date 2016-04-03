<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class pemotongan extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'pemotongan';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('ayamhidup','AYAM HIDUP'),
      array('hasilpemotongan','HASIL PEMOTONGAN'),
      array('kepasar','KE PASAR'),
      array('avg','RATA-RATA'),
      array('per5kg','POTONGAN/5KG'),
      array('susud','SUSUD'),
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('karyawan', 'PENANGGUNG JAWAB'),
      2 => array('bahanpcs', 'JUMLAH AYAM'),
      3 => array('bahankg', 'BERAT TOTAL'),
      4 => array('kepasar', 'KE PASAR (KG)'),
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
    if (!$this->is_ok()) return true;
    if (isset($data['id'])) die('durung tak pikir');
    $ayamhidup = $this->db->get_where('ayam', array('nama' => 'AYAM HIDUP'))->row_array();
    $atiayam = $this->db->get_where('ayam', array('nama' => 'ATI'))->row_array();

    $hasilpcs = 0;
    $hasilkg = 0;
    $atikg = 0;
    foreach ($data['pemotongandetail']['ayam'] as $index => $ayam) {
      if ($ayam == 0) continue;
      if ($ayam == $atiayam['id']) {
        $atikg += $data['pemotongandetail']['kg'][$index];
      }else {
        $hasilpcs += $data['pemotongandetail']['pcs'][$index];
        $hasilkg += $data['pemotongandetail']['kg'][$index];        
      }
    }
    $this->db->insert('pemotongan', array(
      'waktu' => $data['waktu'],
      'karyawan' => $data['karyawan'],
      'bahanpcs' => $data['bahanpcs'],
      'bahankg' => $data['bahankg'],
      'hasilpcs' => $hasilpcs,
      'hasilkg' => $hasilkg,
      'avg' => $data['bahankg'] / $data['bahanpcs'],
      'per5kg' => $hasilpcs / $hasilkg * 5,
      'susud' => $data['bahankg']  - $hasilkg - $data['kepasar'] - $atikg,
      'kepasar' => $data['kepasar'],
    ));
    $pemotongan_id = $this->db->insert_id();
    $this->sirkulasiAyam ($data['waktu'], $ayamhidup['id'], 'KELUAR', 'PEMOTONGAN', $pemotongan_id, $data['bahanpcs'], $data['bahankg']);
    foreach ($data['pemotongandetail']['ayam'] as $index => $ayam) {
      if ($ayam == 0) continue;
      $pcs = $data['pemotongandetail']['pcs'][$index];
      $kg = $data['pemotongandetail']['kg'][$index];
      $this->db->insert('pemotongandetail', array (
        'pemotongan' => $pemotongan_id,
        'ayam' => $ayam,
        'pcs' => $pcs,
        'kg' => $kg,
      ));
      $pdetail_id = $this->db->insert_id();
      $this->sirkulasiAyam ($data['waktu'], $ayam, 'MASUK', 'PEMOTONGAN', $pdetail_id, $pcs, $kg);
    }
  }

  function find ($where = array()) {
    $this->db
      ->select('pemotongan.*')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT(bahanpcs, ' EKOR / ', bahankg, ' KG') as ayamhidup", false)
      ->select("CONCAT(hasilpcs, ' PCs / ', hasilkg, ' KG') as hasilpemotongan", false)
      ->select("CONCAT (kepasar, ' KG') as kepasar", false)
      ->select("CONCAT (avg, ' KG') as avg", false)
      ->select("CONCAT (susud, ' KG') as susud", false)
      ;
    return parent::find($where);
  }

  function is_ok () {
    $syarat = $this->db
      ->or_where('nama', 'ATI')
      ->or_where('nama', 'AYAM HIDUP')
      ->get('ayam')
      ->result();
    return count ($syarat) == 2;
  }
}
