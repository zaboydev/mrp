<?php $no = 1; ?>
<?php foreach ($po as $detail) : ?>
    <tr id="row_<?= $no ?>">
        <td><input id="sel_<?= $no ?>" value="<?= $detail['id'] ?>" type="hidden"><?= print_string($detail['document_number']) ?></td>
        <td><?= print_string($detail['status']) ?></td>
        <td><?= print_number($detail['grand_total'], 2) ?></td>
        <td><?= print_number($detail['payment'], 2) ?></td>
        <td><input id="sis_<?= $no ?>" value="<?= $detail['remaining_payment'] ?>" type="hidden"><?= print_number($detail['remaining_payment'], 2) ?></td>
        <td><input id="in_<?= $no ?>" data-row="<?= $no ?>" type="number" class="sel_applied" value="0"></td>
        <?php $no++; ?>
    </tr>
<?php endforeach; ?>