let url = base_url+"productos";
let urlColor = base_url+"colores";
let token = $("#csrf_token_id").val()

$(window).keydown(function(event) {
  if (event.keyCode == 13) {
    event.preventDefault();
    return false;
  }
});


$(document).on('click', '.detail', function(event) {
  $('#viewModal .modal-content').load(url+"/detalle/"+$(this).attr('data-id'));
});



$(document).ready(function () {
  $("#anio_inicio").numeric({decimal:false, negative:false});
  $("#anio_fin").numeric({decimal:false, negative:false});
  mostrarColores();
  $('#editable').DataTable({
    "pageLength": 50,
    "serverSide": true,
    "order": [[0, "asc"]],
    "ajax": {
      url: url+'/get_data',
      type: 'POST',
      data:{
        csrf_test_name:token
      }
    },
    "language": {
      "url": base_url+ "assets/js/scripts/Spanish.json"
    },
    "pagingType": "full_numbers"
  }); // End of DataTable


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

  $("#form_add_color_express").on('submit', function(e){
    e.preventDefault();
    $(this).parsley().validate();
    if ($(this).parsley().isValid()){
      //$("#btn_add").prop("disabled",true)
      save_data_color();
    }
  });
  //al cambiar la marca, se listan los modelos relacionados a esta marca
  $("#marca").change(function()
	{
		$("#modelo *").remove();
		$("#select2-modelo-container").text("");
		$.ajax({
			url:url+"/get_modelos",
			type: "POST",
			data: {
				id_marca: $("#marca").val(),
				csrf_test_name:token
			},
			success: function(opciones)
			{
				$("#select2-modelo-container").text("Seleccione");
				$("#modelo").html(opciones);
				$("#modelo").val("");
			}
		})
	});




  $('.input-images-2').imageUploader({
    imagesInputName: 'photos',
    preloadedInputName: 'old'
  });

  $("#scrollable-dropdown-menu #proveedor_search").typeahead({
    highlight: true,
  },
  {
    limit:100,
    name: 'proveedor',
    display: function(data) {
      prod=data.proveedor.split("|");
      return prod[1];
    },
    source: function show(q, cb, cba) {
      $.ajax({
        type: "POST",
        data: {"query":q,"csrf_test_name":token},
        url:  url+'/get_proveedor_autocomplete',
      }).done(function(res){
        if(res) cba(JSON.parse(res));
      });
    },
    templates:{
      suggestion:function (data) {
        var prod=data.proveedor.split("|");
        return '<div class="tt-suggestion tt-selectable">'+prod[1]+'</div>';
      }
    }
  }).on('typeahead:selected',onAutocompleted_proveedor);
  function onAutocompleted_proveedor($e, datum) {
    let prod = datum.proveedor.split("|");
    let id_proveedor = prod[0];
    let nombre = prod[1];
    $("#id_proveedor").val(id_proveedor);
    new_proveedor(id_proveedor,nombre)
  }
  //Galeria de imagenes
  if($('#blueimp-gallery').lenght>0){
    var gallery= blueimp.Gallery(
    document.getElementById('links').getElementsByTagName('a'),{
      container: '#blueimp-gallery',
      carousel: true,
      onslide: function (index, slide) {
                var unique_id = this.list[index].getAttribute('data-unique-id');
                console.log(unique_id);
      }
    });
  }
});




$(document).on("click","#btn_add_col", function(evt){
  if($("#color").val() != "")
  {
    add_color();
  }
  else {
    notification('Error', 'Advertencia', 'Debe ingresar un color');
  }
});
function add_color()
{
  var color = $("#color option:selected").text();
  var exis = 0;
  var nc=0;
  $("#colores tr").each(function()
  {

    if($(this).find(".colora").text() == color)
    {
      exis = 1;
    }
    nc++;
  });
  if(!exis)
  {
    var tr = "<tr id='"+nc+"'>";
    tr += "<td class='colora'>"+color+"</td>";
    tr += "<td class='text-center'><a class='btn btn-danger delete_tr' style='color: white'><i class='mdi mdi-trash-can'></i></a></td>";
    $("#colores").append(tr);
    //$("#color").val("");
    //$("#color").focus();
  }
  else {
    notification('Error','Advertencia','Ya se agrego este color');
    //$("#color").val("");
    //$("#color").focus();
  }
}
$(document).on("click","#seguro2", function(event)
{
  if($(this).is(":checked"))
  {
    $(".preciiseg").attr("hidden",true);
  }
});
$(document).on("click","#seguro1", function(event)
{
  if($(this).is(":checked"))
  {
    $(".preciiseg").removeAttr("hidden");
  }
});

