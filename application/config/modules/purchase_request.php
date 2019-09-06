<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['purchase_request']['visible']        = TRUE;
$config['module']['purchase_request']['main_warehouse'] = TRUE;
$config['module']['purchase_request']['parent']         = 'procurement';
$config['module']['purchase_request']['label']          = 'Purchase Request';
$config['module']['purchase_request']['name']           = 'purchase_request';
$config['module']['purchase_request']['route']          = 'purchase_request';
$config['module']['purchase_request']['view']           = config_item('module_path') .'purchase_request/';
$config['module']['purchase_request']['language']       = 'purchase_request_lang';
$config['module']['purchase_request']['helper']         = 'purchase_request_helper';
$config['module']['purchase_request']['table']          = 'tb_purchase_requests';
$config['module']['purchase_request']['model']          = 'Purchase_Request_Model';
$config['module']['purchase_request']['permission']     = array(
  'index'     => 'PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN,CHIEF OF MAINTANCE,FINANCE MANAGER,OPERATION SUPPORT',
  'info'      => 'PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN,CHIEF OF MAINTANCE,FINANCE MANAGER,OPERATION SUPPORT',
  'document'  => 'PIC STOCK,SUPERVISOR,PIC PROCUREMENT,SUPER ADMIN',
  'print'     => 'PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN,OPERATION SUPPORT,CHIEF OPERATION SUPPORT,FINANCE MANAGER,HEAD OF SCHOOL,CHIEF OF FINANCE',
  'approval'  => 'CHIEF OF MAINTANCE,FINANCE MANAGER,SUPER ADMIN,OPERATION SUPPORT',
);
