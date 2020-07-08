<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['user']['visible']    = TRUE;
$config['module']['user']['main_warehouse']  = TRUE;
$config['module']['user']['parent']     = 'master';
$config['module']['user']['name']       = 'user';
$config['module']['user']['label']      = 'User';
$config['module']['user']['route']      = 'user';
$config['module']['user']['view']       = config_item('module_path') .'user/';
$config['module']['user']['language']   = 'user_lang';
$config['module']['user']['table']      = 'tb_auth_users';
$config['module']['user']['model']      = 'User_Model';
$config['module']['user']['permission'] = array(
  'index'   => 'ADMIN,SUPER ADMIN,SUPERVISOR',
  'create'  => 'ADMIN,SUPER ADMIN,SUPERVISOR',
  'edit'    => 'ADMIN,SUPER ADMIN,SUPERVISOR',
  'info'    => 'ADMIN,SUPER ADMIN,SUPERVISOR',
  'save'    => 'ADMIN,SUPER ADMIN,SUPERVISOR',
  'delete'  => 'ADMIN,SUPER ADMIN,SUPERVISOR',
  'import'  => 'ADMIN,SUPER ADMIN,SUPERVISOR',
  );
