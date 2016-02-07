<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class penjualan extends my_controller {

	public function produk ($tpl='table', $id=null) {
	  $data = array('entity' => 'produk');
    $post = $this->input->post();
    $this->load->model('produk');

    $data['thead'] = $this->produk->getTHead();
    $data['fields'] = $this->produk->getInputFields();
    $data['tbody'] = $this->produk->find();
    $data['tablePage'] = $this->produk->getTablePage($id);

    if ($tpl == 'delete') {
      $this->produk->delete($id);
      redirect($data['tablePage']);
    }

    if ($tpl == 'form' && $post) {
      $produk = array();
      foreach ($data['fields'] as $input) {
        $field = $input[0];
        $produk[$field] = $post[$field];
      }
      if (!is_null($id)) $produk['id'] = $id;
      $this->produk->save($produk);

      redirect($data['tablePage']);
    }

    if (!is_null($id)) {
      $data['form'] = $this->produk->findOne($id);
    }

		$this->loadview($tpl, $data);
	}

  public function outlet ($tpl='table', $id=null) {
    $data = array('entity' => 'outlet');
    $post = $this->input->post();
    $this->load->model('outlet');

    $data['thead'] = $this->outlet->getTHead();
    $data['fields'] = $this->outlet->getInputFields();
    $data['tbody'] = $this->outlet->find();
    $data['tablePage'] = $this->outlet->getTablePage($id);

    if ($tpl == 'delete') {
      $this->outlet->delete($id);
      redirect($data['tablePage']);
    }

    if ($tpl == 'form' && $post) {
      $outlet = array();
      foreach ($data['fields'] as $input) {
        $field = $input[0];
        $outlet[$field] = $post[$field];
      }
      if (!is_null($id)) $outlet['id'] = $id;
      $this->outlet->save($outlet);

      redirect($data['tablePage']);
    }

    if (!is_null($id)) {
      $data['form'] = $this->outlet->findOne($id);
    }

    $this->loadview($tpl, $data);
  }

}