$(document).on("click","#imei2", function(event)
{
  if($(this).is(":checked"))
  {
    $(".imeis").attr("hidden",true);
  }
});
$(document).on("click","#imei1", function(event)
{
  if($(this).is(":checked"))
  {
    $(".imeis").removeAttr("hidden");
  }
});

$(document).on("click",".state_change", function(event)
{
  event.preventDefault()
  let id = $(this).attr("id");
  let data = $(this).attr("data-state");
  let dataString = "id=" + id+"&csrf_test_name="+token;
  Swal.fire({
    title: 'Alerta!!',
    text: "Estas seguro de "+ data+" este registro?!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si,'+data,
    cancelButtonText: 'Cancelar',
  }).then((result) => {
    if (result.value) {
      $.ajax({
        type: "POST",
        url: url+"/state_change",
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

function new_proveedor(id_proveedor,nombre){
  let distinto = false;
  if ($("#table_proveedor tr").length > 0){
    $("#table_proveedor tr").each(function(){
      let id_p = $(this).find(".id_proveedor").val();
      if(id_proveedor === id_p) distinto = false
    });
  }else distinto =true

  if(distinto===true){
    let fila = "<tr>";
    fila += "<td><input type='hidden' class='id_pp' value='0'><input type='hidden' class='id_proveedor' value='"+id_proveedor+"'><input type='hidden' class='nombre' value='"+nombre+"'>"+nombre+"</td>";
    fila += "<td class='text-center'><a class='btn btn-danger delete_tr1' style='color: white'><i class='mdi mdi-trash-can'></i></a></td>";
    fila +="</tr>";
    $("#table_proveedor").append(fila);
  }else{
    notification("Error","Alerta","El proveedor ya fue agregado");
  }

}
$(document).on("click", "#btn_proveedor", function(e) {
  e.preventDefault()
  $("#divh").show();
  $("#main_view").hide();
  let id_cliente = $("#id_cliente").val();
  let id_producto = $("#id_producto").val();

  let data = {
    id_cliente:id_cliente,
    id_producto:id_producto,
    proveedores: [],
    csrf_test_name:token
  };
  if ($("#table_proveedor tr").length > 0){
    $("#table_proveedor tr").each(function(){
      let id_pp = $(this).find(".id_pp").val();
      let nombre = $(this).find(".nombre").val();
      let id_proveedor = $(this).find(".id_proveedor").val();
      data.proveedores.push({
        "id_pp" : id_pp,
        "nombre" : nombre,
        "id_proveedor" : id_proveedor,
      });
    })
    $.ajax({
      type:'POST',
      url:url+"/proveedores",
      data: data,
      dataType: 'json',
      success: function (data) {
        $("#divh").hide();
        $("#main_view").show();
        notification(data.type,data.title,data.msg);
        if (data.type == "success") {
          setTimeout("reload();", 1500);
        }
      }
    });
  }else{
    $("#divh").hide();
    $("#main_view").show();
    notification("Warning","Alerta","Ingresa al menos un proveedor");
  }
});
$(document).on("click", ".delete_tr", function(){
	var colora = $(this).parents("tr").find(".colora").text();
	let id_producto = $("#id_producto").val();
	let rowId = $(this).parents("tr").attr("id");
	  var exito=-1;
	   $.ajax({
        type: "POST",
       // url: url+"/eliminar_color",
        url: url+"/get_idColor",
        data: {id:id_producto,color:colora,csrf_test_name:token},
        dataType: 'json',
        success: function (data) {
        notification(data.type,data.title,data.msg);
        if (data.type == "success") {

			  $('#'+rowId).remove();


          }
        }
      });
     if (exito>0)
      $(this).parents("tr").remove();

});
$(document).on("click", ".delete_proveedor", function(e){

  let id = $(this).data("id");
  let tr = $(this).parents('tr').index();
  Swal.fire({
    title: 'Alerta!!',
    text: "Estas seguro de eliminar este proveedor?!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si',
    cancelButtonText: 'Cancelar',
  }).then((result) => {
    if (result.value) {
      $.ajax({
        type: "POST",
        url: url+"/eliminar_proveedor",
        data: {id:id,csrf_test_name:token},
        dataType: 'json',
        success: function (data) {
          notification(data.type,data.title,data.msg);
          if (data.type == "success") {
            /*$("#direccion_table tr").eq(tr).remove();*/
            setTimeout("reload_current();", 1500);
          }
        }
      });
    }
  });
});

$(document).on("click",".delete_row", function(event)
{
  event.preventDefault()
  let id_row = $(this).attr("id");
  let dataString = "id=" + id_row+"&csrf_test_name="+token;
  Swal.fire({
    title: 'Alerta!!',
    text: "Estas seguro de eliminar este regitro?!",
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

$(document).on("click", ".delete", function() {
  $(this).parents("tr").remove();
});

function save_data(){
  $("#divh").show();
  $("#main_view").hide();
  var errors = false;
  var error_array = [];
  var array_json = new Array();
  var costo_s_iva = 0;
  var costo_c_iva = 0;
  var precio_sugerido = 0;
    var modelo = $("#modelo").val();
  $("#precios tr").each(function(index) {
    var desc = $(this).find("#desc_td").val();
    var costo = $(this).find("#costo_td").val();
    var costo_iva = $(this).find("#costo_td_iva").val();
    var idpreciolista = $(this).find(".lista_pr").val();
    var preciolista = $(this).find("#preciolista").val();
    var ganancia = $(this).find(".ganancia_td").val();
    var precio = $(this).find("#precio_td").val();
    var precio_iva = $(this).find("#precio_td_iva").val();
    if(index == 0)
    {
      costo_s_iva = costo;
      costo_c_iva = costo_iva;
      precio_sugerido = precio_iva;
    }
    if (isNaN(preciolista) ||preciolista=="" || parseFloat(preciolista)<=0) {
      errors = true;
      //alert(errors)
      error_array.push('En :'+modelo+" "+desc+' hay un precio  vacio o con valor cero');
    }
    if (ganancia && preciolista)
    {
      var obj = new Object();
      obj.desc = desc;
      obj.costo = costo;
      obj.costo_iva = costo_iva;
      obj.preciolista = preciolista;
      obj.idpreciolista = idpreciolista;
      obj.precio = precio;
      obj.precio_iva = precio_iva;
      obj.ganancia = ganancia;
      //convert object to json string
      text = JSON.stringify(obj);
      array_json.push(text);
    }
  });
  var json_arr = '[' + array_json + ']';
  $("#preciosg").val(json_arr);

  var array_jsonc = new Array();
  var ncolors=0;
  $("#colores tr").each(function(index) {

    var colora = $(this).find(".colora").text();
    if (colora!="" ||  colora.trim().length > 1) {
      var obj = new Object();
      obj.colora = colora;
      //convert object to json string
      text = JSON.stringify(obj);
      array_jsonc.push(text);
      ncolors++;
    }
    else{
      errors = true;
      error_array.push('no ha agregado colores al producto');
    }
  });
  if(ncolors==0){
    var obj = new Object();
    obj.colora = "GENERICO";
    //convert object to json string
    text = JSON.stringify(obj);
    array_jsonc.push(text);
    ncolors++;
  }

  var json_arrc = '[' + array_jsonc + ']';

  $("#coloresg").val(json_arrc);
  $("#costo_s_iva").val(costo_s_iva);
  $("#costo_c_iva").val(costo_c_iva);
  $("#precio_sugerido").val(precio_sugerido);

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
        }else{
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
$(document).on("click","#cesc1, #cesc2", function()
{
//  precios();
});
$(document).on('click', '.exento_iva', function() {
  //var exento_iva = $("input:radio[name=exento_iva]:checked").val();
  precios();
	//alert(exento_iva);
  //$("#tb_recibido").find('.seluno').iCheck('check');
});
function precios()
{
  var costo = $("#ultcosto").val();
  //var id_producto = $("#ultcosto").val();
  var cesc = $("#cesc1").is(":checked");
  var exento_iva = $("input:radio[name=exento_iva]:checked").val();
  //alert(exento_iva);
  var process = "precios";
  var idprod= $("#id_producto").val();
  if(costo != "")
  {
    $.ajax({
      type : "POST",
      url : url+'/precios',
      data : "costo="+costo+"&cesc="+cesc+"&id_producto="+idprod+"&exento_iva="+exento_iva+"&csrf_test_name="+token,
      success : function(datax)
      {
        $("#precios").html(datax);
      }
    });
  }

}

function save_data_color(){
  //alert("aqui");
	$("#divh").show();
	$("#main_view").hide();
	let form = $("#form_add_color_express");
	let formdata = false;
	if (window.FormData) {
		formdata = new FormData(form[0]);
	}
	$.ajax({
		type: 'POST',
		url: urlColor+'/agregar',
		cache: false,
		data: formdata ? formdata : form.serialize(),
		contentType: false,
		processData: false,
		dataType: 'json',
		success: function (data) {
			$("#btn_add").prop("disabled",false);
			$("#divh").hide();
			$("#main_view").show();
			notification(data.type,data.title,data.msg);
			if (data.type == "success") {
				//setTimeout("reload();", 1500);
        $("#modalColorExpress").modal('hide');
        mostrarColores();
			}
		}
	});
}

function edit_data(){
  $("#divh").show();
  $("#main_view").hide();
  var array_json = new Array();
  var errors = false;
  var error_array = [];

  var costo_s_iva = 0;
  var costo_c_iva = 0;
  var precio_sugerido = 0;
  var modelo = $("#modelo").val();
  $("#precios tr").each(function(index) {
    var desc = $(this).find("#desc_td").val();
    var costo = $(this).find("#costo_td").val();
    var costo_iva = $(this).find("#costo_td_iva").val();
    var preciolista = $(this).find(".listaprecios").val();
      var idpreciolista = $(this).find(".lista_pr").val();
    var ganancia = $(this).find(".ganancia_td").val();
    var precio = $(this).find("#precio_td").val();
    var precio_iva = $(this).find("#precio_td_iva").val();
    if(index == 0)
    {
      costo_s_iva = costo;
      costo_c_iva = costo_iva;
      precio_sugerido = precio_iva;
    }
    if (isNaN(preciolista) ||preciolista=="" || parseFloat(preciolista)<=0) {
      errors = true;

      error_array.push('En :'+modelo+" "+desc+' hay un precio  vacio o con valor cero');
    }
    if (!isNaN(preciolista)||preciolista!="" || parseFloat(preciolista)>=0)
    {
      var obj = new Object();
      obj.desc = desc;
      obj.costo = costo;
      obj.costo_iva = costo_iva;
      obj.preciolista = preciolista;
        obj.idpreciolista = idpreciolista;
      obj.precio = precio;
      obj.precio_iva = precio_iva;
      obj.ganancia = ganancia;
      //convert object to json string
      text = JSON.stringify(obj);
      array_json.push(text);
    }
    else {
      errors = true;

      error_array.push('En :'+modelo+" "+desc+' hay un precio  vacio o con valor cero');
    }
  });

  var json_arr = '[' + array_json + ']';
  $("#preciosg").val(json_arr);

  var array_jsonc = new Array();
  var colora ="";
  var ncolors=0;
  $("#colores tr").each(function(index) {

    colora = $(this).find(".colora").text();

    if (colora!="" || colora != '' ||  colora.trim().length > 1){
      var obj = new Object();
      obj.colora = colora;
      //convert object to json string
      text = JSON.stringify(obj);
      array_jsonc.push(text);
      ncolors++;

    }
    else{
      errors = true;
      error_array.push('no ha agregado colores al producto');
    }
  });
  if(ncolors==0){
      errors = true;
      error_array.push('no ha agregado colores al producto');
  }
  var json_arrc = '[' + array_jsonc + ']';

  $("#coloresg").val(json_arrc);

  $("#costo_s_iva").val(costo_s_iva);
  $("#costo_c_iva").val(costo_c_iva);
  $("#precio_sugerido").val(precio_sugerido);
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
         //alert("ok")
        }
      }
    });
  }
  else {
    notification("Error","Error en formulario",error_array.join(",<br>"));

    $("#btn_edit").removeAttr("disabled");
    $("#divh").hide();
    $("#main_view").show();

  }
}

function mostrarColores(){
  $("#color").select2();
  $.ajax({
    type: 'POST',
    url: url+'/get_colores',
    cache: false,
    data: {csrf_test_name:token},
    //contentType: false,
    //processData: false,
    //dataType: 'json',
    success: function (data) {
      document.getElementById("color").innerHTML = data;
    }
  });
}
$(document).on("click", ".td_preciolista", function()
{
	/*
  var valor =  $(this).html();
  var valorx = $(this).parents("tr").find(".preciolista").val();
  $(this).html('');
  var input = "<input type='text' class='form-control preciolista' id='preciolista' name='preciolista' value=''>";
  $(this).html(input);
  $(".preciolista").numeric({decimalPlaces:2,negative:false});
  $(this).find("#preciolista").val(valorx);
  $(this).find("#preciolista").focus();
  */
});


$(document).on("blur", ".preciolista", function(e)
{
	/*
  var costo = parseFloat($(this).closest("tr").find(".costo_td").val());
  var costot = parseFloat($(this).closest("tr").find(".precio_td_iva").val());
  var a = $(this).parents("tr");
  var valor =  parseFloat($(this).val());
  var valorx = valor.toFixed(2);
  var input = "$"+valorx+"<input type='hidden' class='form-control preciolista' id='preciolista' name='preciolista' value='"+valorx+"'>";
  //$(this).parents("tr").find(".td_preciolista").text("%"+valorx);
  console.log(input);
  $(this).parents("tr").find(".td_preciolista").html(input);
  //$(this).parents("tr").find("#preciolista").attr("hidden", true);
  console.log(costo);
  //	var n_ganancia = (valorx/100) * costo;
  var n_ganancia = valor - costot;
  var n_total = costo + n_ganancia;
  var n_total_iva = (n_total * 1.13);
  var ganancia_input = "$ "+n_ganancia.toFixed(2)+"<input type='hidden' class='form-control ganancia_td' id='ganancia_td' name='ganancia_td' value='"+n_ganancia.toFixed(2)+"'>";
  //console.log(ganancia_input);
  a.find(".td_ganancia").html(ganancia_input);
 */
});

/*$(document).on("keypress", ".precio_td", function(e)
{
var costo = parseFloat($(this).closest("tr").find(".costo_td").val());
var a = $(this).parents("tr");
if(e.keyCode == 13)
{
var valor =  parseFloat($(this).val());
var valorx = valor.toFixed(2);
var input = "$"+valorx+"<input type='hidden' class='form-control precio_td' id='precio_td' name='precio_td' value='"+valorx+"'>";
//$(this).parents("tr").find(".td_preciolista").text("%"+valorx);
console.log(input);
$(this).parents("tr").find(".td_precio").html(input);
//$(this).parents("tr").find("#preciolista").attr("hidden", true);
console.log(costo);
var n_ganancia = valor - costo;
var n_total_iva = (valor * 1.13);
var preciolista = (n_ganancia / costo) * 100;
var ganancia_input = "$ "+n_ganancia.toFixed(2)+"<input type='hidden' class='form-control ganancia_td' id='ganancia_td' name='ganancia_td' value='"+n_ganancia.toFixed(2)+"'>";
console.log(ganancia_input);
a.find(".td_ganancia").html(ganancia_input);
var total_input = preciolista.toFixed(2)+"%<input type='hidden' class='form-control preciolista' id='preciolista' name='preciolista' value='"+preciolista.toFixed(2)+"'>";
console.log(total_input);
a.find(".td_preciolista").html(total_input);
var total_input_iva = "$ "+n_total_iva.toFixed(2)+"<input type='hidden' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value='"+n_total_iva.toFixed(2)+"'>";
a.find(".td_precio_iva").html(total_input_iva);
//$(this).attr("hidden", true);
}
});*/
/*
$(document).on("blur", ".precio_td", function(e)
{
var costo = parseFloat($(this).closest("tr").find(".costo_td").val());
var a = $(this).parents("tr");
var valor =  parseFloat($(this).val());
var valorx = valor.toFixed(2);
var input = "$"+valorx+"<input type='hidden' class='form-control precio_td' id='precio_td' name='precio_td' value='"+valorx+"'>";
//$(this).parents("tr").find(".td_preciolista").text("%"+valorx);
console.log(input);
$(this).parents("tr").find(".td_precio").html(input);
//$(this).parents("tr").find("#preciolista").attr("hidden", true);
console.log(costo);
var n_ganancia = valor - costo;
var preciolista = (n_ganancia / costo) * 100;
var ganancia_input = "$ "+n_ganancia.toFixed(2)+"<input type='hidden' class='form-control ganancia_td' id='ganancia_td' name='ganancia_td' value='"+n_ganancia.toFixed(2)+"'>";
console.log(ganancia_input);
a.find(".td_ganancia").html(ganancia_input);
var total_input = preciolista.toFixed(2)+"%<input type='hidden' class='form-control preciolista' id='preciolista' name='preciolista' value='"+preciolista.toFixed(2)+"'>";
console.log(total_input);
a.find(".td_preciolista").html(total_input);
var n_total_iva = (valor * 1.13);
var total_input_iva = "$ "+n_total_iva.toFixed(2)+"<input type='hidden' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value='"+n_total_iva.toFixed(2)+"'>";
a.find(".td_precio_iva").html(total_input_iva);
//$(this).attr("hidden", true);
});
$(document).on("click", ".td_precio", function()
{
var valor =  $(this).html();
var valorx = $(this).parents("tr").find(".precio_td").val();
$(this).html('');
var input = "<input type='text' class='form-control precio_td' id='precio_td' name='precio_td' value=''>";
$(this).html(input);
$(this).find("#precio_td").val(valorx);
$(this).find("#precio_td").focus();
});
$(document).on("click", ".td_precio_iva", function()
{
var valor =  $(this).html();
var valorx = $(this).parents("tr").find(".precio_td_iva").val();
$(this).html('');
var input = "<input type='text' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value=''>";
$(this).html(input);
$(this).find("#precio_td_iva").val(valorx);
$(this).find("#precio_td_iva").focus();
});
$(document).on("blur", ".precio_td_iva", function(e)
{
var costo = parseFloat($(this).closest("tr").find(".costo_td").val());
var a = $(this).parents("tr");
//if(e.keyCode == 13)
//{
var valor =  parseFloat($(this).val());
var valorx = valor.toFixed(2);
var input = "$"+valorx+"<input type='hidden' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value='"+valorx+"'>";
//$(this).parents("tr").find(".td_preciolista").text("%"+valorx);
console.log(input);
$(this).parents("tr").find(".td_precio_iva").html(input);
//$(this).parents("tr").find("#preciolista").attr("hidden", true);
console.log(costo);
var n_ganancia = (valor/1.13) - costo;
var n_total = (valor / 1.13);
var preciolista = (n_ganancia / costo) * 100;
var ganancia_input = "$ "+n_ganancia.toFixed(2)+"<input type='hidden' class='form-control ganancia_td' id='ganancia_td' name='ganancia_td' value='"+n_ganancia.toFixed(2)+"'>";
console.log(ganancia_input);
a.find(".td_ganancia").html(ganancia_input);
var total_input = preciolista.toFixed(2)+"%<input type='hidden' class='form-control preciolista' id='preciolista' name='preciolista' value='"+preciolista.toFixed(2)+"'>";
console.log(total_input);
a.find(".td_preciolista").html(total_input);
var total_input = "$ "+n_total.toFixed(2)+"<input type='hidden' class='form-control precio_td' id='precio_td' name='precio_td' value='"+n_total.toFixed(2)+"'>";
a.find(".td_precio").html(total_input);
//$(this).attr("hidden", true);
//}
});
$(document).on("blur", ".precio_td", function(e)
{
var costo = parseFloat($(this).closest("tr").find(".costo_td").val());
var a = $(this).parents("tr");
var valor =  parseFloat($(this).val());
var valorx = valor.toFixed(2);
var input = "$"+valorx+"<input type='hidden' class='form-control precio_td' id='precio_td' name='precio_td' value='"+valorx+"'>";
//$(this).parents("tr").find(".td_preciolista").text("%"+valorx);
console.log(input);
$(this).parents("tr").find(".td_precio").html(input);
//$(this).parents("tr").find("#preciolista").attr("hidden", true);
console.log(costo);
var n_ganancia = valor - costo;
var preciolista = (n_ganancia / costo) * 100;
var ganancia_input = "$ "+n_ganancia.toFixed(2)+"<input type='hidden' class='form-control ganancia_td' id='ganancia_td' name='ganancia_td' value='"+n_ganancia.toFixed(2)+"'>";
console.log(ganancia_input);
a.find(".td_ganancia").html(ganancia_input);
var total_input = preciolista.toFixed(2)+"%<input type='hidden' class='form-control preciolista' id='preciolista' name='preciolista' value='"+preciolista.toFixed(2)+"'>";
console.log(total_input);
a.find(".td_preciolista").html(total_input);
var n_total_iva = (valor * 1.13);
var total_input_iva = "$ "+n_total_iva.toFixed(2)+"<input type='hidden' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value='"+n_total_iva.toFixed(2)+"'>";
a.find(".td_precio_iva").html(total_input_iva);
//$(this).attr("hidden", true);
});
*/
$(document).on("keyup", "#ultcosto", function()
{
  precios();
  /*
  var iva= parseFloat($("#porcentaje_iva").val()/100);
  alert(iva)
  var tr = $(this).parents("tr");
  var valor =  parseFloat($(this).val());
  var valorx = valor.toFixed(4);
  var costo_iva =  iva*valor;
  var total_input_gana = "$ "+ganancia.toFixed(4)+"<input type='hidden' class='form-control ganancia_td' id='ganancia' name='ganancia' value='"+ganancia.toFixed(4)+"'>";
  var costo_iva_input = "$ "+valorx+"<input type='hidden' class='form-control costo_td' id='costo_td' name='costo_td' value='"+valorx+"'>";
  var costo_input = "$ "+valorx+"<input type='hidden' class='form-control costo_td_iva' id='costo_td_iva' name='costo_td_iva' value='"+costo_iva+"'>";
  tr.find(".td_ganancia").html(total_input_gana);
  tr.find(".td_costo").html(costo_input);
  tr.find(".td_costo_iva").html(costo_iva_input);
*/
})
$(document).on("keyup", ".listaprecios", function()
{

  var tr = $(this).parents("tr");
  var valor =  parseFloat($(this).val());
  var valorx = valor.toFixed(4);
  var costo=tr.find(".precio_td_iva").val();
  var ganancia=valor-costo;
  var total_input_gana = "$ "+ganancia.toFixed(4)+"<input type='hidden' class='form-control ganancia_td' id='ganancia_' name='ganancia' value='"+ganancia.toFixed(4)+"'>";
  tr.find(".td_ganancia").html(total_input_gana);
})
function reload() {
  location.href = url;
}

function reload_current() {
  location.reload()
}
