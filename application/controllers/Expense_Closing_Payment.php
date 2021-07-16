<?php defined('BASEPATH') or exit('No direct script access allowed');

class Expense_Closing_Payment extends MY_Controller
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['expense_closing_payment'];
        $this->load->helper($this->module['helper']);
        $this->load->model($this->module['model'], 'model');
        $this->data['module'] = $this->module;
        $this->load->library('email');
        $this->load->library('upload');
        $this->load->helper('string');
        if (empty($_SESSION['expense_closing']['request_to']))
          $_SESSION['expense_closing']['request_to'] = 1;
        if (empty($_SESSION['expense_closing']['attachment']))
          $_SESSION['expense_closing']['attachment'] = array();
    }

    public function index_data_source()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'index') === FALSE) {
            $return['type'] = 'danger';
            $return['info'] = "You don't have permission to access this page!";
        } else {
            $entities = $this->model->getIndex();
            $data     = array();
            $no       = $_POST['start'];
            $total = array();

            foreach ($entities as $row) {
                $no++;
                $col = array();
                // if ($row['status'] == 'WAITING FOR HEAD DEPT' && config_item('as_head_department')=='yes' && config_item('head_department')==$row['department_name']) {
                //     $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                // }else if($row['status']=='pending' && config_item('auth_role')=='BUDGETCONTROL'){
                //     $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                // }else if($row['status']=='WAITING FOR FINANCE REVIEW' && config_item('auth_role')=='VP FINANCE'){
                //     $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                // }else if($row['status']=='WAITING FOR HOS REVIEW' && config_item('auth_role')=='HEAD OF SCHOOL'){
                //     $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                // }else if($row['status']=='WAITING FOR COO REVIEW' && config_item('auth_role')=='CHIEF OPERATION OFFICER'){
                //     $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                // }else{                    
                //     $col[] = print_number($no);
                // }
                $account = getAccountByCode($row['account']);
                $col[] = print_number($no);
                $col[] = print_string($row['pr_number']);
                $col[] = print_string(strtoupper($row['status']));
                $col[] = print_string(strtoupper($row['department_name']));
                $col[] = print_string($row['cost_center_name']);
                $col[] = print_date($row['pr_date']);
                $col[] = print_date($row['closing_date']);
                $col[] = $account->coa.' '.$account->group;
                $col[] = print_number($row['total_expense'],2);
                $col[] = $row['closing_notes'];
                $col['DT_RowId'] = 'row_'. $row['id'];
                $col['DT_RowData']['pkey']  = $row['id'];

                $total[]         = $row['total_expense'];

                if ($this->has_role($this->module, 'info')){
                    $col['DT_RowAttr']['onClick']     = '';
                    $col['DT_RowAttr']['data-id']     = $row['id'];
                    $col['DT_RowAttr']['data-target'] = '#data-modal';
                    $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/info/'. $row['id']);
                }

                $data[] = $col;
            }

            $result = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->model->countIndex(),
                "recordsFiltered" => $this->model->countIndexFiltered(),
                "data" => $data,
                "total" => array(
                    8  => print_number(array_sum($total), 2),
                )
            );
        }

        echo json_encode($result);
    }

    public function index()
    {
        $this->authorized($this->module, 'index');
        $source =  'Budget Control';
        $this->data['page']['title']            = $this->module['label'] . strtoupper(" " . $source);
        $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
        $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source');
        $this->data['grid']['fixed_columns']    = 2;
        $this->data['grid']['summary_columns']  = array(8);
        $this->data['grid']['order_columns']    = array(
            0   => array( 0 => 1,  1 => 'desc' ),
            1   => array( 0 => 2,  1 => 'desc' ),
            2   => array( 0 => 3,  1 => 'desc' ),
            3   => array( 0 => 4,  1 => 'asc' ),
            4   => array( 0 => 5,  1 => 'asc' ),
            5   => array( 0 => 6,  1 => 'asc' ),
            6   => array( 0 => 7,  1 => 'asc' ),
            7   => array( 0 => 8,  1 => 'asc' ),
            8   => array( 0 => 9,  1 => 'asc' ),
            
        );

        $this->render_view($this->module['view'] . '/index');
    }

    public function info($id)
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'info') === FALSE) {
            $return['type'] = 'denied';
            $return['info'] = "You don't have permission to access this data. You may need to login again.";
        } else {
            $entity = $this->model->findById($id);
            $account = getAccountByCode($entity['account']);

            $this->data['entity'] = $entity;
            $this->data['account'] = $account;

            $return['type'] = 'success';
            $return['info'] = $this->load->view($this->module['view'] . '/info', $this->data, TRUE);
        }

        echo json_encode($return);
    }

    public function create($expense_request_id)
    {
        $this->authorized($this->module, 'document');
        $entity = $this->model->findExpenseRequestByid($expense_request_id);

        $_SESSION['expense_closing']['items']               = $entity['items'];
        $_SESSION['expense_closing']['document__number']    = $entity['pr_number'];
        $_SESSION['expense_closing']['date']                = $entity['required_date'];
        $_SESSION['expense_closing']['notes']               = $entity['notes'];
        $_SESSION['expense_closing']['closing_notes']       = NULL;
        $_SESSION['expense_closing']['account']             = NULL;
        $_SESSION['expense_closing']['id']                  = $expense_request_id;
        $this->render_view($this->module['view'] .'/create');
    }

    public function save()
    {
        if ($this->input->is_ajax_request() == FALSE)
          redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'document') == FALSE){
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {
            if (!isset($_SESSION['expense_closing']['account']) || empty($_SESSION['expense_closing']['account'])){
                $data['success'] = FALSE;
                $data['message'] = 'Account can not null! Please select account!';
            }else{
                if ($this->model->save()){
                    unset($_SESSION['expense_closing']);

                    $data['success'] = TRUE;
                    $data['message'] = 'Expense '. $document_number .' has been close to payment. You will redirected now.';
                } else {
                    $data['success'] = FALSE;
                    $data['message'] = 'Error while saving this document. Please ask Technical Support.';
                }
            }
            
        }

        echo json_encode($data);
    }

    public function set_date()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] . '/denied');

        $_SESSION['expense_closing']['date'] = $_GET['data'];
    }

    public function set_notes()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] . '/denied');

        $_SESSION['expense_closing']['closing_notes'] = $_GET['data'];
    }

    public function set_account()
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['expense_closing']['account'] = $_GET['data'];
    }

    public function discard()
    {
        $this->authorized($this->module['permission']['document']);

        unset($_SESSION['expense_closing']);

        redirect($this->module['route']);
    }
}
