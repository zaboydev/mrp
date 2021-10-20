<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
  <section class="<?=(isset($page['actions'])) ? 'has-actions' : '';?> bg-blue">
    <div class="section-body">

      <div class="row">
      </div>

      
        <div class="row">
        <?php if (is_granted($module, 'approval')):?>
          <div class="col-md-4">
            <div class="card">
              <div class="card-head style-primary">
                <header>Approval</header>
              </div>
                <?php if (is_granted($modules['capex_request'], 'approval')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['capex_request']['route']);?>">
                        Capex Requests (<strong><font color="red"><?=$count_capex_req;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>

                <?php if (is_granted($modules['capex_order_evaluation'], 'approval')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['capex_order_evaluation']['route']);?>">
                        Capex Order Evaluation (<strong><font color="red"><?=$count_capex_evaluation;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>

                <?php if (is_granted($modules['capex_purchase_order'], 'approval')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['capex_purchase_order']['route']);?>">
                        Capex Purchase Order (<strong><font color="red"><?=$count_capex_order;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>

                <?php if (is_granted($modules['inventory_request'], 'approval')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['inventory_request']['route']);?>">
                        Inventory Requests (<strong><font color="red"><?=$count_inventory_req;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>

                <?php if (is_granted($modules['inventory_order_evaluation'], 'approval')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['inventory_order_evaluation']['route']);?>">
                        Inventory Order Evaluation (<strong><font color="red"><?=$count_inventory_evaluation;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>

                <?php if (is_granted($modules['inventory_purchase_order'], 'approval')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['inventory_purchase_order']['route']);?>">
                        Inventory Purchase Order (<strong><font color="red"><?=$count_inventory_order;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>

                <?php if (is_granted($modules['expense_request'], 'approval')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['expense_request']['route']);?>">
                        Expense Requests (<strong><font color="red"><?=$count_expense_req;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>

                <?php if (is_granted($modules['expense_order_evaluation'], 'approval')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['expense_order_evaluation']['route']);?>">
                        Expense Order Evaluation (<strong><font color="red"><?=$count_expense_evaluation;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>

                <?php if (is_granted($modules['expense_purchase_order'], 'approval')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['expense_purchase_order']['route']);?>">
                        Expense Purchase Order (<strong><font color="red"><?=$count_expense_order;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>

                <?php if (is_granted($modules['purchase_request'], 'approval')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['purchase_request']['route']);?>">
                        Purchase Requests (<strong><font color="red"><?=$count_prl;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>

                <?php if (is_granted($modules['purchase_order_evaluation'], 'approval')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['purchase_order_evaluation']['route']);?>">
                        Purchase Order Evaluations (<strong><font color="red"><?=$count_poe;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>

                <?php if (is_granted($modules['purchase_order'], 'approval')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['purchase_order']['route']);?>">
                        Purchase Order (<strong><font color="red"><?=$count_po;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>

                <?php if (is_granted($modules['payment'], 'approval')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['payment']['route']);?>">
                        Payment Request (<strong><font color="red"><?=$count_payment_request;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>
            </div>
          </div>          
        <?php else: ?>  
          <div class="col-sm-4">
            <div class="card">
              <div class="card-head style-primary">
                <header>Search Items in Stores</header>
              </div>

              <div class="card-body">
                <form id="form_search" class="form-search" method="get" action="<?=site_url('search');?>">
                  <div class="form-group">
                    <label for="warehouse" class="">Base</label>
                    <select class="form-control input-lg" id="warehouse" name="warehouse">
                      <option value="ALL BASE">ALL BASE</option>
                      <?php foreach (config_item('auth_warehouses') as $base):?>
                        <option value="<?=$base;?>"><?=$base;?></option>
                      <?php endforeach;?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="keywords" class="">Keywords</label>
                    <input type="text" name="keywords" id="keywords" class="form-control input-lg" maxlength="255" placeholder="Part No / Serial No / Description" required autocomplete="off" autofocus>
                  </div>
                  <div class="form-group">
                    <button class="btn btn-block btn-primary" id="search-button">SEARCH</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="panel-group" id="document_accordion">
              <div class="card panel">
                <div class="card-head style-primary">
                  <header>Documents</header>
                </div>

                <?php if (is_granted($modules['capex_request'], 'document')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['capex_request']['route']);?>">
                        Capex Requests (<strong><font color="red"><?=$count_capex_req_not_approved;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>

                <?php if (is_granted($modules['capex_order_evaluation'], 'document')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['capex_order_evaluation']['route']);?>">
                        Capex Order Evaluation (<strong><font color="red"><?=$count_capex_evaluation_not_approved;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>

                <?php if (is_granted($modules['capex_purchase_order'], 'document')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['capex_purchase_order']['route']);?>">
                        Capex Purchase Order (<strong><font color="red"><?=$count_capex_order_not_approved;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>

                <?php if (is_granted($modules['inventory_request'], 'document')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['inventory_request']['route']);?>">
                        Inventory Requests (<strong><font color="red"><?=$count_inventory_req_not_approved;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>

                <?php if (is_granted($modules['inventory_order_evaluation'], 'document')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['inventory_order_evaluation']['route']);?>">
                        Inventory Order Evaluation (<strong><font color="red"><?=$count_inventory_evaluation_not_approved;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>

                <?php if (is_granted($modules['inventory_purchase_order'], 'document')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['inventory_purchase_order']['route']);?>">
                        Inventory Purchase Order (<strong><font color="red"><?=$count_inventory_order_not_approved;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>

                <?php if (is_granted($modules['expense_request'], 'document')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['expense_request']['route']);?>">
                        Expense Requests (<strong><font color="red"><?=$count_expense_req_not_approved;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>

                <?php if (is_granted($modules['expense_order_evaluation'], 'document')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['expense_order_evaluation']['route']);?>">
                        Expense Order Evaluation (<strong><font color="red"><?=$count_expense_evaluation_not_approved;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>

                <?php if (is_granted($modules['expense_purchase_order'], 'document')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['expense_purchase_order']['route']);?>">
                        Expense Purchase Order (<strong><font color="red"><?=$count_expense_order_not_approved;?></font></strong>)
                      </a>
                    </header>
                  </div>
                <?php endif;?>              

                <?php if (is_granted($modules['purchase_request'], 'index')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['purchase_request']['route']);?>">
                        Purchase Requests
                      </a>
                    </header>
                    <div class="tools">
                      <a href="<?=site_url($modules['purchase_request']['route']);?>" class="btn btn-icon-toggle">
                        <i class="fa fa-list"></i>
                      </a>

                      <?php if (is_granted($modules['purchase_request'], 'document')):?>
                        <a class="btn btn-icon-toggle" data-toggle="collapse" data-parent="#document_accordion" data-target="#purchase_request"><i class="fa fa-plus"></i></a>
                      <?php endif;?>
                    </div>
                  </div>

                  <?php if (is_granted($modules['purchase_request'], 'document')):?>
                    <div id="purchase_request" class="collapse">
                      <ul class="list">
                        <?php foreach (config_item('auth_inventory') as $category):?>
                          <li class="tile">
                            <a class="tile-content ink-reaction" target="_blank" href="<?=site_url($modules['purchase_request']['route'] .'/create/'. $category);?>">
                              <div class="tile-text">
                                <i class="fa fa-edit"></i>
                                <?=$category;?>
                                <!-- <small>Last visit: Today</small> -->
                              </div>
                            </a>
                          </li>
                        <?php endforeach;?>
                      </ul>
                    </div>
                  <?php endif;?>
                <?php endif;?>

                <?php if (is_granted($modules['purchase_order_evaluation'], 'index')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['purchase_order_evaluation']['route']);?>">
                        Purchase Order Evaluations
                      </a>
                    </header>
                    <div class="tools">
                      <a href="<?=site_url($modules['purchase_order_evaluation']['route']);?>" class="btn btn-icon-toggle">
                        <i class="fa fa-list"></i>
                      </a>

                      <?php if (is_granted($modules['purchase_order_evaluation'], 'document')):?>
                        <a class="btn btn-icon-toggle" data-toggle="collapse" data-parent="#document_accordion" data-target="#purchase_order_evaluation"><i class="fa fa-plus"></i></a>
                      <?php endif;?>
                    </div>
                  </div>

                  <?php if (is_granted($modules['purchase_order_evaluation'], 'document')):?>
                    <div id="purchase_order_evaluation" class="collapse">
                      <ul class="list">
                        <?php foreach (config_item('auth_inventory') as $category):?>
                          <li class="tile">
                            <a class="tile-content ink-reaction" target="_blank" href="<?=site_url($modules['purchase_order_evaluation']['route'] .'/create/'. $category);?>">
                              <div class="tile-text">
                                <i class="fa fa-edit"></i>
                                <?=$category;?>
                                <!-- <small>Last visit: Today</small> -->
                              </div>
                            </a>
                          </li>
                        <?php endforeach;?>
                      </ul>
                    </div>
                  <?php endif;?>
                <?php endif;?>

                <?php if (is_granted($modules['goods_received_note'], 'index')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['goods_received_note']['route']);?>">
                        Goods Received Notes
                      </a>
                    </header>
                    <div class="tools">
                      <a href="<?=site_url($modules['goods_received_note']['route']);?>" class="btn btn-icon-toggle">
                        <i class="fa fa-list"></i>
                      </a>

                      <?php if (is_granted($modules['goods_received_note'], 'document')):?>
                        <a class="btn btn-icon-toggle" data-toggle="collapse" data-parent="#document_accordion" data-target="#goods_received_note"><i class="fa fa-plus"></i></a>
                      <?php endif;?>
                    </div>
                  </div>

                  <?php if (is_granted($modules['goods_received_note'], 'document')):?>
                    <div id="goods_received_note" class="collapse">
                      <ul class="list">
                        <?php foreach (config_item('auth_inventory') as $category):?>
                          <li class="tile">
                            <a class="tile-content ink-reaction" target="_blank" href="<?=site_url($modules['goods_received_note']['route'] .'/create/'. $category);?>">
                              <div class="tile-text">
                                <i class="fa fa-edit"></i>
                                <?=$category;?>
                                <!-- <small>Last visit: Today</small> -->
                              </div>
                            </a>
                          </li>
                        <?php endforeach;?>
                      </ul>
                    </div>
                  <?php endif;?>
                <?php endif;?>

                <?php if (is_granted($modules['material_slip'], 'index')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['material_slip']['route']);?>">
                        Material Slips
                      </a>
                    </header>
                    <div class="tools">
                      <a href="<?=site_url($modules['material_slip']['route']);?>" class="btn btn-icon-toggle">
                        <i class="fa fa-list"></i>
                      </a>

                      <?php if (is_granted($modules['material_slip'], 'document')):?>
                        <a class="btn btn-icon-toggle" data-toggle="collapse" data-parent="#document_accordion" data-target="#material_slip"><i class="fa fa-plus"></i></a>
                      <?php endif;?>
                    </div>
                  </div>

                  <?php if (is_granted($modules['material_slip'], 'document')):?>
                    <div id="material_slip" class="collapse">
                      <ul class="list">
                        <?php foreach (config_item('auth_inventory') as $category):?>
                          <li class="tile">
                            <a class="tile-content ink-reaction" target="_blank" href="<?=site_url($modules['material_slip']['route'] .'/create/'. $category);?>">
                              <div class="tile-text">
                                <i class="fa fa-edit"></i>
                                <?=$category;?>
                                <!-- <small>Last visit: Today</small> -->
                              </div>
                            </a>
                          </li>
                        <?php endforeach;?>
                      </ul>
                    </div>
                  <?php endif;?>
                <?php endif;?>

                <?php if (is_granted($modules['internal_delivery'], 'index')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['internal_delivery']['route']);?>">
                        Internal Delivery
                      </a>
                    </header>
                    <div class="tools">
                      <a href="<?=site_url($modules['internal_delivery']['route']);?>" class="btn btn-icon-toggle">
                        <i class="fa fa-list"></i>
                      </a>

                      <?php if (is_granted($modules['internal_delivery'], 'document')):?>
                        <a class="btn btn-icon-toggle" data-toggle="collapse" data-parent="#document_accordion" data-target="#internal_delivery"><i class="fa fa-plus"></i></a>
                      <?php endif;?>
                    </div>
                  </div>

                  <?php if (is_granted($modules['internal_delivery'], 'document')):?>
                    <div id="internal_delivery" class="collapse">
                      <ul class="list">
                        <?php foreach (config_item('auth_inventory') as $category):?>
                          <li class="tile">
                            <a class="tile-content ink-reaction" target="_blank" href="<?=site_url($modules['internal_delivery']['route'] .'/create/'. $category);?>">
                              <div class="tile-text">
                                <i class="fa fa-edit"></i>
                                <?=$category;?>
                                <!-- <small>Last visit: Today</small> -->
                              </div>
                            </a>
                          </li>
                        <?php endforeach;?>
                      </ul>
                    </div>
                  <?php endif;?>
                <?php endif;?>

                <?php if (is_granted($modules['shipping_document'], 'index')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['shipping_document']['route']);?>">
                        Shipping Documents
                      </a>
                    </header>
                    <div class="tools">
                      <a href="<?=site_url($modules['shipping_document']['route']);?>" class="btn btn-icon-toggle">
                        <i class="fa fa-list"></i>
                      </a>

                      <?php if (is_granted($modules['shipping_document'], 'document')):?>
                        <a class="btn btn-icon-toggle" data-toggle="collapse" data-parent="#document_accordion" data-target="#shipping_document"><i class="fa fa-plus"></i></a>
                      <?php endif;?>
                    </div>
                  </div>

                  <?php if (is_granted($modules['shipping_document'], 'document')):?>
                    <div id="shipping_document" class="collapse">
                      <ul class="list">
                        <?php foreach (config_item('auth_inventory') as $category):?>
                          <li class="tile">
                            <a class="tile-content ink-reaction" target="_blank" href="<?=site_url($modules['shipping_document']['route'] .'/create/'. $category);?>">
                              <div class="tile-text">
                                <i class="fa fa-edit"></i>
                                <?=$category;?>
                                <!-- <small>Last visit: Today</small> -->
                              </div>
                            </a>
                          </li>
                        <?php endforeach;?>
                      </ul>
                    </div>
                  <?php endif;?>
                <?php endif;?>

                <?php if (is_granted($modules['commercial_invoice'], 'index')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['commercial_invoice']['route']);?>">
                        Return & Service
                      </a>
                    </header>
                    <div class="tools">
                      <a href="<?=site_url($modules['commercial_invoice']['route']);?>" class="btn btn-icon-toggle">
                        <i class="fa fa-list"></i>
                      </a>

                      <?php if (is_granted($modules['commercial_invoice'], 'document')):?>
                        <a class="btn btn-icon-toggle" data-toggle="collapse" data-parent="#document_accordion" data-target="#commercial_invoice"><i class="fa fa-plus"></i></a>
                      <?php endif;?>
                    </div>
                  </div>

                  <?php if (is_granted($modules['commercial_invoice'], 'document')):?>
                    <div id="commercial_invoice" class="collapse">
                      <ul class="list">
                        <?php foreach (config_item('auth_inventory') as $category):?>
                          <li class="tile">
                            <a class="tile-content ink-reaction" target="_blank" href="<?=site_url($modules['commercial_invoice']['route'] .'/create/'. $category);?>">
                              <div class="tile-text">
                                <i class="fa fa-edit"></i>
                                <?=$category;?>
                                <!-- <small>Last visit: Today</small> -->
                              </div>
                            </a>
                          </li>
                        <?php endforeach;?>
                      </ul>
                    </div>
                  <?php endif;?>
                <?php endif;?>
              </div>
            </div>
          </div>
          <?php if (is_granted($modules['stock_general'], 'index')||is_granted($modules['stock'], 'index')
          ||is_granted($modules['low_stock'], 'index')||is_granted($modules['stock_opname'], 'index')||is_granted($modules['stock_adjustment'], 'index')):?>                
          <div class="col-sm-4">
            <div class="card">
              <div class="card-head style-primary">
                <header>MATERIAL STOCK</header>
              </div>

              <?php if (is_granted($modules['stock_general'], 'index')):?>
              <div class="card-head">
                <header>
                  <a href="<?=site_url($modules['stock_general']['route']);?>">
                    General Stock
                  </a>
                </header>
                <div class="tools">
                  <a href="<?=site_url($modules['stock_general']['route']);?>" class="btn btn-icon-toggle">
                    <i class="fa fa-list"></i>
                  </a>
                </div>
              </div>
              <?php endif;?>

              <?php if (is_granted($modules['stock'], 'index')):?>
              <div class="card-head">
                <header>
                  <a href="<?=site_url($modules['stock']['route']);?>">
                    Stock In Stores
                  </a>
                </header>
                <div class="tools">
                  <a href="<?=site_url($modules['stock']['route']);?>" class="btn btn-icon-toggle">
                    <i class="fa fa-list"></i>
                  </a>
                </div>
              </div>              
              <?php endif;?>

              <?php if (is_granted($modules['low_stock'], 'index')):?>
              <div class="card-head">
                <header>
                  <a href="<?=site_url($modules['low_stock']['route']);?>">
                    Low Stock
                  </a>
                </header>
                <div class="tools">
                  <a href="<?=site_url($modules['stock_low']['route']);?>" class="btn btn-icon-toggle">
                    <i class="fa fa-list"></i>
                  </a>
                </div>
              </div>              
              <?php endif;?>

              <?php if (is_granted($modules['stock_opname'], 'index')):?>
              <div class="card-head">
                <header>
                  <a href="<?=site_url($modules['stock_opname']['route']);?>">
                    Stock Opname Report
                  </a>
                </header>
                <div class="tools">
                  <a href="<?=site_url($modules['stock_opname']['route']);?>" class="btn btn-icon-toggle">
                    <i class="fa fa-list"></i>
                  </a>
                </div>
              </div>              
              <?php endif;?>

              <?php if (is_granted($modules['stock_opname'], 'index')):?>
              <div class="card-head">
                <header>
                  <a href="<?=site_url($modules['stock_opname']['route']);?>">
                    Summary Stock Report
                  </a>
                </header>
                <div class="tools">
                  <a href="<?=site_url($modules['stock_opname']['route']);?>" class="btn btn-icon-toggle">
                    <i class="fa fa-list"></i>
                  </a>
                </div>
              </div>              
              <?php endif;?>

              <?php if (is_granted($modules['stock_adjustment'], 'index')):?>
              <div class="card-head">
                <header>
                  <a href="<?=site_url($modules['stock_adjustment']['route']);?>">
                    Stock Adjustment
                  </a>
                </header>
                <div class="tools">
                  <a href="<?=site_url($modules['stock_adjustment']['route']);?>" class="btn btn-icon-toggle">
                    <i class="fa fa-list"></i>
                  </a>
                </div>
              </div>              
              <?php endif;?>
            </div>
          </div> 
          <?php endif;?> 
        <?php endif; ?> 
        <?php if (is_granted($modules['permintaan_adjustment'], 'index')):?> 
          <div class="col-md-4">
            <div class="card">
              <div class="card-head style-primary">
                <header>Permintaan Adjustment</header>
              </div>
              <div class="card-head">
                <header>
                  <a href="<?=site_url($modules['permintaan_adjustment']['route']);?>">
                    Permintaan Adjustment (<strong><font color="red"><?=$count_adjustmnet;?></font></strong>)
                  </a>
                </header>
                <div class="tools">
                  <a href="<?=site_url($modules['permintaan_adjustment']['route']);?>" class="btn btn-icon-toggle">
                    <i class="fa fa-list"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>   
        <?php endif;?> 
        <?php if(is_granted($modules['expired_stock'], 'index')): ?>
        <div class="col-sm-5">
            <div class="panel-group" id="document_accordion">
              <div class="card panel">
                <div class="card-head style-primary">
                  <header>Stock Expired</header>
                </div>

                <?php if (is_granted($modules['expired_stock'], 'index')):?>
                  <div class="card-head collapsed">
                    <header>
                      <a href="<?=site_url($modules['expired_stock']['route']);?>">
                        Stock Yang Akan Expired (<strong><font color="red"><?=$count_expired_stock;?></font></strong>)
                      </a>
                    </header>
                    <div class="tools">
                      <a href="<?=site_url($modules['purchase_request']['route']);?>" class="btn btn-icon-toggle">
                        <i class="fa fa-list"></i>
                      </a>

                      <?php if (is_granted($modules['purchase_request'], 'index')):?>
                        <a class="btn btn-icon-toggle" data-toggle="collapse" data-parent="#document_accordion" data-target="#expired_stock"><i class="fa fa-external-link"></i></a>
                      <?php endif;?>
                    </div>
                  </div>

                  <?php if (is_granted($modules['expired_stock'], 'index')):?>
                    <div id="expired_stock" class="collapse">
                      <table class="tg">
                        <thead>
                          <tr>
                            <th>Part Number</th>
                            <th>Serial Number</th>
                            <th>Description</th>
                            <th>Expired Date</th>
                          </tr>                        
                        </thead>
                        <tbody>
                          <?php foreach ($expired_stock as $ex):?>
                            <tr>
                              <td><?=$ex['part_number'];?></td>
                              <td><?=$ex['part_number'];?></td>
                              <td><?=$ex['description'];?></td>
                              <td><?=print_date($ex['expired_date'],'d F Y');?></td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                      </table>
                      <ul class="list">
                        
                      </ul>
                    </div>
                  <?php endif;?>
                <?php endif;?>
              </div>
            </div>
        </div>
        <?php endif;?>
        </div>     

    </div>
  </section>
<?php endblock() ?>

<?php startblock('scripts') ?>
  <?=html_script('vendors/pace/pace.min.js') ?>
  <?=html_script('vendors/jQuery/jQuery-2.2.1.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/jquery-ui/jquery-ui.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/bootstrap/bootstrap.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/spin.js/spin.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/autosize/jquery.autosize.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/toastr/toastr.js') ?>

  <script>
  Pace.on('start', function(){
    $('.progress-overlay').show();
  });

  Pace.on('done', function(){
    $('.progress-overlay').hide();
  });

  function popup(mylink, windowname){
    var height;
    var widht;
    var href;

    if (screen.availWidth > 768){
      width = 769;
    } else {
      width = screen.availWidth;
    }

    if (screen.availHeight > 600){
      height = 600;
    } else {
      height = screen.availHeight;
    }

    var left = (screen.availWidth / 2) - (width / 2);
    // var top = 0;
    var top = (screen.availHeight / 2) - (height / 2);

    if (typeof(mylink) == 'string') href = mylink;
    else href = mylink.href;

    window.open(href, windowname, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+width+', height='+height+', top='+top+', left='+left);

    if (! window.focus) return true;
    else return false;
  }

  function search(keywords){
    var warehouse = $('#warehouse').val();
    var action    = $('#form_search').attr('action');
    var url       = action + '?warehouse=' + warehouse + '&keywords=' + keywords;

    $('#keywords').val('');

    popup(url, 'search');
  }

  $( document ).ready(function(){
    var description = <?=$json_description;?>;

    $( '#keywords' ).autocomplete({
      minLength: 3,
      autoFocus: true,
      source: function(request, response) {
        var results = $.ui.autocomplete.filter(description, request.term);
        response(results.slice(0, 10));
      },
      select: function( event, ui ) {
        console.log(ui.item.value);

        var keywords  = ui.item.value;

        search(keywords);
      }
    });
    
    $('#search-button').click(function(){
      $('#form_search').submit();
    });

    $('#form_search').submit(function(e){
      e.preventDefault();

      if ($('#keywords').val() != ''){
        var keywords  = $('#keywords').val();

        search(keywords);
      }
    });
  });
  </script>

  <?=html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>
