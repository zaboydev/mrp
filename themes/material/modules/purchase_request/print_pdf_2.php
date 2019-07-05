<table class="table-no-strip condensed">
  <tr>
    <td>Request By</td>
    <th widtd="40%">: <?=print_person_name($entity['created_by']);?></th>
    <td>PR No.</td>
    <th>: <?=print_string($entity['pr_number']);?></th>
  </tr>
  <tr>
    <td>Inventory</td>
    <th>: <?=print_string($entity['category_name']);?></th>
    <td>PR Date.</td>
    <th>: <?=print_date($entity['pr_date']);?></th>
  </tr>
  <tr>
    <td>Suggested Supplier</td>
    <th>: <?=print_string($entity['suggested_supplier']);?></th>
    <td>Required Date</td>
    <th>: <?=print_date($entity['required_date']);?></th>
  </tr>
  <tr>
    <td>Deliver to</td>
    <th>: <?=print_string($entity['deliver_to']);?></th>
    <td>Status</td>
    <th>: <?=($entity['status'] == 'approved') ? 'BUDGETED' : strtoupper($entity['status']);?></th>
  </tr>
</table>

<div class="clear"></div>

<table class="table" style="margin-top: 20px;">
  <thead>
    <tr>
      <th align="right" width="1">No</th>
      <th>Description</th>
      <th>Part Number</th>
      <th align="right" width="1">Qty</th>
      <th width="1">Unit</th>
      <th align="right" width="1">Price</th>
      <th align="right" width="1">Subtotal</th>
    </tr>
  </thead>
  <tbody>
    <?php $n = 0;?>
    <?php $grand_total = array();?>
    <?php foreach ($entity['items'] as $i => $detail):?>
      <?php $n++;?>
      <?php $grand_total[] = $detail['total'];?>
      <tr>
        <td align="right">
          <?=print_number($n);?>
        </td>
        <td>
          <?=print_string($detail['product_name']);?>
        </td>
        <td>
          <?=print_string($detail['part_number']);?>
        </td>
        <td align="right">
          <?=print_number($detail['quantity'], 2);?>
        </td>
        <td>
          <?=print_string($detail['unit']);?>
        </td>
        <td align="right">
          <?=print_number($detail['price'], 2);?>
        </td>
        <td align="right">
          <?=print_number($detail['total'], 2);?>
        </td>
      </tr>
    <?php endforeach;?>
  </tbody>
  <tfoot>
    <tr>
      <th></th>
      <th>Total</th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th align="right"><?=print_number(array_sum($grand_total), 2);?></th>
    </tr>
  </tfoot>
</table>

<div class="clear"></div>

<?=(empty($entity['notes'])) ? '' : '<p>Note: '.nl2br($entity['notes']).'</p>';?>

<div class="clear"></div>

<table class="condensed" style="margin-top: 20px;">
  <tr>
    <td width="25%" valign="top" align="center">
      <p>Request by:</p>
    </td>
    <td width="25%" valign="top" align="center">
      <p>Approved by:</p>
    </td>
    <td width="25%" valign="top" align="center">
      <p>Approved by:</p>
    </td>
    <td width="25%" valign="top" align="center">
      <p>Acknowledged by:</p>
    </td>
  </tr>
</table>
