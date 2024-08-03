<script>
  Pace.on('start', function() {
    $('.progress-overlay').show();
  });

  Pace.on('done', function() {
    $('.progress-overlay').hide();
  });

  $('.select2').select2();

  $('.number').number(true, 2);

  (function($) {
    $.fn.reset = function() {
      this.find('input:text, input[type="email"], input:password, select, textarea').val('');
      this.find('input:radio, input:checkbox').prop('checked', false);
      return this;
    }

    $.fn.redirect = function(target) {
      var url = $(this).data('href');

      if (target == '_blank') {
        window.open(url, target);
      } else {
        window.document.location = url;
      }
    }

    $.fn.popup = function() {
      var popup = $(this).data('target');
      var source = $(this).data('source');

      $.get(source, function(data) {
        var obj = $.parseJSON(data);

        if (obj.type == 'denied') {
          toastr.options.timeOut = 10000;
          toastr.options.positionClass = 'toast-top-right';
          toastr.error(obj.info, 'ACCESS DENIED!');
        } else {
          $(popup)
            .find('.modal-body')
            .empty()
            .append(obj.info);

          $(popup).modal('show');

          $(popup).on('click', '.modal-header:not(a)', function() {
            $(popup).modal('hide');
          });

          $(popup).on('click', '.modal-footer:not(a)', function() {
            $(popup).modal('hide');
          });
        }
      })
    }
  }(jQuery));

  function submit_post_via_hidden_form(url, params) {
    var f = $("<form target='_blank' method='POST' style='display:none;'></form>").attr('action', url).appendTo(document.body);

    $.each(params, function(key, value) {
      var hidden = $('<input type="hidden" />').attr({
        name: key,
        value: JSON.stringify(value)
      });

      hidden.appendTo(f);
    });

    f.submit();
    f.remove();
  }

  function numberFormat(nStr) {
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
        String.fromCharCode(event.which).toLowerCase() === 'x')) {
      event.preventDefault();
    }
  });

  $(function() {
    // ALL ELEMENTS
    var dataItemGroup = $('[data-tag-name="group"]');
    var dataItemDescription = $('[data-tag-name="item_description"]');
    var dataPartNumber = $('[data-tag-name="part_number"]');
    var dataSerialNumber = $('[data-tag-name="item_serial"]');
    var dataStores = $('[data-tag-name="stores"]');
    var dataUnitOfMeasurement = $('[data-tag-name="item_unit"]');
    var dataItemCondition = $('[data-tag-name="condition"]');
    var dataQuantity = $('[data-tag-name="quantity"]');
    var dataMinimumQuantity = $('[data-tag-name="minimum_quantity"]');
    var dataAvailableQuantity = $('[data-tag-name="available_quantity"]');
    var dataInStoresQuantity = $('[data-tag-name="in_stores_quantity"]');
    var dataOnHandQuantity = $('[data-tag-name="on_hand_quantity"]');
    var dataUnitValue = $('[data-tag-name="unit_value"]');
    var dataReferenceNumber = $('[data-tag-name="reference_number"]');
    var dataReceivedDate = $('[data-tag-name="received_date"]');

    // GENERAL ELEMENTS
    var tableDocumentItems = $('#table-document-items');
    var formDocument = $('#form-document');
    var buttonSubmitDocument = $('#btn-submit-document');
    var buttonDeleteDocumentItem = $('.btn_delete_document_item');
    var autosetInputData = $('[data-input-type="autoset"]');

    // AUTOCOMPLETE ELEMENT
    var searchGeneralStock = $('[data-search-for="stock_general"]');
    var searchItemOnDelivery = $('[data-search-for="item_on_delivery"]');
    var searchItemInStores = $('[data-search-for="item_in_stores"]');
    var searchItemInUse = $('[data-search-for="item_in_use"]');
    var searchStores = $('[data-search-for="stores"]');
    var searchItemCategory = $('[data-search-for="category"]');
    var searchItemGroup = $('[data-search-for="group"]');
    var searchItemDescription = $('[data-search-for="item_description"]');
    var searchItemCondition = $('[data-search-for="condition"]');
    var searchPartNumber = $('[data-search-for="part_number"]');
    var searchAltPartNumber = $('[data-search-for="alternate_part_number"]');
    var searchSerialNumber = $('[data-search-for="item_serial"]');
    var searchUnitOfMeasurement = $('[data-search-for="item_unit"]');

    // FORM ELEMENT
    var inputItemGroup = $('[name="group"]');
    var inputItemDescription = $('[name="description"]');
    var inputPartNumber = $('[name="part_number"]');
    var inputAltPartNumber = $('[name="alternate_part_number"]');
    var inputSerialNumber = $('[name="item_serial"]');
    var inputQuantity = $('[name="quantity"]');
    var inputMinimumQuantity = $('[name="minimum_quantity"]');
    var inputUnitValue = $('[name="unit_value"]');
    var inputStores = $('[name="stores"]');
    var inputUnitOfMeasurement = $('[name="unit"]');
    var inputItemCondition = $('[name="condition"]');

    // MATERIAL SLIP FORM ELEMENT
    var inputRequestDescription = $('[name="request_description"]');
    var inputRequestPartNumber = $('[name="request_part_number"]');
    var inputRequestQuantity = $('[name="request_quantity"]');
    var inputRequestitionReference = $('[name="requestition_reference"]');
    var inputIssuedPartNumber = $('[name="issued_part_number"]');
    var inputIssuedSerialNumber = $('[name="issued_item_serial"]');
    var inputIssuedQuantity = $('[name="issued_quantity"]');
    var inputIssuedTo = $('[name="issued_to"]');
    var inputInStoresQuantity = $('[name="in_stores_quantity"]');
    var inputReferenceId = $('[name="reference_id"]');

    var inputReferenceItemInUseId = $('[name="reference_tb_item_in_uses_id"]');
    var inputReferenceItemOnDeliveryId = $('[name="reference_tb_item_on_deliveries_id"]');
    <?php if ($this->uri->segment(1) == "budgeting") : ?>
      var id_cots = "";
    <?php endif ?>
    toastr.options.closeButton = true;

    $('[data-toggle="redirect"]').on('click', function(e) {
      e.preventDefault;

      var url = $(this).data('url');

      window.document.location = url;
    });

    $('[data-toggle="back"]').on('click', function(e) {
      e.preventDefault;

      history.back();
    });

    $('[data-provide="datepicker"]').datepicker({
      autoclose: true,
      todayHighlight: true,
      format: 'yyyy-mm-dd'
    });

    $('form').on('change', '[data-validation-rule="unique"]', function() {
      var url = $(this).data('validation-url');
      var value = $(this).val();
      var exception = $(this).data('validation-exception');
      var wrapper = $(this).parent('div.form-group');
      var feedback = $(this).parent().find('i.form-control-feedback');
      var submitButton = $(this).closest('form').find('button[type="submit"]');

      $(submitButton).attr('disabled', true);

      $.post(url, {
        value: value,
        exception: exception
      }, function(data) {
        if (data == 'true') {
          $(feedback).remove();
          $(wrapper).removeClass('has-error has-feedback');
          $(submitButton).attr('disabled', false);
        } else {
          $(feedback).remove();
          $(wrapper).removeClass('has-success has-feedback').addClass('has-error');

          toastr.options.positionClass = 'toast-top-right';
          toastr.error(value + ' already exists!', '');
        }
      });
    });

    $('form').on('change', '[data-validation-rule="match"]', function() {
      var input = $(this);
      var value = input.val();
      var label = input.data('validation-label');
      var match = input.data('validation-match');
      var matchInput = $('input[name="' + match + '"]');
      var wrapper = matchInput.parent('div.form-group');
      var submitButton = input.closest('form').find('button[type="submit"]');

      if (value == '') {
        $(matchInput).attr('disabled', true);
        $(submitButton).attr('disabled', false);
      } else {
        $(matchInput).attr('disabled', false).focus();
        $(submitButton).attr('disabled', true);

        $(matchInput).on('focusout', function() {
          var matchValue = $(this).val();

          if (value == matchValue) {
            $(wrapper).removeClass('has-error').addClass('has-success');
            $(submitButton).attr('disabled', false);
          } else {
            $(wrapper).removeClass('has-success').addClass('has-error');

            toastr.options.positionClass = 'toast-top-right';
            toastr.error(label + ' is not match!', '');
          }
        });
      }
    });

    <?php if (in_array('datatable', $page['requirement'])) : ?>
      var datatableElement = $('[data-provide="datatable"]');
      var datatableOptions = new Object();

      datatableOptions.selectedRows = [];
      datatableOptions.selectedIds = [];
      datatableOptions.clickDelay = 700;
      datatableOptions.clickCount = 0;
      datatableOptions.clickTimer = null;
      datatableOptions.summaryColumns = <?= json_encode($grid['summary_columns']); ?>;

      $(datatableElement)
        .addClass('stripe row-border cell-border order-column nowrap')
        .attr('width', '100%');
      <?php if ($this->uri->segment(1) != "budgeting") : ?>

        $(datatableElement).find('thead tr:first-child th:first-child').attr('width', 1).text('No.');
      <?php endif ?>
      $(datatableElement).find('table td:first-child').attr('align', 'right');

      $.fn.dataTable.ext.errMode = 'throw';

      var datatable = $(datatableElement).DataTable({
        searchDelay: 350,
        scrollY: 410,
        scrollX: true,
        scrollCollapse: true,
        lengthMenu: [
          [10, 50, 100, -1],
          [10, 50, 100, "All"]
        ],
        pageLength: 10,
        pagingType: 'full',

        order: <?= json_encode($grid['order_columns']); ?>,
        fixedColumns: {
          leftColumns: <?= $grid['fixed_columns']; ?>
        },

        language: {
          info: "Total _TOTAL_ entries"
        },

        processing: true,
        serverSide: true,
        ajax: {
          url: "<?= $grid['data_source']; ?>",
          type: "POST",
          error: function(xhr, ajaxOptions, thrownError) {
            console.log(xhr.responseText);
            if (xhr.status == 404) {
              toastr.clear();
              toastr.error('Request page not found. Please contact Technical Support.', 'Loading data failed!');
              alert("page not found");
            } else {
              toastr.clear();
              toastr.error(textStatus + ': ' + errorThrown + '. Report this error!', 'Loading data failed!');
            }
          }
        },

        rowCallback: function(row, data) {
          var id = $(row).attr("data-id");
          if ($.inArray(data.DT_RowId, datatableOptions.selectedRows) !== -1) {
            $(row).addClass('selected');
          }

        },
        <?php if ($this->uri->segment(1) == "budgeting") : ?>
          drawCallback: function(settings) {
            var api = this.api();
            var data = api.rows({
              page: 'current'
            }).data()
            $.each(data, function(i, item) {
              var id = $(item[0]).attr("data-id");
              if (id_cots.indexOf("|" + id + ",") !== -1) {
                $("#cb_" + id).attr('checked', true);
              }
            });
          },
        <?php endif ?>
        columnDefs: [{
          searchable: false,
          orderable: false,
          targets: [0]
        }],

        dom: "<'row'<'col-sm-12'tr>>" +
          "<'datatable-footer force-padding no-y-padding'<'row'<'col-sm-4'i<'clearfix'>l><'col-sm-8'p>>>",
      });

      new $.fn.dataTable.Buttons(datatable, {
        dom: {
          container: {
            className: 'btn-group pull-left'
          },
          button: {
            className: 'btn btn-lg btn-icon-toggle ink-reaction'
          }
        },
        buttons: [{
            extend: 'print',
            className: 'btn-tooltip',
            text: '<i class="fa fa-print"></i><small class="top center">Quick Print</small>',
            // titleAttr: 'Quick print',
            autoPrint: false,
            footer: true,
            exportOptions: {
              columns: ':visible'
            }
          },
          {
            extend: 'csv',
            name: 'csv',
            text: '<i class="fa fa-file-text-o"></i><small class="top center">export to CSV</small>',
            // titleAttr: 'export to CSV',
            className: 'btn-tooltip',
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
            text: '<i class="fa fa-file-excel-o"></i><small class="top center">export to EXCEL</small>',

            // titleAttr: 'export to EXCEL',
            className: 'btn-tooltip',
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
            className: 'buttons-pdf btn-tooltip',
            text: '<i class="fa fa-file-pdf-o"></i><small class="top center">export to PDF</small>',
            // titleAttr: 'export to PDF',
            key: {
              ctrlKey: true,
              key: 'd'
            },
            action: function(e, dt, node, config) {
              var pdfUrl = '<?= site_url('pdf'); ?>',
                pdfTitle = '<?= PAGE_TITLE; ?>',
                pdfData = datatable.buttons.exportData({
                  columns: ':visible'
                });

              submit_post_via_hidden_form(
                pdfUrl, {
                  datatable: pdfData,
                  title: pdfTitle
                }
              );
            }
          }
        ]
      });

      datatable.buttons(0, null).container()
        .appendTo($('.btn-toolbar'));

      if (datatableOptions.summaryColumns) {
        datatable.on('xhr', function() {
          var json = datatable.ajax.json();

          $.each(datatableOptions.summaryColumns, function(key, value) {
            $(datatable.column(value).footer()).html(
              json.total[value]
            );
          });
        });
      }

      $(datatableElement).find('tbody').on('click', 'td', function() {
        datatableOptions.clickCount++;

        var modalOpenOnClick = datatable.row(this).data().DT_RowData.modal;
        var singleClickRow = datatable.row(this).data().DT_RowData.single_click;
        var doubleClickRow = datatable.row(this).data().DT_RowData.double_click;

        if (modalOpenOnClick) {
          var dataModal = $('#data-modal');
          var dataPrimaryKey = datatable.row(this).data().DT_RowData.pkey;

          $.get(modalOpenOnClick, function(data) {
            var obj = $.parseJSON(data);

            if (obj.type == 'denied') {
              toastr.options.timeOut = 10000;
              toastr.options.positionClass = 'toast-top-right';
              toastr.error(obj.info, 'ACCESS DENIED!');
            } else {
              $(dataModal)
                .find('.modal-body')
                .empty()
                .append(obj.info);

              $(dataModal)
                .find('#modal-print-data-button')
                .attr('href', obj.link.print);

              $(dataModal)
                .find('#modal-edit-data-button')
                .attr('href', obj.link.edit);

              $(dataModal)
                .find('#modal-delete-data-button')
                .attr('href', obj.link.delete);

              $(dataModal).modal('show');

              $(dataModal).on('click', '.modal-header:not(a)', function() {
                $(dataModal).modal('hide');
              });

              $(dataModal).on('click', '.modal-footer:not(a)', function() {
                $(dataModal).modal('hide');
              });
            }
          });
        } else {
          if (datatableOptions.clickCount === 1) {
            datatableOptions.clickTimer = setTimeout(function() {
              datatableOptions.clickCount = 0;

              if (singleClickRow)
                window.location = singleClickRow;
            }, datatableOptions.clickDelay);
          } else {
            clearTimeout(datatableOptions.clickTimer);
            datatableOptions.clickCount = 0;

            if (doubleClickRow)
              window.location = doubleClickRow;
          }
        }
      });
      <?php if ($this->uri->segment(1) == "budgeting") :  ?>
        $("tbody").on('click', '.edit', function(e) {
          if (e.target.nodeName === "INPUT") {
            if ($(e.target).prop('checked')) {
              id_cots += "|" + $(e.target).attr('data-id') + ",";
            } else {
              id_cots = id_cots.replace("|" + $(this).attr('data-id') + ",", "");
            }
            console.log(id_cots);
          } else {
            var parent = $(e.target).parent();
            id = parent.attr("data-id");
            status = parent.attr("data-status");
            if (status === "APPROVED") {
              toastr.options.timeOut = 10000;
              toastr.options.positionClass = 'toast-top-right';
              toastr.error('This data is locked');
            } else {
              console.log($(this).popup())
            }
          }
        });
        $("#btn-approve-data").click(function() {
          if (id_cots === "") {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.error('Empty Data');
          } else {
            $("#approve-modal").modal("show");
            $("#loadingScreen").attr("style", "display:none");
          }

        });
        $("#yes_btn").click(function() {
          $("#loadingScreen").attr("style", "display:block");
          approveData();
        })

        function approveData() {
          $.ajax({
            type: "POST",
            url: $("#baselink").val() + '/approve',
            data: {
              "id_cots": id_cots
            },
            cache: false,
            success: function(response) {
              $("#loadingScreen").attr("style", "display:none");
              location.reload();
            },
            error: function(xhr, ajaxOptions, thrownError) {
              $("#loadingScreen").attr("style", "display:none");
              console.log(xhr.status);
              console.log(xhr.responseText);
              console.log(thrownError);
            }
          });
        }
      <?php endif ?>
      $('.filter_numeric_text').on('keyup click', function() {
        var i = $(this).data('column');
        var v = $(this).val();
        datatable.columns(i).search(v).draw();
      });

      $('.filter_input_text').on('keyup', function() {
        var i = $(this).data('column');
        var v = $(this).val();
        datatable.columns(i).search(v).draw();
      });

      $('.filter_dropdown').on('change', function() {
        var i = $(this).data('column');
        var v = $(this).val();
        var tipe = $(this).data('tipe');
        $('#' + tipe).html(' | ' + tipe + ' : ' + v);
        datatable.columns(i).search(v).draw();

      });

      $('.filter_boolean').on('change', function() {
        var checked = $(this).is(':checked');
        var i = $(this).data('column');

        if (checked) {
          datatable.columns(i).search('true').draw();
        } else {
          datatable.columns(i).search('').draw();
        }
      });

      $('.filter_daterange').daterangepicker({
        autoUpdateInput: false,
        parentEl: '#offcanvas-datatable-filter',
        locale: {
          cancelLabel: 'Clear'
        }
      }).on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' ' + picker.endDate.format('YYYY-MM-DD'));
        var i = $(this).data('column');
        var v = $(this).val();
        var tipe = $(this).data('tipe');
        $('#' + tipe).html(' | ' + tipe + ' : ' + picker.startDate.format('DD MMM YYYY') + ' - ' + picker.endDate.format('DD MMM YYYY'));
        // datatable.columns(i).search(v).draw();
        datatable.columns(i).search(v).draw();
      }).on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        var i = $(this).data('column');
        datatable.columns(i).search('').draw();
      });

      $('a.column-toggle').on('click', function(e) {
        e.preventDefault();
        var column = datatable.column($(this).attr('data-column'));

        column.visible(!column.visible());

        var label = $(this).attr('data-label');
        var text = (column.visible() === true ? '<div class="tile-text">' + label + '</div>' : '<div class="tile-text text-muted">' + label + '</div>');

        $(this).html(text);
      });

      $('.dataTables_paginate').find('a').removeClass();
      $('#datatable-form').removeClass('hidden');
      $('#datatable-form input').on('keyup', function() {
        datatable.search(this.value).draw();
      });
      $('[data-toggle="reload"]').on('click', function() {
        datatable.ajax.reload(null, false);
      });

      datatable.on('processing.dt', function(e, settings, processing) {
        if (processing) {
          $('.progress-overlay').show();
        } else {
          $('.progress-overlay').hide();
        }
      });
    <?php endif; ?>


    $(document).on('click', '.btn-xhr-submit', function(e) {
      e.preventDefault();

      var button = $(this);
      var form = $(this).closest('.form-xhr');
      var action = form.attr('action');

      button.attr('disabled', true);

      if (form.valid()) {
        $.post(action, form.serialize()).done(function(data) {
          var obj = $.parseJSON(data);

          if (obj.type == 'danger') {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.error(obj.info);
          } else {
            toastr.options.positionClass = 'toast-top-right';
            toastr.success(obj.info);

            form.reset();

            $('[data-dismiss="modal"]').trigger('click');

            if (datatable) {
              datatable.ajax.reload(null, false);
            }
          }
        });
      }

      button.attr('disabled', false);
    });


    $(document).on('click', '.btn-xhr-delete', function(e) {
      e.preventDefault();

      var button = $(this);
      var form = $('.form-xhr');
      var action = button.attr('href');

      button.attr('disabled', true);

      if (confirm('Are you sure want to delete this data? Beware of this data can not be restored after it is removed. Continue?')) {
        $.post(action, form.serialize()).done(function(data) {
          var obj = $.parseJSON(data);
          if (obj.type == 'danger') {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.error(obj.info);

            buttonToDelete.attr('disabled', false);
          } else {
            toastr.options.positionClass = 'toast-top-right';
            toastr.success(obj.info);

            form.reset();

            $('[data-dismiss="modal"]').trigger('click');

            if (datatable) {
              datatable.ajax.reload(null, false);
            }
          }
        }).fail(function() {
          toastr.options.timeOut = 10000;
          toastr.options.positionClass = 'toast-top-right';
          toastr.error('Delete Failed! This data is still being used by another document.');
        });
      }

      button.attr('disabled', false);
    });

    $(document).on('change', '#group_id', function(e) {
      var coa = $('option:selected', this).data('coa');
      var group = $('option:selected', this).data('group');
      $('[id="group"]').val(group);
      $('[id="coa"]').val(coa);
    });

    // $("#group_id").change(function(e) {
    //   var coa = $('option:selected', this).data('coa');
    //   var group = $('option:selected', this).data('group');
    //   $('[id="group"]').val(group);
    //   $('[id="coa"]').val(coa);
    //   // }
    // });


    // if ( $("#table-document > tbody > tr").length == 0 )
    //   $(buttonSubmitDocument).attr('disabled', true);

    $(buttonSubmitDocument).on('click', function(e) {
      e.preventDefault();
      $(buttonSubmitDocument).attr('disabled', true);

      var url = $(this).attr('href');

      $.post(url, formDocument.serialize(), function(data) {
        var obj = $.parseJSON(data);

        if (obj.success == false) {
          toastr.options.timeOut = 10000;
          toastr.options.positionClass = 'toast-top-right';
          toastr.error(obj.message);
        } else {
          toastr.options.timeOut = 4500;
          toastr.options.closeButton = false;
          toastr.options.progressBar = true;
          toastr.options.positionClass = 'toast-top-right';
          toastr.success(obj.message);

          window.setTimeout(function() {
            window.location.href = '<?= site_url($module['route']); ?>';
          }, 5000);
        }

        $(buttonSubmitDocument).attr('disabled', false);
      });
    });

    $(buttonDeleteDocumentItem).on('click', function(e) {
      e.preventDefault();

      var url = $(this).attr('href');
      var tr = $(this).closest('tr');

      $.get(url);

      $(tr).remove();

      if ($("#table-document > tbody > tr").length == 0) {
        $(buttonSubmit).attr('disabled', true);
      }
    });

    $(autosetInputData).on('change', function() {
      var val = $(this).val();
      var url = $(this).data('source');

      $.get(url, {
        data: val
      });
    });

    $.ajax({
      url: $(searchItemInStores).data('source'),
      dataType: "json",
      success: function(resource) {
        $(searchItemInStores).autocomplete({
            autoFocus: true,
            minLength: 3,

            source: function(request, response) {
              var results = $.ui.autocomplete.filter(resource, request.term);
              response(results.slice(0, 5));
            },

            focus: function(event, ui) {
              return false;
            },

            select: function(event, ui) {
              $(dataItemGroup).val(ui.item.group).before(dataItemGroup.next());
              $(dataItemDescription).val(ui.item.description).before(dataItemDescription.next());
              $(dataPartNumber).val(ui.item.part_number).before(dataPartNumber.next());
              $(dataSerialNumber).val(ui.item.item_serial).before(dataSerialNumber.next());
              $(dataStores).val(ui.item.stores).before(dataStores.next());
              $(dataUnitOfMeasurement).val(ui.item.unit).before(dataUnitOfMeasurement.next());
              $(dataItemCondition).val(ui.item.condition).before(dataItemCondition.next());
              $(dataReceivedDate).val(ui.item.date_of_entry).before(dataReceivedDate.next());
              $(dataReferenceNumber).val(ui.item.document_number).before(dataReferenceNumber.next());
              $(dataQuantity).val(ui.item.quantity).before(dataQuantity.next());
              $(dataMinimumQuantity).val(ui.item.minimum_quantity).before(dataMinimumQuantity.next());
              $(dataInStoresQuantity).val(ui.item.available_quantity).before(dataInStoresQuantity.next());
              $(dataOnHandQuantity).val(ui.item.on_hand_quantity).before(dataOnHandQuantity.next());
              $(dataUnitValue).val(ui.item.unit_value).before(dataUnitValue.next());

              // only for Material Slip document
              $(inputReferenceId).val(ui.item.id).before(inputReferenceId.next());
              $(inputIssuedQuantity).data('rule-max', ui.item.available_quantity).data('msg-max', 'max AVAILABLE ' + ui.item.available_quantity);
              $('input[data-valid-quantity="true"]').data('rule-max', ui.item.available_quantity).data('msg-max', 'max AVAILABLE ' + ui.item.available_quantity);

              if (ui.item.item_serial != '') {
                $(inputIssuedQuantity).val(1).attr('readonly', true);
                $(inputRequestQuantity).val(1).attr('readonly', true);
                // $( 'input[data-valid-quantity="true"]' ).val(1).attr('readonly', true);
              }

              $(searchItemInStores).val('');

              if (inputRequestitionReference.length)
                $(inputRequestitionReference).focus();

              return false;
            }
          })
          .data("ui-autocomplete")._renderItem = function(ul, item) {
            $(ul).addClass('list divider-full-bleed');

            return $("<li class='tile'>")
              .append('<a class="tile-content ink-reaction"><div class="tile-text">' + item.label + '</div></a>')
              .appendTo(ul);
          };
      }
    });

    $.ajax({
      url: $(searchItemInUse).data('source'),
      dataType: "json",
      success: function(resource) {
        $(searchItemInUse).autocomplete({
            autoFocus: true,
            minLength: 3,

            source: function(request, response) {
              var results = $.ui.autocomplete.filter(resource, request.term);
              response(results.slice(0, 5));
            },

            focus: function(event, ui) {
              return false;
            },

            select: function(event, ui) {
              $(dataItemGroup).val(ui.item.group).attr('readonly', true).before(dataItemGroup.next());
              $(dataItemDescription).val(ui.item.description).attr('readonly', true).before(dataItemDescription.next());
              $(dataPartNumber).val(ui.item.part_number).attr('readonly', true).before(dataPartNumber.next());
              $(dataSerialNumber).val(ui.item.item_serial).before(dataSerialNumber.next());
              $(dataUnitOfMeasurement).val(ui.item.unit).attr('readonly', true).before(dataUnitOfMeasurement.next());
              $(dataItemCondition).val(ui.item.condition).before(dataItemCondition.next());
              $(dataQuantity).val(ui.item.quantity).before(dataQuantity.next());
              $(dataAvailableQuantity).val(ui.item.available_quantity).before(dataAvailableQuantity.next());
              $(dataUnitValue).val(ui.item.unit_value).before(dataUnitValue.next());

              // only for Material Slip document
              $(inputReferenceItemInUseId).val(ui.item.id).before(inputReferenceItemInUseId.next());
              $(inputQuantity).data('rule-max', ui.item.available_quantity).data('msg-max', 'max AVAILABLE ' + ui.item.available_quantity);

              if (ui.item.item_serial != '') {
                $(inputQuantity).val(1).attr('readonly', true);
              }

              $(searchItemInUse).val('');

              if (inputRequestitionReference.length)
                $(inputRequestitionReference).focus();

              return false;
            }
          })
          .data("ui-autocomplete")._renderItem = function(ul, item) {
            $(ul).addClass('list divider-full-bleed');

            return $("<li class='tile'>")
              .append('<a class="tile-content ink-reaction"><div class="tile-text">' + item.label + '</div></a>')
              .appendTo(ul);
          };
      }
    });

    $.ajax({
      url: $(searchItemOnDelivery).data('source'),
      dataType: "json",
      success: function(resource) {
        $(searchItemOnDelivery).autocomplete({
            autoFocus: true,
            minLength: 3,

            source: function(request, response) {
              var results = $.ui.autocomplete.filter(resource, request.term);
              response(results.slice(0, 5));
            },

            focus: function(event, ui) {
              return false;
            },

            select: function(event, ui) {
              $(dataItemGroup).val(ui.item.group).before(dataItemGroup.next());
              $(dataItemDescription).val(ui.item.description).before(dataItemDescription.next());
              $(dataPartNumber).val(ui.item.part_number).before(dataPartNumber.next());
              $(dataUnitOfMeasurement).val(ui.item.unit).before(dataUnitOfMeasurement.next());
              $(dataMinimumQuantity).val(ui.item.minimum_quantity).before(dataMinimumQuantity.next());
              $(dataOnHandQuantity).val(ui.item.on_hand_quantity).before(dataOnHandQuantity.next());
              $(dataUnitValue).val(ui.item.unit_value).before(dataUnitValue.next());

              // only for Material Slip document
              $(inputReferenceItemOnDeliveryId).val(ui.item.id).before(inputReferenceItemOnDeliveryId.next());
              $(inputQuantity).val(ui.item.on_hand_quantity).data('rule-max', ui.item.on_hand_quantity).data('msg-max', 'max AVAILABLE ' + ui.item.on_hand_quantity);

              $(searchItemOnDelivery).val('');

              if (inputRequestitionReference.length)
                $(inputRequestitionReference).focus();

              return false;
            }
          })
          .data("ui-autocomplete")._renderItem = function(ul, item) {
            $(ul).addClass('list divider-full-bleed');

            return $("<li class='tile'>")
              .append('<a class="tile-content ink-reaction"><div class="tile-text">' + item.label + '</div></a>')
              .appendTo(ul);
          };
      }
    });

    $.ajax({
      url: $(searchGeneralStock).data('source'),
      dataType: "json",
      success: function(resource) {
        $(searchGeneralStock).autocomplete({
            autoFocus: true,
            minLength: 3,

            source: function(request, response) {
              var results = $.ui.autocomplete.filter(resource, request.term);
              response(results.slice(0, 5));
            },

            focus: function(event, ui) {
              return false;
            },

            select: function(event, ui) {
              $(dataItemGroup).val(ui.item.group).before(dataItemGroup.next());
              $(dataItemDescription).val(ui.item.description).before(dataItemDescription.next());
              $(dataPartNumber).val(ui.item.part_number).before(dataPartNumber.next());
              $(dataUnitOfMeasurement).val(ui.item.unit).before(dataUnitOfMeasurement.next());
              $(dataMinimumQuantity).val(ui.item.minimum_quantity).before(dataMinimumQuantity.next());
              $(dataOnHandQuantity).val(ui.item.on_hand_quantity).before(dataOnHandQuantity.next());
              $(dataUnitValue).val(ui.item.unit_value).before(dataUnitValue.next());

              $(inputReferenceItemOnDeliveryId).val('');
              $(searchGeneralStock).val('');

              if (inputSerialNumber.length)
                $(inputSerialNumber).focus();

              return false;
            }
          })
          .data("ui-autocomplete")._renderItem = function(ul, item) {
            $(ul).addClass('list divider-full-bleed');

            return $("<li class='tile'>")
              .append('<a class="tile-content ink-reaction"><div class="tile-text">' + item.label + '</div></a>')
              .appendTo(ul);
          };
      }
    });

    $.ajax({
      url: $('[data-search-for="item_description"]').data('source'),
      dataType: "json",
      success: function(data) {
        $('[data-search-for="item_description"]').autocomplete({
          source: function(request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    $.ajax({
      url: $('[data-search-for="part_number"]').data('source'),
      dataType: "json",
      success: function(data) {
        $('[data-search-for="part_number"]').autocomplete({
          source: function(request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    $.ajax({
      url: $('[data-search-for="alternate_part_number"]').data('source'),
      dataType: "json",
      success: function(data) {
        $('[data-search-for="alternate_part_number"]').autocomplete({
          source: function(request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    $.ajax({
      url: $('[data-search-for="item_serial"]').data('source'),
      dataType: "json",
      success: function(data) {
        $('[data-search-for="item_serial"]').autocomplete({
          source: function(request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    $.ajax({
      url: $('[data-search-for="item_unit"]').data('source'),
      dataType: "json",
      success: function(data) {
        $('[data-search-for="item_unit"]').autocomplete({
          source: function(request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    $.ajax({
      url: $('[data-search-for="stores"]').data('source'),
      dataType: "json",
      success: function(data) {
        $('[data-search-for="stores"]').autocomplete({
          source: function(request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    $(inputSerialNumber).on('change', function() {
      if ($(this).val() != '') {
        $(inputQuantity).val('1').attr('readonly', true);
        $(inputIssuedQuantity).val('1').attr('readonly', true);
      } else {
        $(inputQuantity).attr('readonly', false);
        $(inputIssuedQuantity).attr('readonly', false);
      }
    });

    $('#adjustment_quantity').on('keyup click', function() {
      var adjustment_quantity = $(this).val();
      var current_quantity = $('#adjustment_current_quantity').val();
      var next_quantity = parseFloat(adjustment_quantity) + parseFloat(current_quantity);

      $('#adjustment_next_quantity').val(next_quantity);
    });

    $('#btn-pengajuan-data').on('click', function(e) {
      e.preventDefault();

      var button = $(this);
      var action = button.data('target');

      button.attr('disabled', true);

      if (confirm('Are you sure want to submit this budget year ? Continue?')) {
        $.get(action).done(function(data) {
          var obj = $.parseJSON(data);

          if (obj.type == 'danger') {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.error(obj.info);

            buttonToDelete.attr('disabled', false);
          } else {
            toastr.options.positionClass = 'toast-top-right';
            toastr.success(obj.info);

            if (datatable) {
              datatable.ajax.reload(null, false);
            }
          }
        }).fail(function() {
          toastr.options.timeOut = 10000;
          toastr.options.positionClass = 'toast-top-right';
          toastr.error('Operation Failed! Please try again or ask Technical Support.');
        });
      }

      button.attr('disabled', false);
    });
  });
</script>