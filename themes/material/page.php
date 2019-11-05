<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<section class="has-actions style-default">
  <div class="section-body">

    <?php startblock('content_body') ?>
    <div class="card">

      <?php startblock('page_head') ?>
      <div class="card-head style-primary-dark">
        <header><?= PAGE_TITLE; ?> <span id="Periode"></span><span id="Base"></span><span id="Category"></span></header>

        <?php emptyblock('page_head_tools') ?>
      </div>
      <?php endblock() ?>

      <?php if ($this->session->flashdata('alert'))
        render_callout($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']); ?>

      <?php emptyblock('page_body') ?>
    </div>


    <!-- MODALS -->
    <?php startblock('page_modal') ?>
    <div id="data-modal" class="modal fade-scale" tabindex="-1" role="dialog" aria-labelledby="modal-edit-data-label" aria-hidden="true">
      <div class="modal-dialog modal-fs" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="modal-edit-data-label"><?= strtoupper($module['parent']); ?></h4>
          </div>

          <div class="modal-body no-padding"></div>

          <div class="modal-footer"></div>
        </div>
      </div>
    </div>
    <?php endblock() ?>

    <?php emptyblock('formmodal') ?>

    <?php endblock() ?>
  </div>

  <div class="section-action style-default-bright">
    <div class="section-action-row">
      <div class="btn-toolbar">
        <div id="core-buttons" class="pull-left btn-group">
          <button class="btn btn-icon-toggle btn-lg ink-reaction btn-back" data-toggle="back">
            <i class="md md-arrow-back"></i>
          </button>

          <button class="btn btn-icon-toggle btn-lg ink-reaction btn-home" data-toggle="redirect" data-url="<?= site_url(); ?>">
            <i class="md md-home"></i>
          </button>
        </div>

        <!-- ACTIONS LEFT -->
        <?php emptyblock('actions_left') ?>
      </div>
    </div>

    <!-- ACTIONS RIGHT -->
    <?php emptyblock('actions_right') ?>

  </div>
</section>
<?php endblock() ?>


<?php startblock('offcanvas_left') ?>
<div id="offcanvas-datatable-filter" class="offcanvas-pane" style="width: 600px">
  <div class="offcanvas-head style-primary-dark">
    <header>Data Filter</header>
    <div class="offcanvas-tools">
      <a class="btn btn-icon-toggle pull-right" data-dismiss="offcanvas">
        <i class="md md-close"></i>
      </a>
    </div>
  </div>

  <div class="offcanvas-body no-padding">
    <ul class="list ">
      <li class="tile">
        <a class="tile-content ink-reaction" href="#offcanvas-column-toggle" data-toggle="offcanvas">
          <div class="tile-icon">
            <i class="fa fa-sliders"></i>
          </div>
          <div class="tile-text">
            Column Visibility
            <small>Show/hide any columns</small>
          </div>
        </a>
      </li>

      <?php emptyblock('offcanvas_left_actions') ?>
    </ul>

    <?php emptyblock('datafilter') ?>

  </div>
</div>

<?php emptyblock('offcanvas_left_list') ?>

<div id="offcanvas-column-toggle" class="offcanvas-pane width-8">
  <div class="offcanvas-head style-primary-dark">
    <header>Columns Visibility</header>
    <div class="offcanvas-tools">
      <a class="btn btn-icon-toggle pull-right" data-dismiss="offcanvas">
        <i class="md md-close"></i>
      </a>
      <a class="btn btn-icon-toggle pull-right" href="#offcanvas-datatable-filter" data-toggle="offcanvas">
        <i class="md md-arrow-back"></i>
      </a>
    </div>
  </div>
  <div class="offcanvas-body no-padding">
    <ul class="list divider-full-bleed">
      <?php foreach ($grid['column'] as $key => $value) : ?>
        <?php if ($value != NULL) : ?>
          <li class="tile">
            <a class="tile-content ink-reaction column-toggle" href="javascript:void(0);" data-column="<?= $key; ?>" data-label="<?= $value; ?>">
              <div class="tile-text">
                <?= $value; ?>
              </div>
            </a>
          </li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
  </div>
</div>
<?php endblock() ?>