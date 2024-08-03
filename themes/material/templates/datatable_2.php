<div class="card-body no-padding">
  <table data-provide="datatable_2">
    <thead>
      <tr>
        <?php foreach ($grid['column_2'] as $column):?>
          <th><?=$column;?></th>
        <?php endforeach;?>
      </tr>
    </thead>

    <?php if ($grid['summary_columns'] !== NULL):?>
      <tfoot>
        <tr>
          <th></th>
          <th>Total</th>

          <?php for ($i = 2; $i < count($grid['column_2']); $i++):?>
            <th></th>
          <?php endfor;?>
        </tr>
      </tfoot>
    <?php endif;?>
  </table>
</div>
