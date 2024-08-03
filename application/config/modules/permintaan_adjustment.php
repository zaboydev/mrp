<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['permintaan_adjustment']['visible']         = TRUE;
$config['module']['permintaan_adjustment']['main_warehouse']  = TRUE;
$config['module']['permintaan_adjustment']['parent']          = 'stock';
$config['module']['permintaan_adjustment']['label']           = 'Adjustment Request';
$config['module']['permintaan_adjustment']['name']            = 'permintaan_adjustment';
$config['module']['permintaan_adjustment']['route']           = 'Permintaan_Adjustment';
$config['module']['permintaan_adjustment']['view']            = config_item('module_path') .'permintaan_adjustment/';
$config['module']['permintaan_adjustment']['language']        = 'permintaan_adjustment_lang';
$config['module']['permintaan_adjustment']['table']           = 'tb_stock_adjustment';
$config['module']['permintaan_adjustment']['model']           = 'Permintaan_Adjustment_Model';
$config['module']['permintaan_adjustment']['permission']      = array(
  'index'     => 'VP FINANCE,SUPER ADMIN',
  'summary'   => 'VP FINANCE,SUPER ADMIN',
  'detail'    => 'VP FINANCE,SUPER ADMIN',
);
