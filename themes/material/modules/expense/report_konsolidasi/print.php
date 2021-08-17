<?php
if ($tipe == 'excel') {
    header("Content-type: application/octet-stream");

    header("Content-Disposition: attachment; filename=" . $title . ".xls");

    header("Pragma: no-cache");

    header("Expires: 0");
}

?>
<!DOCTYPE html>
<html>

<head>
    <title><?= $title; ?></title>
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
            font-family: Tahoma;
            font-size: 14px;
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

    <script>
        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
</head>

<body>
    <?php if ($tipe != 'excel') : ?>
        <div class="container-fluid">
            <button class='btn btn-success pull-right float' onclick="printDiv('printMe')">
                <i class="md md-print"></i> Print </button>
        </div>
    <?php endif; ?>
    <div class="container-fluid" id='printMe'>
        <div class="row">
            <div class="col-xs-12">
                <table>
                    <tr>
                        <td>
                            <h3 class="title"><?= $title; ?></h3>
                        </td>
                    </tr>
                </table>
                <hr>
                <br>
                <div class="portlet light ">
                    <table class="tg" width="100%">
                        <thead>
                                            <tr>
                                                <th rowspan="2">CC Code</th>
                                                <th rowspan="2">CC Name</th>
                                                <?php for ($i=1;$i<=find_budget_setting('Active Month');$i++) : ?>
                                                <th rowspan="2"><?= getMonthName($i);?> <?= find_budget_setting('Active Year'); ?> (A)</th>
                                                <th rowspan="2"><?= getMonthName($i);?> <?= find_budget_setting('Active Year'); ?> (B)</th>
                                                <th colspan="2">A vs B</th>
                                                <th rowspan="2">Ytd <?= getMonthName($i);?> <?= find_budget_setting('Active Year'); ?> (A)</th>
                                                <th rowspan="2">Ytd <?= getMonthName($i);?> <?= find_budget_setting('Active Year'); ?> (B)</th>
                                                <th colspan="2">A vs B</th>
                                                <?php endfor; ?>
                                                <th rowspan="2"><?= find_budget_setting('Active Year'); ?> (B)</th>
                                                <th colspan="2">Rest Of Budget</th>
                                            </tr>
                                            <tr>
                                                <?php for ($i=1;$i<=find_budget_setting('Active Month');$i++) : ?>
                                                <th>Rp</th>
                                                <th>%</th>
                                                <th>Rp</th>
                                                <th>%</th>                                             
                                                <?php endfor; ?>
                                                <th>Rp</th>
                                                <th>%</th>    
                                            </tr>
                        </thead>
                        <tbody id="listView">
                            <?php foreach ($items as $i => $cost_center) : ?>
                            <tr>
                                <td><?= $cost_center['cc_code'];?></td>
                                <td><?= $cost_center['cost_center_name'];?></td>
                                <?php for ($i=1;$i<=find_budget_setting('Active Month');$i++) : ?>    
                                <td><?= print_number($cost_center[$i.'-actual']);?></td>
                                <td><?= print_number($cost_center[$i.'-budget']);?></td> 
                                <td><?= print_number($cost_center[$i.'-mtd-ab-rp']);?></td> 
                                <td><?= print_number($cost_center[$i.'-mtd-ab-persen']);?>%</td> 
                                <td><?= print_number($cost_center[$i.'-ytd-actual']);?></td> 
                                <td><?= print_number($cost_center[$i.'-ytd-budget']);?></td> 
                                <td><?= print_number($cost_center[$i.'-ytd-ab-rp']);?></td> 
                                <td><?= print_number($cost_center[$i.'-ytd-ab-persen']);?>%</td> 
                                <?php endfor; ?>
                                <td><?= print_number($cost_center['budget_year']);?></td>
                                <td><?= print_number($cost_center['budget_rest']);?></td>
                                <td><?= print_number($cost_center['budget_rest_persen']);?>%</td>
                            </tr>

                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
</body>

</html>