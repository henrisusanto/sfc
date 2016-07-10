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

  function validate ($data) {
    $syarat = $this->pemotongan->is_ok();
    if (true !== $syarat) return $syarat;
    return parent::validate($data);
  }

  function prepare ($data) {
    $prepared = array();
    $prepared['ayamhidup'] = $this->db->get_where('ayam', array('nama' => 'AYAM HIDUP'))->row_array();
    $prepared['atiayam'] = $this->db->get_where('ayam', array('nama' => 'ATI'))->row_array();
    $hasilpcs= 0;
    $hasilkg = 0;
    $atikg = 0;

    foreach ($data['pemotongandetail']['ayam'] as $index => $ayam) {
      if ($ayam == 0) continue;
      if ($ayam == $prepared['atiayam']['id']) {
        $atikg += $data['pemotongandetail']['kg'][$index];
      }else {
        $hasilpcs += $data['pemotongandetail']['pcs'][$index];
        $hasilkg += $data['pemotongandetail']['kg'][$index];        
      }
    }
    $prepared['record'] = array(
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
    );
    if (isset ($data['id'])) $prepared['record']['id'] = $data['id'];
    return $prepared;
  }

  function update ($data) {
    $reason = 'EDIT PEMOTONGAN';
    $prepared = $this->prepare($data);
    $previous = $this->findOne($data['id']);
    $data['id'] = parent::save($prepared['record']);
    $waktu = $data['waktu'];

    if ($data['bahanpcs'] > $previous['bahanpcs'] && $data['bahankg'] > $previous['bahankg'])
      $this->sirkulasiAyam ($waktu, $prepared['ayamhidup']['id'], 'KELUAR', $reason, $data['id'], 
      $data['bahanpcs'] - $previous['bahanpcs'], $data['bahankg'] - $previous['bahankg']);
    else if ($data['bahanpcs'] < $previous['bahanpcs'] && $data['bahankg'] < $previous['bahankg'])
      $this->sirkulasiAyam ($waktu, $prepared['ayamhidup']['id'], 'MASUK', $reason, $data['id'], 
      $previous['bahanpcs'] - $data['bahanpcs'], $previous['bahankg'] - $data['bahankg']);
    else {
      if ($data['bahanpcs'] > $previous['bahanpcs'])
        $this->sirkulasiAyam ($waktu, $prepared['ayamhidup']['id'], 'KELUAR', $reason, $data['id'], 
        $data['bahanpcs'] - $previous['bahanpcs'], 0);      
      if ($data['bahankg'] > $previous['bahankg'])
        $this->sirkulasiAyam ($waktu, $prepared['ayamhidup']['id'], 'KELUAR', $reason, $data['id'], 
        0, $data['bahankg'] - $previous['bahankg']);
      if ($data['bahanpcs'] < $previous['bahanpcs'])
        $this->sirkulasiAyam ($waktu, $prepared['ayamhidup']['id'], 'MASUK', $reason, $data['id'], 
        $previous['bahanpcs'] - $data['bahanpcs'], 0);
      if ($data['bahankg'] < $previous['bahankg'])
        $this->sirkulasiAyam ($waktu, $prepared['ayamhidup']['id'], 'MASUK', $reason, $data['id'], 
        0, $previous['bahankg'] - $data['bahankg']);
    }

    $CI =& get_instance();
    $CI->load->model('pemotongandetail');
    $excepted = array();
    foreach ($data['pemotongandetail']['ayam'] as $index => $ayam) {
      if ($ayam == 0) continue;
      $pcs = $data['pemotongandetail']['pcs'][$index];
      $kg = $data['pemotongandetail']['kg'][$index];
      $detail = array (
        'pemotongan' => $data['id'],
        'ayam' => $ayam,
        'pcs' => $pcs,
        'kg' => $kg,
      );
      if (!empty ($data['pemotongandetail']['id'][$index])) {
        $detail['id'] = $data['pemotongandetail']['id'][$index];
        $excepted[] = $this->pemotongandetail->update($detail, $data['waktu'], $reason);
      } else $excepted[] = $this->pemotongandetail->save($detail, $data['waktu'], $reason);
    }
    if (!empty ($excepted))
      foreach ($this->pemotongandetail->find(array('pemotongan' => $data['id']), array('id' => $excepted)) as $delete)
        $this->pemotongandetail->delete($delete->id, $data['waktu'], $reason);
  }

  function save ($data) {
    $reason = 'PEMOTONGAN';
    $prepared = $this->prepare($data);
    $pemotongan_id = parent::save($prepared['record']);
    $this->sirkulasiAyam ($data['waktu'], $prepared['ayamhidup']['id'], 'KELUAR', $reason, $pemotongan_id, $data['bahanpcs'], $data['bahankg']);

    $CI =& get_instance();
    $CI->load->model('pemotongandetail');
    foreach ($data['pemotongandetail']['ayam'] as $index => $ayam) {
      if ($ayam == 0) continue;
      $pcs = $data['pemotongandetail']['pcs'][$index];
      $kg = $data['pemotongandetail']['kg'][$index];
      $this->pemotongandetail->save(array (
        'pemotongan' => $pemotongan_id,
        'ayam' => $ayam,
        'pcs' => $pcs,
        'kg' => $kg,
      ), $data['waktu'], $reason);
    }
  }

  function delete ($pemotongan_id) {
    if (!$this->is_ok()) return true;
    $reason = 'PEMOTONGAN BATAL';
    $waktu = date('Y-m-d H:i:s',time());
    $ayamhidup = $this->db->get_where('ayam', array('nama' => 'AYAM HIDUP'))->row_array();
    $data = $this->findOne($pemotongan_id);
    $this->sirkulasiAyam (
      $waktu, 
      $ayamhidup['id'], 
      'MASUK', $reason, 
      $pemotongan_id, 
      $data['bahanpcs'], 
      $data['bahankg']
    );

    $CI =& get_instance();
    $CI->load->model('pemotongandetail');
    foreach ($this->pemotongandetail->find(array('pemotongan' => $pemotongan_id)) as $detail)
      $this->pemotongandetail->delete($detail->id, $waktu, $reason);
    return parent::delete($pemotongan_id);
  }

  function find ($where = array()) {
    $this->db
      ->select('pemotongan.*')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT(bahanpcs, ' EKOR / ', bahankg, ' KG') as ayamhidup", false)
      ->select("CONCAT(hasilpcs, ' PCs / ', hasilkg, ' KG') as hasilpemotongan", false)
      ->select("CONCAT (kepasar, ' KG') as kepasar", false)
      ->select("CONCAT (ROUND (avg, 1), ' KG') as avg", false)
      ->select('ROUND (per5kg, 1) as per5kg', false)
      ->select("CONCAT (susud, ' KG') as susud", false);
    return parent::find($where);
  }

  function is_ok () {
    $syarat = $this->db
      ->or_where('nama', 'ATI')
      ->or_where('nama', 'AYAM HIDUP')
      ->get('ayam')
      ->result();
    return count ($syarat) == 2 ? true : array("PROSES TIDAK DAPAT DILANJUTKAN KARENA 
    AYAM HIDUP DAN ATAU ATI TIDAK DITEMUKAN DALAM DATA MASTER AYAM MENTAH", 'error');
  }
}
