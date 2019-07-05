<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
  <section class="has-actions style-default">
    <div class="section-body">
      <?=form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate', 'id' => 'form-document'));?>
        <div class="card">
          <div class="card-head style-primary-dark">
            <header>Stock Adjustment <?=$entity['part_number'];?></header>
          </div>
          <div class="card-body no-padding">
            <?php
            if ( $this->session->flashdata('alert') )
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
                        <?=print_string($entity['serial_number']);?>
                      </dd>

                      <dt>
                        Part Number
                      </dt>
                      <dd>
                        <?=print_string($entity['part_number']);?>
                      </dd>

                      <dt>
                        Description
                      </dt>
                      <dd>
                        <?=print_string($entity['description']);?>
                      </dd>

                      <dt>
                        Group
                      </dt>
                      <dd>
                        <?=print_string($entity['group']);?>
                      </dd>

                      <dt>
                        Category
                      </dt>
                      <dd>
                        <?=print_string($entity['category']);?>
                      </dd>

                      <dt>
                        Base
                      </dt>
                      <dd>
                        <?=print_string($entity['warehouse']);?>
                      </dd>

                      <dt>
                        Stores
                      </dt>
                      <dd>
                        <?=print_string($entity['stores']);?>
                      </dd>

                      <dt>
                        Condition
                      </dt>
                      <dd>
                        <?=print_string($entity['condition']);?>
                      </dd>

                      <dt>
                        Quantity
                      </dt>
                      <dd>
                        <?=number_format($entity['quantity'], 2);?>
                        <?=print_string($entity['unit']);?>
                      </dd>

                      <dt>
                        Expired
                      </dt>
                      <dd>
                        <?=print_date($entity['expired_date']);?>
                      </dd>

                      <dt>
                        Received
                      </dt>
                      <dd>
                        <?=print_date($entity['received_date']);?>
                      </dd>
                    </dl>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-7">
                  <h4>Adjustment Form</h4>

                  <div class="well well-lg">
                    <div class="row">
                      <div class="col-sm-5">
                        <div class="form-group">
                          <input type="text" name="adjustment_current_quantity" id="adjustment_current_quantity" class="form-control" value="<?=$entity['quantity'];?>" disabled>
                          <label for="adjustment_current_quantity">Current Quantity</label>
                        </div>

                        <div class="form-group">
                          <input type="number" name="adjustment_quantity" id="adjustment_quantity" class="form-control" value="0" autofocus required>
                          <label for="adjustment_quantity">Adjustment</label>
                        </div>

                        <div class="form-group">
                          <input type="text" name="adjustment_next_quantity" id="adjustment_next_quantity" class="form-control" value="<?=$entity['quantity'];?>" readonly>
                          <label for="adjustment_next_quantity">Quantity after adjustment</label>
                        </div>
                      </div>
                      <div class="col-sm-7">
                        <div class="form-group">
                          <textarea name="remarks" id="remarks" class="form-control" rows="5"></textarea>
                          <label for="remarks">Remarks</label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?=form_close();?>
    </div>

    <div class="section-action style-default-bright">
      <div class="section-action-row">
        <div class="btn-toolbar">
          <div id="core-buttons" class="pull-left btn-group">
            <button class="btn btn-icon-toggle btn-lg ink-reaction btn-back" data-toggle="back">
              <i class="md md-arrow-back"></i>
            </button>

            <button class="btn btn-icon-toggle btn-lg ink-reaction btn-home" data-toggle="redirect" data-url="<?=site_url();?>">
              <i class="md md-home"></i>
            </button>
          </div>
        </div>
      </div>

      <div class="section-floating-action-row">
        <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-submit-document" href="<?=site_url($module['route'] .'/adjustment_save/'. $entity['id']);?>">
          <i class="md md-save"></i>
          <small class="top right">Save Adjustment</small>
        </a>
      </div>
    </div>
  </section>
<?php endblock() ?>
