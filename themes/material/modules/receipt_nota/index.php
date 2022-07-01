<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<style>
    .float {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 40px;
        right: 40px;
        border-radius: 50px;
        text-align: center;
        box-shadow: 2px 2px 3px #999;
        z-index: 100000;
    }

    .my-float {
        margin-top: 22px;
    }

    .tg {
        border-collapse: collapse;
        border-spacing: 0;
        border-color: #ccc;
        width: 100%;
    }

    .tg td {
        font-family: "Arial", Helvetica, sans-serif !important;
        font-size: 13px;
        padding: 3px 3px;
        border-style: solid;
        border-width: 1px;
        overflow: hidden;
        word-break: normal;
        border-color: #000;
        color: #333;
        background-color: #fff;

    }

    .tg th {
        font-family: "Arial", Helvetica, sans-serif !important;
        font-size: 15px;
        font-weight: bold;
        padding: 3px 3px;
        border-style: solid;
        border-width: 1px;
        overflow: hidden;
        word-break: normal;
        border-color: #000;
        color: #333;
        background-color: #f0f0f0;
        text-align: center;
    }

    .tg .tg-3wr7 {
        font-weight: bold;
        font-size: 12px;
        font-family: "Arial", Helvetica, sans-serif !important;
        ;
        text-align: center
    }

    .tg .tg-ti5e {
        font-size: 10px;
        font-family: "Arial", Helvetica, sans-serif !important;
        ;
        text-align: center
    }

    .tg .tg-rv4w {
        font-size: 10px;
        font-family: "Arial", Helvetica, sans-serif !important;
    }

    .box {
        background-color: white;
        width: auto;
        height: auto;
        border: 1px solid black;
        padding: 5px;
        margin: 2px;
    }

    .tt td {
        font-family: Arial;
        font-size: 12px;
        padding: 3px 3px;
        border-width: 1px;
        overflow: hidden;
        word-break: normal;
        border-color: #000;
        color: #333;
        background-color: #fff;
    }

    .tt th {
        font-family: Arial;
        font-size: 13px;
        font-weight: bold;
        padding: 3px 3px;
        border-width: 1px;
        overflow: hidden;
        word-break: normal;
        border-color: #000;
        color: #333;
        background-color: #f0f0f0;
    }

    @media print {

        html,
        body {
            display: block;
            font-family: "Tahoma";
            margin: 0px 0px 0px 0px;
        }

        /*@page {
                size: Faktur Besar;
                }*/
        #footer {
            position: fixed;
            bottom: 0;
        }

    }
</style>
<section class="has-actions style-default">
    <div class="section-body">

        <?= form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate', 'id' => 'form_approval')); ?>

        <div class="card">
            <div class="card-head style-primary-dark">
                <header><?= PAGE_TITLE; ?></header>
            </div>

            <div class="card-body no-padding">
                <?php
                if ($this->session->flashdata('alert'))
                    render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);
                ?>

                <div class="document-header force-padding">
                    <div class="row">
                        <div class="newoverlay" id="loadingScreen2" style="display: none;">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-12 col-md-9">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-3">
                                            <div class="form-group">
                                                <input class="form-control input-sm filter_daterange" data-column="2" id="date" data-provide="datepicker" data-date-format="yyyy-mm-dd" readonly>
                                                <label for="currency">Date</label>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-md-6">
                                            <div class="form-group">
                                                <select id="suplier_select" class="form-control">
                                                    <option value="">Pilih Supplier</option>
                                                    <?php foreach ($suplier as $key) {
                                                        ?>
                                                        <option value="<?= $key->vendor ?>"><?= $key->vendor ?></option>
                                                    <?php
                                                    } ?>
                                                </select>
                                                <label for="currency">Supplier</label>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-3">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-sm btn-info btn-export" data-tipe="excel">Excel</button>
                                        <button type="button" class="btn btn-sm btn-danger btn-export" data-tipe="print">Print</button>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class=" document-data table-responsive" id="view_data">
                                    <div class="col-xs-8 col-xs-offset-2">
                                        <h3 style="text-align:center;"><?=$page['title']?></h3>
                                        <div id="listView">
                                            <table class="tg table-nowrap" id="table-document" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th class="middle-alignment">No</th>
                                                        <th class="middle-alignment">PO#</th>
                                                        <th class="middle-alignment">No Nota</th>
                                                        <th class="middle-alignment">Date</th>
                                                        <th class="middle-alignment">Amount PO</th>
                                                        <th class="middle-alignment">Amount Nota</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>    
                                        </div>
                                        
                                    </div>
                                </div>

                            </div>
                        </div>
                        <?= form_close(); ?>
                    </div>
</section>
<?php endblock() ?>

