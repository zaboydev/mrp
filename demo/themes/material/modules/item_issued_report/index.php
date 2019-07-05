<?php
/**
 * @var $entities array
 * @var $auth_role
 * @var $locations
 */
?>

<ul class="nav nav-pills nav-stacked" id="report-menu">
    <li>
        <a href="<?=site_url('item_issued_report/general');?>" target="_blank">
            General Report
        </a>
    </li>
    <li>
        <a data-toggle="collapse" data-parent="#report-menu" href="#warehouse" aria-expanded="true" aria-controls="warehouse">
            Issued By Base Report
            <span class="caret pull-right"></span>
        </a>
        <div id="warehouse" class="collapse" role="accordion" aria-labelledby="warehouse">
            <ul class="nav nav-pills nav-stacked" style="padding: 10px 20px;">
                <?php foreach ($warehouses as $warehouse):?>
                    <li>
                        <a href="<?=site_url('item_issued_report/warehouse/'. $warehouse['code']);?>" target="_blank">
                            <?=$warehouse['code'];?>
                        </a>
                    </li>
                <?php endforeach;?>
            </ul>
        </div>
    </li>
    <li>
        <a data-toggle="collapse" data-parent="#report-menu" href="#aircraft" aria-expanded="false" aria-controls="aircraft">
            Issued By Aircraft Report
            <span class="caret pull-right"></span>
        </a>
        <div id="aircraft" class="collapse" role="accordion" aria-labelledby="aircraft">
            <ul class="nav nav-pills" style="margin: 10px 20px;">
                <?php foreach ($aircrafts as $aircraft):?>
                    <li>
                        <a href="<?=site_url('item_issued_report/aircraft/'. $aircraft['code']);?>" target="_blank">
                            <?=$aircraft['code'];?>
                        </a>
                    </li>
                <?php endforeach;?>
            </ul>
        </div>
    </li>
</ul>
