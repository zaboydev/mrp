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
                            <td>Periode : <?= $periode; ?></td>
                        </tr>
                        <tr>
                            <td>Vendor : <?= strtoupper($vendor); ?></td>
                        </tr>
                    </table>
                    <table class="table">
                        <thead class="table-header">
                            <tr>
                                <th class="middle-alignment">No</th>
                                <th class="middle-alignment">PO# / No Nota</th>
                                <th class="middle-alignment">Date</th>
                                <th class="middle-alignment">Amount Nota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $no = 1; 
                                $total = array();
                            ?>
                            <?php foreach ($entities as $i => $entity) : ?>
                            <tr>
                                <td style="font-weight:bold;"><?= $no++;?></td>
                                <td style="font-weight:bold;"><?= print_string($entity['document_number']);?> </td>
                                <td style="font-weight:bold;"></td>
                                <td style="font-weight:bold;"><?= print_number($entity['grand_total'],2);?></td>
                            </tr>
                            <?php foreach ($entity['receipt'] as $r => $receipt) : ?>
                            <tr>
                                <td></td>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= print_string($receipt['reference_number']);?></td>
                                <td>
                                    <?= ($receipt['tgl_nota']!=null)? print_date($receipt['tgl_nota']):'';?>
                                </td>
                                <td><?= print_number($receipt['received_total_value_idr'],2);?></td>
                            </tr>
                            <?php  
                                $total[] = $receipt['received_total_value_idr'];
                            ?>
                            <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th></th>
                                <th></th>
                                <th><?= print_number(array_sum($total), 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
</body>

</html>