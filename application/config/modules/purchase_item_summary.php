<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['purchase_item_summary']['visible']        = TRUE;
$config['module']['purchase_item_summary']['main_warehouse'] = FALSE;
$config['module']['purchase_item_summary']['parent']         = 'finance_report';
$config['module']['purchase_item_summary']['label']          = 'Purchase Item Summary';
$config['module']['purchase_item_summary']['name']           = 'Purchase Item Summary';
$config['module']['purchase_item_summary']['route']          = 'purchase_item_summary';
$config['module']['purchase_item_summary']['view']           = config_item('module_path') . 'purchase_item_summary/';
$config['module']['purchase_item_summary']['language']       = 'prl_poe_lang';
$config['module']['purchase_item_summary']['helper']         = 'material_slip_helper';
$config['module']['purchase_item_summary']['table']          = 'tb_purchase_orders';
$config['module']['purchase_item_summary']['model']          = 'Purchase_Item_Detail_Model';
$config['module']['purchase_item_summary']['permission']     = array(
    'index'     => 'AP STAFF,PROCUREMENT,FINANCE,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,FINANCE MANAGER,SUPER ADMIN,VP FINANCE,OPERATION SUPPORT,CHIEF OPERATION OFFICER',
    
);
