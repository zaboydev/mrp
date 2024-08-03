<?php include 'themes/material/simple.php' ?>

<?php startblock('body') ?>
<div class="container">

    <h4 class="page-header">Add Expense</h4>

    <form id="form_add_vendor" class="form" role="form" method="post" action="<?= site_url($module['route'] . '/add_input_expense'); ?>">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-nowrap" id="table-document">
                        <thead>
                            <tr>
                                <th class="middle-alignment">Expense Name</th>
                                <th class="middle-alignment">Type</th>
                                <th class="middle-alignment"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($_SESSION['tujuan_dinas']['items'])) : ?>
                        <?php foreach ($_SESSION['tujuan_dinas']['items'] as $id => $item):?>
                            <tr>
                                <td>                                    
                                    <input type="text" name="expense_name[]" value="<?=$item['expense_name'];?>" class="form-control">
                                </td>
                                <td class="remarks item-list">
                                    <select name="fix[]" class="form-control" required>
                                        <option value="false" <?= ($item['expense_name']===False)? 'selected':''?>>Amount Dapat Berubah</option>
                                        <option value="true" <?= ($item['expense_name']===False)? 'selected':''?>>Amount Tetap</option>
                                    </select>
                                </td>  

                                <td class="item-list" style="text-align:center;">
                                    <a  href="javascript:;" title="Delete" class="btn btn-danger btn-xs btn-row-delete-item" data-tipe="delete">
                                        Delete
                                    </a>                     
                                </td>
                            
                            </tr>
                        <?php endforeach;?>
                        <?php endif;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="clearfix">
            <!-- <div class="col-sm-12"> -->
                <button type="button" href="" onClick="addRow()" class="btn btn-primary ink-reaction pull-left">
                    Add
                </button>
            <!-- </div> -->
        </div>
        <hr>

        <div class="clearfix">
            <div class="pull-right">
                <button type="submit" id="submit_button" class="btn btn-primary">Next</button>
            </div>

            <button type="button" class="btn btn-default" onclick="popupClose()">Cancel</button>
        </div>
    </form>

    <div class="clearfix"></div>
    <hr>

    <p>
        Material Resource Planning - PT Bali Widya Dirgantara
    </p>
    <table class="table-row-item hide">
        <tbody>
            <tr>            
                <td class="remarks item-list">
                    <input type="text" name="expense_name[]" class="form-control">
                </td>  
                <td class="fix item-list">
                    <select name="fix[]" class="form-control" required>
                        <option value="false">Amount Dapat Berubah</option>
                        <option value="true">Amount Tetap</option>
                    </select>
                </td>  
                <td class="item-list" style="text-align:center;">
                    <a  href="javascript:;" title="Delete" class="btn btn-danger btn-xs btn-row-delete-item" data-tipe="delete">
                        Delete
                    </a>                     
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php endblock() ?>

<?php startblock('simple_styles') ?>
<?= link_tag('themes/material/assets/css/theme-default/libs/toastr/toastr.css') ?>
<?php endblock() ?>

<?php startblock('simple_scripts') ?>
<?= html_script('themes/material/assets/js/libs/jquery-validation/dist/jquery.validate.min.js') ?>
<?= html_script('themes/material/assets/js/libs/jquery-validation/dist/additional-methods.min.js') ?>
<?= html_script('themes/material/assets/js/libs/toastr/toastr.js') ?>
<script>
    btn_row_delete_item();
    function addRow() {
        var row_payment = $('.table-row-item tbody').html();
        var el = $(row_payment);
        $('#table-document tbody').append(el);

        btn_row_delete_item();
    }
    function btn_row_delete_item() {
        $('.btn-row-delete-item').click(function () {
            $(this).parents('tr').remove();
        });
    }

    $(function() {
        

        $('#submit_button').on('click', function(e) {
            e.preventDefault();

            var button = $(this);
            var form = $('#form_add_vendor');
            var action = form.attr('action');

            button.prop('disabled', true);

            if (form.valid()) {
                console.log('form is valid!');
                $.post(action, form.serialize()).done(function(data) {
                    var obj = $.parseJSON(data);

                    if (obj.success == false) {
                        toastr.options.timeOut = 10000;
                        toastr.options.positionClass = 'toast-top-right';
                        toastr.error(obj.message);
                    } else {
                        window.location.href = '<?= site_url($module['route'] . '/add_level'); ?>';
                        // refreshParent();
                        // popupClose();
                    }
                });
            }

            button.prop('disabled', false);
        });
    });
</script>
<?php endblock() ?>