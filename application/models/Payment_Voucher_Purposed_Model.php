<?php defined('BASEPATH') or exit('No direct script access allowed');

class Payment_Voucher_Purposed_Model extends MY_Model
{
    protected $connection;
    protected $categories;
    protected $budget_year;
    protected $budget_month;

    public function __construct()
    {
        parent::__construct();

        $this->connection   = $this->load->database('budgetcontrol', TRUE);
        // $this->categories   = $this->getCategories();
        $this->budget_year  = find_budget_setting('Active Year');
        $this->budget_month = find_budget_setting('Active Month');
    }

    public function getSelectedColumns()
    {
        $return = array(
            'tb_bank_registers.id'                                                 => NULL,
            'tb_bank_registers.document_number as no_transaksi'                    => 'Transaction Number',
            'tb_bank_registers.category'                                           => 'Type',
            'tb_bank_registers.tanggal'                                            => 'Date',
            'tb_bank_registers.no_cheque'                                          => 'No Cheque',
            'tb_bank_registers.vendor'                                             => 'Pay TO',
            'tb_bank_registers.currency'                                           => 'Currency',
            'tb_bank_registers.coa_kredit'                                         => 'Account',
            'tb_bank_registers.grandtotal as amount_paid'                          => 'Amount IDR',
            'tb_bank_registers.akun_kredit'                                        => 'Amount USD',
            'tb_bank_registers.status'                                             => 'Status',
            'tb_bank_registers.rejected_notes'                                     => 'Attachment',
            'tb_bank_registers.base'                                               => 'Base',
            'tb_bank_registers.notes'                                              => 'Notes',
        );
        if(is_granted($this->data['modules']['payment'], 'approval')){
            $return['tb_bank_registers.approval_notes']  = 'Input Notes';
        }else{
            $return['tb_bank_registers.approval_notes']  = 'Approval/Rejected Notes';
        }



        return $return;
    }

    public function getGroupedColumns()
    {
        $return = array(
            'tb_bank_registers.id',
            'tb_bank_registers.document_number',
            'tb_bank_registers.category',
            'tb_bank_registers.tanggal',
            'tb_bank_registers.no_cheque',
            'tb_bank_registers.vendor',
            'tb_bank_registers.currency',
            'tb_bank_registers.status',
            'tb_bank_registers.base',
            'tb_bank_registers.notes',
            'tb_bank_registers.approval_notes',
            'tb_bank_registers.rejected_notes'
        );

        return $return;
    }

    public function getSearchableColumns()
    {
        $return = array(
            // 'tb_purchase_order_items_payments.id',
            'tb_bank_registers.document_number',
            'tb_bank_registers.category',
            // 'tb_purchase_order_items_payments.tanggal',
            'tb_bank_registers.no_cheque',
            // 'tb_bank_registers.document_number',
            // 'tb_po_item.part_number',
            // 'tb_purchase_order_items_payments.deskripsi',
            'tb_bank_registers.currency',
            'tb_bank_registers.coa_kredit',
            'tb_bank_registers.akun_kredit',
            // 'tb_purchase_order_items_payments.amount_paid',
            'tb_bank_registers.created_by',
            'tb_bank_registers.vendor',
            'tb_bank_registers.status',
            'tb_bank_registers.base'
            // 'tb_purchase_order_items_payments.created_at',
        );

        return $return;
    }

    public function getOrderableColumns()
    {
        $return = array(
            NULL,
            'tb_bank_registers.document_number',
            'tb_bank_registers.category',
            'tb_bank_registers.tanggal',
            'tb_bank_registers.no_cheque',
            'tb_bank_registers.vendor',
            'tb_bank_registers.currency',          
            'tb_bank_registers.coa_kredit',
            'tb_bank_registers.base',
            'tb_bank_registers.notes',
        );

        return $return;
    }

