<?php
class ImportedTransactions extends CI_Controller {
	function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
		$this->load->helper('html');
		//$this->load->helper('ajax');
		$this->load->library('tank_auth');
		$this->load->model('budget_model');
	}
	
	function index()
	{		
		if (!$this->tank_auth->is_logged_in()) {
			redirect(site_url('auth/login/'));
		} else {
			$data['user_id']	= $this->tank_auth->get_user_id();
			$data['username']	= $this->tank_auth->get_username();
			$data['transactions']	= $this->budget_model->get_transactions_for_owner($data['username']);
			
			
			$this->load->library('table');
			

			$data['main_content']	= 'transactions_view';
			$this->load->view('includes/template', $data);
		}
		
	}
	
	public function delete($id)
	{
		
		if (!$this->tank_auth->is_logged_in()) {
			redirect(site_url('auth/login/'));
		} else {
			$transaction = $this->budget_model->get_transaction_by_id($id);
			if ($transaction)
			{
				if ($this->budget_model->get_transaction_owner($transaction['id']) == $this->tank_auth->get_username())
				{
					$this->budget_model->delete_object($transaction['id'], 'transactions');
				}
				
			}
			
		}
		$this->index();
	}
	public function create()
	{
		$this->load->helper('ajax');
		$this->load->helper('mysql_date');
		$this->load->helper('date');
		if (!$this->tank_auth->is_logged_in()) {
			redirect(site_url('auth/login/'));
		} else {
				
			$username = $this->tank_auth->get_username();		
			$this->load->library('form_validation');
			$form_data = array();
			
			if ($this->uri->segment(3))
			{
				if ($this->uri->segment(3) == "accounts")
				{
					$account = $this->budget_model->get_account_by_id($this->uri->segment(4));
					$form_data['account'] = $account;
				}
				if ($this->uri->segment(3) == "categories")
				{
					$category = $this->budget_model->get_category_by_id($this->uri->segment(4));
					$form_data['category'] = $category;
				}
			}
				if ($this->input->post())
				{	
					// field name, error message, validation rules
					$this->form_validation->set_rules('amount', 'Transaction Amount', 'numeric|required');
					if($this->form_validation->run() == FALSE)
					{
						redirect(site_url('transactions/create'));
					}		
					else
					{
						
						$data['amount'] = $this->input->post('amount');
						if ($this->input->post('transaction_type') == "withdrawal")
						{
							$data['amount'] = $this->input->post('amount') * -1;
						} 
						$data['payee'] = $this->input->post('payee');
						$data['check_number'] = $this->input->post('check_number');
						$data['memo'] = $this->input->post('memo');
						$data['transaction_date'] = timestamp_to_mysqldatetime(strtotime($this->input->post('transaction_date')));
						$data['category_id'] =  $this->input->post('category_id');
						$data['account_id'] =  $this->input->post('account_id');

						$data['updated_at'] = timestamp_to_mysqldatetime(now());
						$data['created_at'] = timestamp_to_mysqldatetime(now());;
						$data['created_by'] = $this->tank_auth->get_username();
						$data['updated_by'] = $this->tank_auth->get_username();

						$insert = $this->budget_model->create_transaction($data);

						if ($insert)
						{
							if ($form_data['category'])
							{								
								redirect(site_url('categories/show/' . $form_data['category']['id']) );
							} elseif ($form_data['account'])
							{
								redirect(site_url('transactions/show/' . $form_data['account']['id']) );
							} else
							{
								redirect(site_url('transactions/show/' . $insert['id']) );								
							}

						} else
						{
							$this->session->set_flashdata('error_message', 'Failed to create transaction.');
							redirect(site_url('transactions'));
						}
					}
				} else
				{
					// this is a get, so show the form
					$data['main_content'] = 'create_transaction_form';
					$accounts = $this->budget_model->get_accounts_for_owner($username);
					$categories = $this->budget_model->get_categories_for_owner($username);
					foreach ($accounts as $a)
					{
						$data['accounts'][$a['id']] = $a['name'];
					}
					
					$category_options = array();
					foreach ($categories as $category)
					{
						$category_options[$category['id']] = $category['category_name'];
					}
					$data['categories'] = $category_options;
					$data = array_merge($data, $form_data);
					
					if (is_ajax())
					{
						$this->load->view('create_transaction_form', $data);
					} else
					{						
						$this->load->view('includes/template', $data);
					}
				}	



		}
	}
	public function edit($id)
	{

		$this->load->helper('ajax');
		$this->load->helper('mysql_date');
		$this->load->helper('date');
		if (!$this->tank_auth->is_logged_in()) {
			redirect(site_url('auth/login/'));
		} else {
			$transaction = $this->budget_model->get_transaction_by_id($id);
			$username = $this->tank_auth->get_username();
			
			if ($transaction && $this->budget_model->get_transaction_owner($transaction['id']) == $this->tank_auth->get_username())
			{
				$this->load->library('form_validation');
				if ($this->input->post())
				{	
					// field name, error message, validation rules
					$this->form_validation->set_rules('amount', 'Transaction Amount', 'numeric|required');
					if($this->form_validation->run() == FALSE)
					{
						$this->session->set_flashdata('error_message', 'Sorry, the info you supplied was invalid.');
						redirect(site_url('transactions'));
					}		
					else
					{
						$data['amount'] = $this->input->post('amount');
						if ($this->input->post('transaction_type') == "withdrawal")
						{
							$data['amount'] = $this->input->post('amount') * -1;
						} 
						$data['id'] = $id;
						$data['payee'] = $this->input->post('payee');
						$data['check_number'] = $this->input->post('check_number');
						$data['transaction_date'] = timestamp_to_mysqldatetime(strtotime($this->input->post('transaction_date')));
						$data['category_id'] = $this->input->post('category_id');
						$data['account_id'] = $this->input->post('account_id');
						$data['memo'] = $this->input->post('memo');

						$data['updated_at'] = timestamp_to_mysqldatetime(now());
						$data['updated_by'] = $this->tank_auth->get_username();

						$update = $this->budget_model->update_transaction($data);

						if ($update)
						{
							$this->budget_model->update_current_balance($data['account_id']);
							if ($data['account_id'] != $transaction['account_id'])
							{
								$this->budget_model->update_current_balance($transaction['account_id']);
							}
							redirect(site_url('transactions/show/' . $id) . '/' . url_title($update['payee']));

						} else
						{
							$this->session->set_flashdata('error_message', 'Failed to update transaction.');
							redirect(site_url('transactions'));
						}
					}
				} else
				{
					// this is a get, so show the form
					$data['transaction'] = $transaction;

					$accounts = $this->budget_model->get_accounts_for_owner($username);
					$categories = $this->budget_model->get_categories_for_owner($username);
					foreach ($accounts as $a)
					{
						$data['accounts'][$a['id']] = $a['name'];
					}
					
					$category_options = array();
					foreach ($categories as $category)
					{
						$category_options[$category['id']] = $category['category_name'];
					}
					$data['categories'] = $category_options;
					
					if (is_ajax())
					{
						$this->load->view('edit_transaction_form', $data);
						//$this->load->view('includes/template', $data);
					} else
					{				
						$data['main_content'] = 'edit_transaction_form';		
						$this->load->view('includes/template', $data);
					}
				}	
				
			} else
			{
				$this->index();
			}
			
		}	
	}
	public function show($id)
	{
		
		// probably should first check to see if the wishlist id is valid.
		$transaction = $this->budget_model->get_transaction_by_id($id);
		if ($transaction)
		{

			if ( ($this->tank_auth->is_logged_in() && $this->budget_model->can_view_transaction($transaction['id'], $this->tank_auth->get_username())) ){

				$data['username'] = $this->tank_auth->get_username();
				$data['transaction'] = $transaction;
				$data['transactions'] = $this->budget_model->get_transactions($transaction['id']);
				
				$data['main_content'] = "transaction_view";
				$this->load->view("includes/template", $data);
			}	else
			{
				$this->session->set_flashdata('error_message', "You are not authorized.");
				redirect(site_url('transactions'));
			}
		} else
		{
			$this->session->set_flashdata('error_message', 'Sorry, that is not a valid transaction id.');
			redirect(site_url('transactions'));
		}
	}
	public function create_transaction($id)
	{
		$transaction = $this->budget_model->get_transaction_by_id($id);
		if ($transaction)
		{

			if ( ($this->tank_auth->is_logged_in() && $this->budget_model->can_view_transaction($transaction['id'], $this->tank_auth->get_username())) ){
				
				$this->load->helper('ajax');
				$this->load->helper('mysql_date');
				$this->load->helper('date');
				$this->load->library('form_validation');
				
				$username = $this->tank_auth->get_username();

				if ($this->input->post())
				{	
					// field name, error message, validation rules
					$this->form_validation->set_rules('amount', 'Transaction Amount', 'trim|required');
					if($this->form_validation->run() == FALSE)
					{
						redirect(site_url('transactions/create'));
					}		
					else
					{
						
						$data['amount'] = $this->input->post('amount');
						if ($this->input->post('transaction_type') == "withdrawal")
						{
							$data['amount'] = $this->input->post('amount') * -1;
						} 
						$data['payee'] = $this->input->post('payee');
						$data['check_number'] = $this->input->post('check_number');
						$data['memo'] = $this->input->post('memo');
						$data['transaction_date'] = timestamp_to_mysqldatetime(strtotime($this->input->post('transaction_date')));
						$data['category_id'] =  $this->input->post('category_id');
						$data['transaction_id'] =  $this->input->post('transaction_id');

						$data['updated_at'] = timestamp_to_mysqldatetime(now());
						$data['created_at'] = timestamp_to_mysqldatetime(now());;
						$data['created_by'] = $this->tank_auth->get_username();
						$data['updated_by'] = $this->tank_auth->get_username();

						$insert = $this->budget_model->create_transaction($data);

						if ($insert)
						{
							redirect(site_url('transactions/show/' . $transaction['id']) . '/' . url_title($transaction['name']));

						} else
						{
							$this->session->set_flashdata('error_message', 'Failed to create transaction.');
							redirect(site_url('transactions/show/' . $transaction['id']) . '/' . url_title($transaction['name']));
						}
					}
				} else
				{
					// this is a get, so show the form
					$data['main_content'] = 'create_transaction_form';
					$data['transaction'] = $transaction;
					$transactions = $this->budget_model->get_transactions_for_owner($username);
					$categories = $this->budget_model->get_categories_for_owner($username);
					foreach ($transactions as $a)
					{
						$data['transactions'][$a['id']] = $a['name'];
					}
					
					$category_options = array();
					foreach ($categories as $category)
					{
						$category_options[$category['id']] = $category['category_name'];
					}
					$data['categories'] = $category_options;

					if (is_ajax())
					{
						$this->load->view('create_transaction_form', $data);
					} else
					{						
						$this->load->view('includes/template', $data);
					}
				}	
				
			}	else
			{
				$this->session->set_flashdata('error_message', "You are not authorized.");
				redirect(site_url('transactions'));
			}
		} else
		{
			$this->session->set_flashdata('error_message', 'Sorry, that is not a valid transaction id.');
			redirect(site_url('transactions'));
		}
		
	}
}


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
