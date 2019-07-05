<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['category']['visible']     = TRUE;
$config['module']['category']['main_warehouse']   = TRUE;
$config['module']['category']['parent']      = 'master';
$config['module']['category']['label']       = 'Item Category';
$config['module']['category']['name']        = 'item_category';
$config['module']['category']['route']       = 'item_category';
$config['module']['category']['view']        = config_item('module_path') .'item_category/';
$config['module']['category']['language']    = 'item_category_lang';
$config['module']['category']['table']       = 'tb_master_item_categories';
$config['module']['category']['model']       = 'Item_Category_Model';
$config['module']['category']['permission']  = array(
  'index'   => 'ADMIN,PIC PROCUREMENT,FINANCE',
  'create'  => 'ADMIN,PIC PROCUREMENT,FINANCE',
  'import'  => 'ADMIN,PIC PROCUREMENT,FINANCE',
  'edit'    => 'ADMIN,PIC PROCUREMENT,FINANCE',
  'info'    => 'ADMIN,PIC PROCUREMENT,FINANCE',
  'save'    => 'ADMIN,PIC PROCUREMENT,FINANCE',
  'delete'  => 'ADMIN,FINANCE',
  );
