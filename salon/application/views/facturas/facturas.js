let url = base_url+"facturas";
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
  $("#total1").val("$0.00");
});

$(document).on('change', '#tipodoc', function(event) {
  var data = $('#tipodoc').select2('data');
  if(data) {
  //alert(data[0].id+"-"+data[0].text);
  }

totales();
});
$(document).ready(function () {
  $.fn.modal.Constructor.prototype.enforceFocus = function() {};
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

	$("#form_edit").on('submit', function(e){
		e.preventDefault();
		$(this).parsley().validate();
		if ($(this).parsley().isValid()){
			$("#btn_edit").prop("disabled",true)
			edit_data();
		}
	});

  $("#form_fin").on('submit', function(e){
		e.preventDefault();

		$(this).parsley().validate();


		if ($(this).parsley().isValid()){
			$("#btn_edit #btn_add").prop("disabled",true)
      fin_data();
		}

	});

    $("#scrollable-dropdown-menu #producto").typeahead({
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
  					data: {"query":q,"csrf_test_name":token,id_sucursal: $("#id_sucursal").val()},
  					url:  url+'/get_productos',
  				}).done(function(res){
  					if(res) cba(JSON.parse(res));
  				});
  			},
  			templates:{
  				suggestion:function (data) {
  					var prod=data.producto.split("|");
  					return '<div class="tt-suggestion tt-selectable">'+prod[1]+'</div>';
  				}
  			}
  		}).on('typeahead:selected',onAutocompleted_producto);
  	function onAutocompleted_producto($e, datum) {
  		let prod = datum.producto.split("|");
      let id_producto = prod[0];
      let nombre = prod[1];
      let id_color = prod[2];
      $("#id_producto").val(id_producto);
      new_producto(id_producto,nombre,id_color);
  	}

    $("#scrollable-dropdown-menu #cliente").typeahead({
  			highlight: true,
  		},
  		{
  			limit:100,
  			name: 'cliente',
  			display: function(data) {
  				cli=data.cliente.split("|");
  				return cli[1];
  			},
  			source: function show(q, cb, cba) {
  				$.ajax({
  					type: "POST",
  					data: {"query":q,"csrf_test_name":token},
  					url:  url+'/get_clientes',
  				}).done(function(res){
  					if(res) cba(JSON.parse(res));
  				});
  			},
  			templates:{
  				suggestion:function (data) {
  					var cli=data.cliente.split("|");
  					return '<div class="tt-suggestion tt-selectable">'+cli[1]+'</div>';
  				}
  			}
  		}).on('typeahead:selected',onAutocompleted_cliente);
  	function onAutocompleted_cliente($e, datum) {
  		let cli = datum.cliente.split("|");
  		let id_cliente = cli[0];
  		let nombre = cli[1];
      let clasifica = cli[2];
  		//$("#cliente").attr("readonly", true);
  		$("#id_cliente").val(id_cliente);

      setTimeout(function() {
          get_porcent_client(clasifica);
          //totales();
      }, 1000);

  	}
    function get_porcent_client(clasifica){
      let dataString = "clasifica=" + clasifica;
      $.ajax({
          type: "POST",
          url: url+"/get_porcent_cliente",
        //  data: dataString,
          	data: {"clasifica":clasifica,"csrf_test_name":token},
          dataType: 'json',
          success: function (data) {
              notification(data.type,data.title,data.msg);
              if (data.type == "success") {
                  	$("#porc_clasifica").val(data.porc_clasifica);
                  totales();
              }else {
                $("#porc_clasifica").val('0');
              totales();
              }
          }
      });
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
      url: url+com,
      type: 'POST',
      data:{
        csrf_test_name:token,
        id_sucursal: $("#sucursales").val(),
      }
    },
    "language": {
      "sProcessing": "Procesando...",
      "sLengthMenu": "Mostrar _MENU_ registros",
      "sZeroRecords": "No se encontraron resultados",
      "sEmptyTable": "Ningún dato disponible en esta tabla",
      "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
      "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
      "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
      "sInfoPostFix": "",
      "sSearch": "Buscar:",
      "sUrl": "",
      "sInfoThousands": ",",
      "sLoadingRecords": "Cargando...",
      "oPaginate": {
        "sFirst": "Primero",
        "sLast": "Último",
        "sNext": "Siguiente",
        "sPrevious": "Anterior"
      },
      "oAria": {
        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
      }
    }
  }); // End of DataTable
  //dataTable.ajax.reload();
}

