<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class laporan extends my_controller {

  function belanja () {
    $this->laporan('laporanbelanja');
  }

  function penjualanglobal () {
    $this->laporan('laporanpenjualan');
  }

  function sirkulasibahan () {
    $this->laporan('laporansirkulasibahan');
  }

  function labarugi () {
    $this->laporan('laporanlabarugi');
  }

  function pengeluaran () {
    $this->laporan('laporanpengeluaran');
  }

  function hutang () {
    $this->laporan('laporanhutang');
  }

  function pesanan () {
    $this->laporan('laporanpesanan');
  }

  function stockgudang () {
    $model = 'laporanstockgudang';
    $data = array('entity' => $model);
    $this->load->model($model);
    
    $get = $this->input->get();
    $get = !$get ? array() : $get;
    foreach ($get as $field => $value) 
      if ($value == 0) unset($get[$field]);
      else $get[$field] = urldecode($value);
    $data['form'] = $get;
    $data['thead'] = $this->$model->getTHead();
    $data['tbody'] = $this->$model->find($get);
    $data['tfoot'] = $this->$model->getTFoot($data['tbody']);
    $data['filters'] = $this->$model->getFilters();
    $data['tablePage'] = $this->$model->getTablePage(null);
    $data['titles'] = array('STOK BAHAN', 'STOK AYAM', 'STOK PRODUK');

    $this->loadview('laporanmulti', $data);
  }

  function stockoutlet () {
    $model = 'laporanstockoutlet';
    $data = array('entity' => $model);
    $this->load->model($model);
    
    $get = $this->input->get();
    $get = !$get ? array() : $get;
    foreach ($get as $field => $value) 
      if ($value == 0) unset($get[$field]);
      else $get[$field] = urldecode($value);
    $data['form'] = $get;
    $data['thead'] = $this->$model->getTHead();
    $data['tbody'] = $this->$model->find($get);
    $data['tfoot'] = $this->$model->getTFoot($data['tbody']);
    $data['filters'] = $this->$model->getFilters();
    $data['tablePage'] = $this->$model->getTablePage(null);
    $data['titles'] = array('STOK BAHAN', 'STOK AYAM', 'STOK PRODUK');

    $this->loadview('laporanmulti', $data);
  }

  function penjualanoutlet () {
    $this->laporan('laporanpenjualanoutlet');
  }

  function bawaan () {
    $model = 'laporanbawaan';
    $data = array('entity' => $model);
    $this->load->model($model);
    
    $get = $this->input->get();
    $get = !$get ? array() : $get;
    foreach ($get as $field => $value) 
      if ($value == 0) unset($get[$field]);
      else $get[$field] = urldecode($value);
    $data['form'] = $get;
    $data['thead'] = $this->$model->getTHead();
    $data['tbody'] = $this->$model->find($get);
    $data['tfoot'] = $this->$model->getTFoot($data['tbody']);
    $data['filters'] = $this->$model->getFilters();
    $data['tablePage'] = $this->$model->getTablePage(null);
    $data['titles'] = array('BAWAAN RECEH', 'BAWAAN AYAM', 'BAWAAN BAHAN', 'BAWAAN PRODUK');

    $this->loadview('laporanmulti', $data);
  }

  function setoran () {
    $model = 'laporansetoran';
    $data = array('entity' => $model);
    $this->load->model($model);
    
    $get = $this->input->get();
    $get = !$get ? array() : $get;
    foreach ($get as $field => $value) 
      if ($value == 0) unset($get[$field]);
      else $get[$field] = urldecode($value);
    $data['form'] = $get;
    $data['thead'] = $this->$model->getTHead();
    $data['tbody'] = $this->$model->find($get);
    $data['tfoot'] = $this->$model->getTFoot($data['tbody']);
    $data['filters'] = $this->$model->getFilters();
    $data['tablePage'] = $this->$model->getTablePage(null);
    $data['titles'] = array('SETORAN', 'PENGEMBALIAN PRODUK', 'PENGEMBALIAN BAHAN');

    $this->loadview('laporanmulti', $data);
  }

  function belanjagrafik () {
    $model = 'laporangrafikbelanja';
    $data = array('entity' => $model);
    $this->load->model($model);    

    $get = $this->input->get();
    $get = !$get ? array() : $get;
    foreach ($get as $field => $value) 
      if ($value == 0) unset($get[$field]);
      else $get[$field] = urldecode($value);
    $data['form'] = $get;
      
    $container = array ();
    $barang = array ();
    $legends = array ();
    $data['datamax'] = 0;
    foreach ($this->$model->find($get) as $record) {
      $barang[$record->nama][] = array(date('Y-m-d 00:00', strtotime($record->waktu)), $record->qty);
      $data['datamax'] = $data['datamax'] >= $record->qty ? $data['datamax'] : $record->qty; 
    }
    foreach ($barang as $legend => $number) {
      $container[] = $number;
      if (!isset ($legends[$legend])) $legends[] = $legend;
    }
    $data['datachart'] = json_encode($container);
    $data['datalegends'] = json_encode($legends);
    $data['filters'] = $this->$model->getFilters();
    $data['tablePage'] = $this->$model->getTablePage(null);

    $this->loadview('chart', $data);
  }
}
