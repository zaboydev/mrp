<!DOCTYPE html>
<html>
    <head>
        <style>
            .float{
                position:fixed;
                width:60px;
                height:60px;
                bottom:40px;
                right:40px;
                border-radius:50px;
                text-align:center;
                box-shadow: 2px 2px 3px #999;
                z-index: 100000;
            }
            .my-float{
                margin-top:22px;
            }
            .tg  {border-collapse:collapse;border-spacing:0;border-color:#ccc;width: 100%; }
            .tg td{font-family:Arial;font-size:13px;padding:3px 3px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#000;color:#333;background-color:#fff;}
            .tg th{font-family:Arial;font-size:14px;font-weight:bold;padding:3px 3px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#000;color:#333;background-color:#f0f0f0;}
            .tg .tg-3wr7{font-weight:bold;font-size:12px;font-family:"Arial", Helvetica, sans-serif !important;;text-align:center}
            .tg .tg-ti5e{font-size:10px;font-family:"Arial", Helvetica, sans-serif !important;;text-align:center}
            .tg .tg-rv4w{font-size:10px;font-family:"Arial", Helvetica, sans-serif !important;}
            .box {
                background-color: white;
                width: auto;
                height: auto;
                border: 1px solid black;
                padding: 5px;
                margin: 2px;
            }
            .tt td{font-family:Arial;font-size:12px;padding:3px 3px;border-width:1px;overflow:hidden;word-break:normal;border-color:#000;color:#333;background-color:#fff;}
            .tt th{font-family:Arial;font-size:13px;font-weight:bold;padding:3px 3px;border-width:1px;overflow:hidden;word-break:normal;border-color:#000;color:#333;background-color:#f0f0f0;}
            @media print {
                html, body {
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
            function printDiv(divName){
                var printContents = document.getElementById(divName).innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
            }
        </script>
    </head>
    <body>
        <div class="container-fluid">
            <button class='btn btn-success pull-right float' onclick="printDiv('printMe')">
            <i class="md md-print"></i> Print </button>
        </div>
        <div class="container-fluid" id='printMe'>
            <div class="row">
                <div class="col-xs-12">
                <table>
                    <tr><td><h3>Budgeting</h3></td></tr>
                    <tr><td><?=date('M Y', strtotime($time));?></td></tr>
                </table>
                <hr>
                <br>
                <div class="portlet light ">                    
                    <table class="tg" width="100%">
                        <thead id="table_header">
                            <tr>
                                <!-- <th rowspan="3" class="text-center">Act</th> -->
                                <th rowspan="3" class="text-center">No</th>
                                <th rowspan="3" class="text-center">Part Number</th>
                                <th rowspan="3" class="text-center">Description</th>
                                <th colspan="6" class="text-center">Month to Date</th>
                                <th colspan="6" class="text-center">Year to Date</th>
                            </tr>
                            <tr>
                                <th colspan="2" class="text-center">Budget</th>
                                <th colspan="2" class="text-center">PO</th>
                                <th colspan="2" class="text-center">Balance</th>
                                <th colspan="2" class="text-center">Budget</th>
                                <th colspan="2" class="text-center">PO</th>
                                <th colspan="2" class="text-center">Balance</th>
                                <!-- <th colspan="2" class="text-center">Plan</th> -->
                                <!-- <th colspan="2" class="text-center">Balance</th> -->
                            </tr>
                            <tr>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Price</th>
                                <!-- <th class="text-center">Qty</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Price</th> -->
                            </tr>
                        </thead>
                        <tbody><?php $no=1;?>
                            <?php foreach ($data_budget as $item):?>
                                <tr>
                                    <td style="font-size: 10px;"><?=print_string($no++);?></td>
                                    <td style="font-size: 10px;"><?=print_string($item['item_part_number']);?></td>
                                    <td style="font-size: 10px;"><?=print_string($item['description']);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item['mtd_quantity'],2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item['mtd_budget'],2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($mtd_qty[$item['item_part_number']],2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($mtd_val[$item['item_part_number']],2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item['mtd_quantity']-$mtd_qty[$item['item_part_number']],2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item['mtd_budget']-$mtd_val[$item['item_part_number']],2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item['ytd_quantity'],2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item['ytd_budget'],2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($ytd_qty[$item['item_part_number']],2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($ytd_val[$item['item_part_number']],2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item['ytd_quantity']-$ytd_qty[$item['item_part_number']],2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item['ytd_budget']-$ytd_val[$item['item_part_number']],2);?></td>
                                    
                                </tr>                                
                            <?php endforeach; ?>
                        </tbody>                       
                    </table> 
                    <br><br>                    
                </div>                
            </div>
        </div>
    </body>
</html>
