<?php defined('BASEPATH') OR exit('No direct script access allowed');?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?=$page_title;?> | BWD Material Resource Planning</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/assets/fonts/Lato/lato.css');?>">
  <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/plugins/bootstrap/css/bootstrap.min.css');?>">
  <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/plugins/font-awesome/css/font-awesome.min.css');?>">
  <link rel="stylesheet" type="text/css" href="<?=base_url('vendors/AdminLTE-2.3.6/dist/css/AdminLTE.min.css');?>">
  <link rel="stylesheet" type="text/css" href="<?=base_url('vendors/AdminLTE-2.3.6/dist/css/skins/skin-blue.min.css');?>">
  <link rel="stylesheet" type="text/css" href="<?=base_url('vendors/DataTables-1.10.12/datatables.min.css');?>">
  <link rel="stylesheet" href="<?=base_url('vendors/bootstrap-daterangepicker/daterangepicker.css');?>">
  <link rel="stylesheet" href="<?=base_url('vendors/zebra-dialog/public/css/flat/zebra_dialog.css');?>">
  <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/assets/css/layout.css');?>">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-collapse sidebar-mini">
<div class="wrapper">
  <?php $this->load->view('admin_lte/partial/header');?>
  <?php $this->load->view('admin_lte/partial/aside');?>

  <div class="content-wrapper">
    <section class="content-header visible-xs">
      <h1>
        <?=$page_header;?>
      </h1>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-sm-12">
          <div class="box box-danger">
            <div class="box-header with-border">
              <h3 class="box-title"><?=$page_title;?></h3>

              <div class="box-tools pull-right">
                <a href="<?=site_url();?>" class="btn btn-box-tool">
                  <i class="fa fa-times"></i>
                </a>
              </div>
            </div>

            <div class="box-body">
              <?php if ( $this->session->flashdata('alert') )
                _render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);?>

              <?php if ($page_content)
                $this->load->view($page_content);?>
            </div>

            <div class="box-footer text-center">
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <?php $this->load->view('admin_lte/partial/footer');?>
  <?php $this->load->view('admin_lte/partial/right-sidebar.php');?>
</div>

<script src="<?=base_url('themes/admin_lte/plugins/jQuery/jQuery-2.2.0.min.js');?>"></script>
<script src="<?=base_url('themes/admin_lte/plugins/bootstrap/js/bootstrap.min.js');?>"></script>
<script src="<?=base_url('vendors/DataTables-1.10.12/datatables.min.js');?>"></script>
<script src="<?=base_url('themes/admin_lte/plugins/slimScroll/jquery.slimscroll.min.js');?>"></script>
<script src="<?=base_url('themes/admin_lte/plugins/fastclick/fastclick.js');?>"></script>
<script src="<?=base_url('vendors/zebra-dialog/public/javascript/zebra_dialog.js');?>"></script>
<script src="<?=base_url('vendors/bootstrap-daterangepicker/moment.min.js');?>"></script>
<script src="<?=base_url('vendors/bootstrap-daterangepicker/daterangepicker.js');?>"></script>
<script src="<?=base_url('themes/admin_lte/assets/js/app.min.js');?>"></script>

<script>
var DELAY = 700,
    clicks = 0,
    timer = null,
    rowSelected = [],
    dataTableUrl = "<?=$dataTableUrl;?>",
    singleClickUrl = "<?=$singleClickUrl;?>",
    doubleClickUrl = "<?=$doubleClickUrl;?>",
    createUrl = "<?=$createUrl;?>",
    importUrl = "<?=$importUrl;?>",
    deleteUrl = "<?=$deleteUrl;?>",
    leftColumnsFixed = $('#table-data').attr('data-leftColumnsFixed');

