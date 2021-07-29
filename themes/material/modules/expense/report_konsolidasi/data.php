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
    <td><?= $cost_center[$i.'-budget'];?></td>
    <?php endfor; ?>
</tr>

<?php endforeach; ?>