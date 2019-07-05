<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['stock_mix']['visible']         = FALSE;
$config['module']['stock_mix']['main_warehouse']  = FALSE;
$config['module']['stock_mix']['parent']          = 'stock_mix';
$config['module']['stock_mix']['label']           = 'Mix Items';
$config['module']['stock_mix']['name']            = 'stock_mix';
$config['module']['stock_mix']['route']           = 'stock_mix';
$config['module']['stock_mix']['view']            = config_item('module_path') .'stock_mix/';
$config['module']['stock_mix']['language']        = 'stock_mix_lang';
$config['module']['stock_mix']['table']           = 'tb_stock_adjustments';
$config['module']['stock_mix']['model']           = 'Stock_Mix_Model';
$config['module']['stock_mix']['permission']      = array(
  'index'         => 'PIC PROCUREMENT,PIC STOCK,SUPERVISOR,OTHER,VP FINANCE',
  'info'          => 'PIC PROCUREMENT,PIC STOCK,SUPERVISOR,OTHER,VP FINANCE',
  'create'        => 'PIC STOCK,SUPERVISOR,VP FINANCE',
  'import'        => 'PIC STOCK,SUPERVISOR,VP FINANCE'
);
