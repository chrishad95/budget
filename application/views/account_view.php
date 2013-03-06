
<div class='grid_12'><h1><?php echo $account['name']; ?></h1></div>
<div class='clear'></div>
<div class='grid_12'><?php echo anchor(site_url('accounts/edit/' . $account['id']) . '?height=300&width=400', 'Edit', array('title' => 'Edit Account', 'class' => 'thickbox')); ?></div>
<div class='clear'></div>
<?php $this->load->view('transactions_view') ?>
<?php echo $this->pagination->create_links(); ?>

<?php echo anchor(site_url("transactions/create/accounts/" . $account['id']) . '?height=300&width=400', "Create Transaction", array('title' => 'Create Transaction', 'class' => 'thickbox')); ?>


<?php echo form_open_multipart(site_url('accounts/import/' . $account['id']));?>

<input type="file" name="transactions_file" size="20" />
<br />
<br />
<input type="submit" value="upload" />
</form>