function new_producto(id_producto,nombre,id_color)
{
  $('#totals').show();
	$("#scrollable-dropdown-menu #producto").typeahead("val","");
	let distinto = true;
	monto_ini=0;
  id_factura=0;
  if ($("#proceso").val()=="editar") {
    id_factura = $("#id_factura").val();
  }
  let   porc_clasifica=-1
  if ($("#porc_clasifica").val()!="") {
    porc_clasifica=$("#porc_clasifica").val();
  }
  //  alert(id_producto+" "+nombre+" "+id_color);
  if(id_color=="SERVICIO"){
    //$("#row_serv").show();

    monto_ini=0;
  //  id_factura=0;

    $.ajax({
			type: 'POST',
			url: url+'/detalle_servicio',
			data: "id="+id_producto+"&csrf_test_name="+token+"&id_s="+id_color+"&id_factura="+id_factura,
			dataType: 'json',
			success: function (datax) {
          let stockk="-";
				let fila = "<tr>";
				fila += "<td style='width:5%;'>"+id_producto+"</td>";
				fila += "<td style='width:25%;'><input type='hidden' class='id_producto' value='"+id_producto+"'><input type='hidden' class='id_s' value='"+datax.id_s+"'><input type='hidden' class='nombre' value='"+nombre+"'>"+nombre+"</td>";
        fila += "<td style='width:10%;'><input type='hidden' class='color' value='"+id_color+"'><input type='hidden' class='stock' value='"+stockk+"' style='width:100%;'>"+stockk+"</td>";
        fila += "<td style='width:10%;'><input type='text' class='form-control cantidad numeric' value='' style='width:100%;'></td>";
				fila += "<td style='width:10%;'><input type='hidden' class='form-control costo decimal' value='"+datax.costo+"' style='width:100%;'><input type='hidden' class='form-control precio_minimo decimal' value='"+datax.precio_minimo+"' style='width:100%;'>Pr. Sug $: "+datax.precio_sugerido+"</td>";
				fila += "<td style='width:10%;'><input type='hidden' class='form-control precio_sugerido decimal' value='"+datax.precio_sugerido+"' style='width:100%;'><input type='text' class='form-control precio_base decimal' value='"+datax.precio_sugerido+"' style='width:100%;'></td>";
        fila += "<td style='width:10%;'><input type='text' class='form-control descuento' readonly value='"+monto_ini+"' style='width:100%;'></td>";
				fila += "<td style='width:10%;'><input type='text' class='form-control subtotal' readonly value='"+monto_ini+"' style='width:100%;'></td>";
				fila += "<td style='width:10%;' class='text-center'><a class='btn btn-danger delete_tr' style='color: white'><i class='mdi mdi-trash-can'></i></a></td>";
				fila +="</tr>";
				$("#table_servicio").prepend(fila);
				$(".numeric").numeric({negative:false, decimals:false});
				$(".decimal").numeric({negative:false, decimalPlaces:4});
				$(".sel").select2();
        $(".est").select2();
				$("#table_servicio tr:first").find(".cantidad").focus();
			}
		});


  }
	if(distinto && id_color!="SERVICIO"){
		$.ajax({
			type: 'POST',
			url: url+'/detalle_producto',
			data: "id="+id_producto+"&csrf_test_name="+token+"&id_s="+id_color+"&id_factura="+id_factura,
			dataType: 'json',
			success: function (datax) {

				let fila = "<tr>";
				fila += "<td style='width:5%;'>"+id_producto+"</td>";
				fila += "<td style='width:25%;'><input type='hidden' class='id_producto' value='"+id_producto+"'><input type='hidden' class='id_s' value='"+datax.id_s+"'><input type='hidden' class='nombre' value='"+nombre+"'>"+nombre+"</td>";
        fila += "<td style='width:10%;'><input type='hidden' class='color' value='"+id_color+"'><input type='hidden' class='stock' value='"+datax.stock+"' style='width:100%;'>"+datax.stock+"</td>";
        fila += "<td style='width:10%;'><input type='text' class='form-control cantidad numeric' value='' style='width:100%;'></td>";
				fila += "<td style='width:10%;'><input type='hidden' class='form-control costo decimal' value='"+datax.costo+"' style='width:100%;'><select class='est'><option value='NUEVO'>NUEVO</option><option value='USADO'>USADO</option></select></td>";
				fila += "<td style='width:10%;'>"+datax.precios+"</td>";
        fila += "<td style='width:10%;'><input type='text' class='form-control descuento' readonly value='"+monto_ini+"' style='width:100%;'></td>";

				fila += "<td style='width:10%;'><input type='text' class='form-control subtotal' readonly value='"+monto_ini+"' style='width:100%;'></td>";
				fila += "<td  style='width:10%;' class='text-center'><a class='btn btn-danger delete_tr' style='color: white'><i class='mdi mdi-trash-can'></i></a></td>";
				fila +="</tr>";
				$("#table_producto").prepend(fila);
				$(".numeric").numeric({negative:false, decimals:false});
				$(".decimal").numeric({negative:false, decimalPlaces:4});
				$(".sel").select2();
        $(".est").select2();
				$("#table_producto tr:first").find(".cantidad").focus();
			}
		});

	}
  if(!distinto && id_color!="SERVICIO"){
		notification("Error","Alerta","El producto ya fue agregado");
	}

}

