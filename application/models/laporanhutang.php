<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class laporanhutang extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'debitur';
    $this->thead = array(
      array('nama','DEBITUR'),
      array('saldo','HUTANG'),
    );
    $this->filters = array();
    $this->buildTFoot();
  }

  function find ($where = array()) {
    $this->db
      ->select('*')
      ->select("CONCAT('Rp ', FORMAT(saldo, 2)) AS saldo", false);
    return parent::find($where);
  }

  function getTFoot ($tbody) {
    $total = 0;
    foreach ($tbody as $tb) $total += preg_replace("/([^0-9\\.])/i", "", $tb->saldo);
    $this->tfoot[1] = 'TOTAL HUTANG';
    $this->tfoot[2] = $this->toRp ($total);
    return parent::getTFoot($tbody);
  }

}
