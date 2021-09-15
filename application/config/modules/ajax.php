<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['ajax']['visible']    = FALSE;
$config['module']['ajax']['main_warehouse']  = FALSE;
$config['module']['ajax']['parent']     = 'dashboard';
$config['module']['ajax']['name']       = 'ajax';
$config['module']['ajax']['label']      = 'ajax';
$config['module']['ajax']['route']      = 'ajax';
$config['module']['ajax']['view']       = config_item('module_path') .'ajax/';
$config['module']['ajax']['language']   = 'ajax_lang';
$config['module']['ajax']['table']      = '';
$config['module']['ajax']['model']      = 'Ajax_Model';
$config['module']['ajax']['permission'] = array(
  'index' => 'HEAD DEPT UNIQ JKT,PIC STAFF UNIQ JKT,ASSISTANT HOS,PROCUREMENT MANAGER,AP STAFF,TELLER,OTHER,FINANCE,VP FINANCE,ADMIN,SUPERVISOR,PIC STOCK,PIC PROCUREMENT,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,PROCUREMENT,SUPER ADMIN,FINANCE MANAGER,OPERATION SUPPORT,CHIEF OPERATION OFFICER,PIC STAFF',
  );