/*keyups de movimiento entre casillas*/
$(document).on("keyup", ".cantidad", function(e){
  var errors = false;
  var error_array = [];
  if ($("#id_cliente").val()=="") {
     errors = true;
    error_array.push('Seleccione un cliente');
  }
  else {
      let id_cliente=$("#id_cliente").val();
      var errors = false;
      var error_array = [];
  }

  let   porc_clasifica=-1
  if ($("#porc_clasifica").val()!="") {
    porc_clasifica=$("#porc_clasifica").val();
  }
  tr = $(this).closest('tr');
  stock = parseInt(tr.find('.stock').val());
  id_producto = parseInt(tr.find('.id_s').val());

  fila = tr;
  existencia = parseInt(fila.find('.stock').val());

  a_cant = parseInt($(this).val());

  if (isNaN(a_cant)) {
    a_cant=0;
  }
  a_asignar = 0;

  $('#table_producto tr').each(function(index) {

    if ($(this).find('.id_s').val() == id_producto) {
      t_cant = parseInt($(this).find('.cantidad').val());
      if (isNaN(t_cant)) {
        t_cant = 0;
      }
      a_asignar = a_asignar + t_cant;
    }
  });
  console.log(existencia);
  console.log(a_asignar);
  if (a_asignar > existencia) {
    val = existencia - (a_asignar - a_cant);
    val = Math.trunc(val);
    val = parseInt(val);
    $(this).val(val);
    setTimeout(function() {
      totales();
    }, 1000);
  } else {
    totales();
  }

  if(e.keyCode == 13 && $(this).val()!="")
  {
    tr.find(".est").select2("open");
  }
});
//keyup para cantidades en table_servicio
$(document).on("keyup", ".precio_base", function(e){
  var precio_base= tr.find(".precio_base").val();
  var precio_sugerido= tr.find(".precio_sugerido").val();//oculto
  var precio_minimo= tr.find(".precio_minimo").val();
  /*
  if (precio_base<precio_minimo){
    tr.find(".precio_base").val(precio_minimo);
  }
  */
  setTimeout(function() {
    totales();
  }, 3000);

});
$(document).on("select2:close", ".est", function(){
  var tr = $(this).parents("tr");
  tr.find(".sel").select2("open");
});

