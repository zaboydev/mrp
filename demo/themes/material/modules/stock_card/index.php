<table data-provide="datatable" data-source="<?=$module['route'] .'/index_provider';?>">
  <thead>
    <tr>
      <?php foreach ($grid['column'] as $column):?>
        <th><?=$column;?></th>
      <?php endforeach;?>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th></th>
      <th>Total</th>

      <?php for ($i = 2; $i < count($grid['column']); $i++):?>
        <th></th>
      <?php endfor;?>
    </tr>
  </tfoot>
</table>
