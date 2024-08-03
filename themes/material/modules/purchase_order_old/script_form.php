<?php
/**
 * Created by PhpStorm.
 * User: imann
 * Date: 20/04/2016
 * Time: 1:39
 *
 * @var $json_group
 */
?>
<link rel="stylesheet" href="<?=base_url('themes/admin_lte/plugins/jquery-ui-1.11.4.custom/jquery-ui.min.css');?>">
<script src="<?=base_url('themes/admin_lte/plugins/jquery-ui-1.11.4.custom/jquery-ui.min.js');?>"></script>
<script>
    $( document ).ready(function(){
        var groups = <?=$json_group;?>;
        $( "#group_name" ).autocomplete({
            source: groups
        });

        var models = <?=$json_model;?>;
        $( "#item_model" ).autocomplete({
            source: models
        });

        var measurements = <?=$json_measurement;?>;
        $( "#unit_measurement" ).autocomplete({
            source: measurements
        });
    });
</script>
