<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Controller $controller
 * @property M_pdf $m_pdf
 * @property item_model $item_model
 *
 * Add additional libraries you wish
 * to use in your controllers here
 *
 * @property $data array
 * @property $rule array
 *
 */
class Report extends MY_Controller
{
  protected $module;


  public function __construct()
  {
    parent::__construct();

    $this->module    = 'report';
    $this->module['view'] = '_modules/'.$this->module;

    $this->lang->load($this->module['language']);
    $this->load->model($this->module['model'], 'model');
    // $this->load->helper($this->module);

    $this->set_data('module', $this->module);
    $this->set_data('page_header', lang('page_header'));
    $this->set_data('page_desc', NULL);
  }

  /**
   * GENERAL STOCK REPORT
   * url: report/stock_general
   */
  public function summary_stock()
  {
    // ========================= ACCESS DENIED ========================== //
    $this->authorized($this->acl['stock']['index']);

    // ========================= ACCESS GRANTED ========================= //

    if (isset($_GET['year']) && !empty($_GET['year'])){
      $year = $_GET['year'];
    } else {
      $year = date('Y');
    }

    if (isset($_GET['month']) && !empty($_GET['month'])){
      $month = $_GET['month'];
    } else {
      $month = date('n')-1;
    }

    $period = $this->model->findPeriodInfo($year, $month);

    if (empty($period)){
      $this->set_data('page_content', $this->module['view'] .'/report_empty');
      $this->set_data('page_title', sprintf(lang('page_title_summary_empty')));
    } else {
      $start_date = $period->start_date;
      $end_date = $period->end_date;
      $entities = $this->model->findSummaryStock($start_date, $end_date);

      $this->set_data('entities', $entities);
      $this->set_data('page_content', $this->module['view'] .'/summary_stock');
      $this->set_data('page_title', sprintf(lang('page_title_summary'), $this->monthName($month, 'uppercase'), $year));
    }

    // where to go if single click perform
    if (isset($this->module['permission']['stock']) && $this->has_role($this->module['permission']['stock'])){
      $this->data['singleClickUrl'] = site_url($this->module['route'] .'/stock_general');
    }

    // where to go if double click perform
    if ($this->has_role($this->module, 'edit')){
      $this->data['doubleClickUrl'] = site_url($this->module['route'] .'/edit');
    }

    $this->set_data('year', $year);
    $this->set_data('month', $month);
    $this->set_data('sidebar', $this->module['view'] .'/summary_stock_sidebar');

    $this->render_view();
  }

  public function stock_general()
  {
    // ========================= ACCESS DENIED ========================== //
    $this->authorized($this->acl['stock']['index']);

    // ========================= ACCESS GRANTED ========================= //

    if (isset($_GET['year']) && !empty($_GET['year'])){
      $year = $_GET['year'];
    } else {
      $year = date('Y');
    }

    if (isset($_GET['month']) && !empty($_GET['month'])){
      $month = $_GET['month'];
    } else {
      $month = date('n')-1;
    }

    $period = $this->model->findPeriodInfo($year, $month);

    if (isset($_GET['group']) && !empty($_GET['group'])){
      $group = $_GET['group'];
    } else {
      $group = 'ALL';
    }

    $group = $this->model->findItemGroupInfo($group);
    $this->set_data('group', $group);

    if (isset($_GET['warehouse']) && !empty($_GET['warehouse'])){
      $warehouse = $_GET['warehouse'];
    } else {
      $warehouse = 'GENERAL';
    }

    $this->set_data('warehouse', $warehouse);

    if (isset($_GET['condition']) && !empty($_GET['condition'])){
      $condition = $_GET['condition'];
    } else {
      $condition = 'S/S';
    }

    $this->set_data('condition', $condition);

    if (empty($period)){
      $this->set_data('page_content', $this->module['view'] .'/report_empty');
      $this->set_data('page_title', sprintf(lang('page_title_general_empty')));
    } else {
      $start_date = $period->start_date;
      $end_date = $period->end_date;
      $entities = $this->model->findGeneralStock($end_date, NULL, $group->group, $warehouse, $condition);

      $this->set_data('entities', $entities);
      $this->set_data('groups', $this->model->findAllItemGroups('AVAILABLE'));
      $this->set_data('page_content', $this->module['view'] .'/stock_general');
      $this->set_data('page_title', sprintf(lang('page_title_general'), strtoupper($group->description), strtoupper(config_item('condition')[$condition]), $warehouse, $this->monthName($month, 'uppercase'), $year));
    }

    $this->set_data('year', $year);
    $this->set_data('month', $month);
    $this->set_data('warehouses', $this->model->findAllWarehouses('AVAILABLE'));
    $this->set_data('sidebar', $this->module['view'] .'/stock_general_sidebar');

    // where to go if single click perform
    if ($this->has_role($this->module, 'show')){
      $this->data['singleClickUrl'] = site_url($this->module['route'] .'/show');
    }

    // where to go if double click perform
    if ($this->has_role($this->module, 'edit')){
      $this->data['doubleClickUrl'] = site_url($this->module['route'] .'/edit');
    }

    $this->render_view();
  }

