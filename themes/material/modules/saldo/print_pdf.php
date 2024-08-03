<style>
  @media print {
    .new-page {
      page-break-before: always;
    }
  }
</style>
<table class="table-no-strip condensed">
    <tr>
        <td>TRANSACTION NO </td>
        <th>: <?= print_string($entity['transaction_number']); ?></th>
        <td>DATE</td>
        <th>: <?= print_date($entity['date']); ?></th>
        <td>Last Update at</td>
        <th>: <?= print_date($entity['created_at']); ?></th>
        <td>Last Update by</td>
        <th>: <?= print_string($entity['created_by']); ?></th>
    </tr>
    
</table>

<div class="clear"></div>

<table class="table" style="margin-top: 20px;" width="100%">
    <thead>
        <tr>
            <th style="text-align: center;">No</th>
            <th style="text-align: center;">Account</th>
            <th style="text-align: center;">Debit</th>
            <th style="text-align: center;">Credit</th>
        </tr>
    </thead>
    <tbody>
    <?php $n = 0; $total_amount = array();?>
    <?php foreach ($entity['items'] as $i => $item):?>
        <?php  $n++; ?>
        <tr>
            <td align="right">
                <?=print_number($n);?>
            </td>
            <td>
                (<?=print_string($item['coa']);?>) <?=print_string($item['group']);?>
            </td>                  
            <td align="right">
                <?=print_number($item['debit'], 2);?>
            </td> 
            <td align="right">
                <?=print_number($item['kredit'], 2);?>
            </td>                  
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

<div class="clear"></div>

<?= (empty($entity['notes'])) ? '' : '<p>Note: ' . nl2br($entity['notes']) . '</p>'; ?>

<div class="clear"></div>
