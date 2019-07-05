<?php
/**
 * Created by PhpStorm.
 * User: imann
 * Date: 20/04/2016
 * Time: 1:39
 */
?>
<script>
    $( document ).ready(function(){
        $("#table-data").DataTable({
            order: [[0,"asc"]],
            scrollCollapse: true,
            scrollY: 320,
            fixedHeader: true,
            scrollX: "100%",
            fixedColumns: {
	            leftColumns: 2
	        },
            paging: false
            // dom: '<"top"Bf<"clear">>rt<"bottom"lp<"clear">>'
        });

        // $('.dataTables_length').addClass('pull-left');
    });
</script>