  public function stock_card()
  {
    // ========================= ACCESS DENIED ========================== //
    $this->authorized($this->acl['stock_general']['index']);

    // ========================= ACCESS GRANTED ========================= //

    if (isset($_GET['year']) && !empty($_GET['year'])){
      $year = $_GET['year'];
    } else {
      $year = date('Y');
    }

    if (isset($_GET['month']) && !empty($_GET['month'])){
      $month = $_GET['month'];
    } else {
      $month = date('n')-1;
    }

    $period = $this->model->findPeriodInfo($year, $month);

    if (empty($period)){
      $this->set_data('page_content', $this->module['view'] .'/report_empty');
      $this->set_data('page_title', sprintf(lang('page_title_stock_card_empty')));
    } else {
      $start_date = $period->start_date;
      $end_date = $period->end_date;
      $entities = $this->model->findStockCard($end_date, $start_date);

      $this->set_data('groups', $this->model->findAllItemGroups('AVAILABLE'));
      $this->set_data('entities', $entities);
      $this->set_data('page_content', $this->module['view'] .'/stock_card');
      $this->set_data('page_title', sprintf(lang('page_title_stock_card'), $this->monthName($month, 'uppercase'), $year));
    }

    $this->set_data('year', $year);
    $this->set_data('month', $month);
    $this->set_data('sidebar', $this->module['view'] .'/stock_card_sidebar');

    // where to go if single click perform
    if ($this->has_role($this->module, 'show')){
      $this->data['singleClickUrl'] = site_url($this->module['route'] .'/show');
    }

    // where to go if double click perform
    if ($this->has_role($this->module, 'edit')){
      $this->data['doubleClickUrl'] = site_url($this->module['route'] .'/edit');
    }

    $this->render_view();
  }

  public function stock_card_group()
  {
    // ========================= ACCESS DENIED ========================== //
    $this->authorized($this->acl['stock_general']['index']);

    // ========================= ACCESS GRANTED ========================= //

    if (isset($_GET['group']) && !empty($_GET['group'])){
      $group = $_GET['group'];
    } else {
      $group = 'ALL';
    }

    if (isset($_GET['year']) && !empty($_GET['year'])){
      $year = $_GET['year'];
    } else {
      $year = date('Y');
    }

    if (isset($_GET['month']) && !empty($_GET['month'])){
      $month = $_GET['month'];
    } else {
      $month = date('n')-1;
    }

    $period = $this->model->findPeriodInfo($year, $month);

    if (empty($period)){
      $this->set_data('page_content', $this->module['view'] .'/report_empty');
      $this->set_data('page_title', sprintf(lang('page_title_stock_card_group_empty')));
    } else {
      $start_date = $period->start_date;
      $end_date = $period->end_date;
      $entities = $this->model->findStockCardGroup($group, $end_date, $start_date);
      $group = $this->model->findItemGroupInfo($group);

      $this->set_data('groups', $this->model->findAllItemGroups('AVAILABLE'));
      $this->set_data('group', $group);
      $this->set_data('entities', $entities);
      $this->set_data('page_content', $this->module['view'] .'/stock_card_group');
      $this->set_data('page_title', sprintf(lang('page_title_stock_card_group'), strtoupper($group->description), $this->monthName($month, 'uppercase'), $year));
    }

    $this->set_data('year', $year);
    $this->set_data('month', $month);
    $this->set_data('sidebar', $this->module['view'] .'/stock_card_group_sidebar');

    // where to go if single click perform
    if ($this->has_role($this->module, 'show')){
      $this->data['singleClickUrl'] = site_url($this->module['route'] .'/show');
    }

    // where to go if double click perform
    if ($this->has_role($this->module, 'edit')){
      $this->data['doubleClickUrl'] = site_url($this->module['route'] .'/edit');
    }

    $this->render_view();
  }

