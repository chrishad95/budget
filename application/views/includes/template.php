<?php $this->load->view('includes/header') ?>
<div class='container_12'>
<div id='nav' class='grid_12'><?php echo ( $this->tank_auth->get_username()? 'Hello, ' . $this->tank_auth->get_username() . ', ' . anchor('/auth/logout','Logout'): anchor('/auth/login', 'Login')); ?> 
</div>
	
	<div class='grid_12' id='nav' ><?php echo anchor(site_url('accounts'), 'Accounts'); ?>
		<?php echo anchor(site_url('transactions'), 'Transactions'); ?>
		<?php echo anchor(site_url('categories'), 'Categories'); ?>
		<?php echo anchor(site_url('budgets'), 'Budgets'); ?>
	</div>
	
<div id='error_message' class='grid_12'><?php echo $this->session->flashdata('error_message'); ?></div>
</div>
<div class='container_12'>
	<div class='grid_12'>
	<?php $this->load->view($main_content) ?>
	</div>
</div>
<?php $this->load->view('includes/footer') ?>