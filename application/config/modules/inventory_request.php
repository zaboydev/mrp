<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['inventory_request']['visible']        = TRUE;
$config['module']['inventory_request']['main_warehouse'] = FALSE;
$config['module']['inventory_request']['parent']         = 'inventory';
$config['module']['inventory_request']['label']          = 'Inventory Request';
$config['module']['inventory_request']['name']           = 'Inventory Request';
$config['module']['inventory_request']['route']          = 'inventory_request';
$config['module']['inventory_request']['view']           = config_item('module_path') .'inventory/request/';
$config['module']['inventory_request']['language']       = 'account_payable_lang';
$config['module']['inventory_request']['helper']         = 'inventory_request_helper';
$config['module']['inventory_request']['table']          = 'tb_inventory_purchase_requisitions';
$config['module']['inventory_request']['model']          = 'Inventory_Request_Model';
$config['module']['inventory_request']['permission']     = array(
  'index'     => 'PIC STAFF JKT,HEAD DEPT UNIQ JKT,PIC STAFF UNIQ JKT,SUPER ADMIN,BUDGETCONTROL,PIC STAFF,PIC PROCUREMENT,ASSISTANT HOS',
  'info'      => 'PIC STAFF JKT,HEAD DEPT UNIQ JKT,PIC STAFF UNIQ JKT,SUPER ADMIN,BUDGETCONTROL,PIC STAFF,PIC PROCUREMENT,ASSISTANT HOS',
  'print'     => 'FINANCE SUPERVISOR,PIC STAFF JKT,HEAD DEPT UNIQ JKT,PIC STAFF UNIQ JKT,AP STAFF,SUPER ADMIN,BUDGETCONTROL,PIC STAFF,PIC PROCUREMENT,ASSISTANT HOS',
  'document'  => 'PIC STAFF JKT,PIC STAFF UNIQ JKT,SUPER ADMIN,PIC STAFF',
  'closing'   => 'SUPER ADMIN,PIC PROCUREMENT',
  'approval'  => 'HEAD DEPT UNIQ JKT,BUDGETCONTROL,ASSISTANT HOS',
);
