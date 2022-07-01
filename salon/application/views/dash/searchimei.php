
<!DOCTYPE html>
<html lang="es" dir="ltr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="<?= base_url("assets/img/logofav.png"); ?>" rel="icon" type="image/png">

  <title>Invertec</title>

  <!-- CSS -->
  <link href="<?= base_url("assets/css/bootstrap.min.css"); ?>" rel="stylesheet">
  <link href="<?= base_url("assets/libs/mdi/css/materialdesignicons.min.css"); ?>" rel="stylesheet">
  <link href="<?= base_url("assets/css/animate.css"); ?>" rel="stylesheet">
  <link href="<?= base_url("assets/css/style.css"); ?>" rel="stylesheet">

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
  <link href="<?= base_url("assets/css/loader.css"); ?>" rel="stylesheet">

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

  <script src="<?= base_url("assets/js/inspinia.js"); ?>"></script>
  <script src="<?= base_url("assets/libs/pace/pace.min.js"); ?>"></script>
  <script src="<?= base_url("assets/libs/slimscroll/jquery.slimscroll.min.js"); ?>"></script>
  <script src="<?= base_url("assets/libs/mask/jquery.mask.min.js"); ?>"></script>
  <script src="<?= base_url("assets/js/scripts/utils.js"); ?>"></script>
  <script src="<?= base_url("assets/libs/chartJs/Chart.min.js"); ?>"></script>

</head>
<body  style="background: url(<?=base_url('assets/img/pattern.png') ?>) repeat scroll top center; " >
  <style media="screen">
  /*==================================================
=            Bootstrap 3 Media Queries             =
==================================================*/
/*==========  Mobile First Method  ==========*/
/* Custom, iPhone Retina */
/* Extra Small Devices, Phones */
/* Small Devices, Tablets */
/* Medium Devices, Desktops */
/* Large Devices, Wide Screens */
/*==========  Non-Mobile First Method  ==========*/
/* Large Devices, Wide Screens */
/* Medium Devices, Desktops */
/* Small Devices, Tablets */
/* Extra Small Devices, Phones */
/* Custom, iPhone Retina */
/*=====================================================
=            Bootstrap 2.3.2 Media Queries            =
=====================================================*/
/* default styles here for older browsers.
 I tend to go for a 600px - 960px width max but using percentages
*/
@media only screen and (min-width: 960px) {
/* styles for browsers larger than 960px; */
}
@media only screen and (min-width: 1440px) {
/* styles for browsers larger than 1440px; */
}
@media only screen and (min-width: 2000px) {
/* for sumo sized (mac) screens */
}
@media only screen and (max-device-width: 480px) {
/* styles for mobile browsers smaller than 480px; (iPhone) */
}
@media only screen and (device-width: 768px) {
/* default iPad screens */
}
/* different techniques for iPad screening */
@media only screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation: portrait) {
/* For portrait layouts only */
}
@media only screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation: landscape) {
/* For landscape layouts only */
}
/*******Nuevos mensajes de error******/
.new-message-box {
margin: 15px 0;
padding-left: 20px;
margin-bottom: 25px !important;
}

.new-message-box p {
font-size: 1.15em;
font-weight: 600;
}

.info-tab {
width: 40px;
height: 40px;
display: inline-block;
position: relative;
top: 8px;
}

.info-tab {
float: left;
margin-left: -23px;
}

.info-tab i::before {
width: 24px;
height: 24px;
box-shadow: inset 12px 0 13px rgba(0, 0, 0, 0.5);
}

.info-tab i::after {
width: 0;
height: 0;
border: 12px solid transparent;
border-bottom-color: #fff;
border-left-color: #fff;
bottom: -18px;
}

.info-tab i::before, .info-tab i::after {
content: "";
display: inline-block;
position: absolute;
left: 0;
bottom: -17px;
transform: rotateX(60deg);
}

.note-box, .warning-box, .tip-box-success, .tip-box-danger, .tip-box-warning, .tip-box-info, .tip-box-alert {
padding: 12px 8px 3px 26px;
}

/***Success****/
.new-message-box-success {
background: #eeeeee;
padding: 3px;
margin: 10px 0;
}

.tip-icon-success {
background: #8BC34A;
}

.tip-box-success {
color: #33691E;
background: #DCEDC8;
}

.tip-icon-success::before {
font-size: 25px;
content: "\f00c";
top: 8px;
left: 11px;
font-family: FontAwesome;
position: absolute;
color: white;
}

.tip-icon-success i::before {
background: #8BC34A;
}

/*******Danger*******/
.new-message-box-danger {
background: #eeeeee;
padding: 3px;
margin: 10px 0;
}

.tip-icon-danger {
background: #f44336;
}

.tip-box-danger {
color: #b71c1c;
background: #FFCCBC;
}

.tip-icon-danger::before {
font-size: 25px;
content: "\f00d";
top: 8px;
left: 11px;
font-family: FontAwesome;
position: absolute;
color: white;
}

.tip-icon-danger i::before {
background: #f44336;
}

/*******warning*******/
.new-message-box-warning {
background: #eeeeee;
padding: 3px;
margin: 10px 0;
}

.tip-icon-warning {
background: #FFEB3B;
}

.tip-box-warning {
color: #212121;
background: #FFF9C4;
}

.tip-icon-warning::before {
font-size: 25px;
content: "\f071";
top: 8px;
left: 11px;
font-family: FontAwesome;
position: absolute;
color: #212121;
}

