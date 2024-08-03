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
                    <tr><td>2019</td></tr>
                </table>
                <hr>
                <br>
                <div class="portlet light ">
                    
                    <table class="tg" width="100%">
                        <thead>
                            <tr class="success">
                                <th width="3px" style="font-size: 12px;"><center> No </center></th>
                                <th width="10px" style="font-size: 12px;"><center> Item </center></th>
                                <th style="font-size: 12px;"><center> Part number </center></th>
                                <th style="font-size: 12px;"><center> Year </center></th>
                                <th style="font-size: 12px;"><center> Price </center></th>
                                <th style="font-size: 12px;"><center> Onhand stock </center></th>
                                <th style="font-size: 12px;"><center> Jan val </center></th>
                                <th style="font-size: 12px;"><center> Jan qty </center></th>
                                <th style="font-size: 12px;"><center> Feb val </center></th>
                                <th style="font-size: 12px;"><center> Feb qty </center></th>
                                <th style="font-size: 12px;"><center> Mar val </center></th>
                                <th style="font-size: 12px;"><center> Mar qty </center></th>
                                <th style="font-size: 12px;"><center> Apr val </center></th>
                                <th style="font-size: 12px;"><center> Apr qty </center></th>
                                <th style="font-size: 12px;"><center> Mei val </center></th>
                                <th style="font-size: 12px;"><center> Mei qty </center></th>
                                <th style="font-size: 12px;"><center> Jun val </center></th>
                                <th style="font-size: 12px;"><center> Jun qty </center></th>
                                <th style="font-size: 12px;"><center> Jul val </center></th>
                                <th style="font-size: 12px;"><center> Jul qty </center></th>
                                <th style="font-size: 12px;"><center> Ags val </center></th>
                                <th style="font-size: 12px;"><center> Ags qty </center></th>
                                <th style="font-size: 12px;"><center> Sep val </center></th>
                                <th style="font-size: 12px;"><center> Sep qty </center></th>
                                <th style="font-size: 12px;"><center> Okt val </center></th>
                                <th style="font-size: 12px;"><center> Okt qty </center></th>
                                <th style="font-size: 12px;"><center> Nov val </center></th>
                                <th style="font-size: 12px;"><center> Nov qty </center></th>
                                <th style="font-size: 12px;"><center> Des val </center></th>
                                <th style="font-size: 12px;"><center> Des qty </center></th>
                                <th style="font-size: 12px;"><center> Total val </center></th>
                                <th style="font-size: 12px;"><center> Total qty </center></th> 
                                <th style="font-size: 12px;"><center> Prepared By </center></th>                                
                            </tr>
                        </thead>
                        <tbody><?php $no=1;?>
                            <?php foreach ($data_budget as $item):?>
                                <tr>
                                    <td style="font-size: 10px;"><?=print_string($no++);?></td>
                                    <td style="font-size: 10px;"><?=print_string($item->item_description);?></td>
                                    <td style="font-size: 10px;"><?=print_string($item->part_number);?></td>
                                    <td style="font-size: 10px;"><?=print_string($item->year);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->current_price,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->onhand,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->jan_val,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->jan_qty,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->feb_val,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->feb_qty,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->mar_val,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->mar_qty,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->apr_val,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->apr_qty,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->mei_val,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->mei_qty,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->jun_val,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->jun_qty,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->jul_val,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->jul_qty,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->ags_val,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->ags_qty,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->sep_val,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->sep_qty,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->okt_val,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->okt_qty,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->nov_val,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->nov_qty,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->des_val,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->des_qty,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->total_val,2);?></td>
                                    <td style="font-size: 10px;"><?=print_number($item->total_qty,2);?></td>
                                    <td style="font-size: 10px;"><?=print_string($item->prepared_by);?></td>
                                </tr>                                
                            <?php endforeach; ?>
                        </tbody>                       
                    </table> 
                    <br><br>
                    <table width="100%">
                        <tr>
                            <td style="font-size: 12px;" width="50%" align="center" style="font-family: Arial;">Prepared By,</td>
                            <td style="font-size: 12px;" width="50%" align="center" style="font-family: Arial;">Approved By,</td>
                        </tr>
                         <tr>
                            <td style="font-size: 12px;" width="50%" align="center" style="font-family: Arial;">
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <?=get_user(12);?>
                            </td>
                            <td style="font-size: 12px;" width="50%" align="center" style="font-family: Arial;">
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <?=get_user(9);?>
                            </td>
                        </tr>
                    </table>                      
                </div>                
            </div>
        </div>
    </body>
</html>
