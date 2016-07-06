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

  function save ($data) {
    if (isset($data['id'])) $this->delete($data['id']);

    $total = 0;
    foreach ($data['belanjadetail']['total'] as $hargatotal) $total += $hargatotal;
    $databelanja = array(
      'waktu' => $data['waktu'],
      'karyawan' => $data['karyawan'],
      'total' => $total
    );
    if (isset($data['id'])) $databelanja['id'] = $data['id'];
    $this->db->insert('belanja', $databelanja);
    $belanja = $this->db->insert_id();
    foreach ($data['belanjadetail']['barang'] as $key => $value) {
      $qty = $data['belanjadetail']['qty'][$key];
      $this->db->insert('belanjadetail', array(
        'belanja' => $belanja,
        'distributor' => $data['belanjadetail']['distributor'][$key],
        'barang' => $data['belanjadetail']['barang'][$key],
        'qty' => $qty,
        'hargasatuan' => $data['belanjadetail']['total'][$key] / $qty,
        'total' => $data['belanjadetail']['total'][$key],
      ));
      $id = $this->db->insert_id();
      $this->sirkulasiBarang ($data['waktu'], $data['belanjadetail']['barang'][$key], 'MASUK', 'BELANJA', $id, $qty);
    }
    $this->sirkulasiKeuangan ('KELUAR', 'BELANJA', $total, $belanja, $data['waktu']);
  }

  function delete ($id) {
    $waktu = date('Y-m-d H:i:s',time());
    $previous = $this->findOne($id);
    $this->sirkulasiKeuangan('MASUK', 'PEMBATALAN BELANJA', $previous['total'], $id, $waktu);
    foreach ($this->db->get_where('belanjadetail', array ('belanja' => $id))->result() as $brg)
      $this->sirkulasiBarang ($waktu, $brg->barang, 'KELUAR', 'PEMBATALAN BELANJA', $brg->id, $brg->qty);
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
