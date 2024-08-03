<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['pdf']['visible']    = FALSE;
$config['module']['pdf']['main_warehouse']  = FALSE;
$config['module']['pdf']['parent']     = 'dashboard';
$config['module']['pdf']['name']       = 'pdf';
$config['module']['pdf']['label']      = 'PDF';
$config['module']['pdf']['route']      = '';
$config['module']['pdf']['view']       = config_item('module_path') .'pdf/';
$config['module']['pdf']['language']   = 'pdf_lang';
$config['module']['pdf']['table']      = '';
$config['module']['pdf']['model']      = 'Pdf_Model';
$config['module']['pdf']['permission'] = array(
  'index' => 'PPC,MECHANIC,OTHER,FINANCE,VP FINANCE,ADMIN,SUPERVISOR,PIC STOCK,PIC PROCUREMENT,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,PROCUREMENT,SUPER ADMIN,FINANCE MANAGER,OPERATION SUPPORT,CHIEF OPERATION OFFICER',
  );
