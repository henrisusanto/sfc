function sfcSubmitForm () {
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

  if ($('.expandable-form').length > 0) {
    var clone = []
    $('.expandable-form').each (function (index) {
      var ef = $(this)
      $('select').select2('destroy')
      clone[index] = ef.children('div').clone()
      $('.tambah-item').eq(index).unbind('click').bind('click', function () {
        clone[index].prependTo(ef)
        clone[index] = clone[index].clone()
        ef.find('select').select2()
      })      
    })
    $('select').select2()
  }

  // if (window.location.href.indexOf('sirkulasi') > -1) {
    // $('.table-panel a').remove()
  // }
})