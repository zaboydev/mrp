<?php
$config['maintenance']  = FALSE;
$config['site_mode']    = 'dev';
$config['theme_path']   = 'material';
$config['module_path']  = $config['theme_path'] . '/modules/';

$config['include_base_on_document'] = FALSE;
$config['document_format_divider']  = '/';

$config['condition'] = array(
  'SERVICEABLE',
  'UNSERVICEABLE',
  'REJECTED',
  // 'ALL'       => 'All Condition',
);

$config['document_type'] = array(
  'GRN'   => 'Goods Received Note',
  'MS'    => 'Material Slip',
  'CI'    => 'Commercial Invoice',
  'SD'    => 'Shipping Document',
  'DP'    => 'Internal Delivery',
  'PR'    => 'Purchase Request',
  'PO'    => 'Purchase Order',
  'POE'   => 'Purchase Order Evaluation',
  );

$config['currency'] = array(
  'USD' => 'USD',
  'IDR' => 'IDR',
  'AUD' => 'AUD',
  // 'ALL'       => 'All Condition',
);


$config['access_from']  = 'server_mrp';
