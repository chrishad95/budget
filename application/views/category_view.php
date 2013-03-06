<div class='grid_12'><h1><?php echo $category['category_name']; ?></h1></div>
<div class='clear'></div>
<div class='grid_12'><?php echo anchor(site_url('categories/edit/' . $category['id']) . '?height=300&width=400', 'Edit', array('title' => 'Edit Category', 'class' => 'thickbox')); ?></div>
<div class='clear'></div>
<?php $this->load->view('transactions_view') ?>

<?php echo anchor(site_url("transactions/create/categories/" . $category['id']) . '?height=300&width=400', "Create Transaction", array('title' => 'Create Transaction', 'class' => 'thickbox')); ?>