<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class laporanpengeluaran extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'pengeluaran';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('karyawan','PENANGGUNG JAWAB'),
      array('item','ITEM'),
      array('nominal','NOMINAL'),
    );
    $this->filters = array(
      0 => array('outlet', 'FILTER OUTLET'),
      1 => array('since', 'SEJAK TANGGAL'),
      2 => array('until', 'HINGGA TANGGAL'),
    );
    $this->buildRelation($this->filters[0][2], 'outlet', array(), 'PENGELUARAN GUDANG');
    $this->buildTFoot();
  }

  function getTFoot ($tbody) {
    $total = 0;
    foreach ($tbody as $tb) $total += preg_replace("/([^0-9\\.])/i", "", $tb->nominal);
    $this->tfoot[3] = 'TOTAL';
    $this->tfoot[4] = $this->toRp ($total);
    return parent::getTFoot($tbody);
  }

  function find ($where = array()) {
    $this->translateDateRange($where);
    if (isset($where['outlet'])) {
      $this->db
        ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
        ->select('karyawan.nama as karyawan', false)
        ->select('item')
        ->select("CONCAT('Rp ', FORMAT(setoranpengeluaran.nominal, 2)) AS nominal", false)
        ->from('setoranpengeluaran')
        ->join('setoran', 'setoranpengeluaran.setoran = setoran.id')
        ->join('karyawan', 'karyawan.id = setoran.karyawan', 'LEFT')
        ->join('outlet','outlet.id = setoran.outlet')
        ->where($where);
    } else {
      $this->db
        ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
        ->select('karyawan.nama as karyawan', false)
        ->select('item')
        ->select("CONCAT('Rp ', FORMAT(nominal, 2)) AS nominal", false)
        ->from('pengeluaran')
        ->join('pengeluarandetail', 'pengeluaran.id = pengeluarandetail.pengeluaran')
        ->join('karyawan', 'karyawan.id = pengeluaran.karyawan', 'LEFT')
        ->where($where);
    }
    return 
    $this->db->get()->result();
    // die($this->db->last_query());
  }
}
