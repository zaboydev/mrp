<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['dashboard']['visible']    = FALSE;
$config['module']['dashboard']['main_warehouse']  = FALSE;
$config['module']['dashboard']['parent']     = 'dashboard';
$config['module']['dashboard']['name']       = 'dashboard';
$config['module']['dashboard']['label']      = 'Dashboard';
$config['module']['dashboard']['route']      = '';
$config['module']['dashboard']['view']       = config_item('module_path') .'dashboard/';
$config['module']['dashboard']['language']   = 'dashboard_lang';
$config['module']['dashboard']['table']      = '';
$config['module']['dashboard']['model']      = 'dashboard_Model';
$config['module']['dashboard']['permission'] = array(
  'index' => 'ASSISTANT HOS,PROCUREMENT MANAGER,AP STAFF,TELLER,ADMIN,PROCUREMENT,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,SUPER ADMIN,FINANCE MANAGER,OPERATION SUPPORT,CHIEF OPERATION OFFICER,BUDGETCONTROL,PIC STAFF',
  'approval' => 'ASSISTANT HOS,PROCUREMENT MANAGER,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,FINANCE MANAGER,SUPER ADMIN,VP FINANCE,OPERATION SUPPORT,CHIEF OPERATION OFFICER,BUDGETCONTROL'
);
