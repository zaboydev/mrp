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
                                <th width="3%" rowspan="3" class="middle-alignment">No</th>
                                <th width="12%" rowspan="3" class="middle-alignment">Vendor</th>
                                <th width="16%" rowspan="2" colspan="2" class="middle-alignment">Saldo Awal</th>
                                <th width="16%" rowspan="2" colspan="2" class="middle-alignment">Pembelian</th>
                                <th width="32%" colspan="4" class="middle-alignment">Pembayaran</th>
                                <th width="5%" rowspan="2" class="middle-alignment">Adjustmetn</th>
                                <th width="16%" rowspan="2" colspan="2" class="middle-alignment">Saldo Akhir</th>
                            </tr>
                            <tr>
                                <th width="16%" colspan="2" class="middle-alignment">Cash</th>
                                <th width="16%" colspan="2" class="middle-alignment">Bank</th>
                            </tr>
                            <tr>
                                <th width="8%" class="middle-alignment">USD</th>
                                <th width="8%" class="middle-alignment">IDR</th>
                                <th width="8%" class="middle-alignment">USD</th>
                                <th width="8%" class="middle-alignment">IDR</th>
                                <th width="8%" class="middle-alignment">USD</th>
                                <th width="8%" class="middle-alignment">IDR</th>
                                <th width="8%" class="middle-alignment">USD</th>
                                <th width="8%" class="middle-alignment">IDR</th>
                                <th width="5%" class="middle-alignment">IDR</th>
                                <th width="8%" class="middle-alignment">USD</th>
                                <th width="8%" class="middle-alignment">IDR</th>
                            </tr>
                        </thead>
                        <tbody id="listView">
                            <?php $no = 0; ?>
                            <?php
                            $grand_saldo_awal_usd = array();
                            $grand_pembelian_usd = array();
                            $grand_payment_usd = array();
                            $grand_saldo_akhir_usd = array();

                            $grand_saldo_awal_idr = array();
                            $grand_pembelian_idr = array();
                            $grand_payment_idr = array();
                            $grand_saldo_akhir_idr = array();
                            ?>
                            <?php foreach ($items as $i => $detail) : ?>
                                <?php $no++; ?>
                                <?php
                                    $saldo_akhir_usd = $detail['saldo_awal_usd'] - $detail['payment_saldo_awal_usd'] + $detail['pembelian_usd'] - $detail['payment_usd'];
                                    $saldo_akhir_idr = $detail['saldo_awal_idr'] - $detail['payment_saldo_awal_idr'] + $detail['pembelian_idr'] - $detail['payment_idr'];

                                    $grand_saldo_awal_usd[]     = $detail['saldo_awal_usd'] - $detail['payment_saldo_awal_usd'];
                                    $grand_pembelian_usd[]      = $detail['pembelian_usd'];
                                    $grand_payment_usd[]        = $detail['payment_usd'];
                                    $grand_saldo_akhir_usd[]    = $saldo_akhir_usd;

                                    $grand_saldo_awal_idr[]     = $detail['saldo_awal_idr'] - $detail['payment_saldo_awal_idr'];
                                    $grand_pembelian_idr[]      = $detail['pembelian_idr'];
                                    $grand_payment_idr[]        = $detail['payment_idr'];
                                    $grand_saldo_akhir_idr[]    = $saldo_akhir_idr;
                                    ?>
                                <tr>
                                    <td style="font-weight: bolder;" align="left">
                                        <?= $no; ?>
                                    </td>
                                    <td style="font-weight: bolder;" align="left">
                                        <?= print_string($detail['vendor']); ?>
                                    </td>
                                    <td align="left">
                                        <?= print_number($detail['saldo_awal_usd'] - $detail['payment_saldo_awal_usd'], 2) ?>
                                    </td>
                                    <td align="left">
                                        <?= print_number($detail['saldo_awal_idr'] - $detail['payment_saldo_awal_usd'], 2) ?>
                                    </td>
                                    <td align="left">
                                        <?= print_number($detail['pembelian_usd'], 2) ?>
                                    </td>
                                    <td align="left">
                                        <?= print_number($detail['pembelian_idr'], 2) ?>
                                    </td>
                                    <td align="left">
                                        <?= print_number(0, 2) ?>
                                    </td>
                                    <td align="left">
                                        <?= print_number(0, 2) ?>
                                    </td>
                                    <td align="left">
                                        <?= print_number($detail['payment_usd'], 2) ?>
                                    </td>
                                    <td align="left">
                                        <?= print_number($detail['payment_idr'], 2) ?>
                                    </td>
                                    <td align="left">
                                        <?= print_number(0, 2) ?>
                                    </td>
                                    <td align="left">
                                        <?= print_number($saldo_akhir_usd, 2) ?>
                                    </td>
                                    <td align="left">
                                        <?= print_number($saldo_akhir_idr, 2) ?>
                                    </td>
                                </tr>


                            <?php endforeach; ?>
                            <tr>
                                <td colspan="2" align="right" style="font-weight:bolder">GRAND TOTAL</td>
                                <td style="font-weight:bolder"><?= print_number(array_sum($grand_saldo_awal_usd), 2); ?></td>
                                <td style="font-weight:bolder"><?= print_number(array_sum($grand_saldo_awal_idr), 2); ?></td>
                                <td style="font-weight:bolder"><?= print_number(array_sum($grand_pembelian_usd), 2); ?></td>
                                <td style="font-weight:bolder"><?= print_number(array_sum($grand_pembelian_idr), 2); ?></td>
                                <td style="font-weight:bolder"><?= print_number(0, 2); ?></td>
                                <td style="font-weight:bolder"><?= print_number(0, 2); ?></td>
                                <td style="font-weight:bolder"><?= print_number(array_sum($grand_payment_usd), 2); ?></td>
                                <td style="font-weight:bolder"><?= print_number(array_sum($grand_payment_idr), 2); ?></td>
                                <td style="font-weight:bolder"><?= print_number(0, 2); ?></td>
                                <td style="font-weight:bolder"><?= print_number(array_sum($grand_saldo_akhir_usd), 2); ?></td>
                                <td style="font-weight:bolder"><?= print_number(array_sum($grand_saldo_akhir_idr), 2); ?></td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
</body>

</html>