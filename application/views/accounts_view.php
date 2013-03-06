<?php if (isset($records)) : ?>
<?php foreach($records as $row) : ?>
<div class='grid_4'><?php echo anchor(site_url(array('accounts','show', $row['id'], url_title($row['name']))), $row['name']); ?></div>
<div class='grid_7'><?php echo $row['desc']; ?></div>
<div class='grid_1'><?php echo $row['initial_balance']; ?></div>

<div class='clear'></div>
<?php endforeach; ?>
<?php endif; ?>
<?php echo anchor(site_url("accounts/create") . '?height=300&width=400', "Create Account", array('title' => 'Create Account', 'class' => 'thickbox')); ?>