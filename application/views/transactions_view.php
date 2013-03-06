<?php
setlocale(LC_ALL, ''); // Locale will be different on each system.
$locale = localeconv();
?>

<?php if (isset($transactions)) : ?>
<div class='grid_12'>

	<h2>Transactions</h2>
<table border="1" cellspacing="0" cellpadding="2">
	<tr>
	<td>Date</td>
	<td>Payee</td>
	<td>Amount</td>
	<td>Category</td>
	<td>Amount</td>
	<?php echo (isset($show_balance)?"<td>Balance</td>":""); ?>
	<td>Actions</td>
</tr>
<?php foreach($transactions as $transaction) : ?>
	<tr>
		<td><?php echo $transaction['transaction_date'] ; ?></td>
		<td><?php echo anchor(site_url(array('transactions','show', $transaction['id'], url_title($transaction['payee']))), $transaction['payee']); ?></td>
		
		<td><?php echo anchor(site_url(array('accounts','show', $transaction['account_id'])),  $transaction['account_name']); ?></td>
		<td><?php echo anchor(site_url(array('categories','show', $transaction['category_id'])),  $transaction['category_name']); ?></td>
		<td><?php echo $locale['currency_symbol'], number_format($transaction['amount'], 2, $locale['decimal_point'], $locale['thousands_sep']); ?></td>
		<?php echo (isset($show_balance)?("<td>" . $locale['currency_symbol'] . number_format($transaction['amount'], 2, $locale['decimal_point'], $locale['thousands_sep'])) . "</td>" :'') ; ?>
		<td><?php echo anchor(site_url(array('transactions','edit', $transaction['id'])) . '?height=300&width=400', 'Edit', array('title' => 'Edit Transaction', 'class' => 'thickbox')); ?>
		<?php echo anchor(site_url(array('transactions', 'delete', $transaction['id'])), 'Delete', array('title' => 'delete this transaction', 'class' => 'confirm')); ?>
		</td>
				
	</tr>
<?php endforeach; ?>
</table>	
</div>
<div class='clear'></div>

<?php endif; ?>

<?php $this->load->view("confirm_js_view"); ?>
