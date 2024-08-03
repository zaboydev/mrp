<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
  <!-- <section class="<?=(isset($page['actions'])) ? 'has-actions' : '';?> style-default">
    <div class="section-body">
      <?php if (config_item('auth_warehouse') === config_item('main_warehouse')):?>
        <div class="row">
          <div class="col-md-3 col-sm-6">
            <div class="card card-linked" data-toggle="redirect" data-url="<?=site_url($modules['stock_general']['route']);?>">
              <div class="card-body text-info">
                <h1 class="pull-right">
                  <i class="fa fa-cubes"></i>
                </h1>
                <strong class="text-xl"><?=number_format($total_in_stores_items);?></strong> items<br/>
                In Stores Items
              </div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6">
            <div class="card card-linked" data-toggle="redirect" data-url="<?=site_url($modules['doc_receipt']['route']);?>">
              <div class="card-body text-success">
                <h1 class="pull-right">
                  <i class="fa fa-clipboard"></i>
                </h1>
                <strong class="text-xl"><?=number_format($total_doc_receipts);?></strong> docs<br/>
                Goods Received Notes
              </div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6">
            <div class="card card-linked" data-toggle="redirect" data-url="<?=site_url($modules['stock_low']['route']);?>">
              <div class="card-body text-danger">
                <h1 class="pull-right">
                  <i class="fa fa-sort-amount-desc"></i>
                </h1>
                <strong class="text-xl"><?=number_format(countItemLowStock());?></strong> items<br/>
                Low Stock Items
              </div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6">
            <div class="card card-linked" data-toggle="redirect" data-url="<?=site_url($modules['doc_return']['route']);?>">
              <div class="card-body text-warning">
                <h1 class="pull-right">
                  <i class="fa fa-external-link"></i>
                </h1>
                <strong class="text-xl"><?=number_format($total_doc_returns);?></strong> items<br/>
                On Return/Repair Items
              </div>
            </div>
          </div>
        </div>
      <?php endif;?>

      <div class="row">
        <div class="col-md-3 col-sm-6">
          <div class="card card-linked" data-toggle="redirect" data-url="<?=site_url($modules['doc_usage']['route']);?>">
            <div class="card-body text-info">
              <h1 class="pull-right">
                <i class="fa fa-cubes"></i>
              </h1>
              <strong class="text-xl"><?=number_format(countItemStockInStores());?></strong> items<br/>
              Stock In Stores
            </div>
          </div>
        </div>

        <div class="col-md-3 col-sm-6">
          <div class="card card-linked" data-toggle="redirect" data-url="<?=site_url($modules['doc_usage']['route']);?>">
            <div class="card-body text-info">
              <h1 class="pull-right">
                <i class="fa fa-share-square-o"></i>
              </h1>
              <strong class="text-xl"><?=number_format($total_doc_usages);?></strong> docs<br/>
              Material Slip
            </div>
          </div>
        </div>

        <div class="col-md-3 col-sm-6">
          <div class="card card-linked" data-toggle="redirect" data-url="<?=site_url($modules['doc_shipment']['route']);?>">
            <div class="card-body text-danger">
              <h1 class="pull-right">
                <i class="fa fa-ship"></i>
              </h1>
              <strong class="text-xl"><?=number_format($total_doc_shipments);?></strong> docs<br/>
              Shipping Document
            </div>
          </div>
        </div>

        <div class="col-md-3 col-sm-6">
          <div class="card card-linked" data-toggle="redirect" data-url="<?=site_url($modules['doc_delivery']['route']);?>">
            <div class="card-body text-warning">
              <h1 class="pull-right">
                <i class="fa fa-briefcase"></i>
              </h1>
              <strong class="text-xl"><?=number_format($total_internal_deliveries);?></strong> docs<br/>
              Internal Delivery
            </div>
          </div>
        </div>
      </div>
    </div>
  </section> -->
<?php endblock() ?>
