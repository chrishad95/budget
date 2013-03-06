<?php
class Accounts extends CI_Controller {
	function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
		$this->load->helper('html');
		$this->load->helper('form');
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
			$data['records']	= $this->budget_model->get_accounts_for_owner($data['username']);
			
			
			$this->load->library('table');
			

			$data['main_content']	= 'accounts_view';
			$this->load->view('includes/template', $data);
		}
		
	}
	
	public function delete($id)
	{
		
		if (!$this->tank_auth->is_logged_in()) {
			redirect(site_url('auth/login/'));
		} else {
			$account = $this->budget_model->get_account_by_id($id);
			if ($account)
			{
				if ($account['owner'] == $this->tank_auth->get_username())
				{
					$this->budget_model->delete_account($account['id']);
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
						
			$this->load->library('form_validation');
			if ($this->input->post('name'))
			{	
				// field name, error message, validation rules
				$this->form_validation->set_rules('name', 'Account Name', 'trim|required');
				if($this->form_validation->run() == FALSE)
				{
					redirect(site_url('accounts/create'));
				}		
				else
				{
					$data['name'] = $this->input->post('name');
					$data['initial_balance'] = $this->input->post('initial_balance');
					$data['desc'] = $this->input->post('desc');
					$data['owner'] = $this->tank_auth->get_username();
					
					$data['updated_at'] = timestamp_to_mysqldatetime(now());
					$data['created_at'] = timestamp_to_mysqldatetime(now());;
					$data['created_by'] = $this->tank_auth->get_username();
					$data['updated_by'] = $this->tank_auth->get_username();
		
					$insert = $this->budget_model->create($data);
					
					if ($insert)
					{
						redirect(site_url('accounts/show/' . $insert['id']) . '/' . url_title($data['name']));
						
					} else
					{
						$this->session->set_flashdata('error_message', 'Failed to create account.');
						redirect(site_url('accounts'));
					}
				}
			} else
			{
				// this is a get, so show the form
				$data['main_content'] = 'create_account_form';
				
				if (is_ajax())
				{
					$this->load->view('create_account_form', $data);
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
			$account = $this->budget_model->get_account_by_id($id);
			if ($account && $account['owner'] == $this->tank_auth->get_username())
			{
				$this->load->library('form_validation');
				if ($this->input->post('name'))
				{	
					// field name, error message, validation rules
					$this->form_validation->set_rules('name', 'Account Name', 'trim|required');
					if($this->form_validation->run() == FALSE)
					{
						redirect(site_url('accounts'));
					}		
					else
					{
						$data['id'] = $id;
						$data['name'] = $this->input->post('name');
						$data['initial_balance'] = $this->input->post('initial_balance');
						$data['desc'] = $this->input->post('desc');
						$data['owner'] = $this->tank_auth->get_username();

						$data['updated_at'] = timestamp_to_mysqldatetime(now());
						$data['updated_by'] = $this->tank_auth->get_username();

						$update = $this->budget_model->update($data);

						if ($update)
						{
							redirect(site_url('accounts/show/' . $id) . '/' . url_title($data['name']));

						} else
						{
							$this->session->set_flashdata('error_message', 'Failed to update account.');
							redirect(site_url('accounts'));
						}
					}
				} else
				{
					// this is a get, so show the form
					$data['account'] = $account;

					if (is_ajax())
					{
						$this->load->view('edit_account_form', $data);
						//$this->load->view('includes/template', $data);
					} else
					{				
						$data['main_content'] = 'edit_account_form';		
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
		$account = $this->budget_model->get_account_by_id($id);
		if ($account)
		{

			if ( ($this->tank_auth->is_logged_in() && $this->budget_model->can_view_account($account['id'], $this->tank_auth->get_username())) ){

				$this->load->library('pagination');
				$this->load->library('table');
		
				$config['base_url'] = 'http://localhost/budget/accounts/show/' . $account['id'];
				$config['total_rows'] = $this->budget_model->get_total_rows_for_account($account['id']);
				$config['per_page'] = 4;
				$config['num_links'] = 20;
				$this->pagination->initialize($config);
				
				$data['username'] = $this->tank_auth->get_username();
				$data['account'] = $account;
				$data['transactions'] = $this->budget_model->get_transactions($account['id'], $config['per_page'], $this->uri->segment(4));
				$data['imported_transactions'] = $this->budget_model->get_imported_transactions_for_account($account['id']);
				
				$data['show_balance'] = true;
				$data['main_content'] = "account_view";
				$this->load->view("includes/template", $data);
			}	else
			{
				$this->session->set_flashdata('error_message', "You are not authorized.");
				redirect(site_url('accounts'));
			}
		} else
		{
			$this->session->set_flashdata('error_message', 'Sorry, that is not a valid account id.');
			redirect(site_url('accounts'));
		}
	}
}


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
