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
                    <table class="tg">
                        <thead>
                            <tr>
                                <th rowspan="2" class="middle-alignment">Name</th>
                                <th rowspan="2" class="middle-alignment">No PO</th>
                                <th rowspan="2" class="middle-alignment">Date</th>
                                <th rowspan="2" class="middle-alignment">Quantity</th>
                                <th rowspan="2" class="middle-alignment">Unit</th>
                                <th colspan="2" class="middle-alignment">Amount</th>
                                <th rowspan="2" class="middle-alignment">Status</th>
                                <th rowspan="2" class="middle-alignment">Promised Date</th>
                            </tr>
                            <tr>
                                <th class="middle-alignment">USD</th>
                                <th class="middle-alignment">IDR</th>
                            </tr>
                        </thead>
                        <tbody id="listView">
                            <?php $no = 1; ?>
                            <?php
                            $grand_total_qty = array();
                            $grand_total_amount_idr = array();
                            $grand_total_amount_usd = array();
                            ?>
                            <?php foreach ($items as $i => $detail) : ?>
                                <?php $n++; ?>
                                <?php
                                    $total_qty = array();
                                    $total_amount_idr = array();
                                    $total_amount_usd = array();
                                    ?>
                                <?php if ($detail['items_po']['po_items_count'] > 0) : ?>
                                    <tr>
                                        <td style="font-weight: bolder;" align="left">
                                            <?= print_string($detail['part_number']); ?>
                                        </td>
                                        <td style="font-weight: bolder;" align="left" colspan="8">
                                            <?= print_string($detail['description']); ?>
                                        </td>
                                    </tr>
                                    <?php foreach ($detail['items_po']['po_items'] as $i => $info) : ?>
                                        <tr>
                                            <td>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= print_string($info['vendor']); ?>
                                            </td>
                                            <td>
                                                <?= print_string($info['document_number']); ?>
                                            </td>
                                            <td>
                                                <?= print_date($info['document_date']); ?>
                                            </td>
                                            <td>
                                                <?= print_number($info['quantity'], 2); ?>
                                            </td>
                                            <td>
                                                <?= print_string($info['unit']); ?>
                                            </td>
                                            <td>
                                                <?= $info['kurs_dollar'] > 1 ? print_number($info['total_amount'], 2) : print_number(0, 2); ?>
                                            </td>
                                            <td>
                                                <?= $info['kurs_dollar'] == 1 ? print_number($info['total_amount'], 2) : print_number(0, 2); ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?= print_string($info['status']); ?>
                                            </td>
                                            <td>
                                                <?= print_date($info['due_date']); ?>
                                            </td>
                                        </tr>
                                        <?php
                                                    $total_qty[] = $info['quantity'];
                                                    $grand_total_qty[] = $info['quantity'];
                                                    if ($info['kurs_dollar'] > 1) {
                                                        $total_amount_usd[] = $info['total_amount'];
                                                        $grand_total_amount_usd[] = $info['total_amount'];
                                                    } else {
                                                        $total_amount_idr[] = $info['total_amount'];
                                                        $grand_total_amount_idr[] = $info['total_amount'];
                                                    }
                                                    ?>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="3" align="right" style="font-weight:bolder"><?= print_string($detail['description']); ?></td>
                                        <td style="font-weight:bolder"><?= print_number(array_sum($total_qty), 2); ?></td>
                                        <td style="font-weight:bolder"></td>
                                        <td style="font-weight:bolder"><?= print_number(array_sum($total_amount_usd), 2); ?></td>
                                        <td style="font-weight:bolder"><?= print_number(array_sum($total_amount_idr), 2); ?></td>
                                        <td colspan="4"></td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: #f0f0f0;" colspan="9">&nbsp;</td>
                                    </tr>
                                <?php endif; ?>


                            <?php endforeach; ?>
                            <tr>
                                <td colspan="3" align="right" style="font-weight:bolder">GRAND TOTAL</td>
                                <td style="font-weight:bolder"><?= print_number(array_sum($grand_total_qty), 2); ?></td>
                                <td style="font-weight:bolder"></td>
                                <td style="font-weight:bolder"><?= print_number(array_sum($grand_total_amount_usd), 2); ?></td>
                                <td style="font-weight:bolder"><?= print_number(array_sum($grand_total_amount_idr), 2); ?></td>
                                <td colspan="4"></td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
</body>

</html>