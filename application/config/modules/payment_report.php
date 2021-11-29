<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['payment_report']['visible']        = TRUE;
$config['module']['payment_report']['main_warehouse'] = TRUE;
$config['module']['payment_report']['parent']         = 'account_payable';
$config['module']['payment_report']['label']          = 'Payment Report';
$config['module']['payment_report']['name']           = 'Payment Report';
$config['module']['payment_report']['route']          = 'payment_report';
$config['module']['payment_report']['view']           = config_item('module_path') .'payment/';
$config['module']['payment_report']['language']       = 'payment_report_lang';
$config['module']['payment_report']['helper']         = 'material_slip_helper';
$config['module']['payment_report']['table']          = 'tb_po_payments';
$config['module']['payment_report']['model']          = 'Payment_Model';
$config['module']['payment_report']['permission']     = array(
  'index'     => 'FINANCE SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER,AP STAFF',
  'info'      => 'FINANCE SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER,AP STAFF',
  'print'     => 'FINANCE SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER,AP STAFF',
  'document'  => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER,AP STAFF',//tambhan supervisor
  'payment'   => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER,AP STAFF',
  'import'    => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER,AP STAFF',//tambhan supervisor
);