    private function searchIndex()
    {
        if (!empty($_POST['columns'][1]['search']['value'])) {
            $search_received_date = $_POST['columns'][1]['search']['value'];
            $range_received_date  = explode(' ', $search_received_date);

            $this->db->where('tb_bank_registers.tanggal >= ', $range_received_date[0]);
            $this->db->where('tb_bank_registers.tanggal <= ', $range_received_date[1]);
        }

        if (!empty($_POST['columns'][2]['search']['value'])) {
            $vendor = $_POST['columns'][2]['search']['value'];

            $this->db->where('tb_bank_registers.vendor', $vendor);
        }

        if (!empty($_POST['columns'][3]['search']['value'])) {
            $currency = $_POST['columns'][3]['search']['value'];

            if ($currency != 'all') {
                $this->db->where('tb_bank_registers.currency', $currency);
            }
        }

        if (!empty($_POST['columns'][4]['search']['value'])) {
            $status = $_POST['columns'][4]['search']['value'];
            if($status!='all'){
                $this->db->like('tb_bank_registers.status', $status);
            }           
        } else {
            if(is_granted($this->data['modules']['payment'], 'approval')){
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
                $this->db->where_in('tb_bank_registers.status', $status);
            }else{
                if (config_item('auth_role') == 'TELLER') {
                    $status[] = 'APPROVED';
                    $this->db->where_in('tb_bank_registers.status', $status);
                }
            }       
            
        }

        if (!empty($_POST['columns'][5]['search']['value'])) {
            $base = $_POST['columns'][5]['search']['value'];
            if($base!='ALL'){
                if($base!='JAKARTA'){
                    $this->db->where('tb_bank_registers.base !=','JAKARTA');
                }elseif($base=='JAKARTA'){
                    $this->db->where('tb_bank_registers.base','JAKARTA');
                }   
            }
                    
        } else {
            if(config_item('auth_role') == 'AP STAFF' || config_item('auth_role') == 'FINANCE MANAGER'){
                $base = config_item('auth_warehouse');
                if($base!='JAKARTA'){
                    $this->db->where('tb_bank_registers.base !=','JAKARTA');
                }elseif($base=='JAKARTA'){
                    $this->db->where('tb_bank_registers.base','JAKARTA');
                }   
            }
            
        }

        $i = 0;

        foreach ($this->getSearchableColumns() as $item) {
            if ($_POST['search']['value']) {
                $term = strtoupper($_POST['search']['value']);

                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like('UPPER(' . $item . ')', $term);
                } else {
                    $this->db->or_like('UPPER(' . $item . ')', $term);
                }

                if (count($this->getSearchableColumns()) - 1 == $i)
                    $this->db->group_end();
            }

            $i++;
        }
    }

    function getIndex($return = 'array')
    {
        $this->db->select(array_keys($this->getSelectedColumns()));
        $this->db->from('tb_bank_registers');
        $this->db->join('tb_bank_register_details', 'tb_bank_registers.id = tb_bank_register_details.bank_register_id');
        // $this->db->where('tb_bank_registers.source','BANK RE');
        $this->db->group_by($this->getGroupedColumns());

        $this->searchIndex();

        $column_order = $this->getOrderableColumns();

        if (isset($_POST['order'])) {
            foreach ($_POST['order'] as $key => $order) {
                $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
            }
        } else {
            $this->db->order_by('id', 'desc');
        }

        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);

        $query = $this->db->get();

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
        $this->db->select(array_keys($this->getSelectedColumns()));
        $this->db->from('tb_bank_registers');
        $this->db->join('tb_bank_register_details', 'tb_bank_registers.id = tb_bank_register_details.bank_register_id');
        // $this->db->where('tb_bank_registers.source','PAYMENT');
        $this->db->group_by($this->getGroupedColumns());

        $this->searchIndex();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndex()
    {
        $this->db->select(array_keys($this->getSelectedColumns()));
        $this->db->from('tb_bank_registers');
        $this->db->join('tb_bank_register_details', 'tb_bank_registers.id = tb_bank_register_details.bank_register_id');
        // $this->db->where('tb_bank_registers.source','PAYMENT');
        $this->db->group_by($this->getGroupedColumns());

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function findById($id)
    {
        $this->db->select('tb_bank_registers.*');
        $this->db->where('tb_bank_registers.id', $id);
        $this->db->from('tb_bank_registers');
        $query    = $this->db->get();
        $request  = $query->unbuffered_row('array');

        $select = array(
            'tb_bank_register_details.*'
        );

        $this->db->select($select);
        $this->db->from('tb_bank_register_details');
        $this->db->where('tb_bank_register_details.bank_register_id', $id);
        $query = $this->db->get();

        foreach ($query->result_array() as $i => $item){
            $request['items'][$i] = $item;
        }

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

        return $request;
    }

    public function getHistory($annual_cost_center_id,$account_id,$order_number)
    {
        $select = array(
          'tb_capex_purchase_requisitions.pr_number',
          'tb_capex_purchase_requisitions.pr_date',
          'tb_capex_purchase_requisitions.created_by',
          'tb_capex_purchase_requisition_details.amount',
          'tb_capex_purchase_requisition_details.total',
        );
        $this->connection->select($select);
        $this->connection->from('tb_capex_purchase_requisition_details');
        $this->connection->join('tb_capex_purchase_requisitions', 'tb_capex_purchase_requisitions.id = tb_capex_purchase_requisition_details.capex_purchase_requisition_id');
        $this->connection->join('tb_capex_monthly_budgets', 'tb_capex_monthly_budgets.id = tb_capex_purchase_requisition_details.capex_monthly_budget_id');
        $this->connection->where('tb_capex_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
        $this->connection->where('tb_capex_monthly_budgets.account_id', $account_id);
        $this->connection->where('tb_capex_purchase_requisitions.order_number <',$order_number);
        $query  = $this->connection->get();

        return $query->result_array();
    }

    public function approve($request_payment_id)
    {
        $this->db->trans_begin();

        $send_to_vp_finance = array();

        foreach ($request_payment_id as $key) {
            $id = $key;
            $this->db->select('tb_bank_registers.*');
            $this->db->from('tb_bank_registers');
            $this->db->where('tb_bank_registers.id',$id);
            $query          = $this->db->get();
            $request_payment     = $query->unbuffered_row('array');
            $currency       = $request_payment['currency'];
            $level          = 0;
            $status         = '';

            if (config_item('auth_role')=='FINANCE MANAGER' && $request_payment['status'] == 'WAITING REVIEW BY FIN MNG') {
                if($request_payment['base']=='JAKARTA'){
                    $this->db->set('status', 'APPROVED');
                    $status = 'APPROVED';
                    $level = 0;
                }else{
                    $this->db->set('status', 'APPROVED');
                    $status = 'APPROVED';
                    $level = 0;
                }           
                $this->db->set('review_by', config_item('auth_person_name'));
                $this->db->set('review_at', date('Y-m-d'));
                $this->db->where('id', $id);
                $this->db->update('tb_bank_registers');
            }

            if (config_item('auth_role')=='VP FINANCE' && $request_payment['status'] == 'WAITING REVIEW BY VP FINANCE') {
                $this->db->set('status', 'APPROVED');
                $status = 'APPROVED';
                $level = 0;
                $this->db->set('approved_by', config_item('auth_person_name'));
                $this->db->set('approved_at', date('Y-m-d'));
                $this->db->where('id', $id);
                $this->db->update('tb_bank_registers');
            }
        }

        

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        if(!empty($send_to_vp_finance)){
            $this->send_mail($send_to_vp_finance,3);
        }

        $this->db->trans_commit();        
        
        return TRUE;
    }

    public function reject($request_payment_id,$notes)
    {
        $this->db->trans_begin();

        $send_to_vp_finance = array();
        $x = 0;
        $return = 0;
        $rejected_note = '';

        foreach ($request_payment_id as $key) {
            $id = $key;
            $this->db->select('tb_bank_registers.*');
            $this->db->from('tb_bank_registers');
            $this->db->where('tb_bank_registers.id',$id);
            $query          = $this->db->get();
            $request_payment     = $query->unbuffered_row('array');
            $currency       = $request_payment['currency'];
            $level          = 0;
            $status         = '';

            if($request_payment['status']!='REJECTED' || $request_payment['status']!='REVISI' || $request_payment['status']!='PAID'){
                $this->db->set('status', 'REJECTED');
                $this->db->set('rejected_by', config_item('auth_person_name'));
                $this->db->set('rejected_at', date('Y-m-d'));
                $this->db->set('rejected_notes',$notes[$x]);
                $this->db->where('id', $id);
                $this->db->update('tb_bank_registers');
            }
            $x++;
        }

        

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        // if(!empty($send_to_vp_finance)){
        //     $this->send_mail($send_to_vp_finance,3);
        // }

        $this->db->trans_commit();        
        
        return TRUE;
    }

    public function searchBudget($annual_cost_center_id)
    {
        $query = "";
        $this->column_select = array(
            'SUM(tb_capex_monthly_budgets.mtd_budget) as budget',
            'SUM(tb_capex_monthly_budgets.mtd_used_budget) as used_budget',
            'tb_capex_monthly_budgets.account_id',
            'tb_accounts.account_name',
            'tb_accounts.account_code',
            'tb_capex_monthly_budgets.annual_cost_center_id',
        );

        $this->column_groupby = array(
            'tb_capex_monthly_budgets.account_id',
            'tb_accounts.account_name',
            'tb_accounts.account_code',
            'tb_capex_monthly_budgets.annual_cost_center_id',
        );

        $this->connection->select($this->column_select);
        $this->connection->from('tb_capex_monthly_budgets');
        $this->connection->join('tb_accounts', 'tb_accounts.id = tb_capex_monthly_budgets.account_id');
        $this->connection->where('tb_capex_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
        $this->connection->group_by($this->column_groupby);
        $this->connection->order_by('tb_accounts.account_code ASC, tb_accounts.account_name ASC');
          $query  = $this->connection->get();

        $result = $query->result_array();
        foreach ($result as $key => $value) {
            $result[$key]['maximum_price'] = $value['budget'] - $value['used_budget'];
            $select = array(
                'tb_capex_monthly_budgets.ytd_budget',
                'tb_capex_monthly_budgets.ytd_used_budget',
                'tb_capex_monthly_budgets.id',
            );

            $this->connection->select($select);
            $this->connection->from('tb_capex_monthly_budgets');
            $this->connection->where('tb_capex_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
            $this->connection->where('tb_capex_monthly_budgets.account_id', $value['account_id']);
            $this->connection->where('tb_capex_monthly_budgets.month_number', $this->budget_month);
            $query_row = $this->connection->get();
            $row   = $query_row->unbuffered_row('array');
            $result[$key]['mtd_budget']                 = $row['ytd_budget'] - $row['ytd_used_budget'];
            $result[$key]['capex_monthly_budget_id']  = $row['id'];
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
        $query = $this->connection->get('tb_capex_purchase_requisitions');

        if ($query->num_rows() > 0)
          return true;

        return false;
    }

    public function save()
    {
        // $this->connection->trans_begin();
        $this->db->trans_begin();

        $id                     = (isset($_SESSION['request_closing']['id'])) ? $_SESSION['request_closing']['id'] : NULL;
        $closing_date           = $_SESSION['request_closing']['date'];
        $purposed_date          = $_SESSION['request_closing']['purposed_date'];
        $vendor                 = $_SESSION['request_closing']['vendor'];
        $closing_by             = config_item('auth_person_name');
        $notes                  = (empty($_SESSION['request_closing']['closing_notes'])) ? NULL : $_SESSION['request_closing']['closing_notes'];
        $account                = $_SESSION['request_closing']['coa_kredit'];
        $type                   = $_SESSION['request_closing']['type'];
        $document_number        = $_SESSION['request_closing']['document_number'].payment_request_format_number($_SESSION['request_closing']['type'],$_SESSION['request_closing']['category']);

        $base                   = config_item('auth_warehouse');
        $akun_kredit            = getAccountByCode($account);
        $total_purposed_payment = array();
        $currency               = $_SESSION['request_closing']['currency'];
        $kurs                   = $this->tgl_kurs(date("Y-m-d"));
        $category               = $_SESSION['request_closing']['category'];

        $subtotal                = $this->input->post('subtotal');
        $tax                     = $this->input->post('total_tax');
        $discount                = 0;
        $grandtotal              = $this->input->post('grandtotal');

        

        if ($id === NULL) {
            $this->db->set('document_number', $document_number);
            $this->db->set('category', strtoupper($category));
            $this->db->set('source', 'BANK REGISTER');
            $this->db->set('vendor', strtoupper($vendor));
            $this->db->set('tanggal', $closing_date);
            $this->db->set('purposed_date', $purposed_date);
            $this->db->set('currency', $currency);
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('created_at', date('Y-m-d'));
            $this->db->set('base', $base);
            $this->db->set('notes', $notes);
            $this->db->set('coa_kredit', $account);
            $this->db->set('akun_kredit', $akun_kredit->group); 
            $this->db->set('total', $subtotal);
            $this->db->set('tax', $tax);
            $this->db->set('discount', $discount);
            $this->db->set('grandtotal', $grandtotal);

            $this->db->set('status','PAID');
            $this->db->set('cash_request','CLOSED');
            $this->db->set('paid_by', config_item('auth_person_name'));
            $this->db->set('paid_at', date("Y-m-d",strtotime($date)));     
            // if($type=='CASH'){
            //     $this->db->set('status','APPROVED');
            //     $this->db->set('cash_request','OPEN');
            //     $this->db->set('paid_by', config_item('auth_person_name'));
            //     $this->db->set('paid_at', date("Y-m-d",strtotime($date)));
            // }else{
            //     // if($base=='JAKARTA'){
            //     $this->db->set('status','WAITING REVIEW BY FIN MNG');
            //     // }
            // }
            $this->db->set('type',$type);
            $this->db->insert('tb_bank_registers');
            $bank_register_id = $this->db->insert_id();

            $this->db->set('document_number', $document_number);
            $this->db->set('source', 'BANK REGISTER');            
            $this->db->insert('tb_po_payment_no_transaksi');

            // if($type=='CASH2'){
                $this->db->set('no_jurnal', $document_number);
                $this->db->set('tanggal_jurnal  ', date("Y-m-d",strtotime($closing_date)));
                $this->db->set('source', ($category=='SPEND')? "disbursements":"receipts");
                $this->db->set('vendor', $vendor);
                $this->db->set('grn_no', $document_number);
                $this->db->set('keterangan', $notes);
                $this->db->set('created_by',config_item('auth_person_name'));
                $this->db->set('created_at',date('Y-m-d'));
                $this->db->insert('tb_jurnal');
                $id_jurnal = $this->db->insert_id();
            // }

            if ($currency == 'IDR') {
                $amount_idr = $grandtotal;
                $amount_usd = $grandtotal / $kurs;
            } else {
                $amount_usd = $grandtotal;
                $amount_idr = $grandtotal * $kurs;
            }

            $selectedAccountBank = getAccountByCode($account);

            $this->db->set('id_jurnal', $id_jurnal);
            $this->db->set('jenis_transaksi', strtoupper($selectedAccountBank->group));
            if($category=='SPEND'){
                $this->db->set('trs_debet', 0);
                $this->db->set('trs_kredit', $amount_idr);
                $this->db->set('trs_debet_usd', 0);
                $this->db->set('trs_kredit_usd', $amount_usd);
            }else{
                $this->db->set('trs_debet', $amount_idr);
                $this->db->set('trs_kredit', 0);
                $this->db->set('trs_debet_usd', $amount_usd);
                $this->db->set('trs_kredit_usd', 0);
            }          

            $this->db->set('kode_rekening', $selectedAccountBank->coa);
            $this->db->set('currency', $currency);
            $this->db->insert('tb_jurnal_detail');
        }else{
            //utk edit
            $request_payment_id = $document_id;
        }

        // $request_items_id           = $this->input->post('request_item_id');
        $amount                     = $this->input->post('value');
        $description                = $this->input->post('description');
        $account_code               = $this->input->post('account_code');
        $pajak_id                   = $this->input->post('pajak_id');
        $tax_percentage             = $this->input->post('tax_percentage');
        $tax                        = $this->input->post('tax');

        foreach ($account_code as $key=>$account_code_item){
            if($account_code_item!='' && $account_code_item!=0){
                $account_code_item = $account_code_item;
                $selectedAccount = getAccountByCode($account_code_item);

                $total_purposed_payment[] = $amount[$key];
                $this->db->set('bank_register_id', $bank_register_id);
                $this->db->set('deskripsi', $description[$key]); 
                $this->db->set('amount', $amount[$key]);
                $this->db->set('account_name', $selectedAccount->group);
                $this->db->set('account_code', $account_code_item);
                if($pajak_id[$key]!='non_pajak'){
                    $this->db->set('pajak_id', $pajak_id[$key]);
                    $this->db->set('tax', $tax[$key]);
                }                
                $this->db->set('discount', 0);
                $this->db->set('total', $amount[$key]);
                $this->db->set('created_by', config_item('auth_person_name'));
                $this->db->set('created_at', date('Y-m-d H:i:s'));
                $this->db->insert('tb_bank_register_details');

                if ($currency == 'IDR') {
                    $amount_idr = $amount[$key];
                    $amount_usd = $amount[$key] / $kurs;
                } else {
                    $amount_usd = $amount[$key];
                    $amount_idr = $amount[$key] * $kurs;
                }

                $this->db->set('id_jurnal', $id_jurnal);
                $this->db->set('jenis_transaksi', strtoupper($selectedAccount->group));

                if($category=='SPEND'){
                    $this->db->set('trs_kredit', ($amount_idr<0)? ($amount_idr*-1):0);
                    $this->db->set('trs_debet', ($amount_idr>0)? $amount_idr:0);
                    $this->db->set('trs_kredit_usd', ($amount_usd<0)? ($amount_usd*-1):0);
                    $this->db->set('trs_debet_usd', ($amount_usd>0)? $amount_usd:0);
                }else{
                    $this->db->set('trs_debet', ($amount_idr<0)? ($amount_idr*-1):0);
                    $this->db->set('trs_kredit', ($amount_idr>0)? $amount_idr:0);
                    $this->db->set('trs_debet_usd', ($amount_usd<0)? ($amount_usd*-1):0);
                    $this->db->set('trs_kredit_usd', ($amount_usd>0)? $amount_usd:0);
                } 
                

                $this->db->set('kode_rekening', $selectedAccount->coa);
                $this->db->set('currency', $currency);
                $this->db->insert('tb_jurnal_detail');
            }

            
        }
        

        if ($this->db->trans_status() === FALSE)
          return FALSE;

        $this->db->trans_commit();
        // if($type!='CASH'){
        //     $this->send_mail($request_payment_id,14,$base);
        // }

        return TRUE;
    }

    function getInfoRequestItemById($id)
    {
        $select = array(
            'tb_capex_purchase_requisitions.pr_number',
            'tb_capex_purchase_requisitions.notes',
            'tb_capex_purchase_requisition_details.*',
            'tb_products.product_name',
            'tb_products.product_code',
            'tb_product_groups.group_name as group',
            'tb_capex_monthly_budgets.product_id',
        );

        $this->connection->select($select);
        $this->connection->from('tb_capex_purchase_requisition_details');
        $this->connection->join('tb_capex_purchase_requisitions', 'tb_capex_purchase_requisitions.id = tb_capex_purchase_requisition_details.capex_purchase_requisition_id');
        $this->connection->join('tb_capex_monthly_budgets', 'tb_capex_monthly_budgets.id = tb_capex_purchase_requisition_details.capex_monthly_budget_id');
        $this->connection->join('tb_products', 'tb_products.id = tb_capex_monthly_budgets.product_id');
        $this->connection->join('tb_product_groups', 'tb_product_groups.id = tb_products.product_group_id');
        $this->connection->where('tb_capex_purchase_requisition_details.id', $id);

        $query      = $this->connection->get(); 
        $request    = $query->unbuffered_row('array');

        return $request;
    }

    public function countTotalExpense(){
        $this->connection->select('sum(total)');
        $this->connection->from('tb_capex_purchase_requisition_details');
        $this->connection->group_by('tb_capex_purchase_requisition_details.capex_purchase_requisition_id');
        $this->connection->where('tb_capex_purchase_requisition_details.capex_purchase_requisition_id', $prl_item_id);
        return $this->connection->get('')->row()->sum;
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
        $this->connection->update('tb_bank_registers');


        foreach ($_SESSION['payment']['request'] as $i => $request) {
            foreach ($request['items'] as $j => $key) {
                if($key['request_id']!=NULL){
                    if($this->updateStatusCapex($key['request_id'])){
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

                        
                $akun = getAccountBudgetControlByCode($key['account_code']);

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

    public function updateStatusCapex($id){
        //count total_request
        $this->connection->select('sum(total)');
        $this->connection->from('tb_capex_purchase_requisition_details');
        $this->connection->group_by('tb_capex_purchase_requisition_details.capex_purchase_requisition_id');
        $this->connection->where('tb_capex_purchase_requisition_details.capex_purchase_requisition_id', $id);
        $total_request = $this->connection->get('')->row()->sum;

        //count total_process_amount
        $this->connection->select('sum(process_amount)');
        $this->connection->from('tb_capex_purchase_requisition_details');
        $this->connection->group_by('tb_capex_purchase_requisition_details.capex_purchase_requisition_id');
        $this->connection->where('tb_capex_purchase_requisition_details.capex_purchase_requisition_id', $id);
        $total_process_amount = $this->connection->get('')->row()->sum;

        return ($total_request<=$total_process_amount)? true:false;
    }

    public function findPrlByPoeItemid($poe_item_id)
    {
        $prl_item_id = getPrlid($poe_item_id);

        $this->connection->select('tb_capex_purchase_requisition_details.capex_purchase_requisition_id');
        $this->connection->from('tb_capex_purchase_requisition_details');
        $this->connection->where('tb_capex_purchase_requisition_details.id', $prl_item_id);
        $query      = $this->connection->get();
        $poe_item   = $query->unbuffered_row('array');
        $id         = $poe_item['capex_purchase_requisition_id'];

        $this->connection->select('tb_capex_purchase_requisitions.*, tb_cost_centers.cost_center_name');
        $this->connection->from('tb_capex_purchase_requisitions');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_capex_purchase_requisitions.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->where('tb_capex_purchase_requisitions.id', $id);

        $query    = $this->connection->get();
        $request  = $query->unbuffered_row('array');

        $select = array(
            'tb_capex_purchase_requisition_details.*',
            'tb_accounts.account_name',
            'tb_accounts.account_code',
            'tb_capex_monthly_budgets.account_id',
            'tb_capex_monthly_budgets.ytd_budget',
            'tb_capex_monthly_budgets.ytd_used_budget',
        );

        $group_by = array(
            'tb_capex_purchase_requisition_details.id',
            'tb_accounts.account_name',
            'tb_accounts.account_code',
            'tb_capex_monthly_budgets.account_id',
            'tb_capex_monthly_budgets.ytd_budget',
            'tb_capex_monthly_budgets.ytd_used_budget',
        );

        $this->connection->select($select);
        $this->connection->from('tb_capex_purchase_requisition_details');
        $this->connection->join('tb_capex_monthly_budgets', 'tb_capex_monthly_budgets.id = tb_capex_purchase_requisition_details.capex_monthly_budget_id');
        $this->connection->join('tb_accounts', 'tb_accounts.id = tb_capex_monthly_budgets.account_id');
        $this->connection->where('tb_capex_purchase_requisition_details.capex_purchase_requisition_id', $id);
        $this->connection->group_by($group_by);

        $query = $this->connection->get();

        foreach ($query->result_array() as $key => $value){
            $request['items'][$key] = $value;
            $request['items'][$key]['balance_mtd_budget']       = $value['ytd_budget'] - $value['ytd_used_budget'];

            $this->column_select = array(
                'SUM(tb_capex_monthly_budgets.mtd_budget) as budget',
                'SUM(tb_capex_monthly_budgets.mtd_used_budget) as used_budget',
                'tb_capex_monthly_budgets.account_id',
                'tb_capex_monthly_budgets.annual_cost_center_id',
            );

            $this->column_groupby = array(                
                'tb_capex_monthly_budgets.account_id',
                'tb_capex_monthly_budgets.annual_cost_center_id',
            );

            $this->connection->select($this->column_select);
            $this->connection->from('tb_capex_monthly_budgets');
            $this->connection->where('tb_capex_monthly_budgets.annual_cost_center_id', $request['annual_cost_center_id']);
            $this->connection->where('tb_capex_monthly_budgets.account_id', $value['account_id']);
            $this->connection->group_by($this->column_groupby);

            $query = $this->connection->get();
            $row   = $query->unbuffered_row('array');

            $request['items'][$key]['maximum_price']        =  $value['total'] + $row['budget'] - $row['used_budget'];
            $request['items'][$key]['balance_ytd_budget']   = $row['budget'] - $row['used_budget'];            
            $request['items'][$key]['history']              = $this->getHistory($request['annual_cost_center_id'],$value['account_id'],$request['order_number']);
        }

        return $request;
    }

    public function findRequestByid($id)
    {
        $this->connection->select('tb_capex_purchase_requisitions.*, tb_cost_centers.cost_center_name');
        $this->connection->from('tb_capex_purchase_requisitions');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_capex_purchase_requisitions.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->where('tb_capex_purchase_requisitions.id', $id);

        $query    = $this->connection->get();
        $request  = $query->unbuffered_row('array');

        $select = array(
            'tb_capex_purchase_requisition_details.*',
            'tb_products.product_name',
            'tb_products.product_code as part_number',
            'tb_product_groups.group_name as group',
            'tb_capex_monthly_budgets.product_id',
            'tb_capex_monthly_budgets.ytd_quantity',
            'tb_capex_monthly_budgets.ytd_budget',
            'tb_capex_monthly_budgets.ytd_used_quantity',
            'tb_capex_monthly_budgets.ytd_used_budget',
        );

        $group_by = array(
            'tb_capex_purchase_requisition_details.id',
            'tb_products.product_name',
            'tb_products.product_code',
            'tb_capex_monthly_budgets.product_id',
            'tb_product_groups.group_name',
            'tb_capex_monthly_budgets.ytd_quantity',
            'tb_capex_monthly_budgets.ytd_budget',
            'tb_capex_monthly_budgets.ytd_used_quantity',
            'tb_capex_monthly_budgets.ytd_used_budget',
        );

        $this->connection->select($select);
        $this->connection->from('tb_capex_purchase_requisition_details');
        $this->connection->join('tb_capex_monthly_budgets', 'tb_capex_monthly_budgets.id = tb_capex_purchase_requisition_details.capex_monthly_budget_id');
        $this->connection->join('tb_products', 'tb_products.id = tb_capex_monthly_budgets.product_id');
        $this->connection->join('tb_product_groups', 'tb_product_groups.id = tb_products.product_group_id');
        $this->connection->where('tb_capex_purchase_requisition_details.capex_purchase_requisition_id', $id);
        $this->connection->group_by($group_by);

        $query = $this->connection->get();

        foreach ($query->result_array() as $key => $value){
            $request['items'][$key] = $value;
            $request['items'][$key]['balance_mtd_quantity']     = $value['ytd_quantity'] - $value['ytd_used_quantity'];
            $request['items'][$key]['balance_mtd_budget']       = $value['ytd_budget'] - $value['ytd_used_budget'];
            $request['items'][$key]['mtd_quantity']     = $value['quantity'] + $value['ytd_quantity'] - $value['ytd_used_quantity'];
            $request['items'][$key]['mtd_budget']       = $value['total'] + $value['ytd_budget'] - $value['ytd_used_budget'];


            $this->column_select = array(
                'SUM(tb_capex_monthly_budgets.mtd_quantity) as quantity',
                'SUM(tb_capex_monthly_budgets.mtd_budget) as budget',
                'SUM(tb_capex_monthly_budgets.mtd_used_quantity) as used_quantity',
                'SUM(tb_capex_monthly_budgets.mtd_used_budget) as used_budget',
                'tb_capex_monthly_budgets.product_id',
                'tb_capex_monthly_budgets.annual_cost_center_id',
            );

            $this->column_groupby = array(                
                'tb_capex_monthly_budgets.product_id',
                'tb_capex_monthly_budgets.annual_cost_center_id',
            );

            $this->connection->select($this->column_select);
            $this->connection->from('tb_capex_monthly_budgets');
            $this->connection->where('tb_capex_monthly_budgets.annual_cost_center_id', $request['annual_cost_center_id']);
            $this->connection->where('tb_capex_monthly_budgets.product_id', $value['product_id']);
            $this->connection->group_by($this->column_groupby);

            $query = $this->connection->get();
            $row   = $query->unbuffered_row('array');

            $request['items'][$key]['maximum_quantity'] = $value['quantity'] + $row['quantity'] - $row['used_quantity'];
            $request['items'][$key]['maximum_price']    =  $value['total'] + $row['budget'] - $row['used_budget'];
            $request['items'][$key]['balance_ytd_quantity']     = $row['quantity'] - $row['used_quantity'];
            $request['items'][$key]['balance_ytd_budget']       = $row['budget'] - $row['used_budget'];  
        }

        return $request;
    }

    public function listRequests()
    {
        $this->connection->select(array(
            // 'sum(tb_capex_purchase_requisition_details.amount) as amount',
            'sum(tb_capex_purchase_requisition_details.total) as total',
            'sum(tb_capex_purchase_requisition_details.process_amount) as process_amount',
            'tb_capex_purchase_requisitions.pr_number',
            'tb_capex_purchase_requisitions.pr_date',
            'tb_capex_purchase_requisitions.required_date',
            'tb_capex_purchase_requisitions.status',
            'tb_capex_purchase_requisitions.created_by',
            'tb_capex_purchase_requisitions.notes',
            'tb_cost_centers.cost_center_name',
            'tb_capex_purchase_requisitions.id'
        ));

        $this->connection->from('tb_capex_purchase_requisitions');
        $this->connection->join('tb_capex_purchase_requisition_details', 'tb_capex_purchase_requisition_details.capex_purchase_requisition_id = tb_capex_purchase_requisitions.id');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_capex_purchase_requisitions.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->where('tb_annual_cost_centers.year_number',$this->budget_year);
        $this->connection->where('tb_capex_purchase_requisitions.status','approved');
        $this->connection->where('tb_capex_purchase_requisitions.with_po','f');
        $this->connection->group_by(
            array(
                'tb_capex_purchase_requisitions.pr_number',
                'tb_capex_purchase_requisitions.pr_date',
                'tb_capex_purchase_requisitions.required_date',
                'tb_capex_purchase_requisitions.status',
                'tb_capex_purchase_requisitions.created_by',
                'tb_capex_purchase_requisitions.notes',
                'tb_cost_centers.cost_center_name',
                'tb_capex_purchase_requisitions.id'
            )
        );
        $this->connection->order_by('tb_capex_purchase_requisitions.order_number', 'desc');

        $query = $this->connection->get();
        


        return $query->result_array();
    }
    
    public function infoRequest($id)
    {
        $this->connection->select(array(
            // 'sum(tb_capex_purchase_requisition_details.amount) as amount',
            'sum(tb_capex_purchase_requisition_details.total) as total',
            'sum(tb_capex_purchase_requisition_details.process_amount) as process_amount',
            'tb_capex_purchase_requisitions.pr_number',
            'tb_capex_purchase_requisitions.pr_date',
            'tb_capex_purchase_requisitions.required_date',
            'tb_capex_purchase_requisitions.status',
            'tb_capex_purchase_requisitions.created_by',
            'tb_capex_purchase_requisitions.notes',
            'tb_cost_centers.cost_center_name',
            'tb_capex_purchase_requisitions.id',
            // 'tb_capex_purchase_requisitions.reference_ipc'
        ));

        $this->connection->from('tb_capex_purchase_requisitions');
        $this->connection->join('tb_capex_purchase_requisition_details', 'tb_capex_purchase_requisition_details.capex_purchase_requisition_id = tb_capex_purchase_requisitions.id');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_capex_purchase_requisitions.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->group_by(
            array(
                'tb_capex_purchase_requisitions.pr_number',
                'tb_capex_purchase_requisitions.pr_date',
                'tb_capex_purchase_requisitions.required_date',
                'tb_capex_purchase_requisitions.status',
                'tb_capex_purchase_requisitions.created_by',
                'tb_capex_purchase_requisitions.notes',
                'tb_cost_centers.cost_center_name',
                'tb_capex_purchase_requisitions.id',
                // 'tb_capex_purchase_requisitions.reference_ipc'
            )
        );
        $this->connection->where('tb_capex_purchase_requisitions.id', $id);
        $query      = $this->connection->get(); 
        $request    = $query->unbuffered_row('array');

        $select = array(
            'tb_capex_purchase_requisition_details.*',
            'tb_products.product_name',
            'tb_products.product_code',
            'tb_product_groups.group_name as group',
            'tb_capex_monthly_budgets.product_id',
        );

        $group_by = array(
            'tb_capex_purchase_requisition_details.id',
            'tb_products.product_name',
            'tb_products.product_code',
            'tb_product_groups.group_name',
            'tb_capex_monthly_budgets.product_id',
        );

        $this->connection->select($select);
        $this->connection->from('tb_capex_purchase_requisition_details');
        $this->connection->join('tb_capex_monthly_budgets', 'tb_capex_monthly_budgets.id = tb_capex_purchase_requisition_details.capex_monthly_budget_id');
        $this->connection->join('tb_products', 'tb_products.id = tb_capex_monthly_budgets.product_id');
        $this->connection->join('tb_product_groups', 'tb_product_groups.id = tb_products.product_group_id');
        $this->connection->where('tb_capex_purchase_requisition_details.capex_purchase_requisition_id', $id);
        $this->connection->group_by($group_by);

        $query = $this->connection->get();

        foreach ($query->result_array() as $key => $value){
            $request['items'][$key] = $value;
        }

        return $request;
    }

    public function send_mail($doc_id, $level,$base=null)
    {
        $this->connection->select(
            array(
                'tb_bank_registers.document_number',
                'tb_bank_registers.grandtotal as total',
                'tb_bank_registers.tanggal',
                'tb_bank_registers.currency',
            )
        );
        $this->connection->from('tb_bank_registers');
        $this->connection->join('tb_bank_register_details','tb_bank_registers.id = tb_bank_register_details.request_payment_id');
        $this->connection->group_by(
            array(
                'tb_bank_registers.document_number',
                'tb_bank_registers.tanggal',
                'tb_bank_registers.currency',
            )
        );
        if(is_array($doc_id)){
            $this->connection->where_in('tb_bank_registers.id',$doc_id);
        }else{
            $this->connection->where('tb_bank_registers.id',$doc_id);
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
        $message .= "<p>[ <a href='http://119.2.51.138:7323/capex_closing_payemnt/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
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
}
