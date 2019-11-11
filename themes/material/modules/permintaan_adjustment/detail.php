<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<section class="has-actions style-default">
  <div class="section-body">
    <?= form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate', 'id' => 'form-document')); ?>
    <div class="card">
      <div class="card-head style-primary-dark">
        <header>Permintaan Stock Adjustment <?= $entity['part_number']; ?></header>
      </div>
      <div class="card-body no-padding">
        <?php
        if ($this->session->flashdata('alert'))
          render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);
        ?>

        <div class="document-header force-padding">
          <div class="row">
            <div class="col-sm-6 col-lg-5">
              <h4>Stock In Stores Info</h4>

              <div class="">
                <dl class="dl-inline">
                  <dt>
                    Serial Number
                  </dt>
                  <dd>
                    : <?= print_string($entity['serial_number']); ?>
                  </dd>

                  <dt>
                    Part Number
                  </dt>
                  <dd>
                    : <?= print_string($entity['part_number']); ?>
                  </dd>

                  <dt>
                    Description
                  </dt>
                  <dd>
                    : <?= print_string($entity['description']); ?>
                  </dd>

                  <dt>
                    Group
                  </dt>
                  <dd>
                    : <?= print_string($entity['group']); ?>
                  </dd>

                  <dt>
                    Category
                  </dt>
                  <dd>
                    : <?= print_string($entity['category']); ?>
                  </dd>

                  <?php
                  if ($entity['adjustment_quantity'] != 0) {
                    ?>
                    <dt>
                      Previous Quantity
                    </dt>
                    <dd><strong>
                        : <?= number_format($entity['previous_quantity'], 2); ?>
                        <?= print_string($entity['unit']); ?>
                      </strong>
                    </dd>

                    <dt>
                      Adjustment Quantity
                    </dt>
                    <dd><strong>
                        <font color="red">
                          : <?= number_format($entity['adjustment_quantity'], 2); ?>
                          <?= print_string($entity['unit']); ?> | <?= number_format($entity['unit_value'], 2); ?>
                        </font>
                      </strong></dd>


                    <dt>
                      Balance Quantity
                    </dt>
                    <dd><strong>
                        <font color="green">
                          : <?= number_format($entity['balance_quantity'], 2); ?>
                          <?= print_string($entity['unit']); ?>
                        </font>
                      </strong></dd>
                  <?php
                  } else {
                    ?>
                    <dt>
                      On Hand Quantity
                    </dt>
                    <dd><strong>
                        : <?= number_format($entity['previous_quantity'], 2); ?>
                        <?= print_string($entity['unit']); ?>
                      </strong>
                    </dd>
                    <dt>
                      Previous Unit Value
                    </dt>
                    <dd><strong>
                        : <?php if ($entity['kurs_dollar'] == 1) : ?>IDR <?= number_format($entity['stock_unit_value'], 2); ?><?php else : ?> USD <?= number_format($entity['stock_unit_value'] / $entity['kurs_dollar'], 2); ?><?php endif; ?>

                      </strong>
                    </dd>
                    <dt>
                      Adjustment Unit Value
                    </dt>
                    <dd><strong>
                        <font color="red">
                          : <?php if ($entity['kurs_dollar'] == 1) : ?>IDR <?= number_format(($entity['unit_value'] / $entity['kurs_dollar']) - ($entity['stock_unit_value'] / $entity['kurs_dollar']), 2); ?><?php else : ?> USD <?= number_format(($entity['unit_value'] / $entity['kurs_dollar']) - ($entity['stock_unit_value'] / $entity['kurs_dollar']), 2); ?><?php endif; ?>
                        </font>
                      </strong>
                    </dd>
                    <dt>
                      Balance Unit Value
                    </dt>
                    <dd><strong>
                        <font color="green">
                          : <?php if ($entity['kurs_dollar'] == 1) : ?>IDR <?= number_format(($entity['unit_value'] / $entity['kurs_dollar']), 2); ?><?php else : ?> USD <?= number_format(($entity['unit_value'] / $entity['kurs_dollar']), 2); ?><?php endif; ?>
                        </font>
                      </strong>
                    </dd>
                  <?php
                  }
                  ?>

                  <dt>
                    Remarks
                  </dt>
                  <dd>
                    : <?= print_string($entity['remarks']); ?>
                  </dd>

                  <dt>
                    Created By
                  </dt>
                  <dd>
                    : <?= print_string($entity['created_by']); ?>
                  </dd>

                  <dt>
                    Created At
                  </dt>
                  <dd>
                    : <?= print_date($entity['created_at']); ?>
                  </dd>
                </dl>
              </div>
            </div>

            <div class="col-sm-6 col-lg-7">
              <h4></h4>

              <div class="well well-lg">
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <h3>Silahkan klik pilihan <strong>
                          <font color="green">APPROVED</font>
                        </strong> untuk menyetujui atau <strong>
                          <font color="red">REJECTED</font>
                        </strong> untuk menolak permintaan ini </h3>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group col-md-offset-8">
                      <a href="<?= site_url('secure/adjustment?mode=approved&id=' . $entity['id'] . '&token=' . $entity['adjustment_token'] . ''); ?>" class="btn btn-lg btn-primary">
                        APPROVED
                      </a>
                      <!-- <a href='localhost/adjustment?mode=approved&id=".$entity['id']."&token=".$row['adjustment_token']."' style='color:blue; font-weight:bold;'>APPROVE</a>
                        </div> -->
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <a href="<?= site_url('secure/adjustment?mode=rejected&id=' . $entity['id'] . '&token=' . $entity['adjustment_token'] . ''); ?>" class="btn btn-lg btn-danger">
                        REJECTED
                      </a>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?= form_close(); ?>
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
      </div>
    </div>
  </div>
</section>
<?php endblock() ?>