$(document).on("select2:close", ".sel", function(){
  totales();
  $("#producto").focus();
});

function totales(){
  var total = 0,total_producto=0,total_servicio=0,subtotal1=0,subtotal2=0;
	$("#table_producto tr").each(function(){
		var tr  = $(this);
		var servicio  = tr.find(".color").val();
    var costo  = tr.find(".precios").val();
		var cantidad  = tr.find(".cantidad").val();
    var stock  = parseInt(tr.find(".stock").val());
		if(costo == ""){
			costo = 0;
		}
		if(cantidad == ""){
			cantidad = 0;
		}
    if(stock < parseInt(cantidad)){
      cantidad = stock;
      tr.find(".cantidad").val(stock);
    }
    let porc_clasifica=0;
    if ($("#porc_clasifica").val()!="") {
      porc_clasifica=$("#porc_clasifica").val();
    }
		var costo_iva = parseFloat(costo)*1.13;
    var descto =0.00;
    if(parseFloat(porc_clasifica)>0){
         descto = parseInt(cantidad)*parseFloat(costo)*(porc_clasifica/100);
    }
    if(servicio=="SERVICIO"){
      descto=0.00;
    }
    var subtotal1 = parseInt(cantidad)*parseFloat(costo)-descto;
    total_producto+=subtotal1;
		tr.find(".costo_iva").val(costo_iva.toFixed(2));
		tr.find(".subtotal").val(subtotal1.toFixed(2));
    tr.find(".descuento").val(descto.toFixed(2));
	});
  $("#table_servicio tr").each(function(){
    var tr  = $(this);
    var servicio  = tr.find(".color").val();
    var precio_sugerido= tr.find(".precio_sugerido").val();//oculto
    var precio_base= tr.find(".precio_base").val();
    var precio_minimo= tr.find(".precio_minimo").val();
    var cantidad  = tr.find(".cantidad").val();
    if(precio_sugerido == ""){
      precio_sugerido = 0;
    }
    if(cantidad == ""){
      cantidad = 0;
    }
    let porc_clasifica=0;
    var costo_iva = parseFloat(precio_sugerido)*1.13;
    var descto =0;
    if(descto<0)
      descto =0;
    if (parseFloat(precio_base)<parseFloat(precio_minimo)){
      tr.find(".precio_base").val(precio_sugerido);
      precio_base= precio_minimo;
      descto =0;
    }
    if (parseFloat(precio_base)<parseFloat(precio_sugerido) && parseFloat(precio_base)>=parseFloat(precio_minimo) ){
       descto =parseFloat(cantidad)*(parseFloat(precio_sugerido)-parseFloat(precio_base));
    }
    if(parseInt(cantidad)<=0){
        descto =0;
    }
    subtotal2 = parseInt(cantidad)*parseFloat(precio_base);
    total_servicio+=subtotal2;
    tr.find(".costo_iva").val(costo_iva.toFixed(2));
    tr.find(".subtotal").val(subtotal2.toFixed(2));
    tr.find(".descuento").val(descto.toFixed(2));
  });
  total=total_producto+total_servicio;
  $('#total').val(total.toFixed(2));
  $('#total1').val("$"+total.toFixed(2));

  $('#totals .total_final').text("$ "+total.toFixed(2));


  var data = $('#tipodoc').select2('data');
  if(data) {
   // alert(data[0].id+"-"+data[0].text);
    if(data[0].id==3){

      total_s_iva=total-total*0.13 //falta traer el impuesto iva del controller Ventas y  asu vez pasar a cada formulario agregar, editar y finalizar factura pendiente !!!!
      total_iva=total*0.13
      $('.total_s_iva').text("$ "+total_s_iva.toFixed(2));
      $('.total_iva').text("$ "+total_iva.toFixed(2));
    }
  }


}
$(document).on("click", ".delete_tr", function(){
	$(this).parents("tr").remove();
  totales();
});

