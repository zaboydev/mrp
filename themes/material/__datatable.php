<!-- DATATABLE -->
<div class="card">
  <div class="card-head style-primary-dark">
    <header><?=PAGE_TITLE;?></header>
    <div class="tools">
      <form class="navbar-search hidden" role="search" id="datatable-form">
        <div class="form-group">
          <input type="text" class="form-control input-sm" id="datatable-search-box" placeholder="item, P/N, S/N">
        </div>
        <button type="submit" id="navbar-search-button" class="btn btn-icon-toggle ink-reaction"><i class="fa fa-search"></i></button>
      </form>
      <a class="btn btn-icon-toggle" href="#offcanvas-datatable-filter" data-toggle="offcanvas" title="Data filter">
        <i class="md md-filter-list"></i>
      </a>
      <div class="btn-group" id="datatable-buttons">
      </div>
    </div>
  </div>

  <div class="card-body no-padding">
    <table data-provide="datatable">
      <thead>
        <tr>
          <?php foreach ($grid['column'] as $column):?>
            <th><?=$column;?></th>
          <?php endforeach;?>
        </tr>
      </thead>

      <?php if ($grid['summary_columns'] === NULL):?>
        <tfoot>
          <tr>
            <?php foreach ($grid['column'] as $column):?>
              <th><?=$column;?></th>
            <?php endforeach;?>
          </tr>
        </tfoot>
      <?php else:?>
        <tfoot>
          <tr>
            <th></th>
            <th>Total</th>

            <?php for ($i = 2; $i < count($grid['column']); $i++):?>
              <th></th>
            <?php endfor;?>
          </tr>
        </tfoot>
      <?php endif;?>
    </table>
  </div>
</div>


<!-- DATAMODAL -->
