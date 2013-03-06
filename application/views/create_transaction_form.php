<link rel="stylesheet" type="text/css" media="screen" href="<?php echo site_url('css/cmxform.css'); ?>" />
<div id="create_transaction_form">
	<?php 
	if (isset( $account))
	{
		echo form_open(site_url('transactions/create/accounts/' . $account['id']), 'class="cmxform"');
	} elseif (isset ($category))
	{
		echo form_open(site_url('transactions/create/categories/' . $category['id']), 'class="cmxform"');		
	} else
	{
		echo form_open(site_url('transactions/create/'), 'class="cmxform"');		
	}
	?>
	<fieldset>
		<legend>Transaction Details</legend>
		<ol>
			<li><label for="payee">Payee</label><?php echo form_input('payee', 'Payee'); ?></li>
			<li><label for="check_number">Check Number</label><?php echo form_input('check_number', 'Check Number'); ?></li>
			<li><label for="transaction_date">Date</label><?php echo form_input('transaction_date', 'Date'); ?></li>
			<li><label for="category_id">Category</label><?php echo form_dropdown('category_id', $categories, (isset($category)?$category['id']:null)); ?></li>
			<li><label for="account_id">Account</label><?php echo form_dropdown('account_id', $accounts, (isset($account)?$account['id']:null)); ?></li>
			<li><label for="transaction_type">type</label><?php echo form_dropdown('transaction_type', array('withdrawal' => 'Withdrawal', 'deposit' => 'Deposit'), 'withdrawal'); ?></li>
			<li><label for="amount">Transaction Amount</label><?php echo form_input('amount', 'Transaction Amount'); ?></li>
			<li><label for="memo">Memo</label><?php echo form_input('memo', 'Memo'); ?></li>
			<li><?php echo form_submit("submit", "Create Transaction"); ?></li>
		</ol>
	</fieldset>	
	
	<?php echo form_close(); ?>
</div>