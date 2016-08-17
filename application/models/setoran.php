<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class setoran extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'setoran';
    $this->submodel = array('pesananbayar', 'setoranbarangoutlet', 
    'setoranpengeluaran', 'setoranpenjualan', 'setoransisabarang', 'setoransisaproduk');
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('outlet','OUTLET'),
      array('nominal','SETORAN'),
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('outlet', 'OUTLET'),
      2 => array('karyawan', 'PENANGGUNG JAWAB'),
    );
    $this->required = array('outlet');
    $this->buildRelation($this->inputFields[1][2], 'outlet');
    $this->buildRelation($this->inputFields[2][2], 'karyawan');

    $this->expandables = array();
    $this->expandables[0] = array(
      'label' => 'LAPORAN PENJUALAN',
      'required' => array('produk', 'qty'),
      'fields' => array (
        0 => array('setoranpenjualan[produk][]', 'PRODUK'),
        1 => array('setoranpenjualan[qty][]', 'JUMLAH'),
      )
    );
    $this->buildRelation($this->expandables[0]['fields'][0][2], 'produk');

    $this->expandables[1] = array(
      'label' => 'LAPORAN PENGELUARAN',
      'required' => array('item', 'nominal'),
      'strings' => array('item'),
      'fields' => array (
        0 => array('setoranpengeluaran[item][]', 'ITEM'),
        1 => array('setoranpengeluaran[nominal][]', 'NOMINAL'),
      )
    );

    $this->expandables[2] = array(
      'label' => 'LAPORAN SISA PRODUK',
      'required' => array('produk', 'qty'),
      'fields' => array (
        0 => array('setoransisaproduk[produk][]', 'PRODUK'),
        1 => array('setoransisaproduk[qty][]', 'QTY'),
      )
    );
    $this->buildRelation($this->expandables[2]['fields'][0][2], 'produk');

    $this->expandables[3] = array(
      'label' => 'LAPORAN SISA BAHAN YANG DIKEMBALIKAN KE GUDANG',
      'required' => array('barang', 'qty'),
      'fields' => array (
        0 => array('setoransisabarang[barang][]', 'BAHAN'),
        1 => array('setoransisabarang[qty][]', 'QTY'),
      )
    );
    $this->buildRelation($this->expandables[3]['fields'][0][2], 'baranggudang');

    /*
    $this->expandables[4] = array(
      'label' => 'LAPORAN SISA BAHAN DI OUTLET',
      'required' => array('barang', 'qty'),
      'fields' => array (
        0 => array('setoranbarangoutlet[barang][]', 'BAHAN'),
        1 => array('setoranbarangoutlet[qty][]', 'QTY'),
      )
    );
    $this->buildRelation($this->expandables[4]['fields'][0][2], 'baranggudang');
    */

    $this->expandables[4] = array(
      'label' => 'LAPORAN PEMBAYARAN PESANAN',
      'required' => array('pesanan', 'nominal'),
      'fields' => array (
        0 => array('pesananbayar[pesanan][]', 'PESANAN ATAS NAMA'),
        1 => array('pesananbayar[nominal][]', 'JUMLAH'),
      )
    );
    $this->expandables[4]['fields'][0][2][0] = '';
    foreach ($this->db->get_where('pesanan', array('lunas' => 0))->result() as $pesanan)
      $this->expandables[4]['fields'][0][2][$pesanan->id] = $pesanan->customer;
  }

  function validate ($data) {
      
    if (isset ($data['setoransisabarang']) && !empty ($data['setoransisabarang']['qty'][0])) {
      $stockBahanOutlet = array();
      foreach ($this->db->get_where('barangoutlet', array('outlet' => $data['outlet']))->result() as $barang)
        $stockBahanOutlet [$barang->barang] = $barang->stock;
      foreach ($data['setoransisabarang']['barang'] as $index => $barang)
        if (!isset ($stockBahanOutlet[$barang]) || $stockBahanOutlet[$barang] < $data['setoransisabarang']['qty'][$index])
          return array('SISA BAHAN YANG DIKEMBALIKAN KE GUDANG TIDAK TERDAPAT DI OUTLET YANG BERSANGKUTAN', 'error');      
    }

    return parent::validate($data); 
  }

  function prepare (&$data) {
    $prices = array();
    $data['pemasukan'] = 0;
    $data['pengeluaran'] = 0;
    $data['produkDihasilkan'] = array();
    $data['bahanTerpakai'] = array();

    foreach ($this->db->get('produk')->result() as $product) $prices[$product->id] = $product->harga;
    foreach ($data['setoranpenjualan']['produk'] as $index => $produk) {
      if (0 == $produk) continue;
      $data['pemasukan'] += $prices[$produk] * $data['setoranpenjualan']['qty'][$index];
      $data['setoranpenjualan']['currentprice'][$index] = $prices[$produk];
    }
    foreach ($data['setoranpengeluaran']['nominal'] as $index => $nominal) $data['pengeluaran'] += $nominal;
    foreach ($data['pesananbayar']['nominal'] as $pesanan) $data['pemasukan'] += $pesanan;

    $data['record'] = array(
      'outlet' => $data['outlet'],
      'waktu' => $data['waktu'],
      'nominal' => $data['pemasukan'] - $data['pengeluaran']
    );
    if (isset ($data['id'])) $data['record']['id'] = $data['id'];
  }

  function submodel ($data) {
    $CI =& get_instance();
    $CI->load->model($this->submodel);

    $excepted = array();
    foreach ($data['setoranpenjualan']['produk'] as $index => $produk) {
      if ($produk == 0) continue;
      $record = array(
        'setoran' => $data['id'],
        'produk' => $produk,
        'qty' => $data['setoranpenjualan']['qty'][$index],
        'currentprice' => $data['setoranpenjualan']['currentprice'][$index]
      );
      if (!empty ($data['setoranpenjualan']['id'][$index])) {
        $record['id'] = $data['setoranpenjualan']['id'][$index];
        $excepted[] = $this->setoranpenjualan->update($record, $data['waktu'], $data['reason'], $data['outlet']);
      } else
        $excepted[] = $this->setoranpenjualan->save($record, $data['waktu'], $data['reason'], $data['outlet']); 
    }
    if (!empty ($excepted))
      foreach ($this->setoranpenjualan->find(array('setoran' => $data['id']), array('id' => $excepted)) as $delete)
        $this->setoranpenjualan->delete($delete, $data['waktu'], $data['reason'], $data['outlet']);

    $excepted = array();
    foreach ($data['setoranpengeluaran']['item'] as $index => $item) {
      if ($data['setoranpengeluaran']['nominal'][$index] < 1) continue;
      $record = array(
        'setoran' => $data['id'],
        'item' => $item,
        'nominal' => $data['setoranpengeluaran']['nominal'][$index]
      );
      if (!empty ($data['setoranpengeluaran']['id'][$index])) {
        $record['id'] = $data['setoranpengeluaran']['id'][$index];
        $excepted[] = $this->setoranpengeluaran->update($record, $data['waktu'], $data['reason'], $data['outlet']);
      } else
        $excepted[] = $this->setoranpengeluaran->save($record, $data['waktu'], $data['reason'], $data['outlet']); 
    }
    if (!empty ($excepted))
      foreach ($this->setoranpengeluaran->find(array('setoran' => $data['id']), array('id' => $excepted)) as $delete)
        $this->setoranpenjualan->delete($delete, $data['waktu'], $data['reason'], $data['outlet']);

    $excepted = array();    
    foreach ($data['setoransisaproduk']['produk'] as $index => $produk) {
      if ($produk == 0) continue;
      $record = array(
        'setoran' => $data['id'],
        'produk' => $produk,
        'qty' => $data['setoransisaproduk']['qty'][$index]
      );
      if (!empty ($data['setoransisaproduk']['id'][$index])) {
        $record['id'] = $data['setoransisaproduk']['id'][$index];
        $excepted[] = $this->setoransisaproduk->update($record, $data['waktu'], $data['reason'], $data['outlet']);
      } else
        $excepted[] = $this->setoransisaproduk->save($record, $data['waktu'], $data['reason'], $data['outlet']); 
    }
    if (!empty ($excepted))
      foreach ($this->setoransisaproduk->find(array('setoran' => $data['id']), array('id' => $excepted)) as $delete)
        $this->setoransisaproduk->delete($delete, $data['waktu'], $data['reason'], $data['outlet']);

    $excepted = array();    
    foreach ($data['setoransisabarang']['barang'] as $index => $barang) {
      if ($barang == 0) continue;
      $record = array(
        'setoran' => $data['id'],
        'barang' => $barang,
        'qty' => $data['setoransisabarang']['qty'][$index]
      );
      if (!empty ($data['setoransisabarang']['id'][$index])) {
        $record['id'] = $data['setoransisabarang']['id'][$index];
        $excepted[] = $this->setoransisabarang->update($record, $data['waktu'], $data['reason'], $data['outlet']);
      } else
        $excepted[] = $this->setoransisabarang->save($record, $data['waktu'], $data['reason'], $data['outlet']); 
    }
    if (!empty ($excepted))
      foreach ($this->setoransisabarang->find(array('setoran' => $data['id']), array('id' => $excepted)) as $delete)
        $this->setoransisabarang->delete($delete, $data['waktu'], $data['reason'], $data['outlet']);

    /*
    $excepted = array();
    foreach ($data['setoranbarangoutlet']['barang'] as $index => $barang) {
      if ($barang == 0) continue;
      $record = array(
        'setoran' => $data['id'],
        'barang' => $barang,
        'qty' => $data['setoranbarangoutlet']['qty'][$index]
      );
      if (!empty ($data['setoranbarangoutlet']['id'][$index])) {
        $record['id'] = $data['setoranbarangoutlet']['id'][$index];
        $excepted[] = $this->setoranbarangoutlet->update($record, $data['waktu'], $data['reason'], $data['outlet']);
      } else
        $excepted[] = $this->setoranbarangoutlet->save($record, $data['waktu'], $data['reason'], $data['outlet']); 
    }
    if (!empty ($excepted))
      foreach ($this->setoranbarangoutlet->find(array('setoran' => $data['id']), array('id' => $excepted)) as $delete)
        $this->setoranbarangoutlet->delete($delete, $data['waktu'], $data['reason'], $data['outlet']);
    */

    $excepted = array();
    foreach ($data['pesananbayar']['pesanan'] as $index => $pesanan) {
      if (empty ($pesanan)) continue;
      $record = array(
        'setoran' => $data['id'],
        'pesanan' => $pesanan,
        'nominal' => $data['pesananbayar']['nominal'][$index],
      );
      if (!empty ($data['pesananbayar']['id'][$index])) {
        $record['id'] = $data['pesananbayar']['id'][$index];
        $excepted[] = $this->pesananbayar->update($record, $data['waktu'], $data['reason'], $data['outlet']);
      } else
        $excepted[] = $this->pesananbayar->save($record, $data['waktu'], $data['reason'], $data['outlet']); 
    }
    if (!empty ($excepted))
      foreach ($this->pesananbayar->find(array('setoran' => $data['id']), array('id' => $excepted)) as $delete)
        $this->pesananbayar->delete($delete, $data['waktu'], $data['reason'], $data['outlet']);
  }

  function update ($data) {
    $data['reason'] = 'EDIT SETORAN';
    $this->prepare($data);
    $data['previous'] = $this->findOne($data['id']);
    $data['id'] = parent::save($data['record']);

    $data['previous']['pemasukan'] = 0;
    $data['previous']['pengeluaran'] = 0;
    $CI =& get_instance();
    $keluarmasuk = array('setoranpenjualan', 'pesananbayar', 'setoranpengeluaran');
    $CI->load->model($keluarmasuk);
    foreach ($keluarmasuk as $kmmodel) {
      foreach ($this->$kmmodel->find(array ('setoran' => $data['id'])) as $kmrecord) {
        switch ($kmmodel) {
          case 'setoranpenjualan':
            $data['previous']['pemasukan'] += $kmrecord->currentprice * $kmrecord->qty;break;
          case 'pesananbayar': 
            $data['previous']['pemasukan'] += $kmrecord->nominal;break;
          case 'setoranpengeluaran': 
            $data['previous']['pengeluaran'] += $kmrecord->nominal;break;
        }
      }
    }

    if ($data['pemasukan'] > $data['previous']['pemasukan'])
      $this->sirkulasiKeuanganOutlet ('MASUK', 'EDIT PEMASUKAN', $data['pemasukan'] - $data['previous']['pemasukan'], $data['id'], $data['waktu'], $data['outlet']);
    if ($data['previous']['pemasukan'] > $data['pemasukan'])
      $this->sirkulasiKeuanganOutlet ('KELUAR', 'EDIT PEMASUKAN', $data['previous']['pemasukan'] - $data['pemasukan'], $data['id'], $data['waktu'], $data['outlet']);

    if ($data['pengeluaran'] > $data['previous']['pengeluaran'])
      $this->sirkulasiKeuanganOutlet ('KELUAR', 'EDIT PENGELUARAN', $data['pengeluaran'] - $data['previous']['pengeluaran'], $data['id'], $data['waktu'], $data['outlet']);
    if ($data['previous']['pengeluaran'] > $data['pengeluaran'])
      $this->sirkulasiKeuanganOutlet ('MASUK', 'EDIT PENGELUARAN', $data['previous']['pengeluaran'] - $data['pengeluaran'], $data['id'], $data['waktu'], $data['outlet']);

    if ($data['record']['nominal'] > $data['previous']['nominal']) {
      $this->sirkulasiKeuanganOutlet ('KELUAR', $data['reason'], $data['record']['nominal'] - $data['previous']['nominal'], $data['id'], $data['waktu'], $data['outlet']);
      $this->sirkulasiKeuangan ('MASUK', $data['reason'], $data['record']['nominal'] - $data['previous']['nominal'], $data['id'], $data['waktu']);
    }
    if ($data['previous']['nominal'] > $data['record']['nominal']) {
      $this->sirkulasiKeuanganOutlet ('MASUK', $data['reason'], $data['previous']['nominal'] - $data['record']['nominal'], $data['id'], $data['waktu'], $data['outlet']);
      $this->sirkulasiKeuangan ('KELUAR', $data['reason'], $data['previous']['nominal'] - $data['record']['nominal'], $data['id'], $data['waktu']);
    }

    $this->submodel($data);
    return $data['id'];
  }

  function save ($data) {
    $data['reason'] = 'SETORAN';
    $this->prepare($data);
    $data['id'] = parent::save($data['record']);
    
    $this->sirkulasiKeuanganOutlet ('MASUK', 'PEMASUKAN', $data['pemasukan'], $data['id'], $data['waktu'], $data['outlet']);
    $this->sirkulasiKeuanganOutlet ('KELUAR', 'PENGELUARAN', $data['pengeluaran'], $data['id'], $data['waktu'], $data['outlet']);
    $this->sirkulasiKeuanganOutlet ('KELUAR', $data['reason'], $data['record']['nominal'], $data['id'], $data['waktu'], $data['outlet']);
    $this->sirkulasiKeuangan ('MASUK', $data['reason'], $data['record']['nominal'], $data['id'], $data['waktu']);

    $this->submodel($data);
    return $data['id'];
  }

  function delete ($id) {
    $reason = 'SETORAN BATAL';
    $data = $this->findOne ($id);
    $waktu = date('Y-m-d H:i:s', time());
    $CI =& get_instance();
    $CI->load->model($this->submodel);

    $data['pemasukan'] = 0;
    $data['pengeluaran'] = 0;
    // LOOPING SUBMODEL
    foreach ($this->submodel as $submodel) {
      foreach ($this->$submodel->find(array ('setoran' => $id)) as $delete) {
        if ($submodel == 'setoranpenjualan')
          $data['pemasukan'] += $delete->currentprice * $delete->qty;
        if ($submodel == 'pesananbayar') $data['pemasukan'] += $delete->nominal;
        if ($submodel == 'setoranpengeluaran') $data['pengeluaran'] += $delete->nominal;
        $this->$submodel->delete($delete, $waktu, $reason, $data['outlet']);
      }
    }

    $this->sirkulasiKeuanganOutlet ('KELUAR', 'PEMASUKAN BATAL', $data['pemasukan'], $data['id'], $waktu, $data['outlet']);
    $this->sirkulasiKeuanganOutlet ('MASUK', 'PENGELUARAN BATAL', $data['pengeluaran'], $data['id'], $waktu, $data['outlet']);
    $this->sirkulasiKeuanganOutlet ('MASUK', $reason, $data['nominal'], $data['id'], $waktu, $data['outlet']);
    $this->sirkulasiKeuangan ('KELUAR', $reason, $data['nominal'], $data['id'], $waktu);

    // DELETE PRODUKSI
    $CI->load->model('prosesproduksi');
    $produksi = $this->prosesproduksi->find(array('setoran' => $id));
    if (count ($produksi) > 0) {
      $produksi = (array) reset($produksi);
      $subproduksi = array('produksibarang', 'produksiayam', 'produksiproduk');
      $CI->load->model($subproduksi);
      foreach ($subproduksi as $sp) {
        foreach ($this->$sp->find(array('produksi' => $produksi['id'])) as $deletesp) {
          $this->$deletesp->delete($deletesp->id, $reason, $waktu, $data['outlet']);
        }
      }
      $this->produksi->delete($produksi['id']);      
    }

    return parent::delete($id);  
  }

  function find ($where = array()) {
    $this->db
      ->select('setoran.*, outlet.nama as outlet')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select("CONCAT('Rp ', FORMAT(nominal, 2)) AS nominal", false)
      ->join('outlet', 'outlet.id = setoran.outlet', 'LEFT');
    return parent::find($where);
  }

}
