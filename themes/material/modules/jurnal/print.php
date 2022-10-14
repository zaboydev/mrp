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
                /*font-family: "Tahoma";*/
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
        <!-- <div class="container-fluid">
            <button class='btn btn-success pull-right float' onclick="printDiv('printMe')">
                <i class="md md-print"></i> Print </button>
        </div> -->
    <?php endif; ?>
    <div class="container-fluid" id='printMe'>
        <div class="row">
            <div class="col-xs-12">
                <!-- <table>
                    <tr>
                        <td colspan="6">
                            <h3><?= $title; ?></h3>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <h4><?= $periode; ?></h4>
                        </td>
                    </tr>
                </table> -->
                <h3 class="title"><?= $title; ?></h3>
                <h5 class="periode"><?= $periode; ?></h5>   
                <hr>
                <div class="portlet light ">
                    <table class="table" id="table-document">
                        <thead class="table-header">
                            <tr>
                                <th></th>
                                <th>Date</th>
                                <th>Document Number</th>
                                <th>Account</th>
                                <th>Debit</th>
                                <th>Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($entity as $i => $jurnal) : ?>
                            <tr>
                                <td></td>
                                <td><?= print_date($jurnal['tanggal_jurnal'],'d M Y'); ?></td>
                                <td colspan="4"><?= print_string($jurnal['keterangan']); ?></td>
                                
                            </tr>
                            <?php foreach ($jurnal['items'] as $j => $item) : ?>
                            <tr>
                                <td></td>
                                <td colspan="2" style="text-align:right;"><?= print_string($jurnal['no_jurnal']); ?></td>
                                <td><?= print_string($item['kode_rekening']); ?> <?= print_string($item['jenis_transaksi']); ?></td>
                                <td><?= print_number($item['trs_debet'],2); ?></td>
                                <td><?= print_number($item['trs_kredit'],2); ?></td>
                            </tr>
                            <?php endforeach;?>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</body>

</html>