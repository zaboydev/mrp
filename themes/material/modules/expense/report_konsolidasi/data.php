<?php $no = 1; ?>
<?php
$grand_total_remaining_idr = array();
$grand_total_amount_idr = array();
$grand_total_remaining_usd = array();
$grand_total_amount_usd = array();
?>
<?php foreach ($items as $i => $cost_center) : ?>
<tr>
    <td><?= $cost_center['cc_code'];?></td>
    <td><?= $cost_center['cost_center_name'];?></td>
    <?php for ($i=1;$i<=find_budget_setting('Active Month');$i++) : ?>    
    <td><?= print_number($cost_center[$i.'-actual']);?></td>
    <td><?= print_number($cost_center[$i.'-budget']);?></td> 
    <td><?= print_number($cost_center[$i.'-mtd-ab-rp']);?></td> 
    <td><?= $cost_center[$i.'-mtd-ab-persen'];?>%</td> 
    <td><?= print_number($cost_center[$i.'-ytd-actual']);?></td> 
    <td><?= print_number($cost_center[$i.'-ytd-budget']);?></td> 
    <td><?= print_number($cost_center[$i.'-ytd-ab-rp']);?></td> 
    <td><?= $cost_center[$i.'-ytd-ab-persen'];?>%</td> 
    <?php endfor; ?>
    <td><?= print_number($cost_center['budget_year']);?></td>
    <td><?= print_number($cost_center['budget_rest']);?></td>
    <td><?= $cost_center['budget_rest_persen'];?>%</td>
</tr>

<?php endforeach; ?>