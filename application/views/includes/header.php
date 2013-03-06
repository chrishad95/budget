<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title><?php echo ( isset($title)? "Budget | $title" : "Budget") ; ?></title>
		<script type='text/javascript' src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo site_url('js/thickbox-compressed.js') ?>"></script>

		<!-- Confirm CSS files -->
		<link type='text/css' href='<?php echo site_url('simplemodal/confirm/css/confirm.css'); ?>' rel='stylesheet' media='screen' />		
		<link rel="stylesheet" href="<?php echo site_url('css/thickbox.css'); ?>" />
		<link rel="stylesheet" href="<?php echo site_url('css/960gs/reset.css'); ?>" />
		<link rel="stylesheet" href="<?php echo site_url('css/960gs/text.css'); ?>" />
		<link rel="stylesheet" href="<?php echo site_url('css/960gs/960.css'); ?>" />
	</head>
	<body>