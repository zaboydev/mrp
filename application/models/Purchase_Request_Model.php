<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_Request_Model extends MY_Model
{
  protected $connection;
  protected $categories;
  protected $budget_year;
  protected $budget_month;

  public function __construct()
  {
    parent::__construct();

    $this->connection   = $this->load->database('budgetcontrol', TRUE);
    $this->categories   = $this->getCategories();
    $this->budget_year  = find_budget_setting('Active Year');
    $this->budget_month = find_budget_setting('Active Month');
  }

  public function getSelectedColumns()
  {
    if($_SESSION['request']['request_to'] == 0){
      return array(
        'tb_inventory_purchase_requisitions.id'                       => NULL,
        'tb_inventory_purchase_requisitions.pr_number'                => 'Document Number',
        'tb_inventory_purchase_requisitions.pr_date'                  => 'Document Date',
        'tb_inventory_purchase_requisitions.required_date'            => 'Required Date',
        'tb_product_categories.category_name'                         => 'Category',
        'tb_products.product_name'                                    => 'Description',
        'tb_products.product_code'                                    => 'Part Number',
        'tb_inventory_purchase_requisition_details.additional_info'   => 'Additional Info',
        'tb_inventory_purchase_requisition_details.quantity'          => 'Quantity',
        '(tb_inventory_purchase_requisition_details.quantity - tb_inventory_purchase_requisition_details.sisa) as process_qty'          => 'Processed Quantity',
        'tb_inventory_purchase_requisitions.status'                   => 'Status',
        'tb_inventory_purchase_requisitions.suggested_supplier'       => 'Suggested Supplier',
        'tb_inventory_purchase_requisitions.deliver_to'               => 'Deliver To',
        'tb_inventory_purchase_requisitions.created_by'               => 'Request By',
        'tb_inventory_purchase_requisitions.notes'                    => 'Notes',
      );
    } else {
      return array(
        'tb_inventory_purchase_requisitions.id'                       => NULL,
        'tb_inventory_purchase_requisitions.pr_number'                => 'Document Number',
        'tb_inventory_purchase_requisitions.pr_date'                  => 'Document Date',
        'tb_inventory_purchase_requisitions.required_date'            => 'Required Date',
        'tb_inventory_purchase_requisitions.item_category'                         => 'Category',
        'tb_master_items.description'                                    => 'Description',
        'tb_master_items.part_number'                                    => 'Part Number',
        'tb_inventory_purchase_requisition_details.additional_info'   => 'Additional Info',
        'tb_inventory_purchase_requisition_details.quantity'          => 'Quantity',
        '(tb_inventory_purchase_requisition_details.quantity - tb_inventory_purchase_requisition_details.sisa) as process_qty'          => 'Processed Quantity',
        'tb_inventory_purchase_requisitions.status'                   => 'Status',
        'tb_inventory_purchase_requisitions.suggested_supplier'       => 'Suggested Supplier',
        'tb_inventory_purchase_requisitions.deliver_to'               => 'Deliver To',
        'tb_inventory_purchase_requisitions.created_by'               => 'Request By',
        'tb_inventory_purchase_requisitions.notes'                    => 'Notes',
      );
    }
  }

  public function getSearchableColumns()
  {
    if($_SESSION['request']['request_to'] == 0){
      return array(
        'tb_inventory_purchase_requisitions.pr_number',
        'tb_product_categories.category_name',
        'tb_products.product_name',
        'tb_products.product_code',
        'tb_inventory_purchase_requisition_details.additional_info',
        'tb_inventory_purchase_requisitions.status',
        'tb_inventory_purchase_requisitions.suggested_supplier',
        'tb_inventory_purchase_requisitions.deliver_to',
        'tb_inventory_purchase_requisitions.created_by',
        'tb_inventory_purchase_requisitions.notes',
      );
    } else {
      return array(
        'tb_inventory_purchase_requisitions.pr_number',
        'tb_inventory_purchase_requisitions.item_category',
        'tb_master_items.description',
        'tb_master_items.part_number',
        'tb_inventory_purchase_requisition_details.additional_info',
        'tb_inventory_purchase_requisitions.status',
        'tb_inventory_purchase_requisitions.suggested_supplier',
        'tb_inventory_purchase_requisitions.deliver_to',
        'tb_inventory_purchase_requisitions.created_by',
        'tb_inventory_purchase_requisitions.notes',
      );
    }
    
  }

  public function getOrderableColumns()
  {
    if($_SESSION['request']['request_to'] == 0){
          return array(
          null,
          'tb_inventory_purchase_requisitions.pr_number',
          'tb_inventory_purchase_requisitions.pr_date',
          'tb_inventory_purchase_requisitions.required_date',
          'tb_product_categories.category_name',
          'tb_products.product_name',
          'tb_products.product_code',
          'tb_inventory_purchase_requisition_details.additional_info',
          'tb_inventory_purchase_requisition_details.quantity',
          '(tb_inventory_purchase_requisition_details.quantity - tb_inventory_purchase_requisition_details.sisa)',
          'tb_inventory_purchase_requisitions.status',
          'tb_inventory_purchase_requisitions.suggested_supplier',
          'tb_inventory_purchase_requisitions.deliver_to',
          'tb_inventory_purchase_requisitions.created_by',
          'tb_inventory_purchase_requisitions.notes',
        );
      } else {
        return array(
          null,
          'tb_inventory_purchase_requisitions.pr_number',
          'tb_inventory_purchase_requisitions.pr_date',
          'tb_inventory_purchase_requisitions.required_date',
          'tb_inventory_purchase_requisitions.item_category',
          'tb_master_items.description',
          'tb_master_items.part_number',
          'tb_inventory_purchase_requisition_details.additional_info',
          'tb_inventory_purchase_requisition_details.quantity',
          '(tb_inventory_purchase_requisition_details.quantity - tb_inventory_purchase_requisition_details.sisa)',
          'tb_inventory_purchase_requisitions.status',
          'tb_inventory_purchase_requisitions.suggested_supplier',
          'tb_inventory_purchase_requisitions.deliver_to',
          'tb_inventory_purchase_requisitions.created_by',
          'tb_inventory_purchase_requisitions.notes',
        );
       }

  }

  private function searchIndex()
  {
    $db = $_SESSION['request']['request_to'] == 0 ? $this->connection : $this->db;
    if (!empty($_POST['columns'][3]['search']['value'])){
      $search_required_date = $_POST['columns'][3]['search']['value'];
      $range_date  = explode(' ', $search_required_date);

      $db->where('tb_inventory_purchase_requisitions.required_date >= ', $range_date[0]);
      $db->where('tb_inventory_purchase_requisitions.required_date <= ', $range_date[1]);

    }

    if (!empty($_POST['columns'][4]['search']['value'])){
      $search_status = $_POST['columns'][4]['search']['value'];

      $db->where('tb_inventory_purchase_requisitions.status', $search_status);
    } else {
      $db->where('tb_inventory_purchase_requisitions.status', 'pending');
    }

    if (!empty($_POST['columns'][8]['search']['value'])){
      $search_category = $_POST['columns'][8]['search']['value'];
      if($_SESSION['request']['request_to'] == 0){
        $db->where('UPPER(tb_product_categories.category_name)', strtoupper($search_category));
      } else {
        $db->where('UPPER(tb_inventory_purchase_requisitions.item_category)', strtoupper($search_category));
      }
      
    }

    $i = 0;

    foreach ($this->getSearchableColumns() as $item){
      if ($_POST['search']['value']){
        $term = strtoupper($_POST['search']['value']);

        if ($i === 0){
          $this->connection->group_start();
          $this->connection->like('UPPER('.$item.')', $term);
        } else {
          $this->connection->or_like('UPPER('.$item.')', $term);
        }

        if (count($this->getSearchableColumns()) - 1 == $i)
          $this->connection->group_end();
      }

      $i++;
    }
  }

  function getCategories()
  {
    $categories = array();
    $category   = array();

    foreach (config_item('auth_inventory') as $inventory) {
      $category[] = strtoupper($inventory);
    }

    $this->connection->select('id');
    $this->connection->from('tb_product_categories');
    $this->connection->where_in('UPPER(category_name)', $category);

    $query  = $this->connection->get();

    foreach ($query->result_array() as $key => $value) {
      $categories[] = $value['id'];
    }

    return $categories;
  }

  function getIndex($return = 'array')
  {
    if($_SESSION['request']['request_to'] == 0){
      $this->connection->select(array_keys($this->getSelectedColumns()));
      $this->connection->from('tb_inventory_purchase_requisitions');
      $this->connection->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id','LEFT');
      $this->connection->join('tb_inventory_monthly_budgets', 'tb_inventory_monthly_budgets.id = tb_inventory_purchase_requisition_details.inventory_monthly_budget_id');
      $this->connection->join('tb_products', 'tb_products.id = tb_inventory_monthly_budgets.product_id');
      $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_inventory_purchase_requisitions.product_category_id');
      $this->connection->where_in('tb_product_categories.id', $this->categories);
      $this->connection->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);

      $this->searchIndex();

      $column_order = $this->getOrderableColumns();

      if (isset($_POST['order'])){
        foreach ($_POST['order'] as $key => $order){
          $this->connection->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
        }
      } else {
        $this->connection->order_by('id', 'desc');
      }

      if ($_POST['length'] != -1)
        $this->connection->limit($_POST['length'], $_POST['start']);
      $query = $this->connection->get();
    } else {
      $this->db->select(array_keys($this->getSelectedColumns()));
      $this->db->from('tb_inventory_purchase_requisitions');
      $this->db->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id','LEFT');
      $this->db->join('tb_budget', 'tb_budget.id = tb_inventory_purchase_requisition_details.budget_id');
      $this->db->join('tb_budget_cot', 'tb_budget.id_cot = tb_budget_cot.id');
      $this->db->join('tb_master_items', 'tb_master_items.id = tb_budget_cot.id_item');
      $categories = config_item('auth_inventory');
      $this->db->where_in('tb_inventory_purchase_requisitions.item_category', $categories);
      $this->db->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);

      $this->searchIndex();

      $column_order = $this->getOrderableColumns();

      if (isset($_POST['order'])){
        foreach ($_POST['order'] as $key => $order){
          $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
        }
      } else {
        $this->db->order_by('id', 'desc');
      }

      if ($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
      $query = $this->db->get();
    }

    if ($return === 'object'){
      return $query->result();
    } elseif ($return === 'json'){
      return json_encode($query->result());
    } else {
      return $query->result_array();
    }
  }

  function countIndexFiltered()
  {
    $this->connection->from('tb_inventory_purchase_requisitions');
    $this->connection->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id');
    $this->connection->join('tb_inventory_monthly_budgets', 'tb_inventory_monthly_budgets.id = tb_inventory_purchase_requisition_details.inventory_monthly_budget_id');
    $this->connection->join('tb_products', 'tb_products.id = tb_inventory_monthly_budgets.product_id');
    $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_inventory_purchase_requisitions.product_category_id');
    $this->connection->where_in('tb_product_categories.id', $this->categories);
    $this->connection->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);

    $this->searchIndex();

    $query = $this->connection->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->connection->from('tb_inventory_purchase_requisitions');
    $this->connection->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id');
    $this->connection->join('tb_inventory_monthly_budgets', 'tb_inventory_monthly_budgets.id = tb_inventory_purchase_requisition_details.inventory_monthly_budget_id');
    $this->connection->join('tb_products', 'tb_products.id = tb_inventory_monthly_budgets.product_id');
    $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_inventory_purchase_requisitions.product_category_id');
    $this->connection->where_in('tb_product_categories.id', $this->categories);
    $this->connection->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);

    $query = $this->connection->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    if($_SESSION['request']['request_to'] ==0){
      $this->connection->select('tb_inventory_purchase_requisitions.*, tb_product_categories.category_name');
      $this->connection->from('tb_inventory_purchase_requisitions');
      $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_inventory_purchase_requisitions.product_category_id');
      $this->connection->where('tb_inventory_purchase_requisitions.id', $id);

      $query    = $this->connection->get();
      $request  = $query->unbuffered_row('array');

      $select = array(
        'tb_inventory_purchase_requisition_details.*',
        'tb_products.product_name',
        'tb_products.product_code',
        'tb_inventory_monthly_budgets.product_id',
        'SUM(tb_inventory_monthly_budgets.mtd_quantity) AS fyp_quantity',
        'SUM(tb_inventory_monthly_budgets.mtd_budget) AS fyp_budget',
        'SUM(tb_inventory_monthly_budgets.mtd_used_quantity) AS fyp_used_quantity',
        'SUM(tb_inventory_monthly_budgets.mtd_used_budget) AS fyp_used_budget',
      );

      $group_by = array(
        'tb_inventory_purchase_requisition_details.id',
        'tb_products.product_name',
        'tb_products.product_code',
        'tb_inventory_monthly_budgets.product_id',
      );

      $this->connection->select($select);
      $this->connection->from('tb_inventory_purchase_requisition_details');
      $this->connection->join('tb_inventory_monthly_budgets', 'tb_inventory_monthly_budgets.id = tb_inventory_purchase_requisition_details.inventory_monthly_budget_id');
      $this->connection->join('tb_products', 'tb_products.id = tb_inventory_monthly_budgets.product_id');
      $this->connection->where('tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id', $id);
      $this->connection->group_by($group_by);

      $query = $this->connection->get();

      foreach ($query->result_array() as $key => $value){
        $request['items'][$key] = $value;

        $this->connection->from('tb_inventory_monthly_budgets');
        $this->connection->where('tb_inventory_monthly_budgets.product_id', $value['product_id']);
        $this->connection->where('tb_inventory_monthly_budgets.month_number', $this->budget_month);
        $this->connection->where('tb_inventory_monthly_budgets.year_number', $this->budget_year);

        $query = $this->connection->get();
        $row   = $query->unbuffered_row('array');

        $request['items'][$key]['mtd_quantity'] = $row['mtd_quantity'];
        $request['items'][$key]['mtd_budget'] = $row['mtd_budget'];
        $request['items'][$key]['mtd_used_quantity'] = $row['mtd_used_quantity'];
        $request['items'][$key]['mtd_used_budget'] = $row['mtd_used_budget'];
        $request['items'][$key]['ytd_quantity'] = $row['ytd_quantity'];
        $request['items'][$key]['ytd_budget'] = $row['ytd_budget'];
        $request['items'][$key]['ytd_used_quantity'] = $row['ytd_used_quantity'];
        $request['items'][$key]['ytd_used_budget'] = $row['ytd_used_budget'];
      }
    } else {
      $this->db->select('tb_inventory_purchase_requisitions.*');
      $this->db->from('tb_inventory_purchase_requisitions');
      $this->db->where('tb_inventory_purchase_requisitions.id', $id);

      $query    = $this->db->get();
      $request  = $query->unbuffered_row('array');

      $select = array(
        'tb_inventory_purchase_requisition_details.*',
        'tb_master_items.description',
        'tb_master_items.part_number',
        'tb_budget.id_cot',
        'SUM(tb_budget.mtd_quantity) AS fyp_quantity',
        'SUM(tb_budget.mtd_budget) AS fyp_budget',
        'SUM(tb_budget.mtd_used_quantity) AS fyp_used_quantity',
        'SUM(tb_budget.mtd_used_budget) AS fyp_used_budget',
      );

      $group_by = array(
        'tb_inventory_purchase_requisition_details.id',
        'tb_master_items.description',
        'tb_master_items.part_number',
        'tb_budget.id_cot',
      );

      $this->db->select($select);
      $this->db->from('tb_inventory_purchase_requisition_details');
      $this->db->join('tb_budget', 'tb_budget.id = tb_inventory_purchase_requisition_details.budget_id');
      $this->db->join('tb_budget_cot', 'tb_budget_cot.id = tb_budget.id_cot');
      $this->db->join('tb_master_items', 'tb_master_items.id = tb_budget_cot.id_item');
      $this->db->where('tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id', $id);
      $this->db->group_by($group_by);

      $query = $this->db->get();

      foreach ($query->result_array() as $key => $value){
        $request['items'][$key] = $value;

        $this->db->from('tb_budget');
        $this->db->where('tb_budget.id_cot', $value['id_cot']);
        $this->db->where('tb_budget.month_number', $this->budget_month);

        $query = $this->db->get();
        $row   = $query->unbuffered_row('array');

        $request['items'][$key]['mtd_quantity'] = $row['mtd_quantity'];
        $request['items'][$key]['mtd_budget'] = $row['mtd_budget'];
        $request['items'][$key]['mtd_used_quantity'] = $row['mtd_used_quantity'];
        $request['items'][$key]['mtd_used_budget'] = $row['mtd_used_budget'];
        $request['items'][$key]['ytd_quantity'] = $row['ytd_quantity'];
        $request['items'][$key]['ytd_budget'] = $row['ytd_budget'];
        $request['items'][$key]['ytd_used_quantity'] = $row['ytd_used_quantity'];
        $request['items'][$key]['ytd_used_budget'] = $row['ytd_used_budget'];
      }      
    }
    

    return $request;
  }

  public function isDocumentNumberExists($pr_number)
  {
    $this->connection->where('pr_number', $pr_number);
    $query = $this->connection->get('tb_inventory_purchase_requisitions');

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function isValidDocumentQuantity($pr_number)
  {
    $this->connection->select_sum('tb_receipt_items.received_quantity', 'received_quantity');
    $this->connection->select_sum('tb_stock_in_stores.quantity', 'stored_quantity');
    $this->connection->select('tb_receipt_items.pr_number');
    $this->connection->from('tb_receipt_items');
    $this->connection->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
    $this->connection->where('tb_receipt_items.pr_number', $pr_number);
    $this->connection->group_by('tb_receipt_items.pr_number');

    $query  = $this->connection->get();
    $row    = $query->unbuffered_row('array');

    if ($row['received_quantity'] === $row['received_quantity'])
      return true;

    return false;
  }

  public function save()
  {
    $document_id          = (isset($_SESSION['request']['id'])) ? $_SESSION['request']['id'] : NULL;
    $document_edit        = (isset($_SESSION['request']['edit'])) ? $_SESSION['request']['edit'] : NULL;
    $order_number         = $_SESSION['request']['order_number'];
    $pr_number            = $_SESSION['request']['pr_number'];
    $pr_date              = $_SESSION['request']['pr_date'];
    $required_date        = $_SESSION['request']['required_date'];
    $deliver_to           = (empty($_SESSION['request']['deliver_to'])) ? NULL : $_SESSION['request']['deliver_to'];
    $suggested_supplier   = (empty($_SESSION['request']['suggested_supplier'])) ? NULL : $_SESSION['request']['suggested_supplier'];
    $created_by           = (empty($_SESSION['request']['created_by'])) ? NULL : $_SESSION['request']['created_by'];
    $category             = find_product_category($_SESSION['request']['category']);
    $product_category_id  = $category['id'];
    $notes                = (empty($_SESSION['request']['notes'])) ? NULL : $_SESSION['request']['notes'];

    $this->connection->trans_begin();

    if ($document_id === NULL){
      $this->connection->set('product_category_id', $product_category_id);
      $this->connection->set('order_number', $order_number);
      $this->connection->set('pr_number', $pr_number);
      $this->connection->set('pr_date', $pr_date);
      $this->connection->set('required_date', $required_date);
      $this->connection->set('suggested_supplier', $suggested_supplier);
      $this->connection->set('deliver_to', $deliver_to);
      $this->connection->set('status', 'pending');
      $this->connection->set('notes', $notes);
      $this->connection->set('created_by', $created_by);
      $this->connection->set('updated_by', config_item('auth_person_name'));
      $this->connection->set('created_at', date('Y-m-d H:i:s'));
      $this->connection->set('updated_at', date('Y-m-d H:i:s'));
      $this->connection->insert('tb_inventory_purchase_requisitions');

      $document_id = $this->connection->insert_id();
    } else {
      $this->connection->set('required_date', $required_date);
      $this->connection->set('suggested_supplier', $suggested_supplier);
      $this->connection->set('deliver_to', $deliver_to);
      $this->connection->set('status', 'pending');
      $this->connection->set('notes', $notes);
      $this->connection->set('updated_at', date('Y-m-d'));
      $this->connection->set('updated_by', config_item('auth_person_name'));
      $this->connection->where('id', $document_id);
      $this->connection->update('tb_inventory_purchase_requisitions');

      $this->connection->where('inventory_purchase_requisition_id', $document_id);
      $this->connection->delete('tb_inventory_purchase_requisition_details');
    }
    $request_to = $_SESSION['request']['request_to'];
     if($request_to == 0){
      // request from budget control
        foreach ($_SESSION['request']['items'] as $key => $data){
        if (empty($data['inventory_monthly_budget_id']) || $data['inventory_monthly_budget_id'] == NULL){
          // NEW GROUP
          $this->connection->select('tb_product_groups.id');
          $this->connection->from('tb_product_groups');
          $this->connection->where('UPPER(tb_product_groups.group_name)', strtoupper($data['group_name']));

          $query  = $this->connection->get();

          if ($query->num_rows() == 0){
            $this->connection->set('product_category_id', $product_category_id);
            $this->connection->set('group_name', $data['group_name']);
            $this->connection->set('group_code', strtoupper($data['group_name']));
            $this->connection->set('group_type', 'inventory');
            $this->connection->set('created_at', date('Y-m-d'));
            $this->connection->set('created_by', config_item('auth_person_name'));
            $this->connection->set('updated_at', date('Y-m-d'));
            $this->connection->set('updated_by', config_item('auth_person_name'));
            $this->connection->insert('tb_product_groups');

            $product_group_id = $this->connection->insert_id();
          } else {
            $product_group    = $query->unbuffered_row();
            $product_group_id = $product_group->id;
          }

          // NEW UNIT
          $this->connection->select('tb_product_measurements.id');
          $this->connection->from('tb_product_measurements');
          $this->connection->where('UPPER(tb_product_measurements.measurement_symbol)', strtoupper($data['unit']));

          $query  = $this->connection->get();

          if ($query->num_rows() == 0){
            $this->connection->set('measurement_name', $data['unit']);
            $this->connection->set('measurement_symbol', strtolower($data['unit']));
            $this->connection->set('created_at', date('Y-m-d'));
            $this->connection->set('created_by', config_item('auth_person_name'));
            $this->connection->set('updated_at', date('Y-m-d'));
            $this->connection->set('updated_by', config_item('auth_person_name'));
            $this->connection->insert('tb_product_measurements');

            $product_measurement_id = $this->connection->insert_id();
          } else {
            $product_measurement    = $query->unbuffered_row();
            $product_measurement_id = $product_measurement->id;
          }

          // NEW PRODUCT
          $this->connection->select('tb_products.id');
          $this->connection->from('tb_products');
          $this->connection->where('UPPER(tb_products.product_code)', strtoupper($data['part_number']));

          $query  = $this->connection->get();

          if ($query->num_rows() == 0){
            $this->connection->set('product_measurement_id', $product_measurement_id);
            $this->connection->set('product_group_id', $product_group_id);
            $this->connection->set('product_name', $data['product_name']);
            $this->connection->set('product_code', $data['part_number']);
            $this->connection->set('part_number', $data['part_number']);
            $this->connection->set('product_type', 'inventory');
            $this->connection->set('unit_measurement', $data['unit']);
            $this->connection->set('price', $data['price']);
            $this->connection->set('additional_info', $data['additional_info']);
            $this->connection->set('created_at', date('Y-m-d'));
            $this->connection->set('created_by', config_item('auth_person_name'));
            $this->connection->set('updated_at', date('Y-m-d'));
            $this->connection->set('updated_by', config_item('auth_person_name'));
            $this->connection->insert('tb_products');

            $product_id = $this->connection->insert_id();
          } else {
            $product    = $query->unbuffered_row();
            $product_id = $product->id;
          }

          // NEW PRICE
          $this->connection->from('tb_product_purchase_prices');
          $this->connection->where('tb_product_purchase_prices.product_id', $product_id);

          $query  = $this->connection->get();

          if ($query->num_rows() == 0){
            $this->connection->set('price_before', 0);
            $this->connection->set('current_price', $data['price']);
            $this->connection->set('cur_date', date('Y-m-d'));
            $this->connection->set('created_at', date('Y-m-d'));
            $this->connection->set('created_by', config_item('auth_person_name'));
            $this->connection->set('updated_at', date('Y-m-d'));
            $this->connection->set('updated_by', config_item('auth_person_name'));
            $this->connection->insert('tb_product_purchase_prices');

            $purchase_price_id = $this->connection->insert_id();
          } else {
            $purchase_price    = $query->unbuffered_row();
            $purchase_price_id = $purchase_price->id;

            $this->connection->set('price_before', $purchase_price->current_price);
            $this->connection->set('current_price', $data['price']);
            $this->connection->set('cur_date', date('Y-m-d'));
            $this->connection->set('updated_at', date('Y-m-d'));
            $this->connection->set('updated_by', config_item('auth_person_name'));
            $this->connection->where('id', $purchase_price_id);
            $this->connection->update('tb_product_purchase_prices');
          }

          // NEW BUDGET
          $this->connection->from('tb_inventory_monthly_budgets');
          $this->connection->where('tb_inventory_monthly_budgets.product_id', $product_id);
          $this->connection->where('tb_inventory_monthly_budgets.month_number', $this->budget_month);
          $this->connection->where('tb_inventory_monthly_budgets.year_number', $this->budget_year);

          $query  = $this->connection->get();

          if ($query->num_rows() == 0){
        $this->connection->set('product_id', $product_id);
        $this->connection->set('month_number', $this->budget_month);
        $this->connection->set('year_number', $this->budget_year);
        $this->connection->set('initial_quantity', floatval(0));
        $this->connection->set('initial_budget', floatval(0));
        $this->connection->set('mtd_quantity', floatval(0));
        $this->connection->set('mtd_budget', floatval(0));
        $this->connection->set('mtd_used_quantity', floatval(0));
        $this->connection->set('mtd_used_budget', floatval(0));
        $this->connection->set('mtd_used_quantity_import', floatval(0));
        $this->connection->set('mtd_used_budget_import', floatval(0));
        $this->connection->set('mtd_prev_month_quantity', floatval(0));
        $this->connection->set('mtd_prev_month_budget', floatval(0));
        $this->connection->set('mtd_prev_month_used_quantity', floatval(0));
        $this->connection->set('mtd_prev_month_used_budget', floatval(0));
        $this->connection->set('mtd_prev_month_used_quantity_import', floatval(0));
        $this->connection->set('mtd_prev_month_used_budget_import', floatval(0));
        $this->connection->set('ytd_quantity', floatval(0));
        $this->connection->set('ytd_budget', floatval(0));
        $this->connection->set('ytd_used_quantity', floatval(0));
        $this->connection->set('ytd_used_budget', floatval(0));
        $this->connection->set('ytd_used_quantity_import', floatval(0));
        $this->connection->set('ytd_used_budget_import', floatval(0));
        $this->connection->set('created_at', date('Y-m-d'));
        $this->connection->set('created_by', config_item('auth_person_name'));
        $this->connection->set('updated_at', date('Y-m-d'));
        $this->connection->set('updated_by', config_item('auth_person_name'));
        $this->connection->insert('tb_inventory_monthly_budgets');

        $inventory_monthly_budget_id = $this->connection->insert_id();

        for ($m = 1; $m < $this->budget_month; $m++){
          // PREV BUDGET
          $this->connection->set('product_id', $product_id);
          $this->connection->set('month_number', $m);
          $this->connection->set('year_number', $this->budget_year);
          $this->connection->set('initial_quantity', floatval(0));
          $this->connection->set('initial_budget', floatval(0));
          $this->connection->set('mtd_quantity', floatval(0));
          $this->connection->set('mtd_budget', floatval(0));
          $this->connection->set('mtd_used_quantity', floatval(0));
          $this->connection->set('mtd_used_budget', floatval(0));
          $this->connection->set('mtd_used_quantity_import', floatval(0));
          $this->connection->set('mtd_used_budget_import', floatval(0));
          $this->connection->set('mtd_prev_month_quantity', floatval(0));
          $this->connection->set('mtd_prev_month_budget', floatval(0));
          $this->connection->set('mtd_prev_month_used_quantity', floatval(0));
          $this->connection->set('mtd_prev_month_used_budget', floatval(0));
          $this->connection->set('mtd_prev_month_used_quantity_import', floatval(0));
          $this->connection->set('mtd_prev_month_used_budget_import', floatval(0));
          $this->connection->set('ytd_quantity', floatval(0));
          $this->connection->set('ytd_budget', floatval(0));
          $this->connection->set('ytd_used_quantity', floatval(0));
          $this->connection->set('ytd_used_budget', floatval(0));
          $this->connection->set('ytd_used_quantity_import', floatval(0));
          $this->connection->set('ytd_used_budget_import', floatval(0));
          $this->connection->set('created_at', date('Y-m-d'));
          $this->connection->set('created_by', config_item('auth_person_name'));
          $this->connection->set('updated_at', date('Y-m-d'));
          $this->connection->set('updated_by', config_item('auth_person_name'));
          $this->connection->insert('tb_inventory_monthly_budgets');
        }

        for ($am = 12; $am > $this->budget_month; $am--){
          // PREV BUDGET
          $this->connection->set('product_id', $product_id);
          $this->connection->set('month_number', $am);
          $this->connection->set('year_number', $this->budget_year);
          $this->connection->set('initial_quantity', floatval(0));
          $this->connection->set('initial_budget', floatval(0));
          $this->connection->set('mtd_quantity', floatval(0));
          $this->connection->set('mtd_budget', floatval(0));
          $this->connection->set('mtd_used_quantity', floatval(0));
          $this->connection->set('mtd_used_budget', floatval(0));
          $this->connection->set('mtd_used_quantity_import', floatval(0));
          $this->connection->set('mtd_used_budget_import', floatval(0));
          $this->connection->set('mtd_prev_month_quantity', floatval(0));
          $this->connection->set('mtd_prev_month_budget', floatval(0));
          $this->connection->set('mtd_prev_month_used_quantity', floatval(0));
          $this->connection->set('mtd_prev_month_used_budget', floatval(0));
          $this->connection->set('mtd_prev_month_used_quantity_import', floatval(0));
          $this->connection->set('mtd_prev_month_used_budget_import', floatval(0));
          $this->connection->set('ytd_quantity', floatval(0));
          $this->connection->set('ytd_budget', floatval(0));
          $this->connection->set('ytd_used_quantity', floatval(0));
          $this->connection->set('ytd_used_budget', floatval(0));
          $this->connection->set('ytd_used_quantity_import', floatval(0));
          $this->connection->set('ytd_used_budget_import', floatval(0));
          $this->connection->set('created_at', date('Y-m-d'));
          $this->connection->set('created_by', config_item('auth_person_name'));
          $this->connection->set('updated_at', date('Y-m-d'));
          $this->connection->set('updated_by', config_item('auth_person_name'));
          $this->connection->insert('tb_inventory_monthly_budgets');
        }
      } else {
            $inventory_monthly_budget    = $query->unbuffered_row();
            $inventory_monthly_budget_id = $inventory_monthly_budget->id;
      }
        } else {
          $inventory_monthly_budget_id = $data['inventory_monthly_budget_id'];
          $this->connection->set('status','approved');
          $this->connection->where('id',$document_id);
          $this->connection->update('tb_inventory_purchase_requisitions');

          // old budget 
          $this->connection->where('id',$inventory_monthly_budget_id);
          $temp = $this->connection->get('tb_inventory_monthly_budgets')->row();
          $year = $this->budget_year;
          $month = $this->budget_month - 1;
          $ytd_used_budget = 0;
          $ytd_used_quantity = 0;
          if($month > 0){
              $this->connection->where('product_id',$temp->product_id);
              $this->connection->where('year_number',$year);
              $this->connection->where('month_number',$month);
              $old = $this->connection->get('tb_inventory_monthly_budgets')->row();
              $ytd_used_quantity = $old->ytd_used_quantity;
              $ytd_used_budget = $old->ytd_used_budget;  
          }          
          $this->connection->where('product_id',$temp->product_id);
          $this->connection->where('year_number',$year);
          $this->connection->where('month_number',$month);

          //insert data on used budget 
          $this->connection->set('inventory_monthly_budget_id',$inventory_monthly_budget_id);
          $this->connection->set('inventory_purchase_requisition_id',$document_id);
          $this->connection->set('pr_number',$data['pr_number']);
          $this->connection->set('year_number',$this->budget_year);
          $this->connection->set('month_number',$this->budget_month);
          $this->connection->set('product_name',$data['product_name']);
          $this->connection->set('product_group',$data['group_name']);
          $this->connection->set('product_code',$data['part_number']);
          $this->connection->set('additional_info',$data['additional_info']);
          $this->connection->set('used_budget',$data['total']);
          $this->connection->set('used_quantity',$data['quantity']);
          $this->connection->set('created_at', date('Y-m-d H:i:s'));
          $this->connection->set('created_by', config_item('auth_person_name'));
          $this->connection->set('part_number', $data['part_number']);
          $this->connection->insert('tb_inventory_used_budgets');

          $this->connection->select('sum(used_quantity) as qty ,sum(used_budget) as value');
          $this->connection->from('tb_inventory_used_budgets');
          $this->connection->where('inventory_monthly_budget_id',$inventory_monthly_budget_id);
          $used_budget = $this->connection->get()->row();
          $this->connection->set('ytd_used_quantity',($used_budget->qty+$ytd_used_quantity));
          $this->connection->set('ytd_used_budget',($used_budget->value+$ytd_used_budget));
          $this->connection->set('mtd_used_quantity',($used_budget->qty));
          $this->connection->set('mtd_used_budget',($used_budget->value));
          $this->connection->where('id',$inventory_monthly_budget_id);
          $this->connection->update('tb_inventory_monthly_budgets');

        }

        $this->connection->set('inventory_purchase_requisition_id', $document_id);
        $this->connection->set('inventory_monthly_budget_id', $inventory_monthly_budget_id);
        $this->connection->set('part_number', $data['part_number']);
        $this->connection->set('additional_info', $data['additional_info']);
        $this->connection->set('unit', $data['unit']);
        $this->connection->set('sort_order', floatval($key));
        $this->connection->set('quantity', floatval($data['quantity']));
        $this->connection->set('sisa', floatval($data['quantity']));
        $this->connection->set('price', floatval($data['price']));
        $this->connection->set('total', floatval($data['total']));
        $this->connection->insert('tb_inventory_purchase_requisition_details');
       }
     } else {
         foreach ($_SESSION['request']['items'] as $key => $data ){
          if (empty($data['inventory_monthly_budget_id']) || $data['inventory_monthly_budget_id'] == NULL){
                $this->db->set('item_category', $_SESSION['request']['category']);
                $this->db->set('order_number', $order_number);
                $this->db->set('pr_number', $pr_number);
                $this->db->set('pr_date', $pr_date);
                $this->db->set('required_date', $required_date);
                $this->db->set('suggested_supplier', $suggested_supplier);
                $this->db->set('deliver_to', $deliver_to);
                $this->db->set('status', 'approved');
                $this->db->set('notes', $notes);
                $this->db->set('created_by', $created_by);
                $this->db->set('updated_by', config_item('auth_person_name'));
                $this->db->set('created_at', date('Y-m-d H:i:s'));
                $this->db->set('updated_at', date('Y-m-d H:i:s'));
                $this->db->insert('tb_inventory_purchase_requisitions');

                $document_id = $this->db->insert_id();
            return TRUE;
          } else {
            $budget_id = $data['budget_id'];
            $this->db->select('tb_inventory_purchase_requisitions.*');
            $this->db->from('tb_inventory_purchase_requisitions');
            $this->db->where('order_number', $order_number);
            $inventory = $this->db->get();
             if ($inventory->num_rows() == 0){
                $this->db->set('item_category', $_SESSION['request']['category']);
                $this->db->set('order_number', $order_number);
                $this->db->set('pr_number', $pr_number);
                $this->db->set('pr_date', $pr_date);
                $this->db->set('required_date', $required_date);
                $this->db->set('suggested_supplier', $suggested_supplier);
                $this->db->set('deliver_to', $deliver_to);
                $this->db->set('status', 'approved');
                $this->db->set('notes', $notes);
                $this->db->set('created_by', $created_by);
                $this->db->set('updated_by', config_item('auth_person_name'));
                $this->db->set('created_at', date('Y-m-d H:i:s'));
                $this->db->set('updated_at', date('Y-m-d H:i:s'));
                $this->db->insert('tb_inventory_purchase_requisitions');

                $document_id = $this->db->insert_id();
              } else {

                $document_id = $this->inventory->row()->id;
                $this->db->set('required_date', $required_date);
                $this->db->set('suggested_supplier', $suggested_supplier);
                $this->db->set('deliver_to', $deliver_to);
                $this->db->set('status', 'approved');
                $this->db->set('notes', $notes);
                $this->db->set('updated_at', date('Y-m-d'));
                $this->db->set('updated_by', config_item('auth_person_name'));
                $this->db->where('id', $document_id);
                $this->db->update('tb_inventory_purchase_requisitions');

                $this->db->where('inventory_purchase_requisition_id', $document_id);
                $this->db->delete('tb_inventory_purchase_requisition_details');
              }
              $inventory_monthly_budget_id = $data['inventory_monthly_budget_id'];
              $this->db->set('budget_id',$inventory_monthly_budget_id);
              $this->db->set('inventory_purchase_requisition_id',$document_id);
              $this->db->set('pr_number',$data['pr_number']);
              $this->db->set('year_number',$this->budget_year);
              $this->db->set('month_number',$this->budget_month);
              $this->db->set('product_name',$data['product_name']);
              $this->db->set('product_group',$data['group_name']);
              $this->db->set('product_code',$data['part_number']);
              $this->db->set('additional_info',$data['additional_info']);
              $this->db->set('used_budget',$data['total']);
              $this->db->set('used_quantity',$data['quantity']);
              $this->db->set('created_at', date('Y-m-d H:i:s'));
              $this->db->set('created_by', config_item('auth_person_name'));
              $this->db->set('part_number', $data['part_number']);
              $this->db->insert('tb_used_budgets');

              $this->db->where('id', $inventory_monthly_budget_id);
              $oldBudget = $this->db->get('tb_budget')->row();
              for ($i=$this->budget_month; $i < 13 ; $i++) { 
                $this->db->set('mtd_used_budget', 'mtd_used_budget + '.$data['total'],FALSE);
                $this->db->set('mtd_used_quantity', 'mtd_used_quantity + '.$data['quantity'],FALSE);
                $this->db->set('ytd_used_budget', 'ytd_used_budget + '.$data['total'],FALSE);
                $this->db->set('ytd_used_quantity', 'ytd_used_quantity + '.$data['quantity'],FALSE);
                $this->db->where('id_cot', $oldBudget->id_cot);
                $this->db->where('month_number', $i);
                $this->db->update('tb_budget');
              }
              $this->db->set('inventory_purchase_requisition_id', $document_id);
              $this->db->set('budget_id', $inventory_monthly_budget_id);
              $this->db->set('part_number', $data['part_number']);
              $this->db->set('additional_info', $data['additional_info']);
              $this->db->set('unit', $data['unit']);
              $this->db->set('sort_order', floatval($key));
              $this->db->set('quantity', floatval($data['quantity']));
              $this->db->set('sisa', floatval($data['quantity']));
              $this->db->set('price', floatval($data['price']));
              $this->db->set('total', floatval($data['total']));
              $this->db->insert('tb_inventory_purchase_requisition_details');
          }
         }
     }

    if ($this->connection->trans_status() === FALSE)
      return FALSE;

    $this->connection->trans_commit();
    return TRUE;
  }

  public function delete()
  {
    $this->connection->trans_begin();

    $id = $this->input->post('id');

    $this->connection->select('pr_number, warehouse');
    $this->connection->where('id', $id);
    $this->connection->from('tb_inventory_purchase_requisitions');

    $query = $this->connection->get();
    $row   = $query->unbuffered_row('array');

    $pr_number  = $row['pr_number'];
    $warehouse        = $row['warehouse'];

    $this->connection->select('tb_receipt_items.id, tb_receipt_items.stock_in_stores_id, tb_receipt_items.received_quantity, tb_receipt_items.received_unit_value, tb_stock_in_stores.stock_id, tb_stock_in_stores.serial_id, tb_stock_in_stores.stores');
    $this->connection->from('tb_receipt_items');
    $this->connection->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
    $this->connection->where('tb_receipt_items.pr_number', $pr_number);

    $query  = $this->connection->get();
    $result = $query->result_array();

    foreach ($result as $data) {
      $this->connection->set('stock_id', $data['stock_id']);
      $this->connection->set('serial_id', $data['serial_id']);
      $this->connection->set('warehouse', $warehouse);
      $this->connection->set('stores', $data['stores']);
      $this->connection->set('date_of_entry', date('Y-m-d'));
      $this->connection->set('period_year', config_item('period_year'));
      $this->connection->set('period_month', config_item('period_month'));
      $this->connection->set('document_type', 'REMOVAL');
      $this->connection->set('pr_number', $pr_number);
      $this->connection->set('issued_to', 'DELETE DOCUMENT');
      $this->connection->set('issued_by', config_item('auth_person_name'));
      $this->connection->set('quantity', 0 - floatval($data['received_quantity']));
      $this->connection->set('unit_value', floatval($data['received_unit_value']));
      $this->connection->insert('tb_stock_cards');

      $this->connection->where('id', $data['id']);
      $this->connection->delete('tb_receipt_items');

      $this->connection->where('id', $data['stock_in_stores_id']);
      $this->connection->delete('tb_stock_in_stores');
    }

    $this->connection->where('id', $id);
    $this->connection->delete('tb_inventory_purchase_requisitions');

    if ($this->connection->trans_status() === FALSE)
      return FALSE;

    $this->connection->trans_commit();
    return TRUE;
  }

  public function searchBudget($category)
  {
    $query = "";
    if($_SESSION['request']['request_to'] == 0){
      $this->column_select = array(
        'tb_inventory_monthly_budgets.*',
        'tb_products.product_name',
        'tb_products.product_code',
        'tb_product_groups.group_name',
        'tb_product_measurements.measurement_symbol',
        'tb_product_purchase_prices.current_price',
       );

      $this->connection->select($this->column_select);
      $this->connection->from('tb_inventory_monthly_budgets');
      $this->connection->join('tb_products', 'tb_products.id = tb_inventory_monthly_budgets.product_id');
      $this->connection->join('tb_product_purchase_prices', 'tb_product_purchase_prices.product_id = tb_products.id');
      $this->connection->join('tb_product_measurements', 'tb_product_measurements.id = tb_products.product_measurement_id');
      $this->connection->join('tb_product_groups', 'tb_product_groups.id = tb_products.product_group_id');
      $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_product_groups.product_category_id');
      $this->connection->where('tb_inventory_monthly_budgets.year_number', $this->budget_year);
      $this->connection->where('tb_inventory_monthly_budgets.month_number', $this->budget_month);
      $this->connection->where('UPPER(tb_product_categories.category_name)', strtoupper($category));
      $this->connection->where('tb_inventory_monthly_budgets.ytd_budget - tb_inventory_monthly_budgets.ytd_used_budget > ', 0, FALSE);
      $this->connection->order_by('tb_products.product_name ASC, tb_products.product_code ASC');
      $query  = $this->connection->get();
    } else {
      $this->db->select('tb_budget.*,tb_master_items.description as "product_name", tb_master_items.part_number as "product_code", tb_master_items.group as "group_name", tb_master_items.current_price,tb_master_items.unit as "measurement_symbol"');
      $this->db->from('tb_budget');
      $this->db->join('tb_budget_cot', 'tb_budget.id_cot = tb_budget_cot.id ');
      $this->db->join('tb_master_items', 'tb_master_items on tb_master_items.id = tb_budget_cot.id_item');
       $this->db->join('tb_master_item_groups', 'tb_master_item_groups on tb_master_items.group = tb_master_item_groups.group');
       $this->db->where('UPPER(tb_master_item_groups.category)', strtoupper($category));
     $this->connection->where('tb_budget.ytd_budget - tb_budget.ytd_used_budget > ', 0, FALSE);
      $this->db->order_by('tb_master_items.description ASC, tb_master_items.part_number ASC');
      $this->db->where('tb_budget_cot.year', $this->budget_year);
      $this->db->where('tb_budget.month_number', $this->budget_month);
      
       $query  = $this->db->get();
    }
    
    $result = $query->result_array();
    foreach ($result as $key => $value) {
      $result[$key]['maximum_quantity'] = $value['ytd_quantity'] - $value['ytd_used_quantity'];
      $result[$key]['maximum_price'] = $value['ytd_budget'] - $value['ytd_used_budget'];

      $this->db->from('tb_master_items');
      $this->db->where('UPPER(tb_master_items.part_number)', strtoupper($value['product_code']));

      $query  = $this->db->get();

      if ($query->num_rows() > 0){
        $master_item = $query->unbuffered_row('array');

        $result[$key]['minimum_quantity'] = $master_item['minimum_quantity'];

        $this->db->select('tb_stocks.total_quantity, tb_stocks.average_value');
        $this->db->from('tb_stocks');
        $this->db->where('tb_stocks.item_id', $master_item['id']);
        $this->db->where('tb_stocks.condition', 'SERVICEABLE');

        $query  = $this->db->get();

        if ($query->num_rows() > 0){
          $stock = $query->unbuffered_row('array');

          $result[$key]['on_hand_quantity'] = $stock['total_quantity'];
          $result[$key]['price']            = $stock['average_value'];
        } else {
          $result[$key]['on_hand_quantity'] = 0;
          $result[$key]['price']            = 0;
        }
      } else {
        $result[$key]['minimum_quantity'] = 0;
        $result[$key]['on_hand_quantity'] = 0;
        $result[$key]['price']            = 0;
      }
    }
    return $result;
  }

  public function searchItemsByPartNumber($category)
  {
    $this->column_select = array(
      'tb_master_items.id',
      'tb_master_items.group',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.alternate_part_number',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_stocks.total_quantity AS on_hand_quantity',
      'tb_stocks.average_value AS price'
     );

    $this->db->select($this->column_select);
    $this->db->from('tb_master_items');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->join('tb_stocks', 'tb_stocks.item_id = tb_master_items.id');
    $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
    $this->db->where('tb_master_item_groups.category', $category);

    $this->db->order_by('tb_master_items.group ASC, tb_master_items.description ASC');
    //echo $this->db->_compile_select();
    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;
  }

  public function searchItemsByProductName($category)
  {
    $this->db->select('tb_master_items.description');
    $this->db->from('tb_master_items');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('UPPER(tb_master_item_groups.category)', strtoupper($category));

    $query  = $this->db->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['description'];
    }

    return $return;
  }

  public function searchItemGroups($category)
  {
    $this->connection->select('tb_product_groups.group_name');
    $this->connection->from('tb_product_groups');
    $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_product_groups.product_category_id');
    $this->connection->where('UPPER(tb_product_categories.category_name)', strtoupper($category));

    $query  = $this->connection->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['group_name'];
    }

    return $return;
  }

  public function getAvailableVendors($category)
  {
    $this->db->select('tb_master_vendors.vendor');
    $this->db->from('tb_master_vendors');
    $this->db->join('tb_master_vendor_categories', 'tb_master_vendors.vendor = tb_master_vendor_categories.vendor');
    $this->db->where('UPPER(tb_master_vendors.status)', 'AVAILABLE');
    $this->db->where('UPPER(tb_master_vendor_categories.category)', strtoupper($category));

    $query  = $this->db->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['vendor'];
    }

    return $return;
  }
  public function cancel($id){
    $detail = $this->findById($id);
    if($_SESSION['request']['request_to'] == 0){
      $this->connection->where('inventory_purchase_requisition_id', $id);
      $query = $this->connection->get('tb_inventory_purchase_requisition_details');
      $inventory_purchase_requisition_details = $query->result();
      foreach ($inventory_purchase_requisition_details as $key ) {
        $this->connection->where('id', $key->inventory_monthly_budget_id);
        $query = $this->connection->get('tb_inventory_monthly_budgets');
        $oldBudget =  $query->row();
        $month_number = $oldBudget->month_number;
        $year_number = $oldBudget->year_number;
        $product_id = $oldBudget->product_id;
        for ($i=$month_number; $i < 13; $i++) { 
          $this->connection->set('mtd_used_budget', 'mtd_used_budget - '.$key->total,FALSE);
          $this->connection->set('ytd_used_budget', 'ytd_used_budget - '.$key->total,FALSE);
          $this->connection->set('mtd_used_quantity', 'mtd_used_quantity - '.$key->quantity,FALSE);
          $this->connection->set('ytd_used_quantity', 'ytd_used_quantity - '.$key->quantity,FALSE);
          $this->connection->where('month_number', $i);
          $this->connection->where('year_number', $year_number);
          $this->connection->where('product_id', $product_id);
          $this->connection->update('tb_inventory_monthly_budgets');
        }
        $this->connection->where('inventory_purchase_requisition_id', $id);
        $this->connection->where('inventory_monthly_budget_id',$key->inventory_monthly_budget_id);
        $this->connection->delete('tb_inventory_used_budgets');
      }
      $this->connection->set('status','canceled');
      $this->connection->where('id', $id);
      $this->connection->update('tb_inventory_purchase_requisitions');
    } else {
      $this->db->where('inventory_purchase_requisition_id', $id);
      $query = $this->db->get('tb_inventory_purchase_requisition_details');
      $inventory_purchase_requisition_details = $query->result();
      foreach ($inventory_purchase_requisition_details as $key ) {
        $this->db->where('id', $key->budget_id);
        $query = $this->db->get('tb_budget');
        $oldBudget =  $query->row();
        $month_number = $oldBudget->month_number;
        $id_cot = $oldBudget->id_cot;
        for ($i=$month_number; $i < 13; $i++) { 
          $this->db->set('mtd_used_budget', 'mtd_used_budget - '.$key->total,FALSE);
          $this->db->set('ytd_used_budget', 'ytd_used_budget - '.$key->total,FALSE);
          $this->db->set('mtd_used_quantity', 'mtd_used_quantity - '.$key->quantity,FALSE);
          $this->db->set('ytd_used_quantity', 'ytd_used_quantity - '.$key->quantity,FALSE);
          $this->db->where('month_number', $i);
          $this->db->where('id_cot', $id_cot);
          $this->db->update('tb_budget');
        }
        $this->db->where('inventory_purchase_requisition_id', $id);
        $this->db->where('budget_id',$key->budget_id);
        $this->db->delete('tb_used_budgets');
      }
      $this->db->set('status','canceled');
      $this->db->where('id', $id);
      $this->db->update('tb_inventory_purchase_requisitions');
    }
    return true;
  }
}
