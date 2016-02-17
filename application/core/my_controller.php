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

    $data['menu'][] = array('SIRKULASI', '#', 'refresh', array(
      array('KEUANGAN', 'sirkulasi/keuangan'),
      array('GUDANG', 'sirkulasi/barang'),
      array('AYAM MENTAH', 'sirkulasi/ayam'),
      array('PRODUK', 'sirkulasi/produk'),
    ));

    $data['menu'][] = array('TRANSAKSI', '#', 'shopping-cart', array(
      array('BELANJA GUDANG', 'pengeluaran/belanja'),
      array('BELANJA AYAM', 'pengeluaran/belanjaayam'),
      array('PENGELUARAN', 'pengeluaran/gudang'),
      array('PEMASUKAN', 'pemasukan/gudang'),
      array('PINJAMAN', 'sirkulasi/pinjaman'),
    ));

    $data['menu'][] = array('PRODUKSI', '#', 'cogs', array(
      array('PEMOTONGAN AYAM', 'produksi/pemotongan'),
      array('PRODUKSI GUDANG', 'produksi/gudang'),
    ));

    $data['menu'][] = array('OUTLET', '#', 'home-2', array(
      array('BAWAAN OUTLET', 'produksi/bawaan'),
      array('TRANSAKSI ANTAR OUTLET', 'produksi/transaksiinternal'),
      array('SETORAN OUTLET', 'pemasukan/setoran'),
      array('PENGELUARAN OUTLET', 'pengeluaran/outlet'),
      array('PEMASUKAN OUTLET', 'pemasukan/outlet'),
      array('STOK OUTLET', 'entitas/produkoutlet'),
    ));

    $data['menu'][] = array('MASTER &AMP; STOK', '#', 'key', array(
      array('BARANG GUDANG', 'entitas/baranggudang'),
      array('AYAM MENTAH', 'entitas/ayam'),
      array('PRODUK GUDANG', 'entitas/produk'),
      array('KOMPOSISI', 'entitas/komposisi'),
      array('DAFTAR OUTLET', 'entitas/outlet'),
      array('DATA KARYAWAN', 'entitas/karyawan'),
      array('DATA DISTRIBUTOR', 'entitas/distributor'),
      array('DATA DEBITUR', 'entitas/debitur'),
    ));

    $this->load->view('header', $data);
    $this->load->view('menu', $data);
    if (!is_null($viewer)) {
      if ($viewer != 'form') $this->load->view($viewer, $data);
      else {
        $this->load->view('formheader');
        $this->load->view($viewer, $data);
        if (isset($data['expandables']))
          for ($i=0; $i< count ($data['expandables']); $i++) 
            $this->load->view('subform', array(
              'label' => $data['expandables'][$i]['label'],
              'subfields' => $data['expandables'][$i]['fields'],
            ));
        $this->load->view('formfooter');
      }
    }
    
    $this->load->view('footer', $data);
  }

  function crud ($model, $tpl, $id) {
    $data = array('entity' => $model);
    $post = $this->input->post();
    $this->load->model($model);

    if ($tpl == 'table') {
      $data['thead'] = $this->$model->getTHead();
      $data['tbody'] = $this->$model->find();
    } else if ($tpl == 'form') {
      $data['fields'] = $this->$model->getInputFields();
      $data['expandables'] = $this->$model->getExpandables();
    }
    $data['tablePage'] = $this->$model->getTablePage($id);

    if ($tpl == 'delete') {
      $this->$model->delete($id);
      redirect($data['tablePage']);
    }

    if ($tpl == 'form' && $post) {
      $entity = array();
      foreach ($data['fields'] as $input) {
        $field = $input[0];
        $entity[$field] = $post[$field];
      }
      if (!empty($data['expandables'])) $entity = $post;
      if (!is_null($id)) $entity['id'] = $id;
      $this->$model->save($entity);

      redirect($data['tablePage']);
    }

    if (!is_null($id)) {
      $data['form'] = $this->$model->findOne($id);
    }

    $this->loadview($tpl, $data);
  }

}
