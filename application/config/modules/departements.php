<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['departements']['visible']     = TRUE;
$config['module']['departements']['main_warehouse']   = TRUE;
$config['module']['departements']['parent']      = 'master';
$config['module']['departements']['label']       = 'Departements';
$config['module']['departements']['name']        = 'departements';
$config['module']['departements']['route']       = 'departements';
$config['module']['departements']['view']        = config_item('module_path') .'departements/';
$config['module']['departements']['language']    = 'departements_lang';
$config['module']['departements']['table']       = 'tb_departements';
$config['module']['departements']['model']       = 'Departements_Model';
$config['module']['departements']['helper']         = 'purchase_request_helper';
$config['module']['departements']['permission']  = array(
  'index'   => 'ADMIN,SUPER ADMIN',
  'create'  => 'ADMIN,SUPER ADMIN',
  'import'  => 'ADMIN,SUPER ADMIN',
  'edit'    => 'ADMIN,SUPER ADMIN',
  'info'    => 'ADMIN,SUPER ADMIN',
  'save'    => 'ADMIN,SUPER ADMIN',
  'delete'  => 'ADMIN,SUPER ADMIN',
  );
