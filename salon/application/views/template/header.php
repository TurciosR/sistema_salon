<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="<?= base_url("assets/img/logofav.png"); ?>" rel="icon" type="image/png">

	<title>| Consola de Administracion</title>

	<!-- CSS -->
	<link href="<?= base_url("assets/css/bootstrap.min.css"); ?>" rel="stylesheet">
	<link href="<?= base_url("assets/libs/mdi/css/materialdesignicons.min.css"); ?>" rel="stylesheet">
	<link href="<?= base_url("assets/css/animate.css"); ?>" rel="stylesheet">
	<link href="<?= base_url("assets/css/style.css"); ?>" rel="stylesheet">
	<link href="<?= base_url("assets/css/my_style.css"); ?>" rel="stylesheet">

    <!-- PLUGINS -->
    <link href="<?= base_url("assets/libs/select2/select2.min.css"); ?>" rel="stylesheet">
    <link href="<?= base_url("assets/libs/select2/select2-bootstrap4.min.css"); ?>" rel="stylesheet">
    <link href="<?= base_url("assets/libs/izitoast/iziToast.min.css"); ?>" rel="stylesheet">
    <link href="<?= base_url("assets/libs/dataTables/datatables.min.css"); ?>" rel="stylesheet">
    <link href="<?= base_url("assets/libs/dropify/dropify.min.css"); ?>" rel="stylesheet">
    <link href="<?= base_url("assets/libs/sweetalert2/sweetalert2.min.css"); ?>" rel="stylesheet">
    <link href="<?= base_url("assets/libs/datapicker/bootstrap-datepicker.min.css"); ?>" rel="stylesheet">
    <link href="<?= base_url("assets/libs/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css"); ?>" rel="stylesheet">
    <link href="<?= base_url("assets/libs/jasny/jasny-bootstrap.min.css"); ?>" rel="stylesheet">
    <link href="<?= base_url("assets/libs/parsley/parsley.css"); ?>" rel="stylesheet">
    <link href="<?= base_url("assets/libs/jquery_image_multiple/image-uploader.min.css"); ?>" rel="stylesheet">
    <link href="<?= base_url("assets/libs/typeahead/autocomplete.css"); ?>" rel="stylesheet">
		<link href="<?= base_url("assets/libs/selectize/css/selectize.css"); ?>" rel="stylesheet">
    <link href="<?= base_url("assets/css/loader.css"); ?>" rel="stylesheet">
		<link href="<?= base_url("assets/libs/gallery/css/blueimp-gallery.min.css"); ?>" rel="stylesheet">
	<script>var base_url = '<?php echo base_url() ?>'</script>

	<?php if (isset($css)) : ?>
		<?php foreach ($css as $extra => $url) : ?>
			<link href="<?= base_url("assets/$url"); ?>" rel="stylesheet" type="text/css"/>
		<?php endforeach; ?>
	<?php endif; ?>

    <script src="<?= base_url("assets/js/jquery-3.1.1.min.js"); ?>"></script>
    <script src="<?= base_url("assets/js/popper.min.js"); ?>"></script>
    <script src="<?= base_url("assets/js/bootstrap.js"); ?>"></script>
    <script src="<?= base_url("assets/libs/metisMenu/jquery.metisMenu.js"); ?>"></script>
    <script src="<?= base_url("assets/libs/dataTables/datatables.min.js"); ?>"></script>
    <script src="<?= base_url("assets/libs/dropify/dropify.min.js"); ?>"></script>
    <script src="<?= base_url("assets/libs/select2/select2.full.min.js"); ?>"></script>
    <script src="<?= base_url("assets/libs/sweetalert2/sweetalert2.min.js"); ?>"></script>
    <script src="<?= base_url("assets/libs/parsley/parsley.min.js"); ?>"></script>
    <script src="<?= base_url("assets/libs/parsley/parsley.es.js"); ?>"></script>
    <script src="<?= base_url("assets/libs/numeric/jquery.numeric.js"); ?>"></script>
    <script src="<?= base_url("assets/libs/jasny/jasny-bootstrap.min.js"); ?>"></script>
    <script src="<?= base_url("assets/libs/datapicker/bootstrap-datepicker.min.js"); ?>"></script>
    <script src="<?= base_url("assets/libs/datapicker/bootstrap-datepicker.es.min.js"); ?>"></script>
    <script src="<?= base_url("assets/libs/izitoast/iziToast.min.js"); ?>"></script>
    <script src="<?= base_url("assets/libs/jquery_image_multiple/image-uploader.min.js"); ?>"></script>
    <script src="<?= base_url("assets/libs/typeahead/typeahead.jquery.min.js"); ?>"></script>
		  <script src="<?= base_url("assets/libs/selectize/standalone/selectize.js"); ?>"></script>

    <script src="<?= base_url("assets/js/inspinia.js"); ?>"></script>
    <script src="<?= base_url("assets/libs/pace/pace.min.js"); ?>"></script>
    <script src="<?= base_url("assets/libs/slimscroll/jquery.slimscroll.min.js"); ?>"></script>
    <script src="<?= base_url("assets/libs/mask/jquery.mask.min.js"); ?>"></script>
    <script src="<?= base_url("assets/js/scripts/utils.js"); ?>"></script>
		<script src="<?= base_url("assets/libs/chartJs/Chart.min.js"); ?>"></script>
		<script src="<?= base_url("assets/libs/gallery/js/jquery.blueimp-gallery.min.js");?>"></script>

</head>
