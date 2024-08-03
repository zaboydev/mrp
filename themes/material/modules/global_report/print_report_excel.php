<?php 

header("Content-type: application/octet-stream");

header("Content-Disposition: attachment; filename=".$title.".xls");

header("Pragma: no-cache");

header("Expires: 0");

?>
<style type="text/css">
    .tg  {border-collapse:collapse;border-spacing:0;border-color:#ccc;width: 100%; }
            .tg td{font-family:Arial;font-size:13px;padding:3px 3px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#000;color:#333;background-color:#fff;}
            .tg th{font-family:Arial;font-size:14px;font-weight:bold;padding:3px 3px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#000;color:#333;background-color:#f0f0f0;}
            .tg .tg-3wr7{font-weight:bold;font-size:12px;font-family:"Arial", Helvetica, sans-serif !important;;text-align:center}
            .tg .tg-ti5e{font-size:10px;font-family:"Arial", Helvetica, sans-serif !important;;text-align:center}
            .tg .tg-rv4w{font-size:10px;font-family:"Arial", Helvetica, sans-serif !important;}
</style>
<table>
    <tr><td>Budget vs Purchase Order</td></tr>
    <tr><td><?=date('M Y', strtotime($time));?></td></tr>
</table>
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
                    
