<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['parent']['dashboard']['label']  = 'Dashboard';
$config['parent']['dashboard']['icon']   = 'md md-home';

$config['parent']['master']['label']  = 'Master Data';
$config['parent']['master']['icon']   = 'md md-storage';

$config['parent']['stock']['label']  = 'Inventory Report';
$config['parent']['stock']['icon']   = 'md md-assessment';

$config['parent']['document']['label']  = 'Inventory Process';
$config['parent']['document']['icon']   = 'md md-view-module';

$config['parent']['report']['label']  = 'Inventory Reports';
$config['parent']['report']['icon']   = 'md md-assessment';

$config['parent']['setting']['label']  = 'Settings';
$config['parent']['setting']['icon']   = 'md md-settings';

$config['parent']['budget']['label']  = 'Planning';
$config['parent']['budget']['icon']   = 'md md-attach-money';

$config['parent']['accounting_report']['label']  = 'Accounting Report';
$config['parent']['accounting_report']['icon']   = 'md md-account-balance-wallet';

$config['parent']['procurement']['label']  = 'Procurement';
$config['parent']['procurement']['icon']   = 'md md-assignment';

$config['module'] = array();


//UMUM
require('modules/secure.php');
require('modules/ajax.php');
require('modules/dashboard.php');
require('modules/akunting.php');
require('modules/pdf.php');
require('modules/setting.php');

//PLANNING
require('modules/budget_cot.php');
require('modules/budgeting.php');

//PROCUREMENT
require('modules/purchase_request.php');
require('modules/purchase_order_evaluation.php');
require('modules/purchase_order.php');

//INVENTORY PROCESS
require('modules/goods_received_note.php');
require('modules/internal_delivery.php');
require('modules/material_slip.php');
require('modules/shipping_document.php');
require('modules/shipping_document_receipt.php');
require('modules/commercial_invoice.php');
require('modules/stock.php');
require('modules/permintaan_adjustment.php');
require('modules/mixing.php');
require('modules/adjustment.php');
require('modules/relocation.php');
require('modules/opname_stock.php');

//REPORT
require('modules/stock_card.php');
require('modules/stock_general.php');
require('modules/stock_low.php');
require('modules/stock_opname.php');
require('modules/stock_adjustment.php');
require('modules/stock_mix.php');
require('modules/stock_report.php');
require('modules/stock_daily_report.php');
require('modules/stock_activity_report.php');
require('modules/general_stock_report.php');
require('modules/low_stock.php');
require('modules/expired_stock.php');
require('modules/konsolidasi.php');

require('modules/account_payable.php');
require('modules/jurnal.php');
require('modules/usage_jurnal.php');
// require('modules/doc_receipt.php');
// require('modules/doc_usage.php');
// require('modules/doc_delivery.php');
// require('modules/doc_return.php');
// require('modules/doc_shipment.php');
// require('modules/item_application.php');
//MASTER DATA
require('modules/item.php');
require('modules/item_category.php');
require('modules/item_group.php');
require('modules/item_serial.php');
require('modules/item_unit.php');
require('modules/stores.php');
require('modules/user.php');
require('modules/vendor.php');
require('modules/warehouse.php');
require('modules/kurs.php');
require('modules/pesawat.php');


// require('modules/item_in_stores.php');
// require('modules/item_in_use.php');
// require('modules/item_on_delivery.php');
// require('modules/item_on_return.php');
// require('modules/item_on_shipping.php');


//tambahan






