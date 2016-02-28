<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class laporan extends my_controller {

  function belanjagudang () {
    $this->laporan('laporanbelanja');
  }

  function penjualanglobal () {
    $this->laporan('laporanpenjualan');
  }

  function grafikbahan () {
    $model = 'grafikbahan';
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
