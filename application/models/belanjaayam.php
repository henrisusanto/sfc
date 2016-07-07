<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class belanjaayam extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'belanjaayam';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('karyawan','PENANGGUNG JAWAB'),
      array('distributor','DISTRIBUTOR'),
      array('ekor','JUMLAH'),
      array('kg','BERAT'),
      array('total','HARGA TOTAL')
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('karyawan', 'PENANGGUNG JAWAB'),
      2 => array('distributor', 'DISTRIBUTOR'),
      3 => array('ekor', 'JUMLAH (EKOR)'),
      4 => array('kg', 'BERAT TOTAL'),
      5 => array('total', 'HARGA TOTAL'),
    );
    $this->required = array ('ekor', 'kg', 'total');
    $this->buildRelation($this->inputFields[1][2], 'karyawan');
    $this->buildRelation($this->inputFields[2][2], 'distributor');
  }

  function validate ($data) {
    $syarat = $this->belanjaayam->is_ok();
    if (true !== $syarat) return $syarat;
    return parent::validate($data);
  }

  function update ($data) {
    $reason = 'EDIT BELANJA AYAM';
    $belanjaayam = $this->prepare($data);
    $previous = $this->findOne($data['id']);
    $fkey = parent::save($belanjaayam);
    
    if ($data['ekor'] > $previous['ekor'] && $data['kg'] > $previous['kg'])
      $this->sirkulasiAyam ($data['waktu'], $belanjaayam['ayam'], 'MASUK', $reason, $fkey, 
      $data['ekor'] - $previous['ekor'], $data['kg'] - $previous['kg']);
    else if ($data['ekor'] < $previous['ekor'] && $data['kg'] < $previous['kg'])
      $this->sirkulasiAyam ($data['waktu'], $belanjaayam['ayam'], 'KELUAR', $reason, $fkey, 
      $previous['ekor'] - $data['ekor'], $previous['kg'] - $data['kg']);
    else {
      if ($data['ekor'] > $previous['ekor'])
        $this->sirkulasiAyam ($data['waktu'], $belanjaayam['ayam'], 'MASUK', $reason, $fkey, 
        $data['ekor'] - $previous['ekor'], 0);      
      if ($data['kg'] > $previous['kg'])
        $this->sirkulasiAyam ($data['waktu'], $belanjaayam['ayam'], 'MASUK', $reason, $fkey, 
        0, $data['kg'] - $previous['kg']);
      if ($data['ekor'] < $previous['ekor'])
        $this->sirkulasiAyam ($data['waktu'], $belanjaayam['ayam'], 'KELUAR', $reason, $fkey, 
        $previous['ekor'] - $data['ekor'], 0);
      if ($data['kg'] < $previous['kg'])
        $this->sirkulasiAyam ($data['waktu'], $belanjaayam['ayam'], 'KELUAR', $reason, $fkey, 
        0, $previous['kg'] - $data['kg']);
    }

    if ($data['total'] > $previous['total'])
      $this->sirkulasiKeuangan ('KELUAR', $reason, $data['total'] - $previous['total'], $fkey, $data['waktu']);    
    if ($data['total'] < $previous['total'])
      $this->sirkulasiKeuangan ('MASUK', $reason, $previous['total'] - $data['total'], $fkey, $data['waktu']);
  }

  function save ($data) {
    $belanjaayam = $this->prepare($data);
    $fkey = parent::save($belanjaayam);
    $this->sirkulasiAyam ($data['waktu'], $belanjaayam['ayam'], 'MASUK', 'BELANJA AYAM', $fkey, $data['ekor'], $data['kg']);
    $this->sirkulasiKeuangan ('KELUAR', 'BELANJA AYAM', $data['total'], $fkey, $data['waktu']);
  }

  function prepare ($data) {
    $ayam = $this->db->get_where('ayam', array('nama' => 'AYAM HIDUP'))->row_array();
    $record = array(
      'waktu' => $data['waktu'],
      'karyawan' => $data['karyawan'],
      'distributor' => $data['distributor'],
      'ayam' => $ayam['id'],
      'ekor' => $data['ekor'],
      'kg' => $data['kg'],
      'total' => $data['total'],
    );
    if (isset ($data['id'])) $record['id'] = $data['id'];
    return $record;
  }

  function delete ($id) {
    $waktu = date('Y-m-d H:i:s',time());
    $ayam = $this->db->get_where('ayam', array('nama' => 'AYAM HIDUP'))->row_array();
    $belanjaayam = $this->findOne ($id);
    $this->sirkulasiAyam ($waktu, $ayam['id'], 'KELUAR', 'PEMBATALAN BELANJA AYAM', $id, $belanjaayam['ekor'], $belanjaayam['kg']);
    $this->sirkulasiKeuangan ('MASUK', 'PEMBATALAN BELANJA AYAM', $belanjaayam['total'], $id, $waktu);
    return parent::delete($id);
  }

  function find ($where = array()) {
    $this->db
      ->select('belanjaayam.*')
      ->select('karyawan.nama as karyawan')
      ->select('distributor.nama as distributor')
      ->select("CONCAT(ekor, ' EKOR') as ekor", false)
      ->select("CONCAT(kg, ' KG') as kg", false)
      ->select("CONCAT('Rp ', FORMAT(total, 2)) AS total", false)
      ->join('karyawan', 'karyawan.id = belanjaayam.karyawan' ,'LEFT')
      ->join('distributor', 'distributor.id = belanjaayam.distributor', 'LEFT');
    return parent::find($where);
  }

  function is_ok () {
    $syarat = $this->db
      ->where('nama', 'AYAM HIDUP')
      ->get('ayam')
      ->result();
    return count ($syarat) == 1 ? true : array("PROSES TIDAK DAPAT DILANJUTKAN KARENA 
      AYAM HIDUP TIDAK DITEMUKAN DALAM DATA MASTER AYAM MENTAH", 'error');
  }
}
