<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['stores']['visible']    = TRUE;
$config['module']['stores']['main_warehouse']  = FALSE;
$config['module']['stores']['parent']     = 'master';
$config['module']['stores']['label']      = 'Stores';
$config['module']['stores']['name']       = 'stores';
$config['module']['stores']['route']      = 'stores';
$config['module']['stores']['view']       = config_item('module_path') .'stores/';
$config['module']['stores']['language']   = 'stores_lang';
$config['module']['stores']['table']      = 'tb_master_stores';
$config['module']['stores']['model']      = 'Stores_Model';
$config['module']['stores']['permission'] = array(
  'index'   => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPERVISOR,PIC STOCK,SUPER ADMIN',
  'create'  => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'import'  => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'edit'    => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'info'    => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'save'    => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'delete'  => 'ADMIN,SUPER ADMIN',
  );
