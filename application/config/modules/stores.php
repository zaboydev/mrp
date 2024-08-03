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
  'create'  => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN,SUPERVISOR',
  'import'  => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'edit'    => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN,SUPERVISOR',
  'info'    => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN,SUPERVISOR',
  'save'    => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN,SUPERVISOR',
  'delete'  => 'ADMIN,SUPER ADMIN,SUPERVISOR',
  );
