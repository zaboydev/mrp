<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['stock_adjustment']['visible']           = TRUE;
$config['module']['stock_adjustment']['main_warehouse']    = TRUE;
$config['module']['stock_adjustment']['parent']            = 'stock';
$config['module']['stock_adjustment']['label']             = 'Stock Adjustment';
$config['module']['stock_adjustment']['name']              = 'stock_adjustment';
$config['module']['stock_adjustment']['route']             = 'stock_adjustment';
$config['module']['stock_adjustment']['view']              = config_item('module_path') .'stock_adjustment/';
$config['module']['stock_adjustment']['language']          = 'stock_adjustment_lang';
$config['module']['stock_adjustment']['table']             = 'tb_stock_adjustments';
$config['module']['stock_adjustment']['model']             = 'Stock_Adjustment_Model';
$config['module']['stock_adjustment']['permission']        = array(
  'index'         => 'PIC STOCK,PIC PROCUREMENT,SUPERVISOR,OTHER',
  'show'          => 'PIC STOCK,PIC PROCUREMENT,SUPERVISOR,OTHER',
  'adjustment'    => 'PIC STOCK,SUPERVISOR',
);
