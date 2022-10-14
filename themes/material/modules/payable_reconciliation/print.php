<?php
if ($tipe == 'excel') {
    header("Content-type: application/octet-stream");

    header("Content-Disposition: attachment; filename=" .$title_export.".xls");

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
                            <td>Vendor : <?= strtoupper($vendor); ?></td>
                        </tr>
                        <tr>
                            <td>Currency : <?= $currency; ?></td>
                        </tr>
                    </table>
                    <table class="table">
                        <thead class="table-header">
                            <tr>
                                <th class="middle-alignment">No</th>
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
                            <?php 
                                $no = 1; 
                                $total_due_date = array();
                                $total_a = array();
                                $total_b = array();
                                $total_c = array();
                                $total_d = array();
                            ?>
                            <?php foreach ($items as $i => $detail) : ?>
                                <?php
                                    $total_qty = array();
                                    $total_amount = array();
                                    $a = 0;
                                    $b = 0;
                                    $c = 0;
                                    $d = 0; 
                                ?>
                                <?php 
                                    if ($detail['currency'] == 'USD') {
                                        $total_due = $detail['usd'];
                                    } else {
                                        $total_due = $detail['idr'];
                                    }
                                ?>
                                <?php if ($total_due > 0) : ?>
                                <?php foreach ($detail['po']['po_detail'] as $i => $info) : ?>
                                    <?php
                                        if ($info['ket'] < 31) {
                                            $a += $info['a'];
                                        }
                                        if ($info['ket'] >= 31 && $info['ket'] <= 60) {
                                            $b += $info['a'];
                                        }
                                        if ($info['ket'] >= 61 && $info['ket'] <= 90) {
                                            $c += $info['a'];
                                        }
                                        if ($info['ket'] > 90) {
                                            $d += $info['a'];
                                        }
                                        $total_amount[] = $info['a'];
                                    ?>
                                <?php endforeach; ?>
                                
                                    <tr>
                                        <td style="font-weight:bolder" align="left">
                                            <?= print_string($no); ?>
                                        </td>
                                        <td style="font-weight:bolder" align="left">
                                            <?= print_string($detail['vendor']); ?>
                                        </td>
                                        <td style="text-align:center;font-weight:bolder;" align="left">
                                            <?= print_string($detail['currency']); ?>
                                        </td>
                                        <td style="font-weight:bolder" align="left">
                                            
                                        
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
                                    <?php 
                                        $no++; 
                                        $total_due_date[] = $total_due - $detail['payment'];
                                        $total_a[] = $a;
                                        $total_b[] = $b;
                                        $total_c[] = $c;
                                        $total_d[] = $d;
                                    ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Total</th>
                                <th><?= print_number(array_sum($total_due_date), 2); ?></th>
                                <th><?= print_number(array_sum($total_a), 2); ?></th>
                                <th><?= print_number(array_sum($total_b), 2); ?></th>
                                <th><?= print_number(array_sum($total_c), 2); ?></th>
                                <th><?= print_number(array_sum($total_d), 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
</body>

</html>