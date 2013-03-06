<link rel="stylesheet" type="text/css" media="screen" href="<?php echo site_url('css/cmxform.css'); ?>" />
<div id="edit_transaction_form">
	<?php 
		echo form_open(site_url('transactions/edit/'. $transaction['id']), 'class="cmxform"');
	?>
	<fieldset>
		<legend>Transaction Details</legend>
		<ol>
			<li><label for="payee">Payee</label>
			<?php echo form_input('payee', $transaction['payee']); ?></li>
			<li><label for="check_number">Check Number</label>
			<?php echo form_input('check_number', $transaction['check_number']); ?></li>
			<li><label for="transaction_date">Date</label>
			<?php echo form_input('transaction_date', $transaction['transaction_date']); ?></li>
			<li><label for="category_id">Category</label>
			<?php echo form_dropdown('category_id', $categories, $transaction['category_id']); ?></li>
			<li><label for="account_id">Account</label>
			<?php echo form_dropdown('account_id', $accounts, $transaction['account_id']); ?></li>
			<li><label for="transaction_type">type</label>
			<?php echo form_dropdown('transaction_type', array('withdrawal' => 'Withdrawal', 'deposit' => 'Deposit'), ($transaction['amount'] >= 0?'deposit':'withdrawal')); ?></li>
			<li><label for="amount">Transaction Amount</label>
			<?php echo form_input('amount', abs($transaction['amount'])); ?></li>
			<li><label for="memo">Memo</label>
			<?php echo form_input('memo', $transaction['memo']); ?></li>
			<li><?php echo form_submit("submit", "Edit Transaction"); ?></li>
		</ol>
	</fieldset>	
	
	<?php echo form_close(); ?>
</div>