.tip-icon-warning i::before {
background: #FFEB3B;
}

/*******info*******/
.new-message-box-info {
background: #eeeeee;
padding: 3px;
margin: 10px 0;
}

.tip-box-info {
color: #01579B;
background: #B3E5FC;
}

.tip-icon-info {
background: #03A9F4;
}

.tip-icon-info::before {
font-size: 20px;
top: 8px;
left: 11px;
position: absolute;
color: white;
}

.tip-icon-info i::before {
background: #03A9F4;
}

/*******info*******/
.new-message-box-alert {
background: #FF6F00;
padding: 3px;
margin: 10px 0;
}

.tip-box-alert {
color: #212121;
background: #FFF8E1;
}

.tip-icon-alert {
background: #FF6F00;
}

.tip-icon-alert::before {
font-size: 25px;
content: "\f06a";
top: 8px;
left: 11px;
font-family: FontAwesome;
position: absolute;
color: white;
}

.tip-icon-alert i::before {
background: #FF6F00;
}

/*************************/

  </style>
  <div class="container">
    <div class="row pt-1 pb-1">
      <div class="col-lg-12">
        <h4 style="color:#fff;" class="text-center">Invertec</h4>
        <h5 style="color:#fff;" class="text-center">Busqueda de imei para garantia</h5>
      </div>
    </div>
  </div>

  <section class="search-sec">
    <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <div class="ibox float-e-margins">
              <div class="ibox-title">
                <h5 style="color:#000;">Ingrese un Imei para ver el estado de garantia</h5>
              </div>
              <div class="ibox-content">
                <div class="row">

                  <div class="col-sm-12 text-center">
                    <img src="<?=base_url("assets/img/baner_cert.png") ?>" class="img-responsive" style="width: 60%;margin-left: auto;margin-right: auto;">
                  </div>

                </div>
                <div class="new-message-box">
                  <div class="new-message-box-info">
                    <div class="info-tab tip-icon-info mdi mdi-information-variant" title="error"><i class=""></i></div>
                    <div class="tip-box-info">
                      <p>
                        <strong>Info!</strong> Para encontrar tú imei, presiona *#06#.
                        </p>
                      </div>
                    </div>
                </div>
                <div class="row">

                  <div class="col-lg-9 col-md-9 col-sm-12">
                    <input type="text"  id="imei" class="form-control search-slt" placeholder="IMEI">
                  </div>
                  <div class="col-lg-3 col-md-3 col-sm-12">
                    <button type="button" id="search" style="width:100%" class="btn btn-info btn-sm">Buscar</button>

                      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                      value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                  </div>
                </div>

                <div class="row" id="pr" style="display: none; overflow-x:scroll">
                  <div class="col-lg-12">
                    <table class="table table-stripped" >
                      <thead>
                        <th>NOMBRE</th>
                        <th>FECHA VENTA</th>
                        <th>MARCA</th>
                        <th>MODELO</th>
                        <th>CONDICION</th>
                        <th>DIAS GARANTIA</th>
                        <th>VENCIMIENTO</th>
                        <th>PUEDE RECLAMAR GARANTIA</th>
                        <th>PDF</th>
                      </thead>
                      <tbody id="da">

                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
    </div>
  </section>


  <div class="container mt-5 pt-5">
    <div class="row">
      <div class="col-lg-12 text-center">
        <h5 style="color:#fff;">
          <?=date("Y") ?> © Ludwin Hernández. <a href="">Open Solution Systems</a>
        </h5>
      </div>
    </div>
  </div>

  <script type="text/javascript">
  var base_url = '<?=base_url("") ?>';

  let token = $("#csrf_token_id").val();
  $(document).on('click', '#search', function(event) {
    imei = $("#imei").val();

    $("#pr").hide('slow/400/fast', function() {

    });
    $.ajax({
      url: base_url+"dashboard/imei",
      type: 'POST',
      dataType: 'json',
      data: {
        csrf_test_name:token,
        imei: imei,
      },

      /*
      nombre 	id_detalle 	fecha 	    nombre 	marca 	modelo 	  condicion 	garantia
      DASD 	  4 	        2020-08-28	XIAOMI	XIAOMI	XLR8X20A	USADO	      5
      */
      success: function(xdatos) {
        notification(xdatos.typeinfo,"",xdatos.msg);
        if (xdatos.typeinfo=="Success") {

          var p ="";
          p+="<tr>";
          p+="<td>"+xdatos.data.nombre+"</td>";
          p+="<td>"+xdatos.data.fecha+"</td>";
          p+="<td>"+xdatos.data.marca+"</td>";
          p+="<td>"+xdatos.data.modelo+"</td>";
          p+="<td>"+xdatos.data.condicion+"</td>";
          p+="<td>"+xdatos.data.garantia+"</td>";
          p+="<td>"+xdatos.data.vencimiento+"</td>";

          if (xdatos.data.garantia_vigente=="true") {
            p+="<td>SI"+"</td>";
          }
          else {
            p+="<td>NO"+"</td>";
          }


          p+="<td > <a href='"+base_url+"ventas/garantia/"+xdatos.data.id_venta+"' target='_blank'> <button class='btn btn-info'> <span class='mdi mdi-certificate-outline'></span> PDF </button>  </a>  </td>";

          p+="</tr>";

          $("#da").html(p);


          $("#pr").show('slow/400/fast', function() {

          });
        }
      }
    });

  });
  </script>
</body>
</html>
