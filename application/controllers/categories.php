<?php
class Categories extends CI_Controller {
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
			$data['records']	= $this->budget_model->get_categories_for_owner($data['username']);
			
			
			$this->load->library('table');
			

			$data['main_content']	= 'categories_view';
			$this->load->view('includes/template', $data);
		}
		
	}
	
	public function delete($id)
	{
		
		if (!$this->tank_auth->is_logged_in()) {
			redirect(site_url('auth/login/'));
		} else {
			$category = $this->budget_model->get_category_by_id($id);
			if ($category)
			{
				if ($category['owner'] == $this->tank_auth->get_username())
				{
					$this->budget_model->delete_category($category['id']);
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
			if ($this->input->post('category_name'))
			{	
				// field name, error message, validation rules
				$this->form_validation->set_rules('category_name', 'Category Name', 'trim|required');
				if($this->form_validation->run() == FALSE)
				{
					redirect(site_url('categories/create'));
				}		
				else
				{
					$data['category_name'] = $this->input->post('category_name');
					$data['description'] = $this->input->post('description');
					$data['owner'] = $this->tank_auth->get_username();
					
					$data['updated_at'] = timestamp_to_mysqldatetime(now());
					$data['created_at'] = timestamp_to_mysqldatetime(now());;
					$data['created_by'] = $this->tank_auth->get_username();
					$data['updated_by'] = $this->tank_auth->get_username();
		
					$insert = $this->budget_model->create_category($data);
					
					if ($insert)
					{
						redirect(site_url('categories/show/' . $insert['id']) . '/' . url_title($data['category_name']));
						
					} else
					{
						$this->session->set_flashdata('error_message', 'Failed to create category.');
						redirect(site_url('categories'));
					}
				}
			} else
			{
				// this is a get, so show the form
				$data['main_content'] = 'create_category_form';
				
				if (is_ajax())
				{
					$this->load->view('create_category_form', $data);
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
			$category = $this->budget_model->get_category_by_id($id);
			if ($category && $category['owner'] == $this->tank_auth->get_username())
			{
				$this->load->library('form_validation');
				if ($this->input->post('name'))
				{	
					// field name, error message, validation rules
					$this->form_validation->set_rules('name', 'Category Name', 'trim|required');
					if($this->form_validation->run() == FALSE)
					{
						redirect(site_url('categories'));
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
							redirect(site_url('categories/show/' . $id) . '/' . url_title($data['name']));

						} else
						{
							$this->session->set_flashdata('error_message', 'Failed to update category.');
							redirect(site_url('categories'));
						}
					}
				} else
				{
					// this is a get, so show the form
					$data['category'] = $category;

					if (is_ajax())
					{
						$this->load->view('edit_category_form', $data);
						//$this->load->view('includes/template', $data);
					} else
					{				
						$data['main_content'] = 'edit_category_form';		
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
		$category = $this->budget_model->get_category_by_id($id);
		if ($category)
		{

			if ( ($this->tank_auth->is_logged_in() && $this->budget_model->can_view($category['id'], $this->tank_auth->get_username(), 'categories')) ){

				$data['username'] = $this->tank_auth->get_username();
				$data['category'] = $category;
				$data['transactions'] = $this->budget_model->get_transactions_for_category($category['id']);
				
				$data['main_content'] = "category_view";
				$this->load->view("includes/template", $data);
			}	else
			{
				$this->session->set_flashdata('error_message', "You are not authorized.");
				redirect(site_url('categories'));
			}
		} else
		{
			$this->session->set_flashdata('error_message', 'Sorry, that is not a valid category id.');
			redirect(site_url('categories'));
		}
	}
}


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
