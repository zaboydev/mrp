<script>
var DELAY = 700,
    clicks = 0,
    timer = null,
    rowSelected = [],
    idsSelected = [],
    dataTableUrl = "<?=$dataTableUrl;?>",
    singleClickUrl = "<?=$singleClickUrl;?>",
    doubleClickUrl = "<?=$doubleClickUrl;?>",
    multiSelectUrl = <?=(isset($multiSelectUrl)) ? json_encode($multiSelectUrl, JSON_UNESCAPED_SLASHES) : json_encode(null);?>,
    singleSelectUrl = <?=(isset($singleSelectUrl)) ? json_encode($singleSelectUrl, JSON_UNESCAPED_SLASHES) : json_encode(null);?>,
    leftColumnsFixed = $('#table-data').attr('data-leftColumnsFixed'),
    summary = $('#table-data').data('summary');
function postUrl(path, params, method) {
  method = method || "post";

  var form = document.createElement("form");
  form.setAttribute("method", method);
  form.setAttribute("action", path);

  for (var key in params) {
    if (params.hasOwnProperty(key)) {
      var hiddenField = document.createElement("input");
      hiddenField.setAttribute("type", "hidden");
      hiddenField.setAttribute("name", key);
      hiddenField.setAttribute("value", params[key]);

      form.appendChild(hiddenField);
    }
  }

  document.body.appendChild(form);
  form.submit();
}

function submit_post_via_hidden_form(url, params) {
  var f = $("<form target='_blank' method='POST' style='display:none;'></form>").attr('action', url).appendTo(document.body);

  $.each( params, function( key, value ) {
    var hidden = $('<input type="hidden" />').attr({
      name: key,
      value: JSON.stringify(value)
    });

    hidden.appendTo(f);
  });

  f.submit();
  f.remove();
}

function reset(form) {
  document.getElementById(form).reset();
}

function numberFormat(nStr)
{
  nStr += '';
  x = nStr.split('.');
  x1 = x[0];
  x2 = x.length > 1 ? '.' + x[1] : '';
  var rgx = /(\d+)(\d{3})/;
  while (rgx.test(x1)) {
      x1 = x1.replace(rgx, '$1' + ',' + '$2');
  }
  return x1 + x2;
}

$(document).on('keydown', function(event) {
  if ((event.metaKey || event.ctrlKey) && (
    String.fromCharCode(event.which).toLowerCase() === '0' ||
    String.fromCharCode(event.which).toLowerCase() === 'a' ||
    String.fromCharCode(event.which).toLowerCase() === 'd' ||
    String.fromCharCode(event.which).toLowerCase() === 'e' ||
    String.fromCharCode(event.which).toLowerCase() === 'i' ||
    String.fromCharCode(event.which).toLowerCase() === 'o' ||
    String.fromCharCode(event.which).toLowerCase() === 's' ||
    String.fromCharCode(event.which).toLowerCase() === 'x')
  ) {
    event.preventDefault();
  }
});

