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
            font-family: Tahoma;
            font-size: 12px;
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
            font-family: Tahoma;
            font-size: 13px;
            font-weight: bold;
            padding: 3px 3px;
            border-style: solid;
            border-width: 1px;
            overflow: hidden;
            word-break: normal;
            border-color: #000;
            color: #333;
            background-color: #f0f0f0;
        }

        .tg .tg-3wr7 {
            font-weight: bold;
            font-size: 12px;
            font-family: "Tahoma" !important;
            text-align: center
        }

        .tg .tg-ti5e {
            font-size: 10px;
            font-family: "Tahoma" !important;
            text-align: center
        }

        .tg .tg-rv4w {
            font-size: 10px;
            font-family: "Tahoma" !important;
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
            font-family: Tahoma;
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
            font-family: Tahoma;
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
        .title {
            font-family:"Tahoma";
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
                            <div class="col-xs-9">
                                <div class="row">
                                    <!-- <div class="table-responsive" id="view_data"> -->
                                    <!-- <h3 style="text-align:center;"><?= $page['title'] ?></h3> -->
                                    <div class="col-sm-12">
                                        <div class="table-responsive" id="listView">
                                            <table width="100%">
                                                <tr>
                                                    <th width="50%">Saldo Akhir per Tanggal</th>
                                                    <th width="50%" style="text-align:right;"><?= print_number(0,2)?></th>
                                                </tr>
                                            </table>
                                            <table class="table table-bordered table-nowrap" id="table-document">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Document Number</th>
                                                        <th>Name</th>
                                                        <th>Description</th>
                                                        <th>Debit</th>
                                                        <th>Credit</th>
                                                        <th>Saldo</th>
                                                    </tr>
                                                    
                                                </thead>
                                                <tbody>

                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <div class="col-xs-3">
                                <div class="form">
                                    <div class="form-group">
                                        <select name="account" id="account" class="form-control" data-source="<?= site_url($module['route'] . '/set_account/'); ?>" required data-input-type="autoset">
                                        <option value="">-- SELECT Account --</option>
                                        <?php foreach (getAccount('CASH') as $key => $accountCash) : ?>
                                            <option value="<?= $accountCash['coa']; ?>">
                                            <?= $accountCash['coa']; ?> <?= $accountCash['group']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <?php foreach (getAccount('BANK') as $key => $accountBank) : ?>
                                            <option value="<?= $accountBank['coa']; ?>">
                                            <?= $accountBank['coa']; ?> <?= $accountBank['group']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                        </select>
                                        <label for="vendor">Account</label>
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control input-sm" data-column="2" id="start_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" value="<?=date('Y-m-d')?>" required>
                                        <label for="currency">Start Date</label>
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control input-sm" data-column="2" id="end_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" value="<?=date('Y-m-d')?>" required>
                                        <label for="currency">End Date</label>
                                    </div>
                                    <button id="btn-generate" type="button" class="btn btn-danger btn-block ink-reaction">Generate</button>
                                </div>
                            </div>
                        </div>
                        <?= form_close(); ?>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="section-action style-default-bright">
            <div class="section-floating-action-row">
                <?php if (is_granted($module, 'document')) : ?> 
                <?php if (saldoAwalExists('Cash-Bank')) : ?>                               
                <button data-href="<?= site_url($module['route'] . '/info/Cash-Bank'); ?>" type="button" class="btn btn-floating-action btn-lg btn-primary btn-tooltip ink-reaction" id="btn-info">
                    <i class="md md-assignment"></i>
                    <small class="top right">Info Saldo Awal</small>
                </button>
                <?php else: ?>
                <a href="<?= site_url($module['route'] . '/create/Cash-Bank'); ?>" type="button" class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-create-document">
                    <i class="md md-add"></i>
                    <small class="top right">Create Transaksi</small>
                </a>
                <?php endif ?>
                <?php endif ?>
            </div>
        </div>
        <div id="data-modal" class="modal fade-scale" role="dialog" aria-labelledby="data-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-fs" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>

                        <h4 class="modal-title" id="data-modal-label"><?= strtoupper($module['parent']); ?></h4>
                    </div>

                    <div class="modal-body no-padding"></div>

                    <div class="modal-footer"></div>
                </div>
            </div>
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

    $("#btn-info").on('click', function(e) {
        var dataModal = $('#data-modal');
        // $(dataModal).modal('show');
        $.ajax({
            type: "GET",
            url: $(this).data('href'),
            cache: false,
            success: function(response) {
                var obj = $.parseJSON(response);
                $(dataModal)
                  .find('.modal-body')
                  .empty()
                  .append(obj.info);

                $(dataModal).modal('show');
              
            },
            error: function(xhr, ajaxOptions, thrownError) {
              console.log(xhr.status);
              console.log(xhr.responseText);
              console.log(thrownError);
            }
        });
    });
    

    $(".btn-print-report").on('click', function(e) {
        suplier = $("#suplier_select").val();
        currency = $("#currency_select").val();
        date = $("#date").val();
        tipe = $(this).data('tipe');

        get_po_for_print()
    });

    $("#btn-generate").on('click', function(e) {
        account     = $("#account").val();
        start_date  = $("#start_date").val();
        end_date    = $("#end_date").val();
        if(account==''){
            $('#account').parents('.form-group').addClass('has-error');
            $('#account').after('<span class="help-block">This field is required</span>');
        }else{
            if($('#account').parents('.form-group').hasClass('has-error')){
                $('#account').parents('.form-group').removeClass('has-error');
            }
            $('#account').siblings('.help-block').remove();
        }
        if(start_date==''){
            $('#start_date').parents('.form-group').addClass('has-error');
            $('#start_date').after('<span class="help-block">This field is required</span>');
        }else{
            if($('#start_date').parents('.form-group').hasClass('has-error')){
                $('#start_date').parents('.form-group').removeClass('has-error');
            }
            $('#start_date').siblings('.help-block').remove();
        }
        if(end_date==''){
            $('#end_date').parents('.form-group').addClass('has-error');
            $('#end_date').after('<span class="help-block">This field is required</span>');
        }else{
            if($('#end_date').parents('.form-group').hasClass('has-error')){
                $('#end_date').parents('.form-group').removeClass('has-error');
            }
            
            $('#end_date').siblings('.help-block').remove();
        }

        if(start_date!='' && end_date!='' && account!=''){
            get_data()
        }
    });

    function get_data() {
        $("#loadingScreen2").attr("style", "display:block");
        $.ajax({
            type: "POST",
            url: '<?= base_url() . "saldo/get_data" ?>',
            data: {
                'account': account,
                'start_date': start_date,
                'end_date': end_date
            },
            cache: false,
            success: function(response) {
                $("#loadingScreen2").attr("style", "display:none");
                var data = jQuery.parseJSON(response);
                $("#listView").html(data.info);
            },
            error: function(response){
                $("#loadingScreen2").attr("style", "display:none");
                toastr.options.timeOut = 10000;
                toastr.options.positionClass = 'toast-top-right';
                toastr.error('Error Loading Data');
            }
        });
    }

    function get_po_for_print() {

        var data = {
            'tipe': tipe

        };

        var urlPrint = '<?= base_url() ?>' + 'expense_report_konsolidasi/get_data_for_print/'+tipe;
        window.open(urlPrint);

    }


    $('[data-provide="datepicker"]').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'yyyy-mm-dd',
        // startDate: '0 d',
        // endDate: last_opname
    });

    
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>