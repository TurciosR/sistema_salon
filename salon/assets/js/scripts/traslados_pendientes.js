let url = base_url+"traslados_pendientes";
let token = $("#csrf_token_id").val();
var com = "";
$(window).keydown(function(event) {
    if (event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });

$(document).on('change', '#sucursal', function(event) {
  $("#table_producto").html("");
  $("#total").val("0.00");
});
$(document).ready(function () {
  //alert("aqui");
  $(".sel").select2();
  $(".est").select2();
  $(".numeric").numeric({negative:false, decimals:false});
  $(".decimal").numeric({negative:false, decimalPlaces:4});
    var complemento ='/get_data';
    com = complemento;
  generar();

    $("#form_add").on('submit', function(e){
        e.preventDefault();
        $(this).parsley().validate();
        if ($(this).parsley().isValid()){
            $("#btn_add").prop("disabled",true)
            save_data();
        }
    });
});
$(document).on('change', '#sucursales', function(event) {
  generar();
});
function generar()
{
  //alert("aqui");
  dataTable = $('#editable').DataTable().destroy()
  dataTable = $('#editable').DataTable({
    "pageLength": 50,
    "serverSide": true,
    "order": [[0, "desc"]],
    "ajax": {
      url: url+com,
      type: 'POST',
      data:{
        csrf_test_name:token,
        id_sucursal: $("#sucursales").val(),
      }
    },
    "language": {
        "url": base_url+ "assets/js/scripts/Spanish.json"
    },
    "pagingType": "full_numbers"
  }); // End of DataTable
  //dataTable.ajax.reload();
}
function save_data(){
  $("#divh").show();
    $("#main_view").hide();

  var errors = false;
  var error_array = [];
  var array_json = [];
  var cuantos = 0;
    $("#table_producto tr").each(function(index) {
    var id_producto = $(this).find('.id_producto').val();
    var cantidad = parseInt($(this).find('.cantidad').val());
    var costo = parseFloat($(this).find('.costo').val());
    var est = $(this).find('.est').val();
    var precio_sugerido = parseFloat($(this).find('.precios').val());
    var subtotal = parseFloat($(this).find('.subtotal').val());
    var color = parseFloat($(this).find('.color').val());

    if (isNaN(costo)) {
      costo = 0;
      errors = true;
      error_array.push('No. '+id_producto+' hay un costo vacio');
    }
    if (isNaN(precio_sugerido)) {
      costo = 0;
      errors = true;
      error_array.push('No. '+id_producto+' hay un precio sugerido vacio');
    }

        if (!isNaN(cantidad) && cantidad>0)
        {
            var obj = new Object();
            obj.id_producto = id_producto;
      obj.cantidad = cantidad;
      obj.costo = costo;
      obj.precio_sugerido = precio_sugerido;
      obj.subtotal = subtotal;
      obj.color = color;
      obj.est = est;
            //convert object to json string
            text = JSON.stringify(obj);
            array_json.push(text);
      cuantos++;
        }
    else {
      errors = true;
      error_array.push('No. '+id_producto+' hay una cantidad con valor cero o vacia');
    }
    });
    var json_arr = '[' + array_json + ']';
    $("#data_ingreso").val(json_arr);
  if (cuantos==0) {
    errors = true;
    error_array.push('Llene los datos de al menos un producto');
  }

  if ($("#sucursal").val()==$("#sucursal_destino").val()) {
    errors = true;
    error_array.push('El origen y el destino no pueden ser iguales');
  }


  if (errors==false) {
    let form = $("#form_add");
    let formdata = false;
    if (window.FormData) {
      formdata = new FormData(form[0]);
    }
    $.ajax({
      type: 'POST',
      url: url+'/agregar',
      cache: false,
      data: formdata ? formdata : form.serialize(),
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function (data) {
        $("#divh").hide();
        $("#main_view").show();
        notification(data.type,data.title,data.msg);
        if (data.type == "success") {
          setTimeout("reload();", 1500);
        }
        else {
          $("#divh").hide();
          $("#main_view").show();
          $("#btn_add").removeAttr("disabled");
        }
      }
    });
  }
  else {
    notification("Error","Error en formulario",error_array.join(",<br>"));
    $("#btn_add").removeAttr("disabled");
    $("#divh").hide();
    $("#main_view").show();
  }
}
$(document).on('click', '.aceptar_traslado', function(event) {
  var id = $(this).attr("id_traslado");
  var sucursal_destino = $(this).attr("sucursal_destino");
  var sucursal_despacho = $(this).attr("sucursal_despacho");

  var dataString = "id="+id+"&sucursal_despacho="+sucursal_despacho+"&sucursal_destino="+sucursal_destino+"&csrf_test_name="+token;
  //alert(id);
  Swal.fire({
      title: 'Alerta!!',
      text: "Estas seguro de aceptar este traslado?!",
      type: 'error',
      target:'#page-top',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Aceptar',
      cancelButtonText: 'Cancelar',
  }).then((result) => {
      if (result.value) {

          $.ajax({
              type: "POST",
              url: url+"/aceptar_traslado",
              data: dataString,
              dataType: 'json',
              success: function (data) {
                  notification(data.type,data.title,data.msg);
                  if (data.type == "success") {
                      setTimeout("reload();", 1500);
                  }
              }
          });

      }
  });
});
$(document).on('click', '.anular_traslado', function(event) {
  var id = $(this).attr("id_traslado");

  var dataString = "id="+id+"&csrf_test_name="+token;
  //alert(id);
  Swal.fire({
      title: 'Alerta!!',
      text: "Estas seguro de anular este traslado?!",
      type: 'error',
      target:'#page-top',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Aceptar',
      cancelButtonText: 'Cancelar',
  }).then((result) => {
      if (result.value) {

          $.ajax({
              type: "POST",
              url: url+"/anular_traslado",
              data: dataString,
              dataType: 'json',
              success: function (data) {
                  notification(data.type,data.title,data.msg);
                  if (data.type == "success") {
                      setTimeout("reload();", 1500);
                  }
              }
          });

      }
  });
});
$(document).on('click', '.detail', function(event) {
    $('#viewModal .modal-content').load(url+"/detalle/"+$(this).attr('data-id'));
});
function reload() {
    location.href = url;
}
