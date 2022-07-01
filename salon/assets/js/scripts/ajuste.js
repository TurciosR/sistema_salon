let url = base_url+"ajuste";
let token = $("#csrf_token_id").val();
var com = "";
$(window).keydown(function(event) {
  if (event.keyCode == 13) {
    event.preventDefault();
    return false;
  }
});

$(document).ready(function() {
    generar();
    $(".sel").select2();
    $(".numeric").numeric({negative:false, decimals:false});
    $(".decimal").numeric({negative:false, decimalPlaces:4});

    $("#form_add").on('submit', function(e){
  		e.preventDefault();
  		$(this).parsley().validate();
  		if ($(this).parsley().isValid()){
  			$("#btn_add").prop("disabled",true)
  			save_data();
  		}
  	});

    $("#scrollable-dropdown-menu #producto_des").typeahead({
      highlight: true,
    },
    {
      limit:100,
      name: 'producto',
      display: function(data) {
        prod=data.producto.split("|");
        return prod[1];
      },
      source: function show(q, cb, cba) {
        $.ajax({
          type: "POST",
          data: {"query":q,"csrf_test_name":token, "id_sucursal": $("#sucursal").val()},
          url:  url+'/get_productos',
        }).done(function(res){
          if(res) cba(JSON.parse(res));
        });
      },
      templates:{
        suggestion:function (data) {
          var prod=data.producto.split("|");
          return '<div class="tt-suggestion tt-selectable">'+prod[2]+" "+prod[3]+" "+prod[4]+'</div>';
        }
      }
    }).on('typeahead:selected',onAutocompleted_producto);
    function onAutocompleted_producto($e, datum) {
      let prod = datum.producto.split("|");
      let id_producto = prod[0];
      let nombre = prod[2]+" "+prod[3]+" "+prod[4];
      let id_color = prod[1];
      $("#id_producto").val(id_producto);
      new_producto_des(id_producto,nombre,id_color);
    }

});


$(document).on('change', '#sucursales', function(event) {
  generar();
});
function generar()
{
  dataTable = $('#editable').DataTable().destroy()
  dataTable = $('#editable').DataTable({
    "pageLength": 50,
    "serverSide": true,
    "order": [[0, "desc"]],
    "ajax": {
      url: url+"/get_data_ajuste",
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


function new_producto_des(id_producto,nombre,id_color)
{
  $("#scrollable-dropdown-menu #producto_des").typeahead("val","");
  let distinto = true;
  var id_sucursal = $("#sucursal").val();
  $.ajax({
    type: 'POST',
    url: url+'/detalle_producto',
    data: "id="+id_producto+"&csrf_test_name="+token+"&id_s="+id_color+"&id_sucursal="+id_sucursal,
    dataType: 'json',
    success: function (datax) {
      if(datax.stock>=0)
      {
        if ($("#table_producto tr").length > 0)
        {
          $("#table_producto tr").each(function(){
            let id_p = $(this).find(".id_s").val();
            if(datax.id_s == id_p)
            {
              distinto = false
            }
          });
        }
        if(distinto){
          let fila = "<tr>";
          fila += "<td>"+id_producto+"</td>";
          fila += "<td><input type='hidden' class='id_producto' value='"+id_producto+"'><input type='hidden' class='id_s' value='"+datax.id_s+"'><input type='hidden' class='nombre' value='"+nombre+"'>"+nombre+"</td>";
          fila += "<td><input type='hidden' class='color' value='"+id_color+"'><input type='text' class='form-control stockd' value='"+datax.stock+"' style='width:100%;' readonly>";
          fila += "<td><input type='text' class='form-control cantidadd numeric' value='' style='width:100%;'>";
          fila += "<input type='hidden' class='form-control costod' value='"+datax.costo+"'></td>";
          fila += "<td>"+datax.precios+"</td>";
          //fila += "<td><input type='text' class='form-control costo_iva' readonly value='"+datax.costo_iva+"' style='width:100%;'></td>";
          fila += "<td><input type='text' class='form-control subtotald' readonly value='0' style='width:100%;'></td>";
          fila += "<td class='text-center'><a class='btn btn-danger delete_trd' style='color: white'><i class='mdi mdi-trash-can'></i></a></td>";
          fila +="</tr>";
          $("#table_producto").prepend(fila);
          $(".numeric").numeric({negative:false, decimals:false});
          $(".decimal").numeric({negative:false, decimalPlaces:4});
          $(".sel").select2();
          $("#table_producto tr:first").find(".cantidadd").focus();
        }else{
          notification("Error","Alerta","El producto ya fue agregado");
        }
      }
    }
  });
}
$(document).on("keyup", ".cantidadd", function(e){
  totalesd();
  var tr = $(this).parents("tr");
  if(e.keyCode == 13 && $(this).val()!="")
  {
    tr.find(".sel").select2("open");
  }
});
$(document).on("select2:close", ".sel", function(){
  totalesd();
  $("#producto_des").focus();
});
function totalesd()
{
  var total = 0;
  $("#table_producto tr").each(function(){
    var tr  = $(this);
    var costo  = tr.find(".sel").val();
    var cantidad  = tr.find(".cantidadd").val();
    var stock  = parseInt(tr.find(".stockd").val());
    if(costo == "")
    {
      costo = 0;
    }
    if(cantidad == "")
    {
      cantidad = 0;
    }
    /*if(stock < parseInt(cantidad))
    {
      cantidad = stock;
      tr.find(".cantidadd").val(stock);
    }*/
    var subtotal = parseInt(cantidad)*parseFloat(costo);
    total+=subtotal;
    //tr.find(".costo_iva").val(costo_iva.toFixed(2));
    tr.find(".subtotald").val(subtotal.toFixed(2));
  });
  $('#total').val(total.toFixed(2));
}
$(document).on("click", ".delete_trd", function(){
  $(this).parents("tr").remove();
});

function save_data(){
  $("#divh").show();
	$("#main_view").hide();

  var errors = false;
  var error_array = [];
  var array_json = [];
  var cuantos = 0;
	$("#table_producto tr").each(function(index) {
    var id_producto = $(this).find('.id_producto').val();
    var cantidad = parseInt($(this).find('.cantidadd').val());
    var costo = parseFloat($(this).find('.costod').val());
    var precio_sugerido = parseFloat($(this).find('.sel').val());
    var subtotal = parseFloat($(this).find('.subtotald').val());
    var color = parseFloat($(this).find('.color').val());

		if (!isNaN(cantidad))
		{
			var obj = new Object();
			obj.id_producto = id_producto;
      obj.cantidad = cantidad;
      obj.costo = costo;
      obj.precio_sugerido = precio_sugerido;
      obj.subtotal = subtotal;
      obj.color = color;
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
          $("#btn_add").removeAttr("disabled");
          $("#divh").hide();
        	$("#main_view").show();
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

function reload() {
	location.href = url+"/admin";
}

function reload_current() {
	location.reload()
}

$(document).on('click', '.detail_aj', function(event) {
	$('#viewModal .modal-content').load(url+"/detalle_aj/"+$(this).attr('data-id'));
});
$(document).on('change', '#sucursal', function(event) {
  $("#table_producto").html("");
  $("#total").val("0.00");
});
