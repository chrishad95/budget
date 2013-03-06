<?php if (isset($records)) : ?>
<?php foreach($records as $row) : ?>
<div class='grid_4'><?php echo anchor(site_url(array('categories','show', $row['id'], url_title($row['category_name']))), $row['category_name']); ?></div>
<div class='grid_8'><?php echo $row['description']; ?></div>

<div class='clear'></div>
<?php endforeach; ?>
<?php endif; ?>
<?php echo anchor(site_url("categories/create") . '?height=300&width=400', "Create Category", array('title' => 'Create Category', 'class' => 'thickbox')); ?>