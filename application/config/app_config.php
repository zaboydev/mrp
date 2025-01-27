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
  'engine'                        => 'engine',
  'engine instrument'             => 'engine instrument',
  'flight instrument'             => 'flight instrument',
  'avionics'                      => 'avionics',
  'modification'                  => 'modification',
  'other'                         => 'other',
);

$config['instrument_nf'] = array(
  'IFR' => 'IFR',
  'NF'  => 'NF',
  'VN'  => 'VN',
  'N'   => 'N',
);

$config['instrument_avionic'] = array(
  'GTX'     => 'GTX',
  'ASPEN'   => 'ASPEN',
  'GNS'     => 'GNS'
);

$config['modules_for_head_dept'] = array(
  'dashboard',
  'Capex Request',
  'Expense Request',
  'Inventory Request',
  'purchase_request',
  'purchase_order_evaluation',
  'Expense Order Evaluation',
  'Capex Order Evaluation',
  'business_trip_request',
  'sppd',
  'reimbursement',
  'expense_reimbursement'
);

$config['type_reimbursement'] = array(
  'Duty Allowance'  => 'Duty Allowance',
  'Ticket'          => 'Ticket',
  'Medical'         => 'Medical',
  'Others'          => 'Others'
);

$config['additional_modules_for_hr_depatment'] = array(
  'level',
  'tujuan_perjalanan_dinas',
  'employee_benefit',
  'user_position',
  'employee',
  'expense_reimbursement',
);

$config['hr_department_name'] = 'Human Resources Dept';

$config['account_code_hide'] = array(
  '5-9100',
  '5-9101',
  '5-9102',
  '5-9103',
  '5-9104',
  '5-9105',
  '5-9106',
  '5-9107',
  '5-9108',
  '5-9109',
  '5-9120',
  '5-9127',
  '5-9128',
  '5-9129',
  '6-0100',
  '6-0101',
  '6-0102',
  '6-0103',
  '6-0104',
  '6-0105',
  '6-0106',
  '6-0107',
  '6-0108',
  '6-0109',
  '6-0110',
  '6-0111',
  '6-0127',
  '6-0129',
  '6-0130',
);