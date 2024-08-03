<link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/plugins/DataTables/datatables.min.css');?>"> 
<script src="<?=base_url('themes/admin_lte/plugins/DataTables/datatables.min.js');?>"></script>

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
    });
</script>
