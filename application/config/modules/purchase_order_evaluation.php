<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['purchase_order_evaluation']['visible']        = TRUE;
$config['module']['purchase_order_evaluation']['main_warehouse'] = TRUE;
$config['module']['purchase_order_evaluation']['parent']         = 'procurement';
$config['module']['purchase_order_evaluation']['label']          = 'Purchase Order Evaluation';
$config['module']['purchase_order_evaluation']['name']           = 'purchase_order_evaluation';
$config['module']['purchase_order_evaluation']['route']          = 'purchase_order_evaluation';
$config['module']['purchase_order_evaluation']['view']           = config_item('module_path') .'purchase_order_evaluation/';
$config['module']['purchase_order_evaluation']['language']       = 'purchase_order_evaluation_lang';
$config['module']['purchase_order_evaluation']['helper']         = 'purchase_order_evaluation_helper';
$config['module']['purchase_order_evaluation']['table']          = 'tb_purchase_order_evaluations';
$config['module']['purchase_order_evaluation']['model']          = 'Purchase_Order_Evaluation_Model';
$config['module']['purchase_order_evaluation']['permission']     = array(
  'index'     => 'PIC PROCUREMENT,SUPERVISOR,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,FINANCE,PROCUREMENT,SUPER ADMIN',
  'info'      => 'PIC PROCUREMENT,SUPERVISOR,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,FINANCE,PROCUREMENT,SUPER ADMIN',
  'document'  => 'PIC PROCUREMENT,SUPERVISOR,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,FINANCE,PROCUREMENT,SUPER ADMIN',//tambahan supervisor semua permission
  'approval'  => 'CHIEF OF MAINTANCE,SUPER ADMIN',
  'print'     => 'PIC PROCUREMENT,SUPERVISOR,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,FINANCE,PROCUREMENT,SUPER ADMIN,VP FINANCE,FINANCE MANAGER,VP FINANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,CHIEF OPERATION SUPPORT',
);
