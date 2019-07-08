<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['expired_stock']['visible']         = TRUE;
$config['module']['expired_stock']['main_warehouse']  = TRUE;
$config['module']['expired_stock']['parent']          = 'stock';
$config['module']['expired_stock']['label']           = 'Expire Stock Material';
$config['module']['expired_stock']['name']            = 'expired_stock';
$config['module']['expired_stock']['route']           = 'Expired_Stock';
$config['module']['expired_stock']['view']            = config_item('module_path') .'expire_stock/';
$config['module']['expired_stock']['language']        = 'expired_stock_lang';
$config['module']['expired_stock']['table']           = 'tb_master_part_number';
$config['module']['expired_stock']['model']           = 'Expired_Stock_Model';
$config['module']['expired_stock']['permission']      = array(
  'index'     => 'PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'summary'   => 'VP FINANCE,SUPERVISOR,SUPER ADMIN',
  'detail'    => 'VP FINANCE,SUPERVISOR,SUPER ADMIN',
);
