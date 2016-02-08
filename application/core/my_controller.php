<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class my_controller extends CI_Controller {

  function __construct () {
    parent::__construct();
    $this->load->helper(array('url'));
    date_default_timezone_set('Asia/Jakarta');
  }

  function loadview ($viewer=null, $data=array()) {
    $css = array (
      'bootstrap/css/bootstrap.min.css',
      'css/fonts/ptsans/stylesheet.css',
      'css/fonts/icomoon/style.css',
      'css/mws-style.css',
      'css/icons/icol16.css',
      'css/icons/icol32.css',
      'jui/css/jquery.ui.all.css',
      'jui/css/jquery.ui.timepicker.css',
      'jui/jquery-ui.custom.css',
      'css/mws-theme.css',
      'css/themer.css',
      'plugins/select2/select2.min.css',
      'css/henrisusanto.css',
    );
    foreach ($css as $c) $data['css'][] = base_url("assets/$c");

    $js = array (
      'js/libs/jquery-1.8.3.min.js',
      'js/libs/jquery.mousewheel.min.js',
      'jui/js/jquery-ui-1.9.2.min.js',
      'jui/jquery-ui.custom.min.js',
      'jui/js/jquery.ui.touch-punch.js',
      'jui/js/timepicker/jquery-ui-timepicker.min.js',
      'plugins/select2/select2.full.min.js',
      'plugins/datatables/jquery.dataTables.min.js',
      'bootstrap/js/bootstrap.min.js',
      'js/core/mws.js',
      'js/demo/demo.widget.js',
      'js/demo/demo.table.js',
      'js/henrisusanto.js'
    );
    foreach ($js as $j) $data['js'][] = base_url("assets/$j");

    $data['menu'] = array();

    $data['menu'][] = array('DATA TRANSAKSI', '', '');
    $data['menu'][] = array('Cash Flow', 'keuangan/cashflow', 'retweet');
    $data['menu'][] = array('Belanja', 'belanja/harian', 'shopping-cart');
    $data['menu'][] = array('Setoran Outlet', 'keuangan/setoran', 'cloudy');

    $data['menu'][] = array('DATA MASTER', '', '');
    $data['menu'][] = array('Barang Gudang', 'belanja/baranggudang', 'food');
    $data['menu'][] = array('Produk', 'penjualan/produk', 'bag');
    $data['menu'][] = array('Komposisi', '', 'chemical');
    $data['menu'][] = array('Outlet', 'penjualan/outlet', 'business-card');
    $data['menu'][] = array('Karyawan', 'personal/karyawan', 'users');
    $data['menu'][] = array('Distributor', 'belanja/distributor', 'truck');
    $data['menu'][] = array('Debitur', 'keuangan/debitur', 'official');

    $this->load->view('header', $data);
    $this->load->view('menu', $data);
    if (!is_null($viewer)) $this->load->view($viewer, $data);
    if (isset($data['viewers'])) foreach ($data['viewers'] as $viewer) $this->load->view($viewer);
    $this->load->view('footer', $data);
  }

}
