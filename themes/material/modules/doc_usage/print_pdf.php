<table class="condensed">
  <tr>
    <td valign="top">
      Kepada / To
    </td>
    <td valign="top" width="1">
      :
    </td>
    <td valign="top">
      <?=nl2br($entity['destination_address']);?>
    </td>
  </tr>
  <tr>
    <td valign="top">
      Pengirim / Sent by
    </td>
    <td valign="top" width="1">
      :
    </td>
    <td valign="top">
      <?=nl2br($entity['origin_address']);?>
    </td>
  </tr>
  <tr>
    <td valign="top">
      Nomor Dokumen
    </td>
    <td valign="top" width="1">
      :
    </td>
    <td valign="top">
      <?=$entity['document_number'];?>
    </td>
  </tr>
</table>

<div class="clear"></div>

<table class="table table-striped">
  <thead>
    <tr>
      <th>No</th>
      <th>Description</th>
      <th>Part Number</th>
      <th>Serial Number</th>
      <th>Condition</th>
      <th align="right" colspan="2">Quantity</th>
      <th align="right">Unit Value</th>
      <th align="right">Total Value</th>
      <th>notes</th>
    </tr>
  </thead>
  <tbody>
    <?php $n = 0;?>
    <?php foreach ($entity['items'] as $i => $detail):?>
      <?php $n++;?>
      <?php $subtotal[$i] = $detail['quantity'] * $detail['unit_value'];?>
      <tr>
        <td class="no-space">
          <?=$n;?>
        </td>
        <td>
          <?=$detail['description'];?>
        </td>
        <td>
          <?=$detail['part_number'];?>
        </td>
        <td>
          <?=$detail['serial_number'];?>
        </td>
        <td>
          <?=$detail['condition'];?>
        </td>
        <td align="center">
          <?=number_format($detail['quantity'], 2);?>
        </td>
        <td>
          <?=$detail['unit'];?>
        </td>
        <td align="right">
          <?=number_format($detail['unit_value'], 2);?>
        </td>
        <td align="right">
          <?=number_format($subtotal[$i], 2);?>
        </td>
        <td>
          <?=$detail['notes'];?>
        </td>
      </tr>
    <?php endforeach;?>

    <tr>
      <td></td>
      <td colspan="7">Total value for Insurance purpose (not commercial value)</td>
      <td align="right"><?=number_format(array_sum($subtotal), 2);?></td>
      <td></td>
    </tr>
  </tbody>
</table>

<div class="clear"></div>

<table class="condensed">
  <tr>
    <td width="50%" valign="top">
      Dikirim oleh / Packed by:
      <br><br>Disetujui oleh / Released by:
      <br><br>Diterima oleh / Received by:
      <br><br>Tanggal diterima / Received date:
    </td>
    <td width="50%" valign="top">
      <ol>
        <li>Kembali pada pengirim / Return to sender<br>&nbsp;</li>
        <li>Bukti untuk penerima / Copy for receiver<br>&nbsp;</li>
        <li>Bukti untuk I.S.C. / Copy for I.S.C.<br>&nbsp;</li>
        <li>Bukti untuk akuntan / Copy for accounting<br>&nbsp;</li>
        <li>Bukti untuk pengirim / Copy for sender<br>&nbsp;</li>
      </ol>
    </td>
  </tr>
</table>
