<!DOCTYPE html>
<html>

<head>
	<title><?php echo $title ?></title>

	<?php foreach ($css_lib as $lib) : ?>
		<link rel="stylesheet" type="text/css" href="<?php echo $lib; ?>">
	<?php endforeach; ?>

	<?php foreach ($meta_tag as $tag) : ?>
		<?php echo $tag; ?>
	<?php endforeach; ?>

	<?php foreach ($header_js_lib as $lib) : ?>
		<script type="text/javascript" src="<?php echo $lib; ?>"></script>
	<?php endforeach; ?>

</head>

<body>