<?php startblock('scripts') ?>
<?= html_script('vendors/pace/pace.min.js') ?>
<?= html_script('vendors/jQuery/jQuery-2.2.1.min.js') ?>
<?= html_script('themes/material/assets/js/libs/jquery-ui/jquery-ui.min.js') ?>
<?= html_script('themes/material/assets/js/libs/bootstrap/bootstrap.min.js') ?>
<?= html_script('themes/material/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js') ?>
<?= html_script('themes/material/assets/js/libs/spin.js/spin.min.js') ?>
<?= html_script('themes/material/assets/js/libs/autosize/jquery.autosize.min.js') ?>
<?= html_script('themes/material/assets/js/libs/toastr/toastr.js') ?>
<?= html_script('themes/material/assets/js/libs/jquery-validation/dist/jquery.validate.min.js') ?>
<?= html_script('themes/material/assets/js/libs/jquery-validation/dist/additional-methods.min.js') ?>
<?= html_script('vendors/bootstrap-daterangepicker/moment.min.js') ?>
<?= html_script('vendors/bootstrap-daterangepicker/daterangepicker.js') ?>
<?= html_script('themes/material/assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js') ?>

<script>
    Pace.on('start', function() {
        $('.progress-overlay').show();
    });

    Pace.on('done', function() {
        $('.progress-overlay').hide();
    });

    function popup(mylink, windowname) {
        var height = window.innerHeight;
        var widht;
        var href;

        if (screen.availWidth > 768) {
            width = 769;
        } else {
            width = screen.availWidth;
        }

        var left = (screen.availWidth / 2) - (width / 2);
        var top = 0;
        // var top = (screen.availHeight / 2) - (height / 2);

        if (typeof(mylink) == 'string') href = mylink;
        else href = mylink.href;

        window.open(href, windowname, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + width + ', height=' + height + ', top=' + top + ', left=' + left);

        if (!window.focus) return true;
        else return false;
    }

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
    $("#loadingScreen2").attr("style", "display:none");


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

    $('[data-provide="datepicker"]').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'yyyy-mm-dd',
        // startDate: '0 d',
        // endDate: last_opname
    });

    $('#date').change(function() {
        suplier     = $("#suplier_select").val();
        date        = $("#date").val();
        

        var formData = {
          vendor       : suplier,
          date          : date,
        };
        var url = "<?= $grid['data_source']; ?>";

        if(suplier!='' && date!=''){
            $("#listView").html("");
            getPo(url,formData);
        }
    });

    $('.filter_daterange').daterangepicker({
        autoUpdateInput: false,
        parentEl: '#offcanvas-datatable-filter',
        locale: {
            cancelLabel: 'Clear'
        }
    }).on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + '.' + picker.endDate.format('YYYY-MM-DD'));
        // $(".btn-print-report").attr('disabled', false);

        suplier     = $("#suplier_select").val();
        date        = $("#date").val();
        

        var formData = {
          vendor       : suplier,
          date          : date,
        };
        var url = "<?= $grid['data_source']; ?>";

        if(suplier!='' && date!=''){
            $("#listView").html("");
            getPo(url,formData);
        }        
    }).on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        // $(".btn-print-report").attr('disabled', true);

        suplier     = $("#suplier_select").val();
        date        = $("#date").val();
        $("#listView").html("");

        var formData = {
          vendor       : suplier,
          date          : date,
        };
        var url = "<?= $grid['data_source']; ?>";

        getPo(url,formData);
    });

    $("#suplier_select").change(function() {

        suplier     = $("#suplier_select").val();
        date        = $("#date").val();
        

        var formData = {
          vendor       : suplier,
          date          : date,
        };
        var url = "<?= $grid['data_source']; ?>";

        if(suplier!='' && date!=''){
            $("#listView").html("");
            getPo(url,formData);
        }
    });

    function getPo(url, formData) {
        $("#loadingScreen2").attr("style", "display:block");
        console.log(suplier);
        
        $.ajax({
            url: url,
            type: 'GET',
            data: formData,
            success: function (data) {
                $("#loadingScreen2").attr("style", "display:none");
                var obj = $.parseJSON(data);
                $('#listView').html(obj.info);
            },
            error: function (request, status, error) {
                swal({
                    title: 'Perhatian',
                    text: 'Data Gagal Disimpan! ',
                    type: 'error'
                });
            }
        });
    }

    $('.btn-export').on('click', function(e) {
        suplier     = $("#suplier_select").val();
        date        = $("#date").val();
        var _export       = $(this).data('tipe');
        
        // $("#listView").html("");

        var formData = {
          vendor        : suplier,
          date          : date,
          export        : _export
        };
        var url = "<?= $grid['data_export']; ?>";
        print_report(url,formData);
    });

    function print_report(url, formData) {
      // $("#loadingScreen2").attr("style", "display:block");
      $.ajax({
        url: url,
        type: 'GET',
        data: formData,
        success: function (data) {
          var obj = $.parseJSON(data);
          // $('#report_view').html(obj.info);
          window.open(obj.open);
        },
        error: function (request, status, error) {
          swal({
            title: 'Perhatian',
            text: 'Data Gagal Disimpan! ',
            type: 'error'
          });
        }
      });
    }
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>