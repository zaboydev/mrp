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
  'index' => 'ADMIN,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,SUPER ADMIN,FINANCE MANAGER',
  'approval' => 'CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,FINANCE MANAGER,SUPER ADMIN'
);
