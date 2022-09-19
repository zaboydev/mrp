<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['purchase_supplier_summary']['visible']        = TRUE;
$config['module']['purchase_supplier_summary']['main_warehouse'] = FALSE;
$config['module']['purchase_supplier_summary']['parent']         = 'finance_report';
$config['module']['purchase_supplier_summary']['label']          = 'Purchase Supplier Summary';
$config['module']['purchase_supplier_summary']['name']           = 'Purchase Supplier Summary';
$config['module']['purchase_supplier_summary']['route']          = 'purchase_supplier_summary';
$config['module']['purchase_supplier_summary']['view']           = config_item('module_path') . 'purchase_supplier_summary/';
$config['module']['purchase_supplier_summary']['language']       = 'prl_poe_lang';
$config['module']['purchase_supplier_summary']['helper']         = 'material_slip_helper';
$config['module']['purchase_supplier_summary']['table']          = 'tb_purchase_orders';
$config['module']['purchase_supplier_summary']['model']          = 'Purchase_Item_Detail_Model';
$config['module']['purchase_supplier_summary']['permission']     = array(
    'index'     => 'AP STAFF,PROCUREMENT,FINANCE,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,FINANCE MANAGER,SUPER ADMIN,VP FINANCE,OPERATION SUPPORT,CHIEF OPERATION OFFICER',
    'info'      => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER',
    'print'     => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER',
    'document'  => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER', //tambhan supervisor
    'payment'   => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER',
    'import'    => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER',//tambhan supervisor
);
