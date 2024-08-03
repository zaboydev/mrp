<?php
if ($tipe == 'excel') {
    header("Content-type: application/octet-stream");

    header("Content-Disposition: attachment; filename=" . $title_export . ".xls");

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

        * {
            font-family: "Roboto", sans-serif, Helvetica, Arial, sans-serif;;
        }

        h3.title {
            font-size: 20px;
            text-align: center;
            margin-top:2px;
            margin-bottom: 2px;
        }

        h5.periode {
            font-size: 15px;
            color: #3d405c;
            text-align: center;
            margin-top:2px;
            margin-bottom: 2px;
        }

        h5.sub-title {
            font-size: 15px;
            color: #3d405c;
            text-align: center;
            margin-top:2px;
            margin-bottom: 2px;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
            width:100%;
        }

        .table-header {
            background: #E0F7FF;
            border-top: 2px solid #B3D7E5;
        }

        .table>thead>tr>th {
            padding: 0.6em 1em;
            font-size: 12px;
            color: #2980b9;
        }

        .table>tbody>tr>td,
        .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>tr
        {
            padding: 5px;
            line-height: 1.42857;
            vertical-align: top;
            /* border-top: 1px solid #e7ecf1; */
            border: 1px solid #e7ecf1;
        }

        .table>tbody>tr>td {                
            font-size: 11px;
        }

        .table>tfoot>tr>td {                
            font-size: 11px;
        }

        #footer {
            position: fixed;
            bottom: 0;
        }

        .text-footer {
            font-size: 10px;
            color: #777;
            text-align: center;
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
                <h3 class="title"><?= $title; ?></h3>
                <h5 class="periode">Periode : <?= $periode; ?></h5>
                <hr>
                <div class="portlet light ">
                    <table>
                        <tr>
                            <td>Item : <?= strtoupper($item); ?></td>
                        </tr>
                        <tr>
                            <td>Vendor : <?= strtoupper($vendor); ?></td>
                        </tr>
                        <tr>
                            <td>Currency : <?= $currency; ?></td>
                        </tr>
                    </table>
                    <table class="table" width="100%">
                        <thead class="table-header">
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