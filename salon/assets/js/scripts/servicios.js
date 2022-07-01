let url = base_url+"servicios";
let token = $("#csrf_token_id").val()

$(window).keydown(function(event) {
  if (event.keyCode == 13) {
    event.preventDefault();
    return false;
  }
});
$(document).ready(function () {

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

});
$(document).on("keyup","#color", function(evt){
  if(evt.keyCode == 13)
  {
    if($(this).val() != "")
    {
      add_color();
    }
    else {
      notification('Error', 'Advertencia', 'Debe ingresar un color');
    }
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
  var color = $("#color").val().toUpperCase();
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
    $("#color").val("");
    $("#color").focus();
  }
  else {
    notification('Error','Advertencia','Ya se agrego este color');
    $("#color").val("");
    $("#color").focus();
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
/*
$(document).on("click", "#btn_proveedor", function(e) {
  e.preventDefault()
  $("#divh").show();
  $("#main_view").hide();
  let id_cliente = $("#id_cliente").val();
  let id_servicio = $("#id_servicio").val();
  let data = {
    id_cliente:id_cliente,
    id_servicio:id_servicio,
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
*/
$(document).on("click", ".delete_tr", function(){
	var colora = $(this).parents("tr").find(".colora").text();
	let id_servicio = $("#id_servicio").val();
	let rowId = $(this).parents("tr").attr("id");
	  var exito=-1;
	   $.ajax({
        type: "POST",
       // url: url+"/eliminar_color",
        url: url+"/get_idColor",
        data: {id:id_servicio,color:colora,csrf_test_name:token},
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

  var array_json = new Array();
  var costo_s_iva = 0;
  var costo_c_iva = 0;
  var precio_sugerido = 0;

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
          $("#btn_add").prop("disabled",false)
      }
    }
  });
}
$(document).on("click","#cesc1, #cesc2", function()
{
  costos();
});

function costos()
{
  var costo = $("#ultcosto").val();
  var precio_min = $("#precio_min").val();
  var precio_sug = $("#precio_sug").val();
  var cesc = $("#cesc1").is(":checked");
  var process = "precios";
   if(costo != "")
  {
    $.ajax({
      type : "POST",
      url : url+'/costos',
      data : "costo="+costo+"&cesc="+cesc+"&precio_min="+precio_min+"&precio_sug="+precio_sug+"&csrf_test_name="+token,
      success : function(datax)
      {
        $("#precios").html(datax);
      }
    });
  }

}
function precios()
{
  var costo = $("#ultcosto").val();
  var cesc = $("#cesc1").is(":checked");
  var process = "precios";
  if(costo != "")
  {
    $.ajax({
      type : "POST",
      url : url+'/precios',
      data : "costo="+costo+"&cesc="+cesc+"&csrf_test_name="+token,
      success : function(datax)
      {
        $("#precios").html(datax);
      }
    });
  }

}

function edit_data(){
  $("#divh").show();
  $("#main_view").hide();
  var array_json = new Array();
  var costo_s_iva = 0;
  var costo_c_iva = 0;
  var precio_sugerido = 0;


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
     }else {
         $("#btn_edit").prop("disabled",false)
     }
    }
  });
}
$(document).on("click", ".td_porcentaje", function()
{

});


$(document).on("blur", ".ganancia_min_td", function(e)
{

});
/*
$(document).on("keyup", "#ultcosto,#precio_min,#precio_sug", function()
{
  costos();
})
*/
function reload() {
  location.href = url;
}

function reload_current() {
  location.reload()
}