  public function stock_card_detail($id)
  {
    // ========================= ACCESS DENIED ========================== //
    $this->authorized($this->acl['stock_general']['index']);

    // ========================= ACCESS GRANTED ========================= //

    if (isset($_GET['year']) && !empty($_GET['year'])){
      $year = $_GET['year'];
    } else {
      $year = date('Y');
    }

    if (isset($_GET['month']) && !empty($_GET['month'])){
      $month = $_GET['month'];
    } else {
      $month = date('n')-1;
    }

    $period = $this->model->findPeriodInfo($year, $month);
    $item = $this->model->findItemInfo($id);

    if (empty($period)){
      $this->set_data('page_content', $this->module['view'] .'/report_empty');
      $this->set_data('page_title', sprintf(lang('page_title_stock_card_detail_empty')));
    } else {
      $start_date = $period->start_date;
      $end_date = $period->end_date;
      $entities = $this->model->findStockCardDetail($id, $end_date, $start_date);

      $this->set_data('entities', $entities);
      $this->set_data('page_content', $this->module['view'] .'/stock_card_detail');
      $this->set_data('page_title', sprintf(lang('page_title_stock_card_detail'), $item->part_number, $this->monthName($month, 'uppercase'), $year));
    }

    $this->set_data('year', $year);
    $this->set_data('month', $month);
    $this->set_data('sidebar', $this->module['view'] .'/stock_card_sidebar');

    // where to go if single click perform
    if ($this->has_role($this->module, 'show')){
      $this->data['singleClickUrl'] = site_url($this->module['route'] .'/show');
    }

    // where to go if double click perform
    if ($this->has_role($this->module, 'edit')){
      $this->data['doubleClickUrl'] = site_url($this->module['route'] .'/edit');
    }

    $this->render_view();
  }

  public function stock_card_show($part_number, $item_serial = NULL)
  {
    // ========================= ACCESS DENIED ========================== //
    $this->authorized($this->acl['stock_general']['index']);

    // ========================= ACCESS GRANTED ========================= //

    if (isset($_GET['year']) && !empty($_GET['year'])){
      $year = $_GET['year'];
    } else {
      $year = date('Y');
    }

    if (isset($_GET['month']) && !empty($_GET['month'])){
      $month = $_GET['month'];
    } else {
      $month = date('n')-1;
    }

    $period = $this->model->findPeriodInfo($year, $month);

    if (empty($period)){
      $this->set_data('page_content', $this->module['view'] .'/report_empty');
      $this->set_data('page_title', sprintf(lang('page_title_stock_info_card_empty')));
    } else {
      $start_date = $period->start_date;
      $end_date = $period->end_date;
      $entities = $this->model->findStockCardInfo($part_number, $item_serial, $end_date, $start_date);

      $this->set_data('entities', $entities);
      $this->set_data('initial_quantity', $this->model->findItemInitialQuantity($part_number, $item_serial, $start_date));
      $this->set_data('page_content', $this->module['view'] .'/stock_card_info');
      $this->set_data('page_title', sprintf(lang('page_title_stock_card_info'), $part_number, (($item_serial === NULL) ? '' : $item_serial), $this->monthName($month, 'uppercase'), $year));
    }

    $this->set_data('year', $year);
    $this->set_data('month', $month);
    $this->set_data('sidebar', $this->module['view'] .'/stock_card_info_sidebar');

    $this->render_view();
  }

  public function doc_receipt()
  {
    // ========================= ACCESS DENIED ========================== //
    $this->authorized($this->acl['doc_receipt']['index']);

    // ========================= ACCESS GRANTED ========================= //

    if (isset($_GET['year']) && !empty($_GET['year'])){
      $year = $_GET['year'];
    } else {
      $year = date('Y');
    }

    if (isset($_GET['month']) && !empty($_GET['month'])){
      $month = $_GET['month'];
    } else {
      $month = date('n')-1;
    }

    $period = $this->model->findPeriodInfo($year, $month);

    if (empty($period)){
      $this->set_data('page_content', $this->module['view'] .'/report_empty');
      $this->set_data('page_title', sprintf(lang('page_title_grn_empty')));
    } else {
      $start_date = $period->start_date;
      $end_date = $period->end_date;
      $entities = $this->model->findGRN($start_date, $end_date);

      $this->set_data('entities', $entities);
      $this->set_data('page_content', $this->module['view'] .'/doc_receipt');
      $this->set_data('page_title', sprintf(lang('page_title_grn'), $this->monthName($month, 'uppercase'), $year));
    }

    $this->set_data('year', $year);
    $this->set_data('month', $month);
    $this->set_data('sidebar', $this->module['view'] .'/doc_receipt_sidebar');

    $this->render_view();
  }

