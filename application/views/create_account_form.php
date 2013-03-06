<link rel="stylesheet" type="text/css" media="screen" href="<?php echo site_url('css/cmxform.css'); ?>" />
<div id="add_account_form">
	<?php echo form_open(site_url('accounts/create'), 'class="cmxform"'); ?>
	<fieldset>
		<legend>Account Information</legend>
		<ol>
			<li><label for="name">Name</label><?php echo form_input('name', 'Account Name'); ?></li>			
			<li><label for="initial_balance">Initial Balance</label><?php echo form_input('initial_balance', 'Initial Balance');  ?></li>
			<li><label for="desc">Description</label><?php echo form_input('desc', 'Description');  ?></li>
			<li><?php echo form_submit("submit", "Create Account"); ?></li>
		</ol>
	</fieldset>	
	
	<?php echo form_close(); ?>
</div>