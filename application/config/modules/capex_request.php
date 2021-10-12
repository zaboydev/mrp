<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['capex_request']['visible']        = TRUE;
$config['module']['capex_request']['main_warehouse'] = FALSE;
$config['module']['capex_request']['parent']         = 'capex';
$config['module']['capex_request']['label']          = 'Capex Request';
$config['module']['capex_request']['name']           = 'Capex Request';
$config['module']['capex_request']['route']          = 'capex_request';
$config['module']['capex_request']['view']           = config_item('module_path') .'capex/request/';
$config['module']['capex_request']['language']       = 'account_payable_lang';
$config['module']['capex_request']['helper']         = 'capex_request_helper';
$config['module']['capex_request']['table']          = 'tb_capex_purchase_requisitions';
$config['module']['capex_request']['model']          = 'Capex_Request_Model';
$config['module']['capex_request']['permission']     = array(
  'index'     => 'PIC STAFF JKT,HEAD DEPT UNIQ JKT,PIC STAFF UNIQ JKT,AP STAFF,SUPER ADMIN,BUDGETCONTROL,VP FINANCE,PIC STAFF,HEAD OF SCHOOL,CHIEF OPERATION OFFICER,FINANCE MANAGER,CHIEF OF FINANCE,PIC PROCUREMENT,ASSISTANT HOS',
  'info'      => 'PIC STAFF JKT,HEAD DEPT UNIQ JKT,PIC STAFF UNIQ JKT,AP STAFF,SUPER ADMIN,BUDGETCONTROL,VP FINANCE,PIC STAFF,HEAD OF SCHOOL,CHIEF OPERATION OFFICER,FINANCE MANAGER,CHIEF OF FINANCE,PIC PROCUREMENT,ASSISTANT HOS',
  'print'     => 'FINANCE SUPERVISOR,PIC STAFF JKT,HEAD DEPT UNIQ JKT,PIC STAFF UNIQ JKT,AP STAFF,SUPER ADMIN,BUDGETCONTROL,VP FINANCE,PIC STAFF,HEAD OF SCHOOL,CHIEF OPERATION OFFICER,FINANCE MANAGER,CHIEF OF FINANCE,PIC PROCUREMENT,ASSISTANT HOS',
  'document'  => 'PIC STAFF JKT,PIC STAFF UNIQ JKT,SUPER ADMIN,PIC STAFF',
  'closing'   => 'PIC PROCUREMENT,SUPER ADMIN',
  'payment'   => 'AP STAFF,SUPER ADMIN',
  'approval'  => 'HEAD DEPT UNIQ JKT,BUDGETCONTROL,VP FINANCE,HEAD OF SCHOOL,CHIEF OPERATION OFFICER,FINANCE MANAGER,CHIEF OF FINANCE,ASSISTANT HOS'
);
