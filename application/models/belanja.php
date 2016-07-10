<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class belanja extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'belanja';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('nama','PENANGGUNG JAWAB'),
      array('total','TOTAL')
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('karyawan', 'PENANGGUNG JAWAB'),
    );

    $this->buildRelation($this->inputFields[1][2], 'karyawan');

    $this->expandables = array();
    $this->expandables[0] = array(
      'label' => 'DAFTAR BARANG BELANJA',
      'required' => array('barang', 'qty', 'total'),
      'fields' => array (
        0 => array('belanjadetail[barang][]', 'NAMA BARANG'),
        1 => array('belanjadetail[distributor][]', 'TOKO / PENJUAL'),
        2 => array('belanjadetail[qty][]', 'JUMLAH'),
        3 => array('belanjadetail[total][]', 'HARGA TOTAL'),
      )
    );

    $this->buildRelation($this->expandables[0]['fields'][0][2], 'baranggudang');
    $this->buildRelation($this->expandables[0]['fields'][1][2], 'distributor');
  }

  function update ($data) {
    $reason = 'EDIT BELANJA';
    $databelanja= $this->prepare($data);
    $oldbelanja = $this->findOne($data['id']);

    $CI =& get_instance();
    $CI->load->model('belanjadetail');
    $excepted = array();
    foreach ($data['belanjadetail']['barang'] as $key => $value) {
      $record = array(
        'belanja' => $data['id'],
        'distributor' => $data['belanjadetail']['distributor'][$key],
        'barang' => $data['belanjadetail']['barang'][$key],
        'qty' => $data['belanjadetail']['qty'][$key],
        'hargasatuan' => $data['belanjadetail']['total'][$key] / $data['belanjadetail']['qty'][$key],
        'total' => $data['belanjadetail']['total'][$key],
      );
      if (!empty ($data['belanjadetail']['id'][$key])) $record['id'] = $data['belanjadetail']['id'][$key];
      $excepted[] = $this->belanjadetail->save($record, $data['waktu'], $reason);
    }
    if (!empty ($excepted))
      foreach ($this->belanjadetail->find(array('belanja' => $data['id']), array('id' => $excepted)) as $delete)
        $this->belanjadetail->delete($delete->id, $data['waktu'], $reason);

    if ($oldbelanja['total'] > $databelanja['total'])
      $this->sirkulasiKeuangan ('MASUK', $reason, $oldbelanja['total'] - $databelanja['total'], $data['id'], $data['waktu']);
    else if ($oldbelanja['total'] < $databelanja['total'])
      $this->sirkulasiKeuangan ('KELUAR', $reason, $databelanja['total'] - $oldbelanja['total'], $data['id'], $data['waktu']);

    return parent::save($databelanja['record']);
  }

  function save ($data) {
    $reason = 'BELANJA';
    $databelanja = $this->prepare($data);
    $this->db->insert('belanja', $databelanja['record']);
    $belanja = $this->db->insert_id();
    $CI =& get_instance();
    $CI->load->model('belanjadetail');
    foreach ($data['belanjadetail']['barang'] as $key => $value) {
      $this->belanjadetail->save(array(
        'belanja' => $belanja,
        'distributor' => $data['belanjadetail']['distributor'][$key],
        'barang' => $data['belanjadetail']['barang'][$key],
        'qty' => $data['belanjadetail']['qty'][$key],
        'hargasatuan' => $data['belanjadetail']['total'][$key] / $data['belanjadetail']['qty'][$key],
        'total' => $data['belanjadetail']['total'][$key],
      ), $data['waktu'], $reason);
    }
    $this->sirkulasiKeuangan ('KELUAR', $reason, $databelanja['total'], $belanja, $data['waktu']);
  }

  function prepare ($data) {
    $databelanja = array('total' => 0, 'record' => array());
    foreach ($data['belanjadetail']['total'] as $hargatotal) $databelanja['total'] += $hargatotal;
    $databelanja['record'] = array(
      'waktu' => $data['waktu'],
      'karyawan' => $data['karyawan'],
      'total' => $databelanja['total']
    );
    if (isset ($data['id'])) $databelanja['record']['id'] = $data['id'];
    return $databelanja;
  }

  function delete ($id) {
    $waktu = date('Y-m-d H:i:s',time());
    $reason = 'PEMBATALAN BELANJA';
    $previous = $this->findOne($id);
    $this->sirkulasiKeuangan('MASUK', $reason, $previous['total'], $id, $waktu);
    $CI =& get_instance();
    $CI->load->model('belanjadetail');
    foreach ($this->belanjadetail->find(array('belanja' => $id)) as $delete)
      $this->belanjadetail->delete($delete->id, $waktu, $reason);
    return parent::delete ($id);
  }

  function find ($where = array()) {
    $this->db
      ->select('belanja.*, karyawan.nama')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT('Rp ', FORMAT(total, 2)) AS total", false)
      ->join('karyawan', 'karyawan.id = belanja.karyawan', 'LEFT');
    return parent::find($where);
  }

}
