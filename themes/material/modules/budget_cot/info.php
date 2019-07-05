<div class="card card-underline style-default-bright">
  <div class="card-head style-primary-dark">
    <header><?=strtoupper($module['label']);?></header>

    <div class="tools">
      <div class="btn-group">
        <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-label="Close" title="close">
          <i class="md md-close"></i>
        </a>
      </div>
    </div>
  </div>

  <div class="card-body">
    <h4>Stock Information</h4>

    <div class="">
      <dl class="dl-inline">
        <dt>
          Quantity
        </dt>

        <dd>
          <?=number_format($entity['quantity'], 2);?>
          <?=print_string($entity['unit']);?>
        </dd>

        <dt>
          Condition
        </dt>
        <dd>
          <?=print_string($entity['condition']);?>
        </dd>

        <dt>
          Stores/Base
        </dt>
        <dd>
          <?=print_string($entity['stores']);?>
          / <?=print_string($entity['warehouse']);?>
        </dd>

        <dt>
          Part Number
        </dt>
        <dd>
          <?=print_string($entity['part_number']);?>
        </dd>

        <dt>
          Alt. Part Number
        </dt>
        <dd>
          <?=print_string($entity['alternate_part_number'], 'N/A');?>
        </dd>

        <dt>
          Serial Number
        </dt>
        <dd>
          <?=print_string($entity['serial_number'], 'N/A');?>
        </dd>

        <dt>
          Description
        </dt>
        <dd>
          <?=print_string($entity['description']);?>
        </dd>

        <dt>
          Group/Category
        </dt>
        <dd>
          <?=print_string($entity['group']);?> /
          <?=print_string($entity['category']);?>
        </dd>
      </dl>
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th class="text-right">
              No
            </th>
            <th>
              Received
            </th>
            <th>
              Expired
            </th>
            <th>
              Reference
            </th>
            <th class="text-right">
              Quantity
            </th>
            <th>
              Unit
            </th>
            <th>
              Adjustment
            </th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($entity['items'] as $i => $item):?>
            <tr>
              <td>
                <?=print_number($i+1);?>
              </td>
              <td>
                <?=print_date($item['received_date'],'d F Y');?>
              </td>
              <td>
                <?=print_date($item['expired_date']);?>
              </td>
              <td>
                <?=print_string($item['reference_document']);?>
              </td>
              <td>
                <?=print_number($item['quantity'], 2);?>
              </td>
              <td>
                <?=print_string($entity['unit']);?>
              </td>
              <td>
                <?php if (is_granted($module, 'mix')):?>
                  <a href="<?=site_url($module['route'] .'/mix/'. $item['id']);?>" class="btn btn-sm btn-primary">
                    Mixing
                  </a>
                <?php endif;?>
                <?php if (is_granted($module, 'adjustment')):?>
                  <a href="<?=site_url($module['route'] .'/adjustment/'. $item['id']);?>" class="btn btn-sm btn-primary">
                    Adjustment
                  </a>
                <?php endif;?>
                <?php if (is_granted($module, 'relocation')):?>
                  <a href="<?=site_url($module['route'] .'/relocation/'. $item['id']);?>" class="btn btn-sm btn-primary">
                    Relocation
                  </a>
                <?php endif;?>
              </td>
            </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
  </div>
</div>
