<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class pengeluarangudang extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'pengeluaran';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('karyawan','PENANGGUNG JAWAB'),
      array('total','TOTAL'),
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('karyawan', 'PENANGGUNG JAWAB'),
    );

    $this->buildRelation($this->inputFields[1][2], 'karyawan');

    $this->expandables = array();
    $this->expandables[0] = array(
      'label' => 'DAFTAR PENGELUARAN',
      'fields' => array (
        0 => array('pengeluarandetail[item][]', 'ITEM'),
        1 => array('pengeluarandetail[nominal][]', 'NOMINAL'),
      ),
      'required' => array('item', 'nominal'),
      'strings' => array('item')
    );
  }

  function prepare ($data) {
    $total = 0;
    foreach ($data['pengeluarandetail']['nominal'] as $nominal) $total += $nominal;
    $record = array(
      'waktu' => $data['waktu'],
      'karyawan' => $data['karyawan'],
      'total' => $total,
    );
    if (isset ($data['id'])) $record['id'] = $data['id'];
    return $record;
  }

  function update ($data) {
    $record = $this->prepare($data);
    $previous = $this->findOne($data['id']);
    $pengeluaran_id = parent::save($record);
    $reason = 'EDIT PENGELUARAN';

    $CI =& get_instance();
    $CI->load->model('pengeluarandetail');
    $excepted = array();
    foreach ($data['pengeluarandetail']['item'] as $key => $value) {
      $detail = array(
        'pengeluaran' => $data['id'],
        'item' => $data['pengeluarandetail']['item'][$key],
        'nominal' => $data['pengeluarandetail']['nominal'][$key],
      );
      if (!empty ($data['pengeluarandetail']['id'][$key])) $detail['id'] = $data['pengeluarandetail']['id'][$key];
      $excepted[] = $this->pengeluarandetail->save($detail, $data['waktu'], $reason);
    }
    foreach ($this->pengeluarandetail->find(array('pengeluaran' => $data['id']), array('id' => $excepted)) as $delete)
      $this->pengeluarandetail->delete($delete->id, $data['waktu'], $reason);

    if ($record['total'] > $previous['total'])
      $this->sirkulasiKeuangan ('KELUAR', $reason, $record['total'] - $previous['total'], $data['id'], $data['waktu']);
    else if ($record['total'] < $previous['total'])
      $this->sirkulasiKeuangan ('KELUAR', $reason, $previous['total'] - $record['total'], $data['id'], $data['waktu']);
  }

  function save ($data) {
    $record = $this->prepare($data);
    $pengeluaran_id = parent::save($record);
    
    $CI =& get_instance();
    $CI->load->model('pengeluarandetail');
    foreach ($data['pengeluarandetail']['item'] as $index => $pd)
      $this->pengeluarandetail->save(array(
        'pengeluaran' => $pengeluaran_id,
        'item' => $data['pengeluarandetail']['item'][$index],
        'nominal' => $data['pengeluarandetail']['nominal'][$index],
      ));

    $this->sirkulasiKeuangan ('KELUAR', 'PENGELUARAN', $record['total'], $pengeluaran_id, $data['waktu']);
  }

  function delete ($id) {
    $record = $this->findOne ($id);
    $this->sirkulasiKeuangan ('MASUK', 'PEMBATALAN PENGELUARAN', $record['total'], $id, date ('Y-m-d H:i:s', time()));
    $CI =& get_instance();
    $CI->load->model('pengeluarandetail');
    foreach ($this->pengeluarandetail->find(array('pengeluaran' => $id)) as $detail)
      $this->pengeluarandetail->delete($detail->id);
    return parent::delete($id);
  }

  function find ($where = array()) {
    $this->db
      ->select('pengeluaran.*, karyawan.nama as karyawan')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT('Rp ', FORMAT(total, 2)) AS total", false)
      ->join('karyawan', 'karyawan.id = pengeluaran.karyawan', 'LEFT');
    return parent::find($where);
  }
}
