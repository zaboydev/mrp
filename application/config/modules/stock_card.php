<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['stock_card']['visible']        = TRUE;
$config['module']['stock_card']['main_warehouse'] = TRUE;
$config['module']['stock_card']['parent']         = 'stock';
$config['module']['stock_card']['label']          = 'Stock Card';
$config['module']['stock_card']['name']           = 'stock_card';
$config['module']['stock_card']['route']          = 'stock_card';
$config['module']['stock_card']['view']           = config_item('module_path') .'stock_card/';
$config['module']['stock_card']['language']       = 'stock_card_lang';
$config['module']['stock_card']['table']          = 'tb_stock_cards';
$config['module']['stock_card']['model']          = 'Stock_Card_Model';
$config['module']['stock_card']['permission']     = array(
  'index'     => 'FINANCE,VP FINANCE',
  'info'      => 'FINANCE,VP FINANCE',
  'detail'    => 'FINANCE,VP FINANCE',
);
