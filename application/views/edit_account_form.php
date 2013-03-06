<link rel="stylesheet" type="text/css" media="screen" href="<?php echo site_url('css/cmxform.css'); ?>" />
<div id="edit_account_form">
	<?php echo form_open(site_url('accounts/edit/' . $account['id']), 'class="cmxform"'); ?>
	<fieldset>
		<legend>Account Information</legend>
		<ol>
			<li><label for="name">Name</label><?php echo form_input('name', $account['name']); ?></li>			
			<li><label for="initial_balance">Initial Balance</label><?php echo form_input('initial_balance', $account['initial_balance']);  ?></li>
			<li><label for="desc">Description</label><?php echo form_input('desc', $account['desc']);  ?></li>
			<li><?php echo form_submit("submit", "Update Account"); ?></li>
		</ol>
	</fieldset>	
	
	<?php echo form_close(); ?>
</div>
