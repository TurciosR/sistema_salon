let url = base_url+"corte";
let token = $("#csrf_token_id").val();
var com = "";
$(window).keydown(function(event) {
    if (event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
   if( event.keyCode === 113 ) {
       event.preventDefault();
       $("#btn_add_fact").focus();
        $("#btn_add_fact").removeClass('btn-success').addClass('btn-dark');
   }
  });


$(document).on('change', '#sucursal', function(event) {
  $("#table_producto").html("");
  $("#total").val("0.00");
  $("#total1").val("$0.00");
});

$(document).on('change', '#tipodoc', function(event) {
  var data = $('#tipodoc').select2('data');
  if(data) {
  }
  totales();
});
$(document).ready(function () {
  $('#tipo_corte').select2();
  var complemento ='/get_data';
   com = complemento;
    generar();
});
$(document).on('click', '#btn_consulta', function(event) {
com='/get_data';
  generar();
});
$(document).on('change', '#tipo_corte', function(event) {

  var data = $('#tipo_corte').select2('data');
  var total_fin=$("#total_fin").val();
  if(data) {

    if(data[0].id=="C"){
        $("#total_efectivo").prop("readonly",false);
        $("#total_efectivo").val("");
        $("#total_efectivo").focus();
    }
    if(data[0].id=="X" ){
        $("#total_efectivo").prop("readonly",true);
        $("#total_efectivo").val(total_fin);
        //$("#total_efectivo").focus();
    }
    if(data[0].id=="Z" ){
        $("#total_efectivo").prop("readonly",true);
        $("#total_efectivo").val(total_fin);
        //$("#total_efectivo").focus();
    }
  }


});

$(document).on('change', '#sucursales', function(event) {
  generar();
});
function generar()
{
  let urls=url+'/get_data';

  dataTable = $('#editable').DataTable().destroy()
  dataTable = $('#editable').DataTable({
    "pageLength": 50,
    "serverSide": true,
    "order": [[0, "desc"]],
    "ajax": {
      url: urls,
      type: 'POST',
      data:{
        csrf_test_name:token,
        id_sucursal: $("#sucursales").val(),
        fecha1: $("#fecha1").val(),
        fecha2: $("#fecha2").val(),
      }
    },
    "language": {
        "url": base_url+ "assets/js/scripts/Spanish.json"
    },
    "pagingType": "full_numbers"
  }); // End of DataTable
  //dataTable.ajax.reload();
}

$(document).on('submit', '#form_corte_caja', function(e) {
  e.preventDefault();
  $(this).parsley().validate();
  if ($(this).parsley().isValid()){
    $("#btn_corte_caja").prop("disabled",true)
    save_data_corte();
  }
});
$(document).on('submit', '#form_cierre', function(e) {
  e.preventDefault();
  $(this).parsley().validate();
  if ($(this).parsley().isValid()){
    $("#btn_cierre").prop("disabled",true)
    save_data_cierre();
  }
});



$( '#form_corte_caja' ).on( 'keypress', function( e ) {
       if( e.keyCode === 13 ) {
           e.preventDefault();
       }
   } );

function save_data_cierre(){

  let form = $("#form_cierre");
  let formdata = false;
  if (window.FormData) {
    formdata = new FormData(form[0]);
  }
    $.ajax({
      type: 'POST',
      url: url+'/cierre_turno',
      cache: false,
      data: formdata ? formdata : form.serialize(),

      contentType: false,
      processData: false,
      dataType: 'json',
      success: function (data) {
      //  $("#divh").hide();
        //$("#main_view").show();
        notification(data.type,data.title,data.msg);
        if (data.type == "success") {
            if (data.opsys == 'Linux') {
              $.post("http://"+data.dir_print+"printcorte1.php", {
                totales:data.totales,
                encabezado:data.encabezado,
                cuerpo:data.cuerpo,
                 pie:data.pie,
                });
            }
  		  else {
  			   var direc="http://"+data.dir_print+"printcortewin1.php";
                  $.post(direc, {
                    totales:data.totales,
                      totales:data.totales,
                    encabezado:data.encabezado,
                    cuerpo:data.cuerpo,
                     pie:data.pie,
                      shared_printer_pos:data.dir_print_pos,
                  })
              }
            setTimeout("reload();", 1500);

          }

      }
    });
}
function save_data_corte(){

  var errors = false;
  var error_array = [];
  var array_json = [];
  var cuantos=0;
  /*
  var t_min=$("#t_min").val();
  var t_max=$("#t_max").val();
  var cf_min=$("#cf_min").val();*/



  $("#tabla_devs tr").each(function(index) {
    var tipo_doc= $(this).find("#tipo_doc").val();
    var correlativo= $(this).find("td:eq(1)").text();
    var nombre_doc= $(this).find("td:eq(2)").text();
    var correlativo_afecta= $(this).find("td:eq(3)").text();
    var subtotal_dev= $(this).find("td:eq(4)").text();
    if (isNaN(correlativo)) {
      correlativo = 0;
    }
    if (correlativo>=0 && nombre_doc!=""){
      var obj = new Object();

      obj.tipo_doc = tipo_doc;
      obj.correlativo =correlativo;
      obj.nombre_doc = nombre_doc;
      obj.correlativo_afecta = correlativo_afecta;
      obj.subtotal_dev = subtotal_dev;

      //convert object to json string
      text = JSON.stringify(obj);
          cuantos++;
          array_json.push(text);

    }

  });
  var json_arr = '[' + array_json + ']';
  $("#data_ingreso").val(json_arr);
  /*if (cuantos==0) {
    errors = true;
    error_array.push('Llene los datos de al menos un producto');
  }*/
  //fin serializar datos de tables html
  let form = $("#form_corte_caja");
	let formdata = false;
	if (window.FormData) {
		formdata = new FormData(form[0]);
	}
    $.ajax({
      type: 'POST',
      url: url+'/corte_caja_diario',
      cache: false,
      data: formdata ? formdata : form.serialize(),

      contentType: false,
      processData: false,
      dataType: 'json',
      success: function (data) {
      //  $("#divh").hide();
        //$("#main_view").show();
        notification(data.type,data.title,data.msg);
        if (data.type == "success") {
          if (data.opsys == 'Linux') {
            $.post("http://"+data.dir_print+"printcorte1.php", {
              totales:data.totales,
              encabezado:data.encabezado,
              cuerpo:data.cuerpo,
               pie:data.pie,
              });
          }
    		  else {
    			   var direc="http://"+data.dir_print+"printcortewin1.php";
                $.post(direc, {
                  totales:data.totales,
                  totales:data.totales,
                  encabezado:data.encabezado,
                  cuerpo:data.cuerpo,
                  pie:data.pie,
                  shared_printer_pos:data.dir_print_pos,
                })
          }
          setTimeout("reload();", 1500);
        }
      }
    });
}
$(document).on("keyup", "#total_efectivo", function(){
  var efectivo = parseFloat($('#total_efectivo').val());
  var id_total_general= parseFloat($('#id_total_general').text());
  var  diferencia=0;
  if (isNaN(parseFloat(efectivo))) {
    efectivo = 0;
  }
  if (isNaN(parseFloat(id_total_general))) {
    id_total_general = 0;
  }

  var diferencia = efectivo - id_total_general;
  var diferencia = round(diferencia, 4);
  var diferencia_mostrar = diferencia.toFixed(4);

  if ($('#efectivo').val() != '') {
    $('#id_diferencia').text(diferencia_mostrar);
    $('#diferencia_val').val(diferencia_mostrar);

  }
});

//function to round 2 decimal places
function round(value, decimals) {
  return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
}

//#("saveprint")
function reload() {
	location.href = url;
}

function reload_current() {
	location.reload()
}


function reload_current_des() {
	location.reload()
}
$(document).on('click', '#modal_btn_add', function(event) {
	a = $(this).attr( 'href');
	$('#viewModal .modal-content').load(a);
});

$(document).on('click', '.modal_edit', function(event) {
	$('#viewModal .modal-content').load(url+"/editar/"+$(this).attr('data-id'));
});
//corte admin
$(document).on('click', '#apertura_turno', function(event) {
event.preventDefault();
	Swal.fire({
	  title: "Realizar apertura de turno?",
	  text: "Realizar una apertura de turno!",
	  type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Aceptar',
    cancelButtonText: 'Cancelar',
  }).then((result) => {
      if (result.value) {

	    agregar_turno();
	    //swal("Exito", "Turno iniciado con exito", "error");
	  }
	});
})
function agregar_turno()
{
	var id_apertura = $("#id_apertura").val();
	var id_detalle = $("#id_d_ap1").val();
	$.ajax({
		type:'POST',
		url: url+'/apertura_turno',
    cache: false,
    data:{
      csrf_test_name:token,
      id_detalle: id_detalle,
      id_apertura:id_apertura,
    },
		dataType: 'json',
		success: function(data){
      notification(data.type,data.title,data.msg);
			if(data.type == 'success')
			{
				setInterval("reload();", 1000);
			}
		}
	});
}

$(document).on('click', '.detail', function(event) {
	$('#viewModal .modal-content').load(url+"/detalle/"+$(this).attr('data-id'));
});
//reimprimir corte_detalle
//reimpresion de ticket venta
$(document).on("click", ".printicket", function(){

  var id_corte=$('#id_corte').val();
  $.ajax({
    type: 'POST',
    url: url+'/printdoc',
    data: "id_corte="+id_corte+"&csrf_test_name="+token+"&process=print",
    dataType: 'json',
    success: function (data) {
      notification(data.type,data.title,data.msg);

      if (data.opsys == 'Linux') {
        $.post("http://"+data.dir_print+"printcorte1.php", {
          totales:data.totales,
          encabezado:data.encabezado,
          cuerpo:data.cuerpo,
           pie:data.pie,
          });
      }
      else {
         var direc="http://"+data.dir_print+"printcortewin1.php";
            $.post(direc, {
              totales:data.totales,
              totales:data.totales,
              encabezado:data.encabezado,
              cuerpo:data.cuerpo,
              pie:data.pie,
              shared_printer_pos:data.dir_print_pos,
            })
      }
    }
  });


});
