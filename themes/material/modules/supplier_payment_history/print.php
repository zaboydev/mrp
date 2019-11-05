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
                        <td></td>
                    </tr>
                </table>
                <hr>
                <br>
                <div class="portlet light ">
                    <table class="tg" width="100%">
                        <thead>
                            <tr>
                                <th width="15%" class="middle-alignment" style="text-align:center">Cheque #</th>
                                <th width="15%" class="middle-alignment" style="text-align:center">Chq Date</th>
                                <th width="15%" class="middle-alignment" style="text-align:center">PO #</th>
                                <th width="15%" class="middle-alignment" style="text-align:center">Date</th>
                                <th width="15%" class="middle-alignment" style="text-align:center">Purchase Total Amount</th>
                                <th width="15%" class="middle-alignment" style="text-align:center">Amount Applied</th>
                            </tr>
                        </thead>
                        <tbody id="listView">
                            <?php $no = 1; ?>
                            <?php foreach ($items as $i => $detail) : ?>
                                <?php $n++; ?>
                                <?php
                                    $total_remaining = array();
                                    $total_amount = array();
                                    ?>
                                <?php if ($detail['po']['po_count'] > 0) : ?>
                                    <tr>
                                        <td align="left">
                                            <?= print_string($detail['vendor']); ?>
                                        </td>
                                    </tr>
                                    <?php foreach ($detail['po']['po_detail'] as $i => $info) : ?>
                                        <?php
                                                    $total_remaining_detail = array();
                                                    $total_amount_detail = array();
                                                    ?>
                                        <tr>
                                            <td>
                                                &nbsp;&nbsp;&nbsp;&nbsp;<?= print_string($info['no_cheque']); ?>
                                            </td>
                                            <td>
                                                <?= print_date($info['tanggal']); ?>
                                            </td>
                                            <td>
                                                <?= print_string($info['document_number']); ?>
                                            </td>
                                            <td>
                                                <?= print_date($info['document_date']); ?>
                                            </td>
                                            <td>
                                                <?= print_number($info['grand_total'], 2); ?>
                                            </td>
                                            <td>
                                                <?= print_number($info['amount_paid'], 2); ?>
                                            </td>

                                        </tr>
                                        <?php
                                                    // $total_remaining[] = $info['remaining_payment'];
                                                    $total_amount[] = $info['amount_paid'];
                                                    // $total_remaining_detail[] = $info['remaining_payment'];
                                                    // $total_amount_detail[] = $info['grand_total'];
                                                    ?>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="5" align="right" style="font-weight:bolder">Total Payment</td>
                                        <!-- <td style="font-weight:bolder"></td>
            <td style="font-weight:bolder">&nbsp;</td> -->
                                        <td style="font-weight:bolder"><?= print_number(array_sum($total_amount), 2); ?></td>
                                        <!-- <td colspan="2"></td> -->
                                    </tr>
                                <?php endif; ?>


                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
</body>

</html>