<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['expense_item']['visible']        = TRUE;
$config['module']['expense_item']['main_warehouse'] = TRUE;
$config['module']['expense_item']['parent']         = 'master';
$config['module']['expense_item']['label']          = 'Expense Item Without PO';
$config['module']['expense_item']['name']           = 'Expense Item Without PO';
$config['module']['expense_item']['route']          = 'Expense_Item';
$config['module']['expense_item']['view']           = config_item('module_path') .'expense_item/';
$config['module']['expense_item']['language']       = 'account_payable_lang';
$config['module']['expense_item']['helper']         = 'capex_request_helper';
$config['module']['expense_item']['table']          = 'tb_product_categories';
$config['module']['expense_item']['model']          = 'Expense_Item_Model';
$config['module']['expense_item']['permission']     = array(
  'index'   => 'ADMIN,SUPER ADMIN,PIC STAFF,PIC PROCUREMENT',
  'create'  => 'ADMIN,SUPER ADMIN,PIC STAFF,PIC PROCUREMENT',
  'import'  => 'ADMIN,SUPER ADMIN,PIC STAFF,PIC PROCUREMENT',
  'edit'    => 'ADMIN,SUPER ADMIN,PIC STAFF,PIC PROCUREMENT',
  'info'    => 'ADMIN,SUPER ADMIN,PIC STAFF,PIC PROCUREMENT',
  'save'    => 'ADMIN,SUPER ADMIN,PIC STAFF,PIC PROCUREMENT',
  'delete'  => 'ADMIN,SUPER ADMIN,PIC STAFF,PIC PROCUREMENT',
);
