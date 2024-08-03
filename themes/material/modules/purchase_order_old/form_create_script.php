<?php
/**
 * Created by PhpStorm.
 * User: imann
 * Date: 20/04/2016
 * Time: 1:39
 */
?>

<link rel="stylesheet" href="<?=base_url('themes/admin_lte/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css');?>">
<script src="<?=base_url('themes/admin_lte/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js');?>"></script>
<script src="<?=base_url('themes/admin_lte/plugins/jquery-validation/dist/jquery.validate.min.js');?>"></script>

<script>
    $( document ).ready(function(){
        $("#table-data").DataTable({
            order: [[5,"desc"]],
            scrollCollapse: true,
            scrollY: 320,
            fixedHeader: true,
            scrollX: "100%",
            fixedColumns: {
                leftColumns: 3
            },
            paging: false,
            info: false,
            filter: false
        });

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayBtn: true,
            todayHighlight: true,
            startDate: '0d'
        });

        $('#form_create').validate({
            errorClass: "text-danger",
            errorElement: "span"
        });
    });
</script>
