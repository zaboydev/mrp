<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['product_category']['visible']        = FALSE;
$config['module']['product_category']['main_warehouse'] = TRUE;
$config['module']['product_category']['parent']         = 'master';
$config['module']['product_category']['label']          = 'Product Category';
$config['module']['product_category']['name']           = 'Product Category';
$config['module']['product_category']['route']          = 'Product_Category';
$config['module']['product_category']['view']           = config_item('module_path') .'product_category/';
$config['module']['product_category']['language']       = 'account_payable_lang';
$config['module']['product_category']['helper']         = 'capex_request_helper';
$config['module']['product_category']['table']          = 'tb_product_categories';
$config['module']['product_category']['model']          = 'Product_Category_Model';
$config['module']['product_category']['permission']     = array(
  'index'   => 'ADMIN,SUPER ADMIN',
  'create'  => 'ADMIN,SUPER ADMIN',
  'import'  => 'ADMIN,SUPER ADMIN',
  'edit'    => 'ADMIN,SUPER ADMIN',
  'info'    => 'ADMIN,SUPER ADMIN',
  'save'    => 'ADMIN,SUPER ADMIN',
  'delete'  => 'ADMIN,SUPER ADMIN',
);
