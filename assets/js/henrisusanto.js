function sfcSubmitForm () {
  if ($('.form-belanja-detail').length > 0) {
    $('form:first').find('input,select').each(function () {
      var name = $(this).attr('name'),
      value = $(this).val()
      $('.form-belanja-detail').append('<input type="hidden" name="'+name+'" value="'+value+'">')
    })
  }
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

  // if (window.location.href.indexOf('cashflow') > -1) {
    // $('.table-panel a').remove()
  // }
})