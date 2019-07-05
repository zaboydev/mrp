<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['purchase_order_evaluation']['visible']        = FALSE;
$config['module']['purchase_order_evaluation']['main_warehouse'] = TRUE;
$config['module']['purchase_order_evaluation']['parent']         = 'document';
$config['module']['purchase_order_evaluation']['label']          = 'Purchase Order Evaluation';
$config['module']['purchase_order_evaluation']['name']           = 'purchase_order_evaluation';
$config['module']['purchase_order_evaluation']['route']          = 'purchase_order_evaluation';
$config['module']['purchase_order_evaluation']['view']           = config_item('module_path') .'purchase_order_evaluation/';
$config['module']['purchase_order_evaluation']['language']       = 'purchase_order_evaluation_lang';
$config['module']['purchase_order_evaluation']['helper']         = 'purchase_order_evaluation_helper';
$config['module']['purchase_order_evaluation']['table']          = 'tb_purchase_order_evaluations';
$config['module']['purchase_order_evaluation']['model']          = 'Purchase_Order_Evaluation_Model';
$config['module']['purchase_order_evaluation']['permission']     = array(
  'index'     => 'PIC PROCUREMENT',
  'info'      => 'PIC PROCUREMENT',
  'document'  => 'PIC PROCUREMENT',
  'print'     => 'PIC PROCUREMENT',
);
