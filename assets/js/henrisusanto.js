function sfcSubmitForm () {
  // if ($('.form-belanja-detail').length > 0) {
    // $('form:first').find('input,select').each(function () {
      // var name = $(this).attr('name'),
      // value = $(this).val()
      // $('.form-belanja-detail').append('<input type="hidden" name="'+name+'" value="'+value+'">')
    // })
  // }
  // if ($('.form-bawaan-detail').length > 0) {
    // $('form:first').find('input,select').each(function () {
      // var name = $(this).attr('name'),
      // value = $(this).val()
      // $('.form-bawaan-detail').append('<input type="hidden" name="'+name+'" value="'+value+'">')
    // })
  // }
  $('form').submit()
}

$(function () {
  $('#mws-jui-dialog').dialog({
    modal:true
  }).dialog('close')
  $('.hapus').unbind('click').bind('click', function () {
    $('#mws-jui-dialog').dialog('open')
  })
  $('.tidak').unbind('click').bind('click', function () {
    $('#mws-jui-dialog').dialog('close')
  })

  $('select').select2()

  if ($('.form-belanja-detail').length > 0) {
    $('.form-belanja-detail select').select2('destroy')
    var clone = $('.form-belanja-detail > div').clone()
    $('.form-belanja-detail select').select2()
    $('.tambah-barang').unbind('click').bind('click', function () {
      clone.prependTo('.form-belanja-detail')
      clone = clone.clone()
      $('.form-belanja-detail select').select2()
    })
  }

  if ($('.form-bawaan-detail').length > 0) {
    $('.form-bawaan-detail select').select2('destroy')
    var clone1 = $('.form-bawaan-detail > div').clone()
    $('.form-bawaan-detail select').select2()
    $('.tambah-barang').unbind('click').bind('click', function () {
      clone1.prependTo('.form-bawaan-detail')
      clone1 = clone1.clone()
      $('.form-bawaan-detail select').select2()
    })
  }

  if ($('.form-bawaan-produk-detail').length > 0) {
    $('.form-bawaan-produk-detail select').select2('destroy')
    var clone2 = $('.form-bawaan-produk-detail > div').clone()
    $('.form-bawaan-produk-detail select').select2()
    $('.tambah-produk').unbind('click').bind('click', function () {
      clone2.prependTo('.form-bawaan-produk-detail')
      clone2 = clone2.clone()
      $('.form-bawaan-produk-detail select').select2()
    })
  }
  // if (window.location.href.indexOf('sirkulasi') > -1) {
    // $('.table-panel a').remove()
  // }
})