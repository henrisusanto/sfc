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

}