  public function doc_return()
  {
    // ========================= ACCESS DENIED ========================== //
    $this->authorized($this->acl['doc_return']['index']);

    // ========================= ACCESS GRANTED ========================= //

    if (isset($_GET['year']) && !empty($_GET['year'])){
      $year = $_GET['year'];
    } else {
      $year = date('Y');
    }

    if (isset($_GET['month']) && !empty($_GET['month'])){
      $month = $_GET['month'];
    } else {
      $month = date('n')-1;
    }

    $period = $this->model->findPeriodInfo($year, $month);

    if (empty($period)){
      $this->set_data('page_content', $this->module['view'] .'/report_empty');
      $this->set_data('page_title', sprintf(lang('page_title_doc_return_empty')));
    } else {
      $start_date = $period->start_date;
      $end_date = $period->end_date;
      $entities = $this->model->findCommercialInvoice($start_date, $end_date);

      $this->set_data('entities', $entities);
      $this->set_data('page_content', $this->module['view'] .'/doc_return');
      $this->set_data('page_title', sprintf(lang('page_title_doc_return'), $this->monthName($month, 'uppercase'), $year));
    }

    $this->set_data('year', $year);
    $this->set_data('month', $month);
    $this->set_data('sidebar', $this->module['view'] .'/doc_return_sidebar');

    $this->render_view();
  }

  public function doc_usage()
  {
    // ========================= ACCESS DENIED ========================== //
    $this->authorized($this->acl['doc_usage']['index']);

    // ========================= ACCESS GRANTED ========================= //

    if (isset($_GET['year']) && !empty($_GET['year'])){
      $year = $_GET['year'];
    } else {
      $year = date('Y');
    }

    if (isset($_GET['month']) && !empty($_GET['month'])){
      $month = $_GET['month'];
    } else {
      $month = date('n')-1;
    }

    $period = $this->model->findPeriodInfo($year, $month);

    if (isset($_GET['warehouse']) && !empty($_GET['warehouse'])){
      $warehouse = $_GET['warehouse'];
    } else {
      $warehouse = 'GENERAL';
    }

    $this->set_data('warehouse', $warehouse);

    if (empty($period)){
      $this->set_data('page_content', $this->module['view'] .'/report_empty');
      $this->set_data('page_title', sprintf(lang('page_title_doc_usage_empty')));
    } else {
      $start_date = $period->start_date;
      $end_date = $period->end_date;
      $entities = $this->model->findMaterialSlip($warehouse, $start_date, $end_date);

      $this->set_data('entities', $entities);
      $this->set_data('page_content', $this->module['view'] .'/doc_usage');
      $this->set_data('page_title', sprintf(lang('page_title_doc_usage'), $warehouse, $this->monthName($month, 'uppercase'), $year));
    }

    $this->set_data('year', $year);
    $this->set_data('month', $month);
    $this->set_data('warehouse', $warehouse);
    $this->set_data('warehouses', $this->model->findAllWarehouses('AVAILABLE'));
    $this->set_data('sidebar', $this->module['view'] .'/doc_usage_sidebar');

    $this->render_view();
  }

  public function doc_shipment()
  {
    // ========================= ACCESS DENIED ========================== //
    $this->authorized($this->acl['doc_shipment']['index']);

    // ========================= ACCESS GRANTED ========================= //

    if (isset($_GET['year']) && !empty($_GET['year'])){
      $year = $_GET['year'];
    } else {
      $year = date('Y');
    }

    if (isset($_GET['month']) && !empty($_GET['month'])){
      $month = $_GET['month'];
    } else {
      $month = date('n')-1;
    }

    $period = $this->model->findPeriodInfo($year, $month);

    if (empty($period)){
      $this->set_data('page_content', $this->module['view'] .'/report_empty');
      $this->set_data('page_title', sprintf(lang('page_title_doc_shipment_empty')));
    } else {
      $start_date = $period->start_date;
      $end_date = $period->end_date;
      $entities = $this->model->findShippingDocument($start_date, $end_date);

      $this->set_data('entities', $entities);
      $this->set_data('page_content', $this->module['view'] .'/doc_shipment');
      $this->set_data('page_title', sprintf(lang('page_title_doc_shipment'), $this->monthName($month, 'uppercase'), $year));
    }

    $this->set_data('year', $year);
    $this->set_data('month', $month);
    $this->set_data('sidebar', $this->module['view'] .'/doc_shipment_sidebar');

    $this->render_view();
  }
}
