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
  'index'       => 'ADMIN,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'login'       => 'ADMIN,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'logout'      => 'ADMIN,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'search'      => 'ADMIN,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'approval'    => 'ADMIN,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'denied'      => 'ADMIN,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'connection'  => 'ADMIN,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'maintenance' => 'ADMIN,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'password'    => 'ADMIN,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
);
