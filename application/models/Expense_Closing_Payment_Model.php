<?php defined('BASEPATH') or exit('No direct script access allowed');

class Expense_Closing_Payment_Model extends MY_Model
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
        $this->modules        = config_item('module');
        $this->data['modules']        = $this->modules;
    }

    public function getSelectedColumns()
    {
        $return = array(
            'tb_request_payments.id'                                                 => NULL,
            'tb_request_payments.document_number as no_transaksi'                    => 'Transaction Number',
            'tb_request_payments.tanggal'                                            => 'Date',
            'tb_request_payments.no_cheque'                                          => 'No Cheque',
            'tb_request_payments.vendor'                                             => 'Pay TO',
            'tb_request_payments.currency'                                           => 'Currency',
            'tb_request_payments.coa_kredit'                                         => 'Account',
            'SUM(tb_request_payment_details.amount_paid) as amount_paid'             => 'Amount IDR',
            'tb_request_payments.akun_kredit'                                        => 'Amount USD',
            'tb_request_payments.status'                                             => 'Status',
            'tb_request_payments.rejected_notes'                                     => 'Attachment',
            'tb_request_payments.base'                                               => 'Base',
            'tb_request_payments.notes'                                              => 'Notes',
        );
        if(is_granted($this->data['modules']['expense_closing_payment'], 'approval')){
            $return['tb_request_payments.approval_notes']  = 'Input Notes';
        }else{
            $return['tb_request_payments.approval_notes']  = 'Approval/Rejected Notes';
        }



        return $return;
    }

    public function getSearchableColumns()
    {
        $return = array(
            // 'tb_purchase_order_items_payments.id',
            'tb_request_payments.document_number',
            // 'tb_purchase_order_items_payments.tanggal',
            'tb_request_payments.no_cheque',
            // 'tb_request_payments.document_number',
            // 'tb_po_item.part_number',
            // 'tb_purchase_order_items_payments.deskripsi',
            'tb_request_payments.currency',
            'tb_request_payments.coa_kredit',
            'tb_request_payments.akun_kredit',
            // 'tb_purchase_order_items_payments.amount_paid',
            'tb_request_payments.created_by',
            'tb_request_payments.vendor',
            'tb_request_payments.status',
            'tb_request_payments.base'
            // 'tb_purchase_order_items_payments.created_at',
        );

        return $return;
    }

    public function getOrderableColumns()
    {
        $return = array(
            NULL,
            'tb_request_payments.document_number',
            'tb_request_payments.tanggal',
            'tb_request_payments.no_cheque',
            // 'tb_po.document_number',
            'tb_request_payments.vendor',
            // 'tb_po_item.part_number',
            // 'tb_purchase_order_items_payments.deskripsi',
            'tb_request_payments.currency',          
            'tb_request_payments.coa_kredit',
            // 'tb_purchase_order_items_payments.amount_paid',
            'tb_request_payments.base',
            'tb_request_payments.notes',
            // 'tb_request_payments.created_at'
        );

        return $return;
    }

    public function getGroupedColumns()
    {
        $return = array(
            'tb_request_payments.id',
            'tb_request_payments.document_number',
            'tb_request_payments.tanggal',
            'tb_request_payments.no_cheque',
            'tb_request_payments.vendor',
            'tb_request_payments.currency',
            'tb_request_payments.status',
            'tb_request_payments.base',
            'tb_request_payments.notes',
            'tb_request_payments.approval_notes',
            'tb_request_payments.rejected_notes'
        );

        return $return;
    }

    private function searchIndex()
    {
        if (!empty($_POST['columns'][1]['search']['value'])) {
            $search_received_date = $_POST['columns'][1]['search']['value'];
            $range_received_date  = explode(' ', $search_received_date);

            $this->connection->where('tb_request_payments.tanggal >= ', $range_received_date[0]);
            $this->connection->where('tb_request_payments.tanggal <= ', $range_received_date[1]);
        }

        if (!empty($_POST['columns'][2]['search']['value'])) {
            $vendor = $_POST['columns'][2]['search']['value'];

            $this->connection->where('tb_request_payments.vendor', $vendor);
        }

        if (!empty($_POST['columns'][3]['search']['value'])) {
            $currency = $_POST['columns'][3]['search']['value'];

            if ($currency != 'all') {
                $this->connection->where('tb_request_payments.currency', $currency);
            }
        }

        if (!empty($_POST['columns'][4]['search']['value'])) {
            $status = $_POST['columns'][4]['search']['value'];
            if($status!='all'){
                $this->connection->like('tb_request_payments.status', $status);
            }           
        } else {
            if(is_granted($this->data['modules']['expense_closing_payment'], 'approval')){
                if (config_item('auth_role') == 'FINANCE SUPERVISOR') {
                    $status[] = 'WAITING CHECK BY FIN SPV';
                }
                if (config_item('auth_role') == 'FINANCE MANAGER') {
                    $status[] = 'WAITING REVIEW BY FIN MNG';
                }
                if (config_item('auth_role') == 'HEAD OF SCHOOL') {
                    $status[] = 'WAITING REVIEW BY HOS';
                }
                if (config_item('auth_role') == 'CHIEF OPERATION OFFICER') {
                    $status[] = 'WAITING REVIEW BY CEO';
                }
                if (config_item('auth_role') == 'VP FINANCE') {
                    $status[] = 'WAITING REVIEW BY VP FINANCE';
                }
                if (config_item('auth_role') == 'CHIEF OF FINANCE') {
                    $status[] = 'WAITING REVIEW BY CFO';
                }
                $this->connection->where_in('tb_request_payments.status', $status);
            }elseif(is_granted($this->data['modules']['expense_closing_payment'], 'review')){
				$status[] = 'APPROVED';
				$this->connection->where_in('tb_request_payments.status', $status);
			}else{
                if (config_item('auth_role') == 'TELLER') {
                    $status[] = 'APPROVED';
                    $this->connection->where_in('tb_request_payments.status', $status);
                }
            }       
            
        }

        if (!empty($_POST['columns'][5]['search']['value'])) {
            $base = $_POST['columns'][5]['search']['value'];
            if($base!='ALL'){
                if($base!='JAKARTA'){
                    $this->connection->where('tb_request_payments.base !=','JAKARTA');
                }elseif($base=='JAKARTA'){
                    $this->connection->where('tb_request_payments.base','JAKARTA');
                }   
            }
                    
        } else {
            if(config_item('auth_role') == 'AP STAFF' || config_item('auth_role') == 'FINANCE MANAGER'){
                $base = config_item('auth_warehouse');
                if($base!='JAKARTA'){
                    $this->connection->where('tb_request_payments.base !=','JAKARTA');
                }elseif($base=='JAKARTA'){
                    $this->connection->where('tb_request_payments.base','JAKARTA');
                }   
            }
            
        }

        if (!empty($_POST['columns'][6]['search']['value'])) {
			$type = $_POST['columns'][6]['search']['value'];
			if($type!='all'){
				$this->connection->like('tb_request_payments.type', $type);
			}			
		}

		if (!empty($_POST['columns'][7]['search']['value'])) {
			$account = $_POST['columns'][7]['search']['value'];
			if($account!='all'){
				$this->connection->like('tb_request_payments.coa_kredit', $account);
			}			
		}

        $i = 0;

        foreach ($this->getSearchableColumns() as $item) {
            if ($_POST['search']['value']) {
                $term = strtoupper($_POST['search']['value']);

                if ($i === 0) {
                    $this->connection->group_start();
                    $this->connection->like('UPPER(' . $item . ')', $term);
                } else {
                    $this->connection->or_like('UPPER(' . $item . ')', $term);
                }

                if (count($this->getSearchableColumns()) - 1 == $i)
                    $this->connection->group_end();
            }

            $i++;
        }
    }

    function getIndex($return = 'array')
    {
        $this->connection->select(array_keys($this->getSelectedColumns()));
        $this->connection->from('tb_request_payments');
        $this->connection->join('tb_request_payment_details', 'tb_request_payments.id = tb_request_payment_details.request_payment_id');
        $this->connection->where('tb_request_payments.source','EXPENSE');
        $this->connection->group_by($this->getGroupedColumns());

        $this->searchIndex();

        $column_order = $this->getOrderableColumns();

        if (isset($_POST['order'])) {
            foreach ($_POST['order'] as $key => $order) {
                $this->connection->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
            }
        } else {
            $this->connection->order_by('tb_request_payments.tanggal', 'desc');
        }

        if ($_POST['length'] != -1)
            $this->connection->limit($_POST['length'], $_POST['start']);

        $query = $this->connection->get();

        if ($return === 'object') {
            return $query->result();
        } elseif ($return === 'json') {
            return json_encode($query->result());
        } else {
            return $query->result_array();
        }
    }

    function countIndexFiltered()
    {
        $this->connection->select(array_keys($this->getSelectedColumns()));
        $this->connection->from('tb_request_payments');
        $this->connection->join('tb_request_payment_details', 'tb_request_payments.id = tb_request_payment_details.request_payment_id');
        $this->connection->where('tb_request_payments.source','EXPENSE');
        $this->connection->group_by($this->getGroupedColumns());

        $this->searchIndex();

        $query = $this->connection->get();

        return $query->num_rows();
    }

    public function countIndex()
    {
        $this->connection->select(array_keys($this->getSelectedColumns()));
        $this->connection->from('tb_request_payments');
        $this->connection->join('tb_request_payment_details', 'tb_request_payments.id = tb_request_payment_details.request_payment_id');
        $this->connection->where('tb_request_payments.source','EXPENSE');
        $this->connection->group_by($this->getGroupedColumns());

        $query = $this->connection->get();

        return $query->num_rows();
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

    public function findById($id)
    {
        $this->connection->select('tb_request_payments.*');
        $this->connection->where('tb_request_payments.id', $id);
        $this->connection->from('tb_request_payments');
        $query    = $this->connection->get();
        $request  = $query->unbuffered_row('array');

        $select = array(
            'sum(tb_request_payment_details.amount_paid) as amount_paid',
            'tb_request_payment_details.request_id',
            'tb_request_payment_details.remarks',
            'tb_request_payment_details.pr_number',
            'tb_request_payment_details.advance_account_code',
            'tb_request_payment_details.uang_muka'
        );

        $this->connection->select($select);
        $this->connection->from('tb_request_payment_details');
        $this->connection->where('tb_request_payment_details.request_payment_id', $id);
        $this->connection->group_by(array(
            'tb_request_payment_details.request_id',
            'tb_request_payment_details.remarks',
            'tb_request_payment_details.pr_number',
            'tb_request_payment_details.advance_account_code',
            'tb_request_payment_details.uang_muka'
        ));

        $query = $this->connection->get();
        $total_paid = 0;

        foreach ($query->result_array() as $key => $req){
            $request['request'][$key] = $req;
            if($req['request_id']!=NULL){
                $select = array(
                    'tb_request_payment_details.*'
                );

                $this->connection->select($select);
                $this->connection->from('tb_request_payment_details');
                $this->connection->where('tb_request_payment_details.request_id', $req['request_id']);
                $this->connection->where('tb_request_payment_details.request_payment_id', $id);
                $query = $this->connection->get();

                foreach ($query->result_array() as $i => $item){
                    $request['request'][$key]['items'][$i] = $item;
                }
            }else{
                $select = array(
                    'tb_request_payment_details.*'
                );

                $this->connection->select($select);
                $this->connection->from('tb_request_payment_details');
                $this->connection->where('tb_request_payment_details.remarks', $req['remarks']);
                $this->connection->where('tb_request_payment_details.request_payment_id', $id);
                $query = $this->connection->get();

                foreach ($query->result_array() as $i => $item){
                    $request['request'][$key]['items'][$i] = $item;
                }
            }
            $total_paid = $total_paid+$req['amount_paid'];
        }
        $request['total_paid'] = $total_paid;

        if($request['status']=='PAID'){
            $this->db->select('tb_jurnal.*');
            $this->db->where('tb_jurnal.no_jurnal', $request['document_number']);
            $this->db->from('tb_jurnal');
            $queryJurnal    = $this->db->get();
            $jurnal         = $queryJurnal->unbuffered_row('array');

            $this->db->select('tb_jurnal_detail.*');
            $this->db->from('tb_jurnal_detail');
            $this->db->where('tb_jurnal_detail.id_jurnal', $jurnal['id']);

            $queryDetailJurnal = $this->db->get();

            foreach ($queryDetailJurnal->result_array() as $key => $detail){
                $request['jurnalDetail'][$key] = $detail;
            }
        }

        // $request['advance_list'] = $this->getAdvance($id);
        $request['advance_list'] = array();

        return $request;
    }

    public function getHistory($annual_cost_center_id,$account_id,$order_number)
    {
        $select = array(
          'tb_expense_purchase_requisitions.pr_number',
          'tb_expense_purchase_requisitions.pr_date',
          'tb_expense_purchase_requisitions.created_by',
          'tb_expense_purchase_requisition_details.amount',
          'tb_expense_purchase_requisition_details.total',
        );
        $this->connection->select($select);
        $this->connection->from('tb_expense_purchase_requisition_details');
        $this->connection->join('tb_expense_purchase_requisitions', 'tb_expense_purchase_requisitions.id = tb_expense_purchase_requisition_details.expense_purchase_requisition_id');
        $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
        $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
        $this->connection->where('tb_expense_monthly_budgets.account_id', $account_id);
        $this->connection->where('tb_expense_purchase_requisitions.order_number <',$order_number);
        $query  = $this->connection->get();

        return $query->result_array();
    }

    public function approve($request_payment_id)
    {
        $this->connection->trans_begin();

        $send_to_vp_finance = array();

        foreach ($request_payment_id as $key) {
            $id = $key;
            $this->connection->select('tb_request_payments.*');
            $this->connection->from('tb_request_payments');
            $this->connection->where('tb_request_payments.id',$id);
            $query          = $this->connection->get();
            $request_payment     = $query->unbuffered_row('array');
            $currency       = $request_payment['currency'];
            $level          = 0;
            $status         = '';

            if (config_item('auth_role')=='FINANCE MANAGER' && $request_payment['status'] == 'WAITING REVIEW BY FIN MNG') {
                if($request_payment['base']=='JAKARTA'){
                    $this->connection->set('status', 'APPROVED');
                    $status = 'APPROVED';
                    $level = 0;
                }else{
                    $this->connection->set('status', 'APPROVED');
                    $status = 'APPROVED';
                    $level = 0;
                }           
                $this->connection->set('review_by', config_item('auth_person_name'));
                $this->connection->set('review_at', date('Y-m-d'));
                $this->connection->where('id', $id);
                $this->connection->update('tb_request_payments');

                $statusPayment = $this->getStatuspayment($id);

                if($statusPayment['status']=='PAID'){
                    //langsung ubah request payment
                    $this->buat_pembayaran_langsung($id,$statusPayment['advance_account_code']); 
                }
            }

            if (config_item('auth_role')=='VP FINANCE' && $request_payment['status'] == 'WAITING REVIEW BY VP FINANCE') {
                $this->connection->set('status', 'APPROVED');
                $status = 'APPROVED';
                $level = 0;
                $this->connection->set('approved_by', config_item('auth_person_name'));
                $this->connection->set('approved_at', date('Y-m-d'));
                $this->connection->where('id', $id);
                $this->connection->update('tb_request_payments');
            }
        }

        

        if ($this->connection->trans_status() === FALSE)
            return FALSE;

        if(!empty($send_to_vp_finance)){
            $this->send_mail($send_to_vp_finance,3);
        }

        $this->connection->trans_commit();        
        
        return TRUE;
    }

    public function reject($request_payment_id,$notes)
    {
        $this->connection->trans_begin();

        $send_to_vp_finance = array();
        $x = 0;
        $return = 0;
        $rejected_note = '';

        foreach ($request_payment_id as $key) {
            $id = $key;
            $this->connection->select('tb_request_payments.*');
            $this->connection->from('tb_request_payments');
            $this->connection->where('tb_request_payments.id',$id);
            $query          = $this->connection->get();
            $request_payment     = $query->unbuffered_row('array');
            $currency       = $request_payment['currency'];
            $level          = 0;
            $status         = '';

            if($request_payment['status']!='REJECTED' || $request_payment['status']!='REVISI' || $request_payment['status']!='PAID'){
                $this->connection->set('status', 'REJECTED');
                $this->connection->set('rejected_by', config_item('auth_person_name'));
                $this->connection->set('rejected_at', date('Y-m-d'));
                $this->connection->set('rejected_notes',$notes[$x]);
                $this->connection->where('id', $id);
                $this->connection->update('tb_request_payments');
            }
            $x++;
        }

        

        if ($this->connection->trans_status() === FALSE)
            return FALSE;

        // if(!empty($send_to_vp_finance)){
        //     $this->send_mail($send_to_vp_finance,3);
        // }

        $this->connection->trans_commit();        
        
        return TRUE;
    }

    public function searchBudget($annual_cost_center_id)
    {
        $query = "";
        $this->column_select = array(
            'SUM(tb_expense_monthly_budgets.mtd_budget) as budget',
            'SUM(tb_expense_monthly_budgets.mtd_used_budget) as used_budget',
            'tb_expense_monthly_budgets.account_id',
            'tb_accounts.account_name',
            'tb_accounts.account_code',
            'tb_expense_monthly_budgets.annual_cost_center_id',
        );

        $this->column_groupby = array(
            'tb_expense_monthly_budgets.account_id',
            'tb_accounts.account_name',
            'tb_accounts.account_code',
            'tb_expense_monthly_budgets.annual_cost_center_id',
        );

        $this->connection->select($this->column_select);
        $this->connection->from('tb_expense_monthly_budgets');
        $this->connection->join('tb_accounts', 'tb_accounts.id = tb_expense_monthly_budgets.account_id');
        $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
        $this->connection->group_by($this->column_groupby);
        $this->connection->order_by('tb_accounts.account_code ASC, tb_accounts.account_name ASC');
          $query  = $this->connection->get();

        $result = $query->result_array();
        foreach ($result as $key => $value) {
            $result[$key]['maximum_price'] = $value['budget'] - $value['used_budget'];
            $select = array(
                'tb_expense_monthly_budgets.ytd_budget',
                'tb_expense_monthly_budgets.ytd_used_budget',
                'tb_expense_monthly_budgets.id',
            );

            $this->connection->select($select);
            $this->connection->from('tb_expense_monthly_budgets');
            $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
            $this->connection->where('tb_expense_monthly_budgets.account_id', $value['account_id']);
            $this->connection->where('tb_expense_monthly_budgets.month_number', $this->budget_month);
            $query_row = $this->connection->get();
            $row   = $query_row->unbuffered_row('array');
            $result[$key]['mtd_budget']                 = $row['ytd_budget'] - $row['ytd_used_budget'];
            $result[$key]['expense_monthly_budget_id']  = $row['id'];
        }
        return $result;
    }

    public function isDocumentNumberExists($pr_number)
    {
        $this->db->where('document_number', $pr_number);
        $query = $this->db->get('tb_po_payment_no_transaksi');

        if ($query->num_rows() > 0)
          return true;

        return false;
    }

    public function isOrderNumberExists($order_number)
    {
        $this->connection->where('order_number', $order_number);
        $query = $this->connection->get('tb_expense_purchase_requisitions');

        if ($query->num_rows() > 0)
          return true;

        return false;
    }

    public function save()
    {
        $this->connection->trans_begin();
        $this->db->trans_begin();

        $id                     = (isset($_SESSION['request_closing']['id'])) ? $_SESSION['request_closing']['id'] : NULL;
        $closing_date           = $_SESSION['request_closing']['date'];
        $purposed_date          = $_SESSION['request_closing']['purposed_date'];
        $vendor                 = $_SESSION['request_closing']['vendor'];
        $closing_by             = config_item('auth_person_name');
        $notes                  = (empty($_SESSION['request_closing']['closing_notes'])) ? NULL : $_SESSION['request_closing']['closing_notes'];
        $account                = $_SESSION['request_closing']['coa_kredit'];
        $type                   = $_SESSION['request_closing']['type'];
        $document_number        = $_SESSION['request_closing']['document_number'].payment_request_format_number($_SESSION['request_closing']['type']);

        $base                   = config_item('auth_warehouse');
        $akun_kredit            = getAccountByCode($account);
        $total_purposed_payment = array();
        $currency               = $_SESSION['request_closing']['currency'];
        $kurs                   = $this->tgl_kurs(date("Y-m-d"));

        

        if ($id === NULL) {
            $this->connection->set('document_number', $document_number);
            $this->connection->set('source', 'EXPENSE');
            $this->connection->set('vendor', strtoupper($vendor));
            $this->connection->set('tanggal', $closing_date);
            $this->connection->set('purposed_date', $purposed_date);
            $this->connection->set('currency', $currency);
            $this->connection->set('created_by', config_item('auth_person_name'));
            $this->connection->set('created_at', date('Y-m-d'));
            $this->connection->set('base', $base);
            $this->connection->set('notes', $notes);
            $this->connection->set('coa_kredit', $account);
            $this->connection->set('akun_kredit', $akun_kredit->group);         
            if($type=='CASH'){
                $this->connection->set('status','APPROVED');
                $this->connection->set('cash_request','OPEN');
                $this->connection->set('paid_by', config_item('auth_person_name'));
                $this->connection->set('paid_at', date("Y-m-d",strtotime($closing_date)));
            }else{
                // if($base=='JAKARTA'){
                $this->connection->set('status','WAITING REVIEW BY FIN MNG');
                // }
            }
            $this->connection->set('type',$type);
            $this->connection->insert('tb_request_payments');
            $request_payment_id = $this->connection->insert_id();

            $this->db->set('document_number', $document_number);
            $this->db->set('source', 'EXPENSE');            
            $this->db->insert('tb_po_payment_no_transaksi');

            if($type=='CASH2'){
                $this->db->set('no_jurnal', $document_number);
                $this->db->set('tanggal_jurnal  ', date("Y-m-d",strtotime($closing_date)));
                $this->db->set('source', "AP-EXP");
                $this->db->set('vendor', $vendor);
                $this->db->set('grn_no', $document_number);
                $this->db->set('keterangan', strtoupper("pembayaran purchase order"));
                $this->db->set('created_by',config_item('auth_person_name'));
                $this->db->set('created_at',date('Y-m-d'));
                $this->db->insert('tb_jurnal');
                $id_jurnal = $this->db->insert_id();
            }
        }else{
            //utk edit
            $request_payment_id = $id;
        }

        $request_items_id           = $this->input->post('request_item_id');
        $amount                     = $this->input->post('value');
        $remarks                    = $this->input->post('remarks');
        $account_code               = $this->input->post('account_code');
        $uang_muka                  = $this->input->post('uang_muka');
        $advance_account_code                  = $this->input->post('advance_account_code');

        $array_request_id = array();
        $array_advance_nominal = array();

        /*foreach ($_SESSION['request_closing']['items'] as $key => $request) {
            foreach ($request['request_detail'] as $j => $item) {
                $total_purposed_payment[] = $item['total'];
                $this->connection->set('request_payment_id', $request_payment_id);
                $this->connection->set('request_item_id', $item['id']);
                $this->connection->set('request_id', $item['expense_purchase_requisition_id']);
                $this->connection->set('pr_number', $request['pr_number']);
                $this->connection->set('amount_paid', $item['total']);
                $this->connection->set('remarks', $request['notes']);
                $this->connection->set('account_code', $item['account_code']);
                $this->connection->set('deskripsi', $item['account_code'].' '.$item['account_name']);
                $this->connection->set('created_by', config_item('auth_person_name'));
                $this->connection->set('adj_value', 0);
                $this->connection->set('quantity_paid', 1);
                $this->connection->set('uang_muka', 0);
                $this->connection->insert('tb_request_payment_details');

                $this->connection->set('process_amount', '"process_amount" + ' . $item['total'], false);
                $this->connection->where('id', $item['id']);
                $this->connection->update('tb_expense_purchase_requisition_details');

                // $process_amount_expense = countProcessAmountExpense($item['request_id']);
                if($this->updateStatusExpense($item['request_id'])){
                    if($type=='CASH2'){
                        $this->connection->set('closing_date', $closing_date);
                        $this->connection->set('status', 'close');
                        $this->connection->set('closing_notes', $notes);
                        $this->connection->set('closing_by', $closing_by);
                        $this->connection->set('account', $account);
                    }else{
                        $this->connection->set('status', 'PAYMENT PURPOSED');
                    }                
                    $this->connection->where('id', $item['request_id']);
                    $this->connection->update('tb_expense_purchase_requisitions');
                }

                if($type=='CASH2'){
                    if ($currency == 'IDR') {
                        $amount_idr = $item['total'];
                        $amount_usd = $item['total'] / $kurs;
                    } else {
                        $amount_usd = $item['total'];
                        $amount_idr = $item['total'] * $kurs;
                    }

                        
                    $akun = getAccountBudgetControlByCode($item['account_code']);

                    $this->db->set('id_jurnal', $id_jurnal);
                    $this->db->set('jenis_transaksi', strtoupper($akun->group));
                    $this->db->set('trs_kredit', 0);
                    $this->db->set('trs_debet', $amount_idr);
                    $this->db->set('trs_kredit_usd', 0);
                    $this->db->set('trs_debet_usd', $amount_usd);
                    $this->db->set('kode_rekening', $akun->coa);
                    $this->db->set('currency', $currency);
                    $this->db->insert('tb_jurnal_detail');
                }
            }
            
        }*/
        $total_amount = array();
        $total_advance = array();
        foreach ($request_items_id as $key=>$request_item_id){

            if($request_item_id!=NULL){
                $request_item = $this->getInfoRequestItemById($request_item_id);
            }            

            $selectedAccount = getAccountMrpByCode($account_code[$key]);

            $total_purposed_payment[] = $amount[$key];
            if($request_item_id!=NULL){
                $this->connection->set('request_item_id', $request_item['id']); 
                $this->connection->set('request_id', $request_item['expense_purchase_requisition_id']);
                $this->connection->set('pr_number', $request_item['pr_number']);
            }
            $this->connection->set('request_payment_id', $request_payment_id); 
            $this->connection->set('amount_paid', $amount[$key]);
            $this->connection->set('remarks', $remarks[$key]);
            $this->connection->set('account_code', $account_code[$key]);
            $this->connection->set('deskripsi', $selectedAccount->coa.' '.$selectedAccount->group);
            $this->connection->set('created_by', config_item('auth_person_name'));
            $this->connection->set('adj_value', 0);
            $this->connection->set('quantity_paid', 1);
            $this->connection->set('uang_muka', $uang_muka[$key]);
            $this->connection->set('advance_account_code', $advance_account_code[$key]);
            $this->connection->insert('tb_request_payment_details');

            if($request_item_id!=NULL){
                $this->connection->set('process_amount', '"process_amount" + ' . $amount[$key], false);
                $this->connection->where('id', $request_item['id']);
                $this->connection->update('tb_expense_purchase_requisition_details');

                // $process_amount_expense = countProcessAmountExpense($item['request_id']);
                if($this->updateStatusExpense($request_item['expense_purchase_requisition_id'])){
                    if($type=='CASH2'){
                        $this->connection->set('closing_date', $closing_date);
                        $this->connection->set('status', 'close');
                        $this->connection->set('closing_notes', $notes);
                        $this->connection->set('closing_by', $closing_by);
                        $this->connection->set('account', $account);
                    }else{
                        $this->connection->set('status', 'PAYMENT PURPOSED');
                    }                
                    $this->connection->where('id', $request_item['expense_purchase_requisition_id']);
                    $this->connection->update('tb_expense_purchase_requisitions');
                }
                if(!in_array($request_item['expense_purchase_requisition_id'],$array_request_id)){
                    array_push($array_request_id,$request_item['expense_purchase_requisition_id']);
                    array_push($array_advance_nominal,$uang_muka[$key]);
                    $total_advance[] = $uang_muka[$key];
                }
            }
            

            if($type=='CASH2'){
                if ($currency == 'IDR') {
                    $amount_idr = $amount[$key];
                    $amount_usd = $amount[$key] / $kurs;
                } else {
                    $amount_usd = $amount[$key];
                    $amount_idr = $amount[$key] * $kurs;
                }

                        
                $akun = getAccountMrpByCode($account_code[$key]);

                $this->db->set('id_jurnal', $id_jurnal);
                $this->db->set('jenis_transaksi', strtoupper($akun->group));
                $this->db->set('trs_kredit', 0);
                $this->db->set('trs_debet', $amount_idr);
                $this->db->set('trs_kredit_usd', 0);
                $this->db->set('trs_debet_usd', $amount_usd);
                $this->db->set('kode_rekening', $akun->coa);
                $this->db->set('currency', $currency);
                $this->db->insert('tb_jurnal_detail');
            }

            $total_amount[] = $amount[$key];
        }

        $this->connection->set('total', (array_sum($total_amount)-array_sum($total_advance)));
        $this->connection->set('advance_total', array_sum($total_advance));
        $this->connection->where('id', $request_payment_id);
        $this->connection->update('tb_request_payments');


        if($type=='CASH2'){
            $total_amount = array_sum($total_purposed_payment);
            if ($currency == 'IDR') {
                $amount_idr = $total_amount;
                $amount_usd = $total_amount / $kurs;
            } else {
                $amount_usd = $total_amount;
                $amount_idr = $total_amount * $kurs;
            }
            $this->db->set('id_jurnal', $id_jurnal);
            $this->db->set('jenis_transaksi', $akun_kredit->group);
            $this->db->set('trs_debet', 0);
            $this->db->set('trs_kredit', $amount_idr);
            $this->db->set('trs_debet_usd', 0);
            $this->db->set('trs_kredit_usd', $amount_usd);
            $this->db->set('kode_rekening', $account);
            $this->db->set('currency', $currency);
            $this->db->insert('tb_jurnal_detail');
        }

        foreach ($array_request_id as $key=>$request_id){
            $this->connection->set('advance_nominal', '"advance_nominal" - ' . $array_advance_nominal[$key], false);
            $this->connection->where('id', $request_id);
            $this->connection->update('tb_expense_purchase_requisitions');
        }

        // $this->connection->set('closing_date', $closing_date);
        // $this->connection->set('status', 'close');
        // $this->connection->set('closing_notes', $notes);
        // $this->connection->set('closing_by', $closing_by);
        // $this->connection->set('account', $account);
        // $this->connection->where('id', $id);
        // $this->connection->update('tb_expense_purchase_requisitions');

        // $this->connection->select('tb_expense_purchase_requisition_details.total, tb_expense_purchase_requisition_details.id');
        // $this->connection->from('tb_expense_purchase_requisition_details');
        // $this->connection->where('tb_expense_purchase_requisition_details.expense_purchase_requisition_id', $id);

        // $query  = $this->connection->get();
        // $result = $query->result_array();

        // foreach ($result as $data) {
        //     $this->connection->set('process_amount', '"process_amount" + ' . $data['total'], false);
        //     $this->connection->where('id', $data['id']);
        //     $this->connection->update('tb_expense_purchase_requisition_details');
        // }
        

        if ($this->connection->trans_status() === FALSE || $this->db->trans_status() === FALSE)
            return FALSE;

        $this->connection->trans_commit();
        $this->db->trans_commit();
        if($type!='CASH'){
            $this->send_mail($request_payment_id,14,$base);
        }

        return TRUE;
    }

    function getInfoRequestItemById($id){
        $select = array(
            'tb_expense_purchase_requisition_details.*',
            'tb_expense_purchase_requisitions.pr_number',
            'tb_expense_purchase_requisitions.notes',
            'tb_accounts.account_name',
            'tb_accounts.account_code',
            'tb_expense_monthly_budgets.account_id',
        );

        $this->connection->select($select);
        $this->connection->from('tb_expense_purchase_requisition_details');
        $this->connection->join('tb_expense_purchase_requisitions', 'tb_expense_purchase_requisitions.id = tb_expense_purchase_requisition_details.expense_purchase_requisition_id');
        $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
        $this->connection->join('tb_accounts', 'tb_accounts.id = tb_expense_monthly_budgets.account_id');
        $this->connection->where('tb_expense_purchase_requisition_details.id', $id);

        $query      = $this->connection->get(); 
        $request    = $query->unbuffered_row('array');

        return $request;
    }

    function save_pembayaran()
    {
        $this->connection->trans_begin();
        $this->db->trans_begin();
        // $item = $this->input->post('item');
        $account        = $this->input->post('account');
        $vendor         = $this->input->post('vendor');
        $no_cheque      = $this->input->post('no_cheque');
        $tanggal        = $this->input->post('date');
        $amount         = $this->input->post('amount');
        $no_jurnal      = $this->input->post('no_transaksi');
        $currency       = $this->input->post('currency');
        $no_konfirmasi  = $this->input->post('no_konfirmasi');
        $paid_base      = $this->input->post('paid_base');
        $kurs           = $this->tgl_kurs(date("Y-m-d"));
        $tipe           = $this->input->post('tipe');
        $po_payment_id          = $this->input->post('po_payment_id');
        if ($currency == 'IDR') {
            $amount_idr = $amount;
            $amount_usd = $amount / $kurs;
        } else {
            $amount_usd = $amount;
            $amount_idr = $amount * $kurs;
        }


        $this->db->set('no_jurnal', $no_jurnal);
        $this->db->set('tanggal_jurnal  ', date("Y-m-d",strtotime($tanggal)));
        $this->db->set('source', "AP");
        $this->db->set('vendor', $vendor);
        $this->db->set('grn_no', $no_jurnal);
        $this->db->set('keterangan', strtoupper("pembayaran expense payment"));
        $this->db->set('created_by',config_item('auth_person_name'));
        $this->db->set('created_at',date('Y-m-d'));
        $this->db->insert('tb_jurnal');
        $id_jurnal = $this->db->insert_id();

        $akun_kredit = getAccountByCode($account);
        $this->db->set('id_jurnal', $id_jurnal);
        $this->db->set('jenis_transaksi', $akun_kredit->group);
        $this->db->set('trs_debet', 0);
        $this->db->set('trs_kredit', $amount_idr);
        $this->db->set('trs_debet_usd', 0);
        $this->db->set('trs_kredit_usd', $amount_usd);
        $this->db->set('kode_rekening', $account);
        $this->db->set('currency', $currency);
        $this->db->insert('tb_jurnal_detail');

        $this->connection->set('coa_kredit', $account);
        $this->connection->set('no_cheque', $no_cheque);
        $this->connection->set('akun_kredit', $akun_kredit->group);
        $this->connection->set('no_konfirmasi', $no_konfirmasi);
        $this->connection->set('paid_base', $paid_base);
        $this->connection->set('vendor', $vendor);
        $this->connection->set('status', "PAID");
        $this->connection->set('paid_by', config_item('auth_person_name'));
        $this->connection->set('paid_at', date("Y-m-d",strtotime($tanggal)));
        $this->connection->where('id', $po_payment_id);
        $this->connection->update('tb_request_payments');


        foreach ($_SESSION['payment']['request'] as $i => $request) {
            foreach ($request['items'] as $j => $key) {
                if($key['request_id']!=NULL){
                    if($this->updateStatusExpense($key['request_id'])){
                        $this->connection->set('closing_date', $tanggal);
                        $this->connection->set('status', 'close');
                        // $this->connection->set('closing_notes', $notes);
                        $this->connection->set('closing_by', config_item('auth_person_name'));
                        $this->connection->set('account', $account);         
                        $this->connection->where('id', $key['request_id']);
                        $this->connection->update('tb_expense_purchase_requisitions');

                    }
                }
                

                if ($currency == 'IDR') {
                    $amount_idr = $key["amount_paid"];
                    $amount_usd = $key["amount_paid"] / $kurs;
                } else {
                    $amount_usd = $key["amount_paid"];
                    $amount_idr = $key["amount_paid"] * $kurs;
                }

                        
                $akun = getAccountMrpByCode($key['account_code']);

                $this->db->set('id_jurnal', $id_jurnal);
                $this->db->set('jenis_transaksi', strtoupper($akun->group));
                $this->db->set('trs_kredit', ($amount_idr<0)? ($amount_idr*-1):0);
                $this->db->set('trs_debet', ($amount_idr>0)? $amount_idr:0);

                $this->db->set('trs_kredit_usd', ($amount_usd<0)? ($amount_usd*-1):0);
                $this->db->set('trs_debet_usd', ($amount_usd>0)? $amount_usd:0);

                $this->db->set('kode_rekening', $akun->coa);
                $this->db->set('currency', $currency);
                $this->db->insert('tb_jurnal_detail');
            }

            if($request['request_id']!=NULL){
                $expense_request_id = $request['request_id'];

                $this->connection->select('tb_expense_purchase_requisitions.*');
                $this->connection->from('tb_expense_purchase_requisitions');
                $this->connection->where('tb_expense_purchase_requisitions.id', $expense_request_id);

                $query    = $this->connection->get();
                $expense_request  = $query->unbuffered_row('array');

                if($expense_request['reference_document']!=null){            
                    $reference_document = json_decode($expense_request['reference_document']);
                    

                    if($reference_document[0]=='SPD'){
                        //create SPPD
                        $spd_id = $reference_document[1];
                        $create_sppd = $this->create_sppd($spd_id);
                    }
                } 
                
                if($request['uang_muka']>0){

                    if ($currency == 'IDR') {
                        $uang_muka_idr = $request["uang_muka"];
                        $uang_muka_usd = $request["uang_muka"] / $kurs;
                    } else {
                        $uang_muka_usd = $request["uang_muka"];
                        $uang_muka_idr = $request["uang_muka"] * $kurs;
                    }

                    $akun = getAccountMrpByCode($request['advance_account_code']);

                    $this->db->set('id_jurnal', $id_jurnal);
                    $this->db->set('jenis_transaksi', strtoupper($akun->group));
                    $this->db->set('trs_kredit', $uang_muka_idr);
                    $this->db->set('trs_debet', 0);

                    $this->db->set('trs_kredit_usd', $uang_muka_usd);
                    $this->db->set('trs_debet_usd', 0);

                    $this->db->set('kode_rekening', $akun->coa);
                    $this->db->set('currency', $currency);
                    $this->db->insert('tb_jurnal_detail');

                    if($reference_document[0]=='SPPD'){
                        $advanceList = findAdvanceList($reference_document[1],'SPPD');
                        foreach ($advanceList as $advance) {
                            $this->db->set('status', 'CLOSED');
                            $this->db->where('id', $advance['id']);
                            $this->db->update('tb_advance_payments');
                        }
                    }
                }
            }

            
            
        }

        foreach ($_SESSION["payment"]["attachment"] as $file) {
            $this->connection->set('id_purchase', $po_payment_id);
            $this->connection->set('tipe', "payment");
            $this->connection->set('file', $file);
            $this->connection->set('type_att', "payment");
            $this->connection->insert('tb_attachment');
        }
        if ($this->db->trans_status() === FALSE || $this->connection->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        $this->connection->trans_commit();
        return TRUE;
    }

    public function countTotalExpense($id){
        $this->connection->select('sum(total)');
        $this->connection->from('tb_expense_purchase_requisition_details');
        $this->connection->group_by('tb_expense_purchase_requisition_details.expense_purchase_requisition_id');
        $this->connection->where('tb_expense_purchase_requisition_details.expense_purchase_requisition_id', $id);
        return $this->connection->get('')->row()->sum;
    }

    public function updateStatusExpense($id){
        //count total_request
        $this->connection->select('sum(total)');
        $this->connection->from('tb_expense_purchase_requisition_details');
        $this->connection->group_by('tb_expense_purchase_requisition_details.expense_purchase_requisition_id');
        $this->connection->where('tb_expense_purchase_requisition_details.expense_purchase_requisition_id', $id);
        $total_request = $this->connection->get('')->row()->sum;

        //count total_process_amount
        $this->connection->select('sum(process_amount)');
        $this->connection->from('tb_expense_purchase_requisition_details');
        $this->connection->group_by('tb_expense_purchase_requisition_details.expense_purchase_requisition_id');
        $this->connection->where('tb_expense_purchase_requisition_details.expense_purchase_requisition_id', $id);
        $total_process_amount = $this->connection->get('')->row()->sum;

        return ($total_request<=$total_process_amount)? true:false;
    }

    public function checkExpenseReferenceDocument($id){
        $this->connection->select('*');
        $this->connection->from('tb_expense_purchase_requisitions');
        $this->connection->where('tb_expense_purchase_requisitions.id', $id);

        $query    = $this->connection->get();
        $request  = $query->unbuffered_row('array');

        return ($request['reference_document']!=NULL)? TRUE:FALSE;
    }

    public function findPrlByPoeItemid($poe_item_id)
    {
        $prl_item_id = getPrlid($poe_item_id);

        $this->connection->select('tb_expense_purchase_requisition_details.expense_purchase_requisition_id');
        $this->connection->from('tb_expense_purchase_requisition_details');
        $this->connection->where('tb_expense_purchase_requisition_details.id', $prl_item_id);
        $query      = $this->connection->get();
        $poe_item   = $query->unbuffered_row('array');
        $id         = $poe_item['expense_purchase_requisition_id'];

        $this->connection->select('tb_expense_purchase_requisitions.*, tb_cost_centers.cost_center_name');
        $this->connection->from('tb_expense_purchase_requisitions');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_expense_purchase_requisitions.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->where('tb_expense_purchase_requisitions.id', $id);

        $query    = $this->connection->get();
        $request  = $query->unbuffered_row('array');

        $select = array(
            'tb_expense_purchase_requisition_details.*',
            'tb_accounts.account_name',
            'tb_accounts.account_code',
            'tb_expense_monthly_budgets.account_id',
            'tb_expense_monthly_budgets.ytd_budget',
            'tb_expense_monthly_budgets.ytd_used_budget',
        );

        $group_by = array(
            'tb_expense_purchase_requisition_details.id',
            'tb_accounts.account_name',
            'tb_accounts.account_code',
            'tb_expense_monthly_budgets.account_id',
            'tb_expense_monthly_budgets.ytd_budget',
            'tb_expense_monthly_budgets.ytd_used_budget',
        );

        $this->connection->select($select);
        $this->connection->from('tb_expense_purchase_requisition_details');
        $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
        $this->connection->join('tb_accounts', 'tb_accounts.id = tb_expense_monthly_budgets.account_id');
        $this->connection->where('tb_expense_purchase_requisition_details.expense_purchase_requisition_id', $id);
        $this->connection->group_by($group_by);

        $query = $this->connection->get();

        foreach ($query->result_array() as $key => $value){
            $request['items'][$key] = $value;
            $request['items'][$key]['balance_mtd_budget']       = $value['ytd_budget'] - $value['ytd_used_budget'];

            $this->column_select = array(
                'SUM(tb_expense_monthly_budgets.mtd_budget) as budget',
                'SUM(tb_expense_monthly_budgets.mtd_used_budget) as used_budget',
                'tb_expense_monthly_budgets.account_id',
                'tb_expense_monthly_budgets.annual_cost_center_id',
            );

            $this->column_groupby = array(                
                'tb_expense_monthly_budgets.account_id',
                'tb_expense_monthly_budgets.annual_cost_center_id',
            );

            $this->connection->select($this->column_select);
            $this->connection->from('tb_expense_monthly_budgets');
            $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $request['annual_cost_center_id']);
            $this->connection->where('tb_expense_monthly_budgets.account_id', $value['account_id']);
            $this->connection->group_by($this->column_groupby);

            $query = $this->connection->get();
            $row   = $query->unbuffered_row('array');

            $request['items'][$key]['maximum_price']        =  $value['total'] + $row['budget'] - $row['used_budget'];
            $request['items'][$key]['balance_ytd_budget']   = $row['budget'] - $row['used_budget'];            
            $request['items'][$key]['history']              = $this->getHistory($request['annual_cost_center_id'],$value['account_id'],$request['order_number']);
        }

        return $request;
    }

    public function findExpenseRequestByid($id)
    {
        $this->connection->select('tb_expense_purchase_requisitions.*, tb_cost_centers.cost_center_name');
        $this->connection->from('tb_expense_purchase_requisitions');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_expense_purchase_requisitions.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->where('tb_expense_purchase_requisitions.id', $id);

        $query    = $this->connection->get();
        $request  = $query->unbuffered_row('array');

        $select = array(
            'tb_expense_purchase_requisition_details.*',
            'tb_accounts.account_name',
            'tb_accounts.account_code',
            'tb_expense_monthly_budgets.account_id',
            'tb_expense_monthly_budgets.ytd_budget',
            'tb_expense_monthly_budgets.ytd_used_budget',
            'tb_expense_purchase_requisitions.id as request_id',
            'tb_expense_purchase_requisitions.pr_number',
            'tb_expense_purchase_requisitions.notes',
        );

        $group_by = array(
            'tb_expense_purchase_requisition_details.id',
            'tb_accounts.account_name',
            'tb_accounts.account_code',
            'tb_expense_monthly_budgets.account_id',
            'tb_expense_monthly_budgets.ytd_budget',
            'tb_expense_monthly_budgets.ytd_used_budget',
            'tb_expense_purchase_requisitions.id',
            'tb_expense_purchase_requisitions.pr_number',
            'tb_expense_purchase_requisitions.notes',
        );

        $this->connection->select($select);
        $this->connection->from('tb_expense_purchase_requisition_details');
        $this->connection->join('tb_expense_purchase_requisitions', 'tb_expense_purchase_requisitions.id = tb_expense_purchase_requisition_details.expense_purchase_requisition_id');
        $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
        $this->connection->join('tb_accounts', 'tb_accounts.id = tb_expense_monthly_budgets.account_id');
        $this->connection->where('tb_expense_purchase_requisition_details.expense_purchase_requisition_id', $id);
        $this->connection->group_by($group_by);

        $query = $this->connection->get();

        foreach ($query->result_array() as $key => $value){
            $request['items'][$key] = $value;
            $request['items'][$key]['balance_mtd_budget']       = $value['ytd_budget'] - $value['ytd_used_budget'];

            $this->column_select = array(
                'SUM(tb_expense_monthly_budgets.mtd_budget) as budget',
                'SUM(tb_expense_monthly_budgets.mtd_used_budget) as used_budget',
                'tb_expense_monthly_budgets.account_id',
                'tb_expense_monthly_budgets.annual_cost_center_id',
            );

            $this->column_groupby = array(                
                'tb_expense_monthly_budgets.account_id',
                'tb_expense_monthly_budgets.annual_cost_center_id',
            );

            $this->connection->select($this->column_select);
            $this->connection->from('tb_expense_monthly_budgets');
            $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $request['annual_cost_center_id']);
            $this->connection->where('tb_expense_monthly_budgets.account_id', $value['account_id']);
            $this->connection->group_by($this->column_groupby);

            $query = $this->connection->get();
            $row   = $query->unbuffered_row('array');

            $request['items'][$key]['maximum_price']        =  $value['total'] + $row['budget'] - $row['used_budget'];
            $request['items'][$key]['balance_ytd_budget']   = $row['budget'] - $row['used_budget'];
        }

        return $request;
    }

    function tgl_kurs($date)
    {
        // $CI =& get_instance();
        $kurs_dollar = 0;
        $tanggal = $date;

        while ($kurs_dollar == 0) {

            $this->db->select('kurs_dollar');
            $this->db->from('tb_master_kurs_dollar');
            $this->db->where('date', $tanggal);

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $row    = $query->unbuffered_row();
                $kurs_dollar   = $row->kurs_dollar;
            } else {
                $kurs_dollar = 0;
            }
            $tgl = strtotime('-1 day', strtotime($tanggal));
            $tanggal = date('Y-m-d', $tgl);
        }

        return $kurs_dollar;
    }

    public function send_mail($doc_id, $level,$base=null)
    {
        $this->connection->select(
            array(
                'tb_request_payments.document_number',
                'SUM(tb_request_payment_details.amount_paid) as total',
                'tb_request_payments.tanggal',
                'tb_request_payments.currency',
            )
        );
        $this->connection->from('tb_request_payments');
        $this->connection->join('tb_request_payment_details','tb_request_payments.id = tb_request_payment_details.request_payment_id');
        $this->connection->group_by(
            array(
                'tb_request_payments.document_number',
                'tb_request_payments.tanggal',
                'tb_request_payments.currency',
            )
        );
        if(is_array($doc_id)){
            $this->connection->where_in('tb_request_payments.id',$doc_id);
        }else{
            $this->connection->where('tb_request_payments.id',$doc_id);
        }
        $query = $this->connection->get();
        $item_message = '<tbody>';
        foreach ($query->result_array() as $key => $item) {
            $item_message .= "<tr>";
            $item_message .= "<td>" . print_date($item['tanggal']) . "</td>";
            $item_message .= "<td>" . $item['document_number'] . "</td>";
            $item_message .= "<td>" . $item['currency'] . "</td>";
            $item_message .= "<td>" . print_number($item['total'], 2) . "</td>";
            $item_message .= "</tr>";
        }
        $item_message .= '</tbody>';

        if($base!=null){
            $recipientList = $this->getNotifRecipient($level,$base);
        }else{
            $recipientList = $this->getNotifRecipient($level);
        }       
        $recipient = array();
        foreach ($recipientList as $key) {
          array_push($recipient, $key->email);
        }

        $from_email = "bifa.acd@gmail.com";
        $to_email = "aidanurul99@rocketmail.com";
        $ket_level = '';

        $levels_and_roles = config_item('levels_and_roles');
        $ket_level = $levels_and_roles[$level];

        //Load email library 
        $this->load->library('email');
        $this->email->set_newline("\r\n");
        $message = "<p>Dear " . $ket_level . "</p>";
        $message .= "<p>Payment Request utk Expense Berikut perlu Persetujuan Anda </p>";
        $message .= "<table class='table'>";
        $message .= "<thead>";
        $message .= "<tr>";
        $message .= "<th>Tanggal</th>";
        $message .= "<th>No Payment Request</th>";
        $message .= "<th>Currency</th>";
        $message .= "<th>Nominal</th>";
        $message .= "</tr>";
        $message .= "</thead>";
        $message .= $item_message;
        $message .= "</table>";
        $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
        $message .= "<p>[ <a href='".$this->config->item('url_mrp')."' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
        $message .= "<p>Thanks and regards</p>";
        $this->email->from($from_email, 'Material Resource Planning');
        $this->email->to($recipient);
        $this->email->subject('Permintaan Approval Payment Request Expense');
        $this->email->message($message);

        //Send mail 
        if ($this->email->send())
            return true;
        else
            return $this->email->print_debugger();
    }

    public function getNotifRecipient($level,$base=null)
    {
        $this->db->select('email');
        $this->db->from('tb_auth_users');
        $this->db->where('auth_level', $level);
        if($level==14){
            if($base=='JAKARTA'){
                $this->db->where('warehouse', $base);
            }else{
                $this->db->where('warehouse !=', 'JAKARTA');
            }           
        }
        return $this->db->get('')->result();
    }

    public function listAttachments($id)
    {
      
        $this->connection->where('id_purchase', $id);
        $this->connection->where('tipe', 'payment');
        $this->connection->where('type_att', 'payment');
        return $this->connection->get('tb_attachment')->result_array();
    }

    public function getListAttachments($id)
	{
        $this->connection->where('id_purchase', $id);
        $this->connection->where('tipe', 'payment');
        $this->connection->where('type_att', 'payment');
        return $this->connection->get('tb_attachment')->result();
	}

    function add_attachment_to_db($id, $url)
	{
		$this->connection->trans_begin();

		$this->connection->set('id_purchase', $id);
        $this->connection->set('tipe', "payment");
        $this->connection->set('file', $url);
        $this->connection->set('type_att', "payment");
        $this->connection->insert('tb_attachment');

		if ($this->connection->trans_status() === FALSE)
			return FALSE;

		$this->connection->trans_commit();
		return TRUE;
	}

    function delete_attachment_in_db($id_att)
	{
		$this->connection->trans_begin();

		$this->connection->where('id', $id_att);
		$this->connection->delete('tb_attachment');

		if ($this->connection->trans_status() === FALSE)
		return FALSE;

		$this->connection->trans_commit();
		return TRUE;
	}

    public function listRequests()
	{
		$this->connection->select(array(
            'sum(tb_expense_purchase_requisition_details.amount) as amount',
            'sum(tb_expense_purchase_requisition_details.total) as total',
            'sum(tb_expense_purchase_requisition_details.process_amount) as process_amount',
            'tb_expense_purchase_requisitions.pr_number',
            'tb_expense_purchase_requisitions.pr_date',
            'tb_expense_purchase_requisitions.required_date',
            'tb_expense_purchase_requisitions.status',
            'tb_expense_purchase_requisitions.created_by',
            'tb_expense_purchase_requisitions.notes',
            'tb_cost_centers.cost_center_name',
            'tb_expense_purchase_requisitions.id'
        ));

        $this->connection->from('tb_expense_purchase_requisitions');
        $this->connection->join('tb_expense_purchase_requisition_details', 'tb_expense_purchase_requisition_details.expense_purchase_requisition_id = tb_expense_purchase_requisitions.id');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_expense_purchase_requisitions.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        // $this->connection->where('tb_annual_cost_centers.year_number',$this->budget_year);
        $this->connection->where_in('tb_expense_purchase_requisitions.status',['approved','PAYMENT PURPOSED']);
        $this->connection->where('tb_expense_purchase_requisitions.with_po','f');
        $this->connection->group_by(
            array(
                'tb_expense_purchase_requisitions.pr_number',
                'tb_expense_purchase_requisitions.pr_date',
                'tb_expense_purchase_requisitions.required_date',
                'tb_expense_purchase_requisitions.status',
                'tb_expense_purchase_requisitions.created_by',
                'tb_expense_purchase_requisitions.notes',
                'tb_cost_centers.cost_center_name',
                'tb_expense_purchase_requisitions.id'
            )
        );
        $this->connection->order_by('tb_expense_purchase_requisitions.order_number', 'desc');

        $query = $this->connection->get();
        


        return $query->result_array();
    }
    
    public function infoRequest($id)
    {
        $this->connection->select(array(
            'sum(tb_expense_purchase_requisition_details.amount) as amount',
            'sum(tb_expense_purchase_requisition_details.total) as total',
            'sum(tb_expense_purchase_requisition_details.process_amount) as process_amount',
            'tb_expense_purchase_requisitions.pr_number',
            'tb_expense_purchase_requisitions.pr_date',
            'tb_expense_purchase_requisitions.required_date',
            'tb_expense_purchase_requisitions.status',
            'tb_expense_purchase_requisitions.created_by',
            'tb_expense_purchase_requisitions.notes',
            'tb_cost_centers.cost_center_name',
            'tb_expense_purchase_requisitions.id',
            'tb_expense_purchase_requisitions.advance_nominal',
            'tb_expense_purchase_requisitions.advance_account_code',
            // 'tb_expense_purchase_requisitions.reference_ipc'
        ));

        $this->connection->from('tb_expense_purchase_requisitions');
        $this->connection->join('tb_expense_purchase_requisition_details', 'tb_expense_purchase_requisition_details.expense_purchase_requisition_id = tb_expense_purchase_requisitions.id');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_expense_purchase_requisitions.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->group_by(
            array(
                'tb_expense_purchase_requisitions.pr_number',
                'tb_expense_purchase_requisitions.pr_date',
                'tb_expense_purchase_requisitions.required_date',
                'tb_expense_purchase_requisitions.status',
                'tb_expense_purchase_requisitions.created_by',
                'tb_expense_purchase_requisitions.notes',
                'tb_cost_centers.cost_center_name',
                'tb_expense_purchase_requisitions.id',
                'tb_expense_purchase_requisitions.advance_nominal',
                'tb_expense_purchase_requisitions.advance_account_code',
                // 'tb_expense_purchase_requisitions.reference_ipc'
            )
        );
        $this->connection->where('tb_expense_purchase_requisitions.id', $id);
        $query      = $this->connection->get(); 
        $request    = $query->unbuffered_row('array');

        $select = array(
            'tb_expense_purchase_requisition_details.*',
            'tb_accounts.account_name',
            'tb_accounts.account_code',
            'tb_expense_monthly_budgets.account_id',
        );

        $group_by = array(
            'tb_expense_purchase_requisition_details.id',
            'tb_accounts.account_name',
            'tb_accounts.account_code',
            'tb_expense_monthly_budgets.account_id',
        );

        $this->connection->select($select);
        $this->connection->from('tb_expense_purchase_requisition_details');
        $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
        $this->connection->join('tb_accounts', 'tb_accounts.id = tb_expense_monthly_budgets.account_id');
        $this->connection->where('tb_expense_purchase_requisition_details.expense_purchase_requisition_id', $id);
        $this->connection->group_by($group_by);

        $query = $this->connection->get();

        foreach ($query->result_array() as $key => $value){
            $request['items'][$key] = $value;
        }

        return $request;
    }

    public function cancel()
	{
		$this->connection->trans_begin();

		$id = $this->input->post('id');
        $notes = $this->input->post('cancel_notes');

        $this->connection->select('tb_request_payments.*');
        $this->connection->where('tb_request_payments.id', $id);
        $this->connection->from('tb_request_payments');
        $query    = $this->connection->get();
        $request  = $query->unbuffered_row('array');

		if($request['status']!='CANCELED'){
			$this->connection->set('status', 'CANCELED');
			$this->connection->set('canceled_by', config_item('auth_person_name'));
			$this->connection->set('canceled_at', date('Y-m-d'));
			$this->connection->set('rejected_notes', $notes);
			$this->connection->where('id', $id);
			$this->connection->update('tb_request_payments');
			$status = 'CANCELED';
		}		

		// $this->db->select('tb_purchase_order_items_payments.*');
        // $this->db->from('tb_purchase_order_items_payments');
        // $this->db->where('tb_purchase_order_items_payments.po_payment_id',$id);
		// $query    		= $this->db->get();

        $this->db->select('tb_request_payment_details.*');
        $this->connection->from('tb_request_payment_details');
        $this->connection->where('tb_request_payment_details.request_payment_id', $id);

        $query = $this->connection->get();
		
		foreach ($query->result_array() as $key => $item) {
			$request_id = $item['request_id'];
			$value = $item["amount_paid"]+$item["adj_value"];
			if($item['request_item_id']!==null){
                $this->connection->set('process_amount', '"process_amount" - ' . $value, false);
                $this->connection->where('id', $item['request_item_id']);
                $this->connection->update('tb_expense_purchase_requisition_details');

                if($this->updateStatusExpense($request_item['expense_purchase_requisition_id'])){
                    $this->connection->set('status', 'approved'); 
                    $this->connection->where('id', $request_id);
                    $this->connection->update('tb_expense_purchase_requisitions');
                }
			}
			
		}


		if ($this->connection->trans_status() === FALSE)
			return FALSE;

		$this->connection->trans_commit();
		return TRUE;
	}

    public function getStatuspayment($id)
    {
        $this->connection->select(array(
            'sum(tb_request_payment_details.amount_paid) as amount_paid',
            'tb_request_payment_details.uang_muka',
            'tb_request_payment_details.request_payment_id',
            'tb_request_payment_details.advance_account_code',
        ));

        $this->connection->from('tb_request_payment_details');
        $this->connection->where('tb_request_payment_details.request_payment_id',$id);
        $this->connection->group_by(
            array(
                'tb_request_payment_details.uang_muka',
                'tb_request_payment_details.request_payment_id',
                'tb_request_payment_details.advance_account_code',
            )
        );
        $query = $this->connection->get();
        $result = $query->unbuffered_row('array');

        if($result['amount_paid']==$result['uang_muka']){
            $status = 'PAID';
            $advance_account_code = $result['advance_account_code'];
        }else{
            $status = 'NOT PAID';
            $advance_account_code = $result['advance_account_code'];
        }


        return [
            'status' => $status,
            'advance_account_code' => $advance_account_code
        ];
    }

    function buat_pembayaran_langsung($request_payment_id,$account)
    {
        $this->connection->trans_begin();
        $this->db->trans_begin();
        // $item = $this->input->post('item');
        $request_payment = $this->findById($request_payment_id);
        $account        = $account;
        $vendor         = $request_payment['vendor'];
        $no_cheque      = $request_payment['no_cheque'];
        $tanggal        = date('Y-m-d');
        $no_jurnal      = $request_payment['document_number'];
        $currency       = $request_payment['currency'];
        $no_konfirmasi  = $request_payment['no_konfirmasi'];
        $paid_base      = $request_payment['paid_base'];
        $kurs           = $this->tgl_kurs(date("Y-m-d"));
        $tipe           = $request_payment['type'];
        $po_payment_id          = $request_payment_id;
        
        $amount         = $request_payment['total_paid'];
        if ($currency == 'IDR') {
            $amount_idr = $amount;
            $amount_usd = $amount / $kurs;
        } else {
            $amount_usd = $amount;
            $amount_idr = $amount * $kurs;
        }


        $this->db->set('no_jurnal', $no_jurnal);
        $this->db->set('tanggal_jurnal  ', date("Y-m-d",strtotime($tanggal)));
        $this->db->set('source', "EXPENSE-PAYMENT");
        $this->db->set('vendor', $vendor);
        $this->db->set('grn_no', $no_jurnal);
        $this->db->set('keterangan', strtoupper("pembayaran expense payment"));
        $this->db->set('created_by',config_item('auth_person_name'));
        $this->db->set('created_at',date('Y-m-d'));
        $this->db->insert('tb_jurnal');
        $id_jurnal = $this->db->insert_id();

        $akun_kredit = getAccountByCode($account);
        $this->db->set('id_jurnal', $id_jurnal);
        $this->db->set('jenis_transaksi', $akun_kredit->group);
        $this->db->set('trs_debet', 0);
        $this->db->set('trs_kredit', $amount_idr);
        $this->db->set('trs_debet_usd', 0);
        $this->db->set('trs_kredit_usd', $amount_usd);
        $this->db->set('kode_rekening', $account);
        $this->db->set('currency', $currency);
        $this->db->insert('tb_jurnal_detail');

        $this->connection->set('coa_kredit', $account);
        $this->connection->set('no_cheque', $no_cheque);
        $this->connection->set('akun_kredit', $akun_kredit->group);
        $this->connection->set('no_konfirmasi', $no_konfirmasi);
        $this->connection->set('paid_base', $paid_base);
        $this->connection->set('vendor', $vendor);
        $this->connection->set('status', "PAID");
        $this->connection->set('paid_by', config_item('auth_person_name'));
        $this->connection->set('paid_at', date("Y-m-d",strtotime($tanggal)));
        $this->connection->where('id', $po_payment_id);
        $this->connection->update('tb_request_payments');


        foreach ($request_payment['request'] as $i => $request) {
            foreach ($request['items'] as $j => $key) {
                if($key['request_id']!=NULL){
                    if($this->updateStatusExpense($key['request_id'])){
                        $this->connection->set('closing_date', $tanggal);
                        $this->connection->set('status', 'close');
                        // $this->connection->set('closing_notes', $notes);
                        $this->connection->set('closing_by', config_item('auth_person_name'));
                        $this->connection->set('account', $account);         
                        $this->connection->where('id', $key['request_id']);
                        $this->connection->update('tb_expense_purchase_requisitions');
                    }
                }
                

                if ($currency == 'IDR') {
                    $amount_idr = $key["amount_paid"];
                    $amount_usd = $key["amount_paid"] / $kurs;
                } else {
                    $amount_usd = $key["amount_paid"];
                    $amount_idr = $key["amount_paid"] * $kurs;
                }

                        
                $akun = getAccountMrpByCode($key['account_code']);

                $this->db->set('id_jurnal', $id_jurnal);
                $this->db->set('jenis_transaksi', strtoupper($akun->group));
                $this->db->set('trs_kredit', ($amount_idr<0)? ($amount_idr*-1):0);
                $this->db->set('trs_debet', ($amount_idr>0)? $amount_idr:0);

                $this->db->set('trs_kredit_usd', ($amount_usd<0)? ($amount_usd*-1):0);
                $this->db->set('trs_debet_usd', ($amount_usd>0)? $amount_usd:0);

                $this->db->set('kode_rekening', $akun->coa);
                $this->db->set('currency', $currency);
                $this->db->insert('tb_jurnal_detail');
            }

            
        }

        if ($this->db->trans_status() === FALSE || $this->connection->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        $this->connection->trans_commit();
        return TRUE;
    }

    function getAdvance($request_payment_id=NULL){

        if($request_payment_id!=NULL){
            $request_payment = $this->findById($request_payment_id);
            $payment_item   = $request_payment['request'];
        }else{
            $payment_item = $_SESSION['request_closing']['items'];
        }
        $advance_list = array();
        foreach ($payment_item as $item) {
            $expense_request_id = $item['request_id'];

            $this->connection->select('tb_expense_purchase_requisitions.*');
            $this->connection->from('tb_expense_purchase_requisitions');
            $this->connection->where('tb_expense_purchase_requisitions.id', $expense_request_id);

            $query    = $this->connection->get();
            $expense_request  = $query->unbuffered_row('array');

            if($expense_request['reference_document']!=null){            
                $reference_document = json_decode($expense_request['reference_document']);

                if($reference_document[0]=='SPPD'){
                    $ref_doc = findAdvanceList($reference_document[1],$reference_document[0]);

                    foreach ($ref_doc as $item) {
                        array_push($advance_list,$item);
                    }
                }
                // array_push($advance_list,$reference_document);
            }  
            // array_push($advance_list,$expense_request);
        }

        return $advance_list;
    }

    public function create_sppd($spd_id)
    {
        $this->db->trans_begin();
        $this->connection->trans_begin();

        $spd = $this->findSpdById($spd_id);

        // CREATE NEW DOCUMENT
        $document_number  = sppd_last_number() . sppd_format_number();
        $date             = date('Y-m-d');
        $cost_center_code           = $spd['cost_center_code'];
        $cost_center_name           = $spd['cost_center_name'];
        $annual_cost_center_id      = $spd['annual_cost_center_id'];
        $warehouse                  = $spd['warehouse'];
        $notes                      = NULL;
        $person_in_charge           = $spd['user_id'];
        $selected_person            = getEmployeeByEmployeeNumber($person_in_charge);
        $person_name                = $selected_person['name'];
        $department_id              = $spd['department_id'];
        $head_dept                      = $spd['head_dept'];
        $advance_spd                    = 0;
        $spd                            = $this->findSpdById($spd_id);
        $level                          = getLevelByPosition($occupation);

        $this->db->set('annual_cost_center_id', $annual_cost_center_id);
        $this->db->set('warehouse', $warehouse);
        $this->db->set('document_number', $document_number);
        $this->db->set('spd_id', $spd_id);
        $this->db->set('spd_number', $spd['document_number']);
        $this->db->set('date', $date);
        $this->db->set('head_dept', $head_dept);
        $this->db->set('advance_spd', $advance_spd);
        $this->db->set('notes', $notes);
        $this->db->set('request_by', '[auto]');
        $this->db->set('created_by', '[auto by expense payment]');
        $this->db->set('updated_by', '[auto]');
        $this->db->insert('tb_sppd');
        $document_id = $this->db->insert_id();


        //update status SPD
        $this->db->set('status','CLOSED');
        $this->db->set('payment_status','PAID');
        $this->db->where('id', $spd_id);
        $this->db->update('tb_business_trip_purposes');
        //end

        //sementara signer di hide

        // $this->db->set('document_type','SPPD');
        // $this->db->set('document_number',$document_number);
        // $this->db->set('document_id', $document_id);
        // $this->db->set('action','requested by');
        // $this->db->set('date', $date);
        // $this->db->set('username', '[auto]');
        // $this->db->set('person_name', '[auto]');
        // $this->db->set('roles', 'Employee');
        // $this->db->set('notes', null);
        // $this->db->set('sign', '[auto]');
        // $this->db->set('created_at', date('Y-m-d H:i:s'));
        // $this->db->insert('tb_signers');

        // $this->db->set('document_type','SPPD');
        // $this->db->set('document_number',$document_number);
        // $this->db->set('document_id', $document_id);
        // $this->db->set('action','known by');
        // $this->db->set('date', date('Y-m-d'));
        // $this->db->set('username', '[auto]');
        // $this->db->set('person_name', '[auto]');
        // $this->db->set('roles', 'Head Dept/Spv');
        // $this->db->set('notes', "approval automatic");
        // $this->db->set('sign', '[auto]');
        // $this->db->set('created_at', date('Y-m-d H:i:s'));
        // $this->db->insert('tb_signers');

        // $this->db->set('document_type','SPPD');
        // $this->db->set('document_number',$document_number);
        // $this->db->set('document_id', $document_id);
        // $this->db->set('action','approved by');
        // $this->db->set('date', date('Y-m-d'));
        // $this->db->set('username', '[auto]');
        // $this->db->set('person_name', '[auto]');
        // $this->db->set('roles', 'HR MANAGER');
        // $this->db->set('notes', "approval automatic");
        // $this->db->set('sign', '[auto]');
        // $this->db->set('created_at', date('Y-m-d H:i:s'));
        // $this->db->insert('tb_signers');

        $total_relisasi = 0;

        foreach ($spd['items'] as $item) {
            $total_relisasi = $item['total'];
            $this->db->set('real_qty', $item['qty']);
            $this->db->set('real_amount', $item['amount']);
            $this->db->set('real_total', $item['total']);
            $this->db->where('id',$item['id']);
            $this->db->update('tb_business_trip_purpose_items');
        }

        // if(!empty($_SESSION['sppd']['attachment'])){
        //     foreach ($_SESSION["sppd"]["attachment"] as $key) {
        //         $this->db->set('id_poe', $document_id);
        //         $this->db->set('id_po', $document_id);
        //         $this->db->set('file', $key);
        //         $this->db->set('tipe', 'SPPD');
        //         $this->db->set('tipe_att', 'other');
        //         $this->db->insert('tb_attachment_poe');
        //     }
        // }

        // if(!empty($_SESSION['sppd']['attachment_detail'])){
        //     foreach ($_SESSION["sppd"]["attachment_detail"] as $key) {
        //         $att = explode("|,", $key);
        //         $this->db->set('id_poe', $att[1]);
        //         $this->db->set('id_po', $document_id);
        //         $this->db->set('file', $att[0]);
        //         $this->db->set('tipe', $att[2]);
        //         $this->db->set('tipe_att', 'other');
        //         $this->db->insert('tb_attachment_poe');
        //     }
        // }

        if ($this->db->trans_status() === FALSE || $this->connection->trans_status() === FALSE)
            return FALSE;

        //sppd dari expense kirim email ke?
            

        $this->db->trans_commit();
        $this->connection->trans_commit();
        return TRUE;
    }

    public function findSpdById($id)
    {
        $selected = array(
            'tb_business_trip_purposes.*',
            'tb_master_business_trip_destinations.business_trip_destination'
        );
        $this->db->select($selected);
        $this->db->where('tb_business_trip_purposes.id', $id);
        $this->db->join('tb_master_business_trip_destinations', 'tb_master_business_trip_destinations.id = tb_business_trip_purposes.business_trip_destination_id');
        $query      = $this->db->get('tb_business_trip_purposes');
        $row        = $query->unbuffered_row('array');

        $cost_center    = findCostCenter($row['annual_cost_center_id']);
        $head_dept      = findUserByUsername($row['head_dept']);
        $row['cost_center_code']    = $cost_center['cost_center_code'];
        $row['cost_center_name']    = $cost_center['cost_center_name'];
        $row['department_name']     = $cost_center['department_name']; 
        $row['department_id']       = $cost_center['department_id'];  
        $row['head_dept_name']       = $head_dept['person_name'];
        if($row['reference_document']!=null){            
            $reference_document = json_decode($row['reference_document']);
            $row['pr_number'] = $reference_document[2];
            $row['url_expense'] = $reference_document[3];
        }       

        $this->db->select('*');
        $this->db->from('tb_business_trip_purpose_items');
        $this->db->where('tb_business_trip_purpose_items.business_trip_purpose_id', $id);

        $query = $this->db->get();

        foreach ($query->result_array() as $key => $value) {
            $row['items'][$key] = $value;
        }

        $this->db->select('*');
        $this->db->from('tb_signers');
        $this->db->where('tb_signers.document_number', $row['document_number']);
        $query_signers = $this->db->get();
        foreach ($query_signers->result_array() as $key => $valuesigners) {
            $row['signers'][$valuesigners['action']]['sign'] = $valuesigners['sign'];
            $row['signers'][$valuesigners['action']]['person_name'] = $valuesigners['person_name'];
            $row['signers'][$valuesigners['action']]['date'] = $valuesigners['date'];
            $row['signers'][$valuesigners['action']]['action'] = $valuesigners['action'];
            $row['signers'][$valuesigners['action']]['roles'] = $valuesigners['roles'];
        }

        return $row;
    }
}
