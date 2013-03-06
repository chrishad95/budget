<div class='grid_12'>
<?php echo anchor(site_url('transactions/edit/' . $transaction['id']) . '?height=300&width=400', 'Edit', array('title' => 'Edit Transaction', 'class' => 'thickbox')); ?>
	<fieldset>
		<legend>Transaction Details</legend>
		<ol>
			<li><label for="payee">Payee: </label><?php echo $transaction['payee']; ?></li>
			<li><label for="check_number">Check Number</label><?php echo $transaction['check_number']; ?></li>
			<li><label for="transaction_date">Date</label><?php echo $transaction['transaction_date']; ?></li>
			<li><label for="category_id">Category</label><?php echo anchor(site_url(array('categories','show', $transaction['category_id'])),  $transaction['category_name']); ?></li>
			<li><label for="account_id">Account</label><?php echo anchor(site_url(array('accounts','show', $transaction['account_id'])),  $transaction['account_name']); ?></li>
			<li><label for="amount">Transaction Amount</label><?php echo $transaction['amount']; ?></li>
			<li><label for="memo">Memo</label><?php echo $transaction['memo']; ?></li>
		</ol>
	</fieldset>	
</div>
<div class='clear'></div>


<td></td>
		<td></td>
		