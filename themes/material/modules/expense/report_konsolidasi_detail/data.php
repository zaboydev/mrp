
<?php foreach ($items as $i => $account) : ?>
<tr>
    <td><?= $account['account_code'];?></td>
    <td><?= $account['account_name'];?></td>
    <?php foreach ($account['annual_cost_centers'] as $i => $cost_center) : ?>
    <td><?= print_number($cost_center['ytd_actual']);?></td>
    <?php
        $total_actual[$cost_center['id']][] = $cost_center['ytd_actual'];
    ?>
    <?php endforeach; ?>
    <?php foreach ($account['annual_cost_centers'] as $i => $cost_center) : ?>
    <td><?= print_number($cost_center['ytd_budget']);?></td>
    <?php
        $total_budget[$cost_center['id']][] = $cost_center['ytd_budget'];
    ?>
    <?php endforeach; ?>
</tr>
<?php endforeach; ?>

<tr>
    <th colspan="2">Total</th>
    <?php foreach ($account['annual_cost_centers'] as $i => $cost_center) : ?>
    <th><?= print_number(array_sum($total_actual[$cost_center['id']]))?></th>
    <?php endforeach; ?>
    <?php foreach ($account['annual_cost_centers'] as $i => $cost_center) : ?>
    <th><?= print_number(array_sum($total_budget[$cost_center['id']]))?></th>
    <?php endforeach; ?>
</tr>