$(document).on("click", ".delete", function() {
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
    var cantidad = parseInt($(this).find('.cantidad').val());
    var costo = parseFloat($(this).find('.costo').val());
    var est = $(this).find('.est').val();
    var precio_sugerido = parseFloat($(this).find('.precios').val());
    var descuento = parseFloat($(this).find('.descuento').val());
    var precio_final = precio_sugerido-descuento;
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
      obj.descuento = descuento;
      obj.precio_final = precio_final;
      obj.subtotal = subtotal;
      obj.color = color;
      obj.est = est;
      obj.tipo =0;//"PRODUCTO"
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
  $("#table_servicio tr").each(function(index) {
    var id_producto = $(this).find('.id_producto').val();
    var cantidad = parseInt($(this).find('.cantidad').val());
    var costo = parseFloat($(this).find('.costo').val());
    var est = $(this).find('.est').val();
    var precio_sugerido = parseFloat($(this).find('.precio_sugerido').val());
    var descuento = parseFloat($(this).find('.descuento').val());
    var precio_final =  parseFloat($(this).find('.precio_base').val());
    var subtotal = parseFloat($(this).find('.subtotal').val());
    var color = parseFloat($(this).find('.color').val());
    var est="SERVICIO";
    var color=-1;
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
      obj.descuento = descuento;
      obj.precio_final = precio_final;
      obj.subtotal = subtotal;
      obj.color = color;
      obj.est = est;
      obj.tipo ="1";//"SERVICIO"
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
 //alert(json_arr);
	$("#data_ingreso").val(json_arr);
  if (cuantos==0) {
    errors = true;
    error_array.push('Llene los datos de al menos un producto');
  }

  if ($("#id_cliente").val()=="") {
    error_array.push('Seleccione un cliente');
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
          $('#viewModal .modal-content').load(urls);
            setTimeout(setselect,1000);
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


function edit_data(){
  $("#divh").show();
  $("#main_view").hide();

  var errors = false;
  var error_array = [];
  var array_json = [];
  var cuantos = 0;
  var total1 = $('#total1').val();
  var total =  $('#total').val();


  $("#table_producto tr").each(function(index) {
    var id_producto = $(this).find('.id_producto').val();
    var cantidad = parseInt($(this).find('.cantidad').val());
    var costo = parseFloat($(this).find('.costo').val());
    var est = $(this).find('.est').val();
    var precio_sugerido = parseFloat($(this).find('.precios').val());
    var descuento = parseFloat($(this).find('.descuento').val());
    var precio_final = precio_sugerido-descuento;
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
      obj.descuento = descuento;
      obj.precio_final = precio_final;
      obj.subtotal = subtotal;
      obj.color = color;
      obj.est = est;
      obj.tipo ="0";//"PRODUCTO"
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
  $("#table_servicio tr").each(function(index) {
    var id_producto = $(this).find('.id_producto').val();
    var cantidad = parseInt($(this).find('.cantidad').val());
    var costo = parseFloat($(this).find('.costo').val());
    var est = $(this).find('.est').val();
    var precio_sugerido = parseFloat($(this).find('.precio_sugerido').val());
    var descuento = parseFloat($(this).find('.descuento').val());
    var precio_final =  parseFloat($(this).find('.precio_base').val());
    var subtotal = parseFloat($(this).find('.subtotal').val());
    var color = parseFloat($(this).find('.color').val());
    var est="SERVICIO";
    var color=-1;
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
      obj.descuento = descuento;
      obj.precio_final = precio_final;
      obj.subtotal = subtotal;
      obj.color = color;
      obj.est = est;
      obj.tipo ="1";//"SERVICIO"
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

  if ($("#id_cliente").val()=="") {
    error_array.push('Seleccione un cliente');
  }

  if (errors==false) {
    let form = $("#form_edit");
    let formdata = false;
    if (window.FormData) {
      formdata = new FormData(form[0]);
    }
    $.ajax({
      type: 'POST',
      url: url+'/editar',
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

function fin_data(){
//  $("#divh").show();
//  $("#main_view").hide();
  var id_factura=	$("#id_factura").val();

  var errors = false;
  var error_array = [];
  var array_json = [];
  var cuantos = 0;
  var total1 = $('#total1').val();
  var total =  $('#total').val();

  $("#table_producto tr").each(function(index) {
    var id_producto = $(this).find('.id_producto').val();
    var cantidad = parseInt($(this).find('.cantidad').val());
    var costo = parseFloat($(this).find('.costo').val());
    var est = $(this).find('.est').val();
    var precio_sugerido = parseFloat($(this).find('.precios').val());
    var descuento = parseFloat($(this).find('.descuento').val());
    var precio_final = precio_sugerido-descuento;
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
      obj.descuento = descuento;
      obj.precio_final = precio_final;
      obj.subtotal = subtotal;
      obj.color = color;
      obj.est = est;
      obj.tipo ="0";//"PRODUCTO"
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
  $("#table_servicio tr").each(function(index) {
    var id_producto = $(this).find('.id_producto').val();
    var cantidad = parseInt($(this).find('.cantidad').val());
    var costo = parseFloat($(this).find('.costo').val());
    var est = $(this).find('.est').val();
    var precio_sugerido = parseFloat($(this).find('.precio_sugerido').val());
    var descuento = parseFloat($(this).find('.descuento').val());
    var precio_final =  parseFloat($(this).find('.precio_base').val());
    var subtotal = parseFloat($(this).find('.subtotal').val());
    var color = parseFloat($(this).find('.color').val());
    var est="SERVICIO";
    var color=-1;
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
      obj.descuento = descuento;
      obj.precio_final = precio_final;
      obj.subtotal = subtotal;
      obj.color = color;
      obj.est = est;
      obj.tipo ="1";//"SERVICIO"
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

  if ($("#id_cliente").val()=="") {
    error_array.push('Seleccione un cliente');
  }

  if (errors==false) {
    let form = $("#form_fin");
    let formdata = false;
    if (window.FormData) {
      formdata = new FormData(form[0]);
    }
    var urls=url+"/get_data_cliente/"+id_factura;
    $.ajax({
      type: 'POST',
      url: url+'/finalizar',
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
          //Open the model
        //  var tipodoc =  $('#tipodoc').val();
        //  alert(tipodoc)
          $('#viewModal .modal-content').load(urls);
            setTimeout(setselect,1000);
        }
        else {
          //$("#divh").hide();
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
}function setselect(){
  $('#giro').select2({
  dropdownParent: $('#viewModal')
});
//alert("select2")
}
$(document).on('submit', '#form_edit_cte', function(e) {
  e.preventDefault();
  $(this).parsley().validate();

  if ($(this).parsley().isValid()){
    $("#btn_edit").prop("disabled",true)
    save_data_cte();
  }
});
function save_data_cte(){
	let form = $("#form_edit_cte");
	let formdata = false;
	if (window.FormData) {
		formdata = new FormData(form[0]);
	}
  id_factura=$("#id_vta").val();
  id_cliente=$("#id_client").val();
  var efectivo = parseFloat($('#efectivo').val());
  var cambio= parseFloat($('#cambio').val());
    $.ajax({
      type: 'POST',
      url: url+'/up_data_client',
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
          //Open the model
            alert("actualizado:"+data.total_letras)
            alert(data.opsys)
            if (data.opsys == 'Linux') {
              if (data.tipodoc == '1') {
              $.post("http://"+data.dir_print+"printpos1.php", {
                headerfactura: data.headerfact,
                datosfactura: data.facturar,
                total_letras:data.total_letras,
                efectivo: efectivo,
                cambio: cambio,
                headers:data.header,
              //  footers:footers,
                });
            }
            if (data.tipodoc == '2') {
            $.post("http://"+data.dir_print+"printcof1.php", {
              headerfactura: data.headerfact,
              datosfactura: data.facturar,
              total_letras:data.total_letras,
              efectivo: efectivo,
              cambio: cambio,
              headers:data.header,
            //  footers:footers,
              });
          }
          if (data.tipodoc == '3') {
          $.post("http://"+data.dir_print+"printccf1.php", {
            headerfactura: data.headerfact,
            datosfactura: data.facturar,
            total_letras:data.total_letras,
            efectivo: efectivo,
            cambio: cambio,
            headers:data.header,
          //  footers:footers,
            });
        }
          }
              if (data.opsys == 'Windows') {
                $.post("http://"+dir_print+"printposwin1.php", {
                datosfactura: data.facturar,
                efectivo: efectivo,
                cambio: cambio,
                total_letras:data.total_letras,
                //shared_printer_pos:shared_printer_pos,
                //     headers:headers,
                //     footers:footers,
                     })

            }
              setTimeout("reload();", 1500);
          }

      }
    });
}


$(document).on("keyup", "#efectivo", function(){
  var efectivo = parseFloat($('#efectivo').val());


  var efectivo = parseFloat($('#efectivo').val());
  var totalfinal = parseFloat($('#totfin').val());

  if (isNaN(parseFloat(efectivo))) {
    efectivo = 0;
  }
  if (isNaN(parseFloat(totalfinal))) {
    totalfinal = 0;
  }

  var cambio = efectivo - totalfinal;
  var cambio = round(cambio, 2);
  var cambio_mostrar = cambio.toFixed(2);

  if ($('#efectivo').val() != '' && efectivo >= totalfinal) {
    $('#cambio').val(cambio_mostrar);

  } else {
    $('#cambio').val('0');
    if (efectivo < totalfinal) {
    }
  }


});
//function to round 2 decimal places
function round(value, decimals) {
  return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
}
function actualiza_client(){

}
//#("saveprint")
function reload() {
	location.href = url;
}

function reload_current() {
	location.reload()
}

$(document).on("click",".delete_row", function(event)
{
    event.preventDefault()
    let id_row = $(this).attr("id");
    let dataString = "id=" + id_row+"&csrf_test_name="+token;
    Swal.fire({
        title: 'Alerta!!',
        text: "Estas seguro de eliminar este registro?!",
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
                url: url+"/delete",
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


$(document).on("click", ".delete_trd", function(){
  $(this).parents("tr").remove();
});


function reload_current_des() {
	location.reload()
}

$(document).on('click', '.detail', function(event) {
	$('#viewModal .modal-content').load(url+"/detalle/"+$(this).attr('data-id'));
});

$(document).on('click', '.status_change', function(event) {
	$('#viewModal .modal-content').load(url+"/change_state/"+$(this).attr('data-id'));
});

$(document).on('click', '.change_s', function(event) {
  let id_row = $(this).attr("id_v");
  var id_estado  = $(".estado").val();
  let dataString = "id=" + id_row+"&csrf_test_name="+token+"&id_estado="+id_estado;
  $.ajax({
      type: "POST",
      url: url+"/change",
      data: dataString,
      dataType: 'json',
      success: function (data) {
          notification(data.type,data.title,data.msg);
          if (data.type == "success") {
              setTimeout("reload();", 1500);
          }
      }
  });
});
