<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['secure']['visible']    = FALSE;
$config['module']['secure']['main_warehouse']  = FALSE;
$config['module']['secure']['parent']     = 'secure';
$config['module']['secure']['name']       = 'secure';
$config['module']['secure']['label']      = 'Security';
$config['module']['secure']['route']      = 'secure';
$config['module']['secure']['view']       = config_item('module_path') .'secure/';
$config['module']['secure']['language']   = 'secure_lang';
$config['module']['secure']['table']      = '';
// $config['module']['secure']['model']      = 'Auth_Model';
$config['module']['secure']['model']      = 'Secure_Model';
$config['module']['secure']['permission'] = array(
  'index'       => 'PPC,HR STAFF,MECHANIC,FINANCE SUPERVISOR,PIC STAFF JKT,HEAD DEPT UNIQ JKT,PIC STAFF UNIQ JKT,OTHER,FINANCE,VP FINANCE,ADMIN,SUPERVISOR,PIC STOCK,PIC PROCUREMENT,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,PROCUREMENT,SUPER ADMIN,FINANCE MANAGER,OPERATION SUPPORT,CHIEF OPERATION OFFICER,AP STAFF,TELLER,BUDGETCONTROL,PIC STAFF,PROCUREMENT MANAGER,ASSISTANT HOS',
  
  'login'       => 'PPC,HR STAFF,MECHANIC,FINANCE SUPERVISOR,PIC STAFF JKT,HEAD DEPT UNIQ JKT,PIC STAFF UNIQ JKT,OTHER,FINANCE,VP FINANCE,ADMIN,SUPERVISOR,PIC STOCK,PIC PROCUREMENT,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,PROCUREMENT,SUPER ADMIN,FINANCE MANAGER,OPERATION SUPPORT,CHIEF OPERATION OFFICER,AP STAFF,TELLER,BUDGETCONTROL,PIC STAFF,PROCUREMENT MANAGER,ASSISTANT HOS',

  'logout'      => 'PPC,HR STAFF,MECHANIC,FINANCE SUPERVISOR,PIC STAFF JKT,HEAD DEPT UNIQ JKT,PIC STAFF UNIQ JKT,OTHER,FINANCE,VP FINANCE,ADMIN,SUPERVISOR,PIC STOCK,PIC PROCUREMENT,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,PROCUREMENT,SUPER ADMIN,FINANCE MANAGER,OPERATION SUPPORT,CHIEF OPERATION OFFICER,AP STAFF,TELLER,BUDGETCONTROL,PIC STAFF,PROCUREMENT MANAGER,ASSISTANT HOS',

  'search'      => 'PPC,HR STAFF,MECHANIC,FINANCE SUPERVISOR,PIC STAFF JKT,HEAD DEPT UNIQ JKT,PIC STAFF UNIQ JKT,OTHER,FINANCE,VP FINANCE,ADMIN,SUPERVISOR,PIC STOCK,PIC PROCUREMENT,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,PROCUREMENT,SUPER ADMIN,FINANCE MANAGER,OPERATION SUPPORT,CHIEF OPERATION OFFICER,AP STAFF,TELLER,BUDGETCONTROL,PIC STAFF,PROCUREMENT MANAGER,ASSISTANT HOS',

  'approval'    => 'PPC,HR STAFF,MECHANIC,FINANCE SUPERVISOR,PIC STAFF JKT,HEAD DEPT UNIQ JKT,PIC STAFF UNIQ JKT,OTHER,FINANCE,VP FINANCE,ADMIN,SUPERVISOR,PIC STOCK,PIC PROCUREMENT,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,PROCUREMENT,SUPER ADMIN,FINANCE MANAGER,OPERATION SUPPORT,CHIEF OPERATION OFFICER,AP STAFF,TELLER,BUDGETCONTROL,PIC STAFF,PROCUREMENT MANAGER,ASSISTANT HOS',

  'denied'      => 'PPC,HR STAFF,MECHANIC,FINANCE SUPERVISOR,PIC STAFF JKT,HEAD DEPT UNIQ JKT,PIC STAFF UNIQ JKT,OTHER,FINANCE,VP FINANCE,ADMIN,SUPERVISOR,PIC STOCK,PIC PROCUREMENT,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,PROCUREMENT,SUPER ADMIN,FINANCE MANAGER,OPERATION SUPPORT,CHIEF OPERATION OFFICER,AP STAFF,TELLER,BUDGETCONTROL,PIC STAFF,PROCUREMENT MANAGER,ASSISTANT HOS',

  'connection'  => 'PPC,HR STAFF,MECHANIC,FINANCE SUPERVISOR,PIC STAFF JKT,HEAD DEPT UNIQ JKT,PIC STAFF UNIQ JKT,OTHER,FINANCE,VP FINANCE,ADMIN,SUPERVISOR,PIC STOCK,PIC PROCUREMENT,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,PROCUREMENT,SUPER ADMIN,FINANCE MANAGER,OPERATION SUPPORT,CHIEF OPERATION OFFICER,AP STAFF,TELLER,BUDGETCONTROL,PIC STAFF,PROCUREMENT MANAGER,ASSISTANT HOS',

  'maintenance' => 'PPC,HR STAFF,MECHANIC,FINANCE SUPERVISOR,PIC STAFF JKT,HEAD DEPT UNIQ JKT,PIC STAFF UNIQ JKT,OTHER,FINANCE,VP FINANCE,ADMIN,SUPERVISOR,PIC STOCK,PIC PROCUREMENT,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,PROCUREMENT,SUPER ADMIN,FINANCE MANAGER,OPERATION SUPPORT,CHIEF OPERATION OFFICER,AP STAFF,TELLER,BUDGETCONTROL,PIC STAFF,PROCUREMENT MANAGER,ASSISTANT HOS',

  'password'    => 'PPC,HR STAFF,MECHANIC,FINANCE SUPERVISOR,PIC STAFF JKT,HEAD DEPT UNIQ JKT,PIC STAFF UNIQ JKT,OTHER,FINANCE,VP FINANCE,ADMIN,SUPERVISOR,PIC STOCK,PIC PROCUREMENT,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,PROCUREMENT,SUPER ADMIN,FINANCE MANAGER,OPERATION SUPPORT,CHIEF OPERATION OFFICER,AP STAFF,TELLER,BUDGETCONTROL,PIC STAFF,PROCUREMENT MANAGER,ASSISTANT HOS',
);
