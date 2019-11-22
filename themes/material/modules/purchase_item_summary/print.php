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
            font-family: Arial;
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
            font-family: Arial;
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
                            <h3><?= $title; ?></h3>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4><?= $periode; ?></h4>
                        </td>
                    </tr>
                </table>
                <hr>
                <br>
                <div class="portlet light ">
                    <table class="tg" width="100%">
                        <thead>
                            <tr>
                                <th rowspan="2" width="40%" colspan="2" class="middle-alignment" style="text-align:center">Supplier Name</th>
                                <th rowspan="2" width="10%" class="middle-alignment" style="text-align:center">Quantity</th>
                                <th rowspan="2" width="10%" class="middle-alignment" style="text-align:center">Unit</th>
                                <th colspan="2" width="40%" class="middle-alignment" style="text-align:center">Amount</th>
                            </tr>
                            <tr>
                                <th width="20%" class="middle-alignment">USD</th>
                                <th width="20%" class="middle-alignment">IDR</th>
                            </tr>
                        </thead>
                        <tbody id="listView">
                            <?php $no = 1; ?>
                            <?php
                            $grand_total_quantity = array();
                            $grand_total_amount_idr = array();
                            $grand_total_amount_usd = array();
                            ?>
                            <?php foreach ($items as $i => $detail) : ?>
                                <?php $n++; ?>
                                <?php
                                    $total_quantity = array();
                                    $total_amount_idr = array();
                                    $total_amount_usd = array();
                                    ?>
                                <?php //if ($detail['po']['po_count'] > 0) : 
                                    ?>
                                <tr>
                                    <td align="left" style="font-weight:bolder">
                                        <?= print_string($detail['part_number']); ?>
                                    </td>
                                    <td align="left" style="font-weight:bolder">
                                        <?= print_string($detail['description']); ?>
                                    </td>
                                    <td colspan="4"></td>
                                </tr>
                                <?php foreach ($detail['base'] as $i => $info_base) : ?>
                                    <tr>
                                        <td colspan="2" style="font-weight:bolder">
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= print_string($info_base['warehouse']); ?>
                                        </td>
                                        <td colspan="4"></td>
                                    </tr>
                                    <?php foreach ($info_base['items_grn']['grn_items'] as $i => $info) : ?>
                                        <td colspan="2">
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= print_string($info['received_from']); ?>
                                        </td>

                                        <td>
                                            <?= print_number($info['quantity'], 2); ?>
                                        </td>
                                        <td align="center">
                                            <?= print_string($info['unit']); ?>
                                        </td>
                                        <td>
                                            <?= $info['kurs_dollar'] > 1 ? print_number($info['total_value_usd'], 2) : print_number(0, 2); ?>
                                        </td>
                                        <td>
                                            <?= $info['kurs_dollar'] == 1 ? print_number($info['total_value_idr'], 2) : print_number(0, 2); ?>
                                        </td>

                                        </tr>
                                        <?php
                                                    if ($info['kurs_dollar'] > 1) {
                                                        $total_amount_usd[] = $info['total_value_usd'];
                                                        $grand_total_amount_usd[] = $info['total_value_usd'];
                                                    } else {
                                                        $total_amount_idr[] = $info['total_value_idr'];
                                                        $grand_total_amount_idr[] = $info['total_value_idr'];
                                                    }
                                                    $total_quantity[] = $info['quantity'];
                                                    $grand_total_quantity[] = $info['quantity'];
                                                    ?>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>

                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bolder"><?= print_string($detail['description']); ?> TOTAL</td>
                                    <td style="font-weight:bolder"><?= print_number(array_sum($total_quantity), 2); ?></td>
                                    <td style="font-weight:bolder"></td>
                                    <td style="font-weight:bolder"><?= print_number(array_sum($total_amount_usd), 2); ?></td>
                                    <td style="font-weight:bolder"><?= print_number(array_sum($total_amount_idr), 2); ?></td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f0f0f0;" colspan="6">&nbsp;</td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="2" align="right" style="font-weight:bolder">GRAND TOTAL</td>
                                <td style="font-weight:bolder"><?= print_number(array_sum($grand_total_quantity), 2); ?></td>
                                <td style="font-weight:bolder"></td>
                                <td style="font-weight:bolder"><?= print_number(array_sum($grand_total_amount_usd), 2); ?></td>
                                <td style="font-weight:bolder"><?= print_number(array_sum($grand_total_amount_idr), 2); ?></td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
</body>

</html>