<?php
/**
 * @var $auth_fullname string
 * @var $auth_role string
 */
?>

<aside class="main-sidebar">
  <section class="sidebar">
    <ul class="sidebar-menu">
      <li class="header">MAIN NAVIGATION</li>

      <?php if ($auth_role === 'ADMIN'):?>
        <li>
          <a href="<?=site_url('warehouse');?>">
            <i class="fa fa-home"></i>
            <span>Manage Warehouses</span>
          </a>
        </li>

        <li>
          <a href="<?=site_url('stores');?>">
            <i class="fa fa-cube"></i>
            <span>Manage Stores</span>
          </a>
        </li>

        <li>
          <a href="<?=site_url('aircraft_type');?>">
            <i class="fa fa-paper-plane"></i>
            <span>Manage Aircraft Types</span>
          </a>
        </li>

        <li>
          <a href="<?=site_url('aircraft');?>">
            <i class="fa fa-plane"></i>
            <span>Manage Aircrafts</span>
          </a>
        </li>

        <li>
          <a href="<?=site_url('vendor');?>">
            <i class="fa fa-bookmark"></i>
            <span>Manage Vendors</span>
          </a>
        </li>

        <li>
          <a href="<?=site_url('user');?>">
            <i class="fa fa-user"></i>
            <span>Manage Users</span>
          </a>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-gear"></i> <span>Settings</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li>
              <a href="<?=site_url('setting/warehouse');?>">
                Warehouse Setting
              </a>
            </li>
          </ul>
        </li>

      <?php else:?>
        <?php if (config_item('auth_warehouse') === config_item('main_base')):?>
          <li class="treeview">
            <a href="#">
              <i class="fa fa-database"></i> <span>Master Data</span>
              <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
              <?php if (is_granted($acl['warehouse']['index'])):?>
                <li>
                  <a href="<?=site_url('warehouse');?>">
                    Warehouse
                  </a>
                </li>
              <?php endif;?>

              <?php if (is_granted($acl['stores']['index'])):?>
                <li>
                  <a href="<?=site_url('stores');?>">
                    Stores
                  </a>
                </li>
              <?php endif;?>

              <?php if (is_granted($acl['aircraft_type']['index'])):?>
                <li>
                  <a href="<?=site_url('aircraft_type');?>">
                    Aircraft Types
                  </a>
                </li>
              <?php endif;?>

              <?php if (is_granted($acl['aircraft']['index'])):?>
                <li>
                  <a href="<?=site_url('aircraft');?>">
                    Aircrafts
                  </a>
                </li>
              <?php endif;?>

              <?php if (is_granted($acl['vendor']['index'])):?>
                <li>
                  <a href="<?=site_url('vendor');?>">
                    Vendors
                  </a>
                </li>
              <?php endif;?>

              <?php if (is_granted($acl['unit_of_measurement']['index'])):?>
                <li>
                  <a href="<?=site_url('unit_of_measurement');?>">
                    Unit of Meas.
                  </a>
                </li>
              <?php endif;?>

              <?php if (is_granted($acl['item_group']['index'])):?>
                <li>
                  <a href="<?=site_url('item_group');?>">
                    Item Groups
                  </a>
                </li>
              <?php endif;?>

              <?php if (is_granted($acl['item']['index'])):?>
                <li>
                  <a href="<?=site_url('item');?>">
                    Items
                  </a>
                </li>
              <?php endif;?>
            </ul>
          </li>
        <?php endif;?>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-cube"></i> <span>Inventories</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <?php if (is_granted($acl['item_in_stores']['general_stock']) && config_item('auth_warehouse') === config_item('main_base')):?>
              <li>
                <a href="<?=site_url('item_in_stores/general_stock');?>">
                  General Stock
                </a>
              </li>
            <?php endif;?>

            <?php if (is_granted($acl['item_in_stores']['low_stock']) && config_item('auth_warehouse') === config_item('main_base')):?>
              <li>
                <a href="<?=site_url('item_in_stores/low_stock');?>">
                  Low Stock Items
                </a>
              </li>
            <?php endif;?>

            <?php if (is_granted($acl['item_in_stores']['index'])):?>
              <li>
                <a href="<?=site_url('item_in_stores');?>">
                  In Stores Items
                </a>
              </li>
            <?php endif;?>

            <?php if (is_granted($acl['item_in_use']['index'])):?>
              <li>
                <a href="<?=site_url('item_in_use');?>">
                  In Use Items
                </a>
              </li>
            <?php endif;?>

            <?php if (is_granted($acl['item_on_delivery']['index'])):?>
              <li>
                <a href="<?=site_url('item_on_delivery');?>">
                  On Delivery Items
                </a>
              </li>
            <?php endif;?>

            <?php if (is_granted($acl['item_on_shipping']['index'])):?>
              <li>
                <a href="<?=site_url('item_on_shipping');?>">
                  On Shipping Items
                </a>
              </li>
            <?php endif;?>

            <?php if (is_granted($acl['item_on_return']['index']) && config_item('auth_warehouse') === config_item('main_base')):?>
              <li>
                <a href="<?=site_url('item_on_return');?>">
                  On Return/Repair Items
                </a>
              </li>
            <?php endif;?>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-clipboard"></i> <span>Documents</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <?php if (is_granted($acl['purchase_request']['index']) && config_item('auth_warehouse') === config_item('main_base')):?>
              <li>
                <a href="<?=site_url('purchase_request');?>">
                  Purchase Requests
                </a>
              </li>
            <?php endif;?>

            <?php if (is_granted($acl['purchase_order']['index']) && config_item('auth_warehouse') === config_item('main_base')):?>
              <li>
                <a href="<?=site_url('purchase_order');?>">
                  Purchase Orders
                </a>
              </li>
            <?php endif;?>

            <?php if (is_granted($acl['purchase_order_evaluation']['index']) && config_item('auth_warehouse') === config_item('main_base')):?>
              <li>
                <a href="<?=site_url('purchase_order_evaluation');?>">
                  Purch. Order Evaluation
                </a>
              </li>
            <?php endif;?>

            <?php if (is_granted($acl['goods_received_note']['index'])):?>
              <?php if (config_item('auth_warehouse') === config_item('main_base')):?>
                <li>
                  <a href="<?=site_url('goods_received_note');?>">
                    Goods Rec. Notes
                  </a>
                </li>
              <?php endif;?>
            <?php endif;?>

            <?php if (is_granted($acl['material_slip']['index'])):?>
              <li>
                <a href="<?=site_url('material_slip');?>">
                  Material Slips
                </a>
              </li>
            <?php endif;?>

            <?php if (is_granted($acl['internal_delivery']['index'])):?>
              <li>
                <a href="<?=site_url('internal_delivery');?>">
                  Internal Delivery
                </a>
              </li>
            <?php endif;?>

            <?php if (is_granted($acl['shipping_document']['index'])):?>
              <li>
                <a href="<?=site_url('shipping_document');?>">
                  Shipping Documents
                </a>
              </li>
            <?php endif;?>

            <?php if (is_granted($acl['commercial_invoice']['index'])):?>
              <?php if (config_item('auth_warehouse') === config_item('main_base')):?>
                <li>
                  <a href="<?=site_url('commercial_invoice');?>">
                    Commercial Invoices
                  </a>
                </li>
              <?php endif;?>
            <?php endif;?>
          </ul>
        </li>

        <?php if (config_item('auth_warehouse') === config_item('main_base')):?>
          <li class="treeview">
            <a href="#">
              <i class="fa fa-file-o"></i> <span>Reports</span>
              <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
              <?php if (is_granted($acl['item_in_stores']['index'])):?>
                <li>
                  <a href="<?=site_url('report/summary_stock');?>">
                    General Stock
                  </a>
                </li>
                <li>
                  <a href="<?=site_url('report/stock_card');?>">
                    Stock Card
                  </a>
                </li>
              <?php endif;?>

              <?php if (is_granted($acl['goods_received_note']['index'])):?>
                <?php if (config_item('auth_warehouse') === config_item('main_base')):?>
                  <li>
                    <a href="<?=site_url('report/goods_received_note');?>">
                      Goods Rec. Notes
                    </a>
                  </li>
                <?php endif;?>
              <?php endif;?>

              <?php if (is_granted($acl['material_slip']['index'])):?>
                <li>
                  <a href="<?=site_url('report/material_slip');?>">
                    Material Slips
                  </a>
                </li>
              <?php endif;?>

              <?php if (is_granted($acl['internal_delivery']['index'])):?>
                <li>
                  <a href="<?=site_url('report/internal_delivery');?>">
                    Internal Delivery
                  </a>
                </li>
              <?php endif;?>

              <?php if (is_granted($acl['shipping_document']['index'])):?>
                <li>
                  <a href="<?=site_url('report/shipping_document');?>">
                    Shipping Documents
                  </a>
                </li>
              <?php endif;?>

              <?php if (is_granted($acl['commercial_invoice']['index'])):?>
                <?php if (config_item('auth_warehouse') === config_item('main_base')):?>
                  <li>
                    <a href="<?=site_url('report/commercial_invoice');?>">
                      Commercial Invoices
                    </a>
                  </li>
                <?php endif;?>
              <?php endif;?>
            </ul>
          </li>
        <?php endif;?>

      <?php endif;?>
    </ul>
  </section>
</aside>