$(function(){
  toastr.options.closeButton = true;

  $('.card-linked').on('click', function(){
    var url = $(this).data('url');
    window.document.location = url;
  });

  $('.btn-home').on('click', function(){
    var url = '<?=site_url();?>';
    window.document.location = url;
  });

  $('.btn-back').on('click', function(){
    history.back();
  });

  $('[data-validation-rule="unique"]').on('change', function(){
    var url = $( this ).data('validation-url');
    var value = $( this ).val();
    var exception = $( this ).data('validation-exception');
    var wrapper = $( this ).parent( 'div.form-group' );
    var feedback = $( this ).parent().find( 'i.form-control-feedback' );
    var submitButton = $( this ).closest('form').find( 'button[type="submit"]' );

    $( submitButton ).attr('disabled', true);

    $.post( url, { value: value, exception: exception }, function(data) {
      if(data == 'true') {
        $(feedback).remove();
        $(wrapper).removeClass('has-error has-feedback').addClass('has-success');
        $( submitButton ).attr('disabled', false);
      } else {
        $(feedback).remove();
        $(wrapper).removeClass('has-success has-feedback').addClass('has-error');

        toastr.options.positionClass = 'toast-top-right';
        toastr.error(value +' already exists!', '');
      }
    });
  });

  $('table')
    .addClass('stripe row-border cell-border order-column nowrap')
    .attr('width', '100%');

  $('table thead tr:first-child th:first-child').attr('width', 1).text('No.');
  $('table td:first-child').attr('align', 'right');

  var table = $('#table-data').DataTable({
    order: [],
    searchDelay: 350,
    dom: "<'row'<'col-sm-12'tr>>"
      +
      "<'datatable-footer force-padding no-y-padding'<'row'<'col-sm-4'i<'clearfix'>l><'col-sm-8'p>>>",
    scrollY: 410,
    scrollX: true,
    scrollCollapse: true,
    fixedColumns: {
      leftColumns: leftColumnsFixed
    },
    lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
    pageLength: 10,
    pagingType: 'full',
    language: {
      info: "Total _TOTAL_ entries"
    },
    processing: true,
    serverSide: true,
    ajax: {
      url: dataTableUrl,
      type: "POST",
      error: function(){
        toastr.clear();
        toastr.error('Loading data failed! Please reload this page.', '');
      }
    },

    rowCallback: function( row, data ) {
      if ( $.inArray(data.DT_RowId, rowSelected) !== -1 ) {
        $(row).addClass('selected');
      }
    },
    columnDefs: [
      {
        searchable: false,
        orderable: false,
        targets: [ 0 ]
      }
    ]
  });

  new $.fn.dataTable.Buttons( table, {
    dom: {
      collection: {
        tag: 'div',
        className: 'dropdown-menu list-group'
      },
      button: {
        className: 'btn btn-flat ink-reaction'
      }
    },
    buttons: [
      {
        extend: 'collection',
        className: 'btn-top',
        background: false,
        fade: false,
        text: '<i class="md md-more-horiz"></i>',
        titleAttr: 'Data options',
        autoClose: true,
        buttons: [
          {
            text: '<strong>Select All</strong> (ctrl + a)',
            name: 'selectAll',
            key: {
              ctrlKey: true,
              key: 'a'
            },
            action: function( e, dt, node, config ){
              dt.rows().eq(0).each( function ( i ) {
                var row = dt.row( i );
                var data = row.data();
                var id = data.DT_RowId;
                var index = $.inArray(id, rowSelected);

                if ( index === -1 ) {
                  rowSelected.push( id );
                }
              } ).draw(false);

              if (rowSelected.length > 0){
                dt.buttons( 'selectNone:name' ).enable();
                dt.buttons( '.multi_select_button' ).enable();
              }

              if (rowSelected.length == dt.page.info().recordsDisplay){
                this.disable();
              }
            }
          },
          {
            text: '<strong>Select None</strong> (ctrl + 0)',
            name: 'selectNone',
            key: {
              ctrlKey: true,
              key: '0'
            },
            enabled: false,
            action: function( e, dt, node, config ){
              dt.rows().eq(0).each( function ( i ) {
                var row = dt.row( i );
                var data = row.data();
                var id = data.DT_RowId;
                var index = $.inArray(id, rowSelected);

                if ( index !== -1 ) {
                  rowSelected.splice( index, 1 );
                }
              } ).draw(false);

              if (rowSelected.length === 0){
                this.disable();
                dt.buttons( '.multi_select_button' ).disable();
              }

              dt.buttons( 'selectAll:name' ).enable();
            }
          }
        ]
      },
      {
        extend: 'print',
        className: 'btn-top',
        text: '<i class="fa fa-print"></i>',
        titleAttr: 'Quick print',
        autoPrint: false,
        footer: true,
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'csv',
        name: 'csv',
        text: '<i class="fa fa-file-text-o"></i>',
        footer: true,
        key: {
          ctrlKey: true,
          key: 's'
        },
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'excel',
        name: 'excel',
        text: '<i class="fa fa-file-excel-o"></i>',
        footer: true,
        key: {
          ctrlKey: true,
          key: 'x'
        },
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        name: 'pdf',
        className: 'buttons-pdf',
        text: '<i class="fa fa-file-pdf-o"></i>',
        key: {
          ctrlKey: true,
          key: 'd'
        },
        action: function ( e, dt, node, config ) {
          var pdfUrl = '<?=site_url('pdf');?>',
            pdfTitle = '<?=$page_title;?>',
            pdfData = table.buttons.exportData( {
              columns: ':visible'
            } );

          submit_post_via_hidden_form(
            pdfUrl, { table: pdfData, title: pdfTitle }
          );
        }
      }
    ]
  });

  table.buttons().container()
    .appendTo( $('#datatable-buttons') );

  $.each( singleSelectUrl, function(key, value) {
    table.button().add(key, {
      text: value.label,
      className: 'single_select_button',
      enabled: false,
      action: function(){
        window.location = value.url;
      }
    } );
  });

  $.each( multiSelectUrl, function(key, value) {
    table.button().add(key, {
      text: value.label,
      className: 'multi_select_button',
      enabled: false,
      action: function(){
        var rows = JSON.stringify(rowSelected);
        postUrl(value.url, { rows:rows });
      }
    } );
  });

  if (summary){
    table.on( 'xhr', function () {
      var json = table.ajax.json();

      $.each (summary, function(key, value){
        $(table.column( value ).footer()).html(
          json.total[value]
        );
      });
    });
  }

  $('#table-data tbody').on( 'click', 'td:not(:first-child)', function() {
    clicks++;

    var id = table.row(this).data().DT_RowData.pkey;

    if (clicks === 1) {
      timer = setTimeout(function() {
        clicks = 0;

        if (singleClickUrl)
          window.location = singleClickUrl + '/' + id;
      }, DELAY);
    } else {
      clearTimeout(timer);
      clicks = 0;

      if (doubleClickUrl)
        window.location = doubleClickUrl + '/' + id;
    }
  });

  $('#table-data tbody').on('click', 'td:first-child', function () {
    var id = table.row(this).id();
    var index = $.inArray(id, rowSelected);
    var row = table.row( this ).node();

    if ( index === -1 ) {
      rowSelected.push( id );
    } else {
      rowSelected.splice( index, 1 );
    }

    $( row ).toggleClass('selected');
    $( this ).closest('tr').toggleClass('selected');

    table.buttons( 'selectNone:name' ).disable( rowSelected.length === 0 );
    table.buttons( 'selectNone:name' ).enable( rowSelected.length > 0 );
    table.buttons( '.multi_select_button' ).enable( rowSelected.length > 0 );
    table.buttons( '.single_select_button' ).enable( rowSelected.length === 1 );
  } );

  $('.filter_numeric_text').on( 'keyup click', function () {
    var i = $(this).data('column');
    var v = $(this).val();
    table.columns(i).search(v).draw();
  });

  $('.filter_dropdown').on( 'change', function () {
    var i = $(this).data('column');
    var v = $(this).val();
    table.columns(i).search(v).draw();
  });

  $('.filter_boolean').on( 'change', function () {
    var checked = $(this).is(':checked');
    var i = $(this).data('column');

    if (checked){
      table.columns(i).search('true').draw();
    } else {
      table.columns(i).search('').draw();
    }
  });

  $('.datepicker').daterangepicker({
    locale: {
      format: 'YYYY-MM-DD',
      cancelLabel: 'Clear'
    },
    singleDatePicker: true,
    showDropdowns: true
  });

  $('.filter_daterange').daterangepicker({
    autoUpdateInput: false,
    locale: {
      cancelLabel: 'Clear'
    },
    ranges: {
      'Today': [moment(), moment()],
      'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Last 7 Days': [moment().subtract(6, 'days'), moment()],
      'Last 30 Days': [moment().subtract(29, 'days'), moment()],
      'This Month': [moment().startOf('month'), moment().endOf('month')],
      'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    }
  }).on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' ' + picker.endDate.format('YYYY-MM-DD'));
    var i = $(this).data('column');
    var v = $(this).val();
    table.columns(i).search(v).draw();
  }).on('cancel.daterangepicker', function(ev, picker) {
    $(this).val('');
    var i = $(this).data('column');
    table.columns(i).search('').draw();
  });

  $('a.column-toggle').on( 'click', function (e) {
    e.preventDefault();
    var column = table.column( $(this).attr('data-column') );

    column.visible( ! column.visible() );

    var label = $(this).attr('data-label');
    var text  = (column.visible() === true ? '<div class="tile-text">'+label+'</div>' : '<div class="tile-text text-muted">'+label+'</div>');

    $( this ).html(text);
  } );

  $('.buttons-print, .buttons-csv, .buttons-excel, .buttons-pdf').removeClass().addClass('btn btn-icon-toggle btn-primary-dark ink-reaction').appendTo('#additional-buttons');
  $('#table-data_paginate').find('a').removeClass();
  $('#datatable-form').removeClass('hidden');
  $('#datatable-form input').on( 'keyup', function () {
    table.search( this.value ).draw();
  });

  $('.delete-confirm').on('click', function(){
    return confirm('Selected data will be deleted. Continue?');
  });

  $('#ajax-form-create').submit(function(e){
    e.preventDefault();

    var url = $(this).attr('action');
    var submitButton = $(this).find('button[type="submit"]');
    var resetButton = $(this).find('button[type="reset"]');

    submitButton.attr('disabled', true);

    $.post( url, $(this).serialize() )
      .done(function(data){
        if ( data.success == false ){
          toastr.options.timeOut = 10000;
          toastr.options.positionClass = 'toast-top-right';
          toastr.error('Please check again required form input and try again.', 'FAILED!');

          submitButton.attr('disabled', false);
        } else {
          toastr.options.positionClass = 'toast-top-right';
          toastr.success(data.message);

          submitButton.attr('disabled', false);
          resetButton.trigger('click');

          $('[data-dismiss="offcanvas"]').trigger('click');

          if (table){
            table.ajax.reload();
          }
        }
      });
  });
});
</script>
