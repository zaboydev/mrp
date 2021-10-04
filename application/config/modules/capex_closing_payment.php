<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['capex_closing_payment']['visible']        = TRUE;
$config['module']['capex_closing_payment']['main_warehouse'] = TRUE;
$config['module']['capex_closing_payment']['parent']         = 'capex';
$config['module']['capex_closing_payment']['label']          = 'Capex Closing Payment';
$config['module']['capex_closing_payment']['name']           = 'Capex Closing Payment';
$config['module']['capex_closing_payment']['route']          = 'capex_closing_payment';
$config['module']['capex_closing_payment']['view']           = config_item('module_path') .'capex/closing_payment/';
$config['module']['capex_closing_payment']['language']       = 'account_payable_lang';
$config['module']['capex_closing_payment']['helper']         = 'purchase_order_helper';
$config['module']['capex_closing_payment']['table']          = 'tb_po';
$config['module']['capex_closing_payment']['model']          = 'Capex_Closing_Payment_Model';
$config['module']['capex_closing_payment']['permission']     = array(
  'index'     => 'SUPER ADMIN,TELLER,AP STAFF',
  'info'      => 'SUPER ADMIN,TELLER,AP STAFF',
  'print'     => 'SUPER ADMIN,TELLER,AP STAFF',
  'document'  => 'SUPER ADMIN,TELLER,AP STAFF',  
  'approval'  => 'SUPER ADMIN,VP FINANCE,HEAD OF SCHOOL,CHIEF OPERATION OFFICER',
);
