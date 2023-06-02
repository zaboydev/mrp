<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<section class="style-default">
    <div class="section-body">
        <form class="card style-default-bright" method="post">
        <div class="card-head style-primary-dark">
            <header><?$entity['employee_number'];?></header>
        </div>

        <div class="card-body">
            
        </div>
        </form>
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
        // GENERAL ELEMENTS
        var formDocument = $('#form-document');
        var buttonSubmitDocument = $('#btn-submit-document');
        var buttonDeleteDocumentItem = $('.btn_delete_document_item');
        var buttonEditDocumentItem = $('.btn_edit_document_item');
        var autosetInputData = $('[data-input-type="autoset"]');

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

        // var today       = new Date();
        var today = $('[name="required_date"]').val();
        var today_2 = $('[name="pr_date"]').val();
        // today.setDate(today.getDate() + 30);
        $('#required_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'yyyy-mm-dd',
        startDate: today,
        });

        $('[data-provide="datepicker"]').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'yyyy-mm-dd',
        startDate: today_2,
        });

        $(document).on('click', '.btn-xhr-submit', function(e) {
        e.preventDefault();

        var button = $(this);
        var form = $('.form-xhr');
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

        $(buttonSubmitDocument).on('click', function(e) {
        e.preventDefault();
        $(buttonSubmitDocument).attr('disabled', true);

        var url = $(this).attr('href');
        if (confirm('Are you sure want to save this request and sending email? Continue?')) {
            $.post(url, formDocument.serialize(), function(data) {
            console.log(data);
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
        }


        });

        $(buttonEditDocumentItem).on('click', function(e) {
        e.preventDefault();

        //var id = $(this).data('todo').id;
        var id = $(this).data('todo').todo;
        var data_send = {
            id: id
            //i: i
        };
        var save_method;

        save_method = 'update';


        $.ajax({
            url: "<?= site_url($module['route'] . '/ajax_editItem/') ?>/" + id,
            type: "GET",
            data: data_send,
            dataType: "JSON",
            success: function(response) {
            var action = "<?= site_url($module['route'] . '/edit_item/') ?>/" + id;
            console.log(JSON.stringify(action));
            console.log(JSON.stringify(response));
            var maximum_price = parseFloat(response.maximum_price);
            var mtd_budget = parseFloat(response.mtd_budget);
                

            $('#account_id').val(response.account_id);
            $('#account_name').val(response.account_name);
            $('#account_code').val(response.account_code);
            $('#annual_cost_center_id').val(response.annual_cost_center_id);
            $('#maximum_price').val(maximum_price);
            $('#mtd_budget').val(mtd_budget);
            $('#expense_monthly_budget_id').val(response.expense_monthly_budget_id);
            $('#additional_info').val(response.additional_info);
            $('#amount').val(response.amount);
            $('#reference_ipc').val(response.reference_ipc);

            $('input[id="amount"]').attr('data-rule-max', parseFloat(response.maximum_price)).attr('data-msg-max', 'max available '+ parseInt(response.maximum_price));

            // $('#issued_quantity').attr('max', parseInt(ui.item.qty_konvers)).focus();
            $('#amount').attr('max', parseFloat(response.maximum_price)).focus();

            $('#unbudgeted_item').val(0);

            $('#account_name').prop("readonly", true);
            $('#account_code').prop("readonly", true);

            $('#modal-add-item form').attr('action', action);
            $('#modal-add-item').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Edit Item'); // Set title to Bootstrap modal title

            },
            error: function(jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
            }
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
        url: $('#search_budget').data('target'),
        dataType: "json",
        error: function(xhr, response, results) {
            console.log(xhr.responseText);
        },
        success: function(resource) {
            $('#search_budget').autocomplete({
                autoFocus: true,
                minLength: 1,

                source: function(request, response) {
                var results = $.ui.autocomplete.filter(resource, request.term);
                response(results.slice(0, 10));
                console.log(results);
                },

                focus: function(event, ui) {
                return false;
                },

                select: function(event, ui) {
                // var maximum_quantity = parseFloat(ui.item.maximum_quantity);
                var maximum_price = parseFloat(ui.item.maximum_price);
                var mtd_budget = parseFloat(ui.item.mtd_budget);
                

                $('#account_id').val(ui.item.account_id);
                $('#account_name').val(ui.item.account_name);
                $('#account_code').val(ui.item.account_code);
                $('#annual_cost_center_id').val(ui.item.annual_cost_center_id);
                $('#maximum_price').val(maximum_price);
                $('#mtd_budget').val(mtd_budget);
                $('#expense_monthly_budget_id').val(ui.item.expense_monthly_budget_id);

                $('input[id="amount"]').attr('data-rule-max', parseFloat(ui.item.maximum_price)).attr('data-msg-max', 'max available '+ parseInt(ui.item.maximum_price));

                // $('#issued_quantity').attr('max', parseInt(ui.item.qty_konvers)).focus();
                $('#amount').attr('max', parseFloat(ui.item.maximum_price)).focus();

                $('#unbudgeted_item').val(0);

                $('#account_name').prop("readonly", true);
                $('#account_code').prop("readonly", true);

                $('#search_budget').val('');

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

        $('input[id="amount"]').on('keydown keyup', function (e) {
        if (parseFloat($(this).val()) > parseFloat($('input[id="maximum_price"]').val())){
            alert('Maximum limit is ' + $('input[id="maximum_price"]').val());
            $(this).val($('input[id="maximum_price"]').val());
            $(this).focus();
        }else{
            $("#modal-add-item-submit").prop("disabled", false);
        }

        if (parseFloat($(this).val()) == 0){
            $("#modal-add-item-submit").prop("disabled", true);
        }else{
            $("#modal-add-item-submit").prop("disabled", false);
        }

        return !(e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46);
        });


        $("#relocation_budget").on("change", function() {
        var budget_value = $("#budget_value").val();
        if (parseFloat($(this).val()) > parseFloat($("#budget_value").val())) {

            // $("#modal-add-item-submit").prop("disabled", true);

            $("#relocation_budget").closest("div").addClass("has-error").append('<p class="help-block total-error">Not allowed! max available ' + budget_value + '</p>').focus();
            $(this).val(budget_value);
            $(this).focus();
            // toastr.options.timeOut = 10000;
            // toastr.options.positionClass = 'toast-top-right';
            // toastr.error('Price or total price is over maximum price allowed! You can not add this item.');
        } else {
            console.log(321)
            $("#relocation_budget").closest("div").removeClass("has-error");
            // $(".total-error").remove();
            $("#modal-add-item-submit").prop("disabled", false);
        }
        });
    });

    function sum() {
        var total = parseInt($("#quantity").val()) * parseInt($("#price").val());

        $("#total").val(total).trigger("change");
    }

    function unbudgeted() {
        var status = $('#inventory_monthly_budget_id').val();

        if (status == null) {
        $('.form-unbudgeted').removeClass('hide');
        }
    }
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>