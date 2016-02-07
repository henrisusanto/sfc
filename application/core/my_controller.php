<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class my_controller extends CI_Controller {

  function __construct () {
    parent::__construct();
    $this->load->helper(array('url'));
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
      'jui/jquery-ui.custom.css',
      'css/mws-theme.css',
      'css/themer.css',
      'css/henrisusanto.css',
    );
    foreach ($css as $c) $data['css'][] = base_url("assets/$c");

    $js = array (
      'js/libs/jquery-1.8.3.min.js',
      'js/libs/jquery.mousewheel.min.js',
      'jui/js/jquery-ui-1.9.2.min.js',
      'jui/jquery-ui.custom.min.js',
      'jui/js/jquery.ui.touch-punch.js',
      'plugins/datatables/jquery.dataTables.min.js',
      'bootstrap/js/bootstrap.min.js',
      'js/core/mws.js',
      'js/demo/demo.table.js',
      'js/henrisusanto.js'
    );
    foreach ($js as $j) $data['js'][] = base_url("assets/$j");

    $data['menu'] = array();
    $data['menu'][] = array('Distributor', 'belanja/distributor', 'truck');
    $data['menu'][] = array('Barang Gudang', 'belanja/baranggudang', 'food');
    $data['menu'][] = array('Produk', 'penjualan/produk', 'bag');

    $this->load->view('header', $data);
    $this->load->view('menu', $data);
    if (!is_null($viewer)) $this->load->view($viewer, $data);
    $this->load->view('footer', $data);
  }

}
