<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
  <section class="has-actions style-default">
    <div class="section-body">
      <?=form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate floating-label', 'id' => 'form-document'));?>
        <div class="card">
          <div class="card-head style-primary-dark">
            <header><?=PAGE_TITLE;?></header>
          </div>

          <div class="card-body no-padding">
            <?php
            if ( $this->session->flashdata('alert') )
              render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);
            ?>

            <div class="document-header force-padding">
              <div class="row">
                <div class="col-sm-4 col-lg-3">
                  <div class="form-group">
                    <input type="text" name="received_date" id="received_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-end-date="0d" class="form-control datepicker" value="<?=$entity['received_date'];?>" required>
                    <label for="received_date">Date of received</label>
                  </div>

                  <div class="form-group">
                    <input type="text" name="document_date" id="document_date" class="form-control" value="<?=$entity['document_date'];?>" disabled>
                    <label for="document_date">Date of sent</label>
                  </div>

                  <div class="form-group">
                    <input type="text" name="document_number" id="document_number" class="form-control" value="<?=$entity['document_number'];?>" readonly>
                    <label for="document_number">Document No.</label>
                  </div>
                </div>

                <div class="col-sm-8 col-lg-9">
                  <div class="row">
                    <div class="col-sm-12 col-lg-5">
                      <div class="form-group">
                        <input type="text" name="origin_warehouse" id="origin_warehouse" class="form-control" value="<?=$entity['origin_warehouse'];?>" readonly>
                        <label for="origin_warehouse">Sent From</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="sent_by" id="sent_by" class="form-control" value="<?=$entity['sent_by'];?>" disabled>
                        <label for="sent_by">Sent By</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="released_by" id="released_by" class="form-control" value="<?=$entity['released_by'];?>" disabled>
                        <label for="released_by">Released By</label>
                      </div>
                    </div>

                    <div class="col-sm-12 col-lg-7">
                      <div class="form-group">
                        <textarea name="notes" id="notes" class="form-control" rows="4" disabled><?=$entity['notes'];?></textarea>
                        <label for="notes">Notes</label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <?php if (isset($entity['items'])):?>
              <div class="document-data table-responsive">
                <table class="table table-hover" id="table-document">
                  <thead>
                  <tr>
                    <th class="text-right">No.</th>
                    <th>Group</th>
                    <th>Description</th>
                    <th>P/N</th>
                    <th>S/N</th>
                    <th>Condition</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Stores</th>
                    <th>notes</th>
                  </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($entity['items'] as $i => $items):?>
                      <?php
                      $i++;
                      $available_quantity = $items['quantity'] - $items['quantity_received'];
                      if ($available_quantity > 0):
                        ?>
                        <tr id="row_<?=$i;?>">
                          <td width="1" class="text-right">
                            <?=$i;?>
                          </td>
                          <td>
                            <?=$items['group'];?>
                          </td>
                          <td>
                            <?=$items['description'];?>
                          </td>
                          <td class="no-space">
                            <?=$items['part_number'];?>
                          </td>
                          <td class="no-space">
                            <?=$items['serial_number'];?>
                          </td>
                          <td>
                            <?=print_config('condition', $items['condition']);?>
                          </td>
                          <td>
                            <input type="text" size="4" name="item[<?=$items['id'];?>][quantity_received]" value="<?=$available_quantity;?>">
                          </td>
                          <td>
                            <?=$items['unit'];?>
                          </td>
                          <td>
                            <input type="text" size="10" name="item[<?=$items['id'];?>][stores]" data-tag-name="stores" data-search-for="stores" data-source="<?=site_url($modules['ajax']['route'] .'/json_stores/'. $entity['category']);?>" required>
                          </td>
                          <td>
                            <input type="text" size="40" name="item[<?=$items['id'];?>][notes]" value="<?=$items['notes'];?>">
                          </td>
                        </tr>
                      <?php
                      endif;
                    endforeach;
                    ?>
                  </tbody>
                </table>
              </div>
            <?php endif;?>
          </div>
        </div>
      <?=form_close();?>
    </div>

    <div class="section-action style-default-bright">
      <div class="section-floating-action-row">
        <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-submit-document" href="<?=site_url($module['route'] .'/receive_save/'. $entity['id']);?>">
          <i class="md md-save"></i>
          <small class="top right">Save Receiving</small>
        </a>
      </div>
    </div>
  </section>
<?php endblock() ?>
