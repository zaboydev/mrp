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
  'index'   => 'ADMIN,PIC PROCUREMENT,PIC STOCK,SUPERVISOR',
  'create'  => 'ADMIN,PIC STOCK,SUPERVISOR',
  'import'  => 'ADMIN,PIC STOCK,SUPERVISOR',
  'edit'    => 'ADMIN,PIC STOCK,SUPERVISOR',
  'info'    => 'ADMIN,PIC STOCK,SUPERVISOR',
  'save'    => 'ADMIN,PIC STOCK,SUPERVISOR',
  'delete'  => 'ADMIN,PIC STOCK,SUPERVISOR',
  );
