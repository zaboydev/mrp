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

$config['account_types'] = array(
  'Asset'                   => 'Asset',
  'Bank'                    => 'Bank',
  'Other Asset'             => 'Other Asset',
  'Accounts Receivable'     => 'Accounts Receivable',
  'Other Current Asset'     => 'Other Current Asset',
  'Cash'                    => 'Cash',
  'Fixed Asset'             => 'Fixed Asset',
  'Liability'               => 'Liability',
  'Accounts Payable'        => 'Accounts Payable',
  'Other Liability'         => 'Other Liability',
  'Other Current Liability' => 'Other Current Liability',
  'Credit Card'             => 'Credit Card',
  'Long Term Liability'     => 'Long Term Liability',
  'Equity'                  => 'Equity',
  'Income'                  => 'Income',
  'Cost of Sales'           => 'Cost of Sales',
  'Expense'                 => 'Expense',
  'Other Income'            => 'Other Income',
  'Other Expense'           => 'Other Expense',
);


$config['access_from']  = 'server_mrp';
$config['head_office_cost_center_id']  = [26,30];
$config['unique_user']  = ['aidanurul'];
$config['url_mrp']      = 'http://to.baliflightacademy.com:7323/';
$config['url_budgetcontrol']      = 'http://to.baliflightacademy.com:7324/';

$config['source_grn'] = array(
  'purchase_order'    => 'Purchase Order',
  'internal_delivery' => 'Internal Delivery',
);

$config['source_return_service'] = array(
  'stock'             => 'Stock',
  'internal_delivery' => 'Internal Delivery',
);

$config['source_poe'] = array(
  'request'             => 'Purchase Request',
  'return'              => 'Return & Service',
);

$config['component_type'] = array(
  'system'                        => 'system',
  'engine instrument'             => 'engine instrument',
  'flight instrument'             => 'flight instrument',
  'avionics'                      => 'avionics',
  'modification'                  => 'modification',
);
