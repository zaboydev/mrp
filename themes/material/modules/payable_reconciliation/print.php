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
                    <table class="tg">
                        <thead>
                            <tr>
                                <th width="15%" class="middle-alignment">Name</th>
                                <th width="10%" class="middle-alignment">Currency</th>
                                <th width="15%" class="middle-alignment">Total Due</th>
                                <th width="15%" class="middle-alignment">0-30 Days</th>
                                <th width="15%" class="middle-alignment">31-60 Days</th>
                                <th width="15%" class="middle-alignment">61-90 Days</th>
                                <th width="15%" class="middle-alignment">90+ Days</th>
                            </tr>
                        </thead>
                        <tbody id="listView">
                            <?php $no = 1; ?>
                            <?php foreach ($items as $i => $detail) : ?>
                                <?php $n++; ?>
                                <?php
                                    $total_qty = array();
                                    $total_amount = array();
                                    ?>
                                <tr>
                                    <?php $a = 0;
                                        $b = 0;
                                        $c = 0;
                                        $d = 0; ?>
                                    <?php foreach ($detail['po']['po_detail'] as $i => $info) : ?>
                                        <?php
                                                if ($info['ket'] < 31) {
                                                    $a = $info['a'];
                                                }
                                                if ($info['ket'] >= 31 && $info['ket'] <= 60) {
                                                    $b = $info['a'];
                                                }
                                                if ($info['ket'] >= 61 && $info['ket'] <= 90) {
                                                    $c = $info['a'];
                                                }
                                                if ($info['ket'] > 90) {
                                                    $d = $info['a'];
                                                }
                                                ?>
                                    <?php endforeach; ?>
                                    <td align="left">
                                        <?= print_string($detail['vendor']); ?>
                                    </td>
                                    <td align="left">
                                        <?= print_string($detail['currency']); ?>
                                    </td>
                                    <td align="left">
                                        <?php if ($detail['currency'] == 'USD') {
                                                $total_due = $detail['usd'];
                                            } else {
                                                $total_due = $detail['idr'];
                                            }
                                            ?>
                                        <?= print_number($total_due - $detail['payment'], 2); ?>

                                    </td>
                                    <td>
                                        <?= print_number($a, 2); ?>
                                    </td>
                                    <td>
                                        <?= print_number($b, 2); ?>
                                    </td>
                                    <td>
                                        <?= print_number($c, 2); ?>
                                    </td>
                                    <td>
                                        <?= print_number($d, 2); ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
</body>

</html>