function submit_post_via_hidden_form(url, params) {
  var f = $("<form target='_blank' method='POST' style='display:none;'></form>").attr('action', url).appendTo(document.body);

  $.each( params, function( key, value ) {
    var hidden = $('<input type="hidden" />').attr({
      name: key,
      value: JSON.stringify(value)
    });

    hidden.appendTo(f);
    console.log(hidden);
  });

  f.submit();
  f.remove();
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
  $('table')
    .addClass('stripe row-border cell-border order-column nowrap')
    .attr('width', '100%');

  $('table thead tr:first-child th:first-child').attr('width', 1).text('No.');
  $('table td:first-child').attr('align', 'right');

  var table = $('#table-data').DataTable({
    // set default settings
    order: [], //Initial no order.
    searchDelay: 350,
    dom: 'Bfrtip<"clearfix">l',

    // fixed left columns
    scrollY: 380,
    scrollX: true,
    scrollCollapse: true,
    fixedColumns: {
      leftColumns: leftColumnsFixed
    },
    lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
    pageLength: 10,
    pagingType: 'full',

    // change language info
    language: {
      info: "Total _TOTAL_ entries",
      lengthMenu: "Show _MENU_ entries"
    },

    // server side processing
    processing: true,
    serverSide: true,

    // Load data for the table's content from an Ajax source
    ajax: {
      url: dataTableUrl,
      type: "POST",
      error: function(){
        $.Zebra_Dialog('Loading data failed! <br />May you have been logged out by sistem. <br />Please reload this page.', {
          'type':     'warning',
          'title':    'Warning',
          'buttons':  [
            {caption: 'Reload', callback: function() { history.go(0); }},
            {caption: 'Cancel', callback: function() { close(); }}
          ]
        });
      }
    },

    rowCallback: function( row, data ) {
      if ( $.inArray(data.DT_RowId, rowSelected) !== -1 ) {
        $(row).addClass('selected');
      }
    },

    // Set column definition initialisation properties.
    columnDefs: [ {
        searchable: false,
        orderable: false,
        targets: [ 0 ]
      } ],

    buttons: [ {
        extend: 'colvis',
        name: 'colvis',
        text: '<i class="fa fa-eye hidden-lg"></i><span class="visible-lg">Columns</span>',
        titleAttr: 'Column Visibility',
        columns: ':not(:first-child)'
      }, {
        extend: 'print',
        name: 'print',
        text: '<i class="fa fa-print hidden-lg"></i><span class="visible-lg">Print</span>',
        titleAttr: 'Quick print',
        autoPrint: false,
        footer: true,
        exportOptions: {
          columns: ':visible'
        }
      }, {
        extend: 'collection',
        name: 'export',
        text: '<i class="fa fa-file-text-o hidden-lg"></i><span class="visible-lg">Export</span>',
        titleAttr: 'Export to file',
        autoClose: true,
        buttons: [ {
            extend: 'csv',
            name: 'csv',
            text: '<strong>CSV</strong> (ctrl + s)',
            footer: true,
            key: {
              ctrlKey: true,
              key: 's'
            },
            exportOptions: {
              columns: ':visible'
            }
          }, {
            extend: 'excel',
            name: 'excel',
            text: '<strong>Excel</strong> (ctrl + x)',
            footer: true,
            key: {
              ctrlKey: true,
              key: 'x'
            },
            exportOptions: {
              columns: ':visible'
            }
          }, {
            name: 'pdf',
            text: '<strong>PDF</strong> (ctrl + d)',
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
      }, {
        extend: 'collection',
        name: 'options',
        text: '<i class="fa fa-list-ul hidden-lg"></i><span class="visible-lg">Options</span>',
        titleAttr: 'Data options',
        autoClose: true,
        buttons: [
          {
            extend: 'selectAll',
            text: '<strong>Select All</strong> (ctrl + a)',
            name: 'selectAll',
            key: {
              ctrlKey: true,
              key: 'a'
            }
          },
          {
            extend: 'selectNone',
            text: '<strong>Select None</strong> (ctrl + 0)',
            name: 'selectNone',
            key: {
              ctrlKey: true,
              key: '0'
            }
          },
          {
            extend: 'copy',
            name: 'copy',
            enabled: false,
            text: '<strong>Copy</strong> (ctrl + c)',
            key: {
              ctrlKey: true,
              key: 'c'
            },
            exportOptions: {
              modifier: {
                selected: true
              }
            }
          },
          {
            name: 'delete',
            enabled: false,
            available: function( dt, config){
              return deleteUrl ? true : false;
            },
            text: '<strong>Delete Selected</strong>',
            titleAttr: 'Delete selected data',
            action: function(){
              if (confirm("Selected data will be deleted and lost forever!\n\nAre you sure want to continue?")){
                window.location = deleteUrl;
              }
            }
          }
        ]
      }, {
        name: 'addNew',
        text: '<i class="fa fa-plus hidden-lg"></i><span class="visible-lg">Create</span>',
        titleAttr: 'Create new data',
        key: {
          ctrlKey: true,
          key: 'b'
        },
        available: function( dt, config){
          return createUrl ? true : false;
        },
        action: function(){
          window.location = createUrl;
        }
      }, {
        name: 'importCsv',
        text: '<i class="fa fa-clipboard hidden-lg"></i><span class="visible-lg">Import</span>',
        titleAttr: 'Import from CSV',
        key: {
          ctrlKey: true,
          key: 'i'
        },
        available: function( dt, config){
          return importUrl ? true : false;
        },
        action: function(){
          window.location = importUrl;
        }
      } ]
  });

  // only perform this action on ajax
  table.on( 'select.dt deselect.dt', function () {
    var selectedRows = table.rows( { selected: true } ).count();

    table.button( 'copy:name' ).enable( selectedRows > 0 );
    table.button( 'delete:name' ).enable( selectedRows > 0 );
  } );

  // perform single or double click on row (except first column)
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
    console.log(table.row(this).id());
    var id = table.row(this).id();
    var index = $.inArray(id, rowSelected);

    if ( index === -1 ) {
      rowSelected.push( id );
    } else {
      rowSelected.splice( index, 1 );
    }

    if ( table.row( this, { selected: true } ).any() ) {
      table.row( this ).deselect();
    }
    else {
      table.row( this ).select();
    }
  } );

  $('.filter_numeric_text').on( 'keyup click', function () {   // for select box
    var i = $(this).data('column');
    var v = $(this).val();
    table.columns(i).search(v).draw();
  });

  $('.filter_dropdown').on( 'change', function () {   // for select box
    var i = $(this).data('column');
    var v = $(this).val();
    table.columns(i).search(v).draw();
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

  // autofocus on search input on ready
  $('div.dataTables_filter input').focus();
});
</script>

<?php if (isset($page_script))
  $this->load->view($page_script);?>
</body>
</html>
