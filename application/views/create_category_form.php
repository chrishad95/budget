<link rel="stylesheet" type="text/css" media="screen" href="<?php echo site_url('css/cmxform.css'); ?>" />
<div id="add_category_form">
	<?php echo form_open(site_url('categories/create'), 'class="cmxform"'); ?>
	<fieldset>
		<legend>Category Information</legend>
		<ol>
			<li><label for="category_name">Name</label><?php echo form_input('category_name', 'Category Name'); ?></li>			
			<li><label for="description">Description</label><?php echo form_input('description', 'Description');  ?></li>
			<li><?php echo form_submit("submit", "Create Category"); ?></li>
		</ol>
	</fieldset>	
	
	<?php echo form_close(); ?>
</div>