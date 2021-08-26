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
  'index'     => 'SUPER ADMIN,BUDGETCONTROL,PIC STAFF',
  'info'      => 'SUPER ADMIN,BUDGETCONTROL,PIC STAFF',
  'print'     => 'SUPER ADMIN,BUDGETCONTROL,PIC STAFF',
  'document'  => 'SUPER ADMIN,PIC STAFF',
  'approval'  => 'BUDGETCONTROL'
);
