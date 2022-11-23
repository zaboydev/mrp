<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['aircraft_component_status']['visible']         = FALSE;
$config['module']['aircraft_component_status']['main_warehouse']  = TRUE;
$config['module']['aircraft_component_status']['parent']          = 'aircraft';
$config['module']['aircraft_component_status']['label']           = 'Component Status';
$config['module']['aircraft_component_status']['name']            = 'aircraft_component_status';
$config['module']['aircraft_component_status']['route']           = 'aircraft_component_status';
$config['module']['aircraft_component_status']['view']            = config_item('module_path') .'pesawat/component_status/';
$config['module']['aircraft_component_status']['language']        = 'kurs_lang';
$config['module']['aircraft_component_status']['table']           = 'tb_master_pesawat';
$config['module']['aircraft_component_status']['model']           = 'Pesawat_Model';
$config['module']['aircraft_component_status']['permission']      = array(
  'create'                  => 'PPC,MECHANIC,ADMIN,SUPERVISOR,SUPER ADMIN',
  'edit'                    => 'PPC,MECHANIC,ADMIN,SUPERVISOR,SUPER ADMIN',
  'info'                    => 'PPC,MECHANIC,ADMIN,SUPERVISOR,SUPER ADMIN',
  'save'                    => 'PPC,MECHANIC,ADMIN,SUPERVISOR,SUPER ADMIN',
  'delete'                  => 'PPC,MECHANIC,ADMIN,SUPERVISOR,SUPER ADMIN',
  'index'  	                => 'PPC,MECHANIC,ADMIN,SUPERVISOR,SUPER ADMIN',
  'create_component'        => 'SUPER ADMIN,PPC,MECHANIC',  
  'create_component_status' => 'SUPER ADMIN,PPC,MECHANIC',
  'approval'                => 'SUPER ADMIN,CHIEF OF MAINTANCE'
  );
