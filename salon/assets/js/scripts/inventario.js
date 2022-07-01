let url = base_url+"inventario";
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
  $(".sel").select2();
  $(".numeric").numeric({negative:false, decimals:false});
  $(".decimal").numeric({negative:false, decimalPlaces:4});
  var proceso = $("#proceso").val();
  if(proceso == "carga")
  {
    var complemento ='/get_data_carga';
    com = complemento;
  }
  if(proceso == "descarga")
  {
    var complemento ='/get_data_descarga';
    com = complemento;
  }
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

  if(proceso == "carga")
  {
    $("#barcore").keydown(function(event) {
    if (event.keyCode == 13) {
      event.preventDefault();
      var bar = $("#barcore").val();
      $.ajax({
        type: 'POST',
        url:  url+'/get_productos',
        data: {"query":bar,"csrf_test_name":token},
        dataType: 'json',
        success: function (data) {
          $("#barcore").val("");
          if (data[0]) {
            //console.log(data);
            var prod=data[0].producto.split("|");
            let id_producto = prod[0];
            //alert(prod[0]);
            let nombre = prod[1]+" "+prod[2];

            $("#id_producto").val(id_producto);
            new_producto(id_producto,nombre);
          }
          else{
            notification("warning","Error","Producto no encontrado");

          }
          setTimeout(function(){$("#barcore").focus();}, 200);
        }
      });
      return false;
    }
  });

    $(".color").select2();
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
                      data: {"query":q,"csrf_test_name":token},
                      url:  url+'/get_productos',
                  }).done(function(res){
                      if(res) cba(JSON.parse(res));
                  });
              },
              templates:{
                  suggestion:function (data) {
                      var prod=data.producto.split("|");
                      return '<div class="tt-suggestion tt-selectable">'+prod[1]+" "+prod[2]+'</div>';
                  }
              }
          }).on('typeahead:selected',onAutocompleted_producto);
      function onAutocompleted_producto($e, datum) {
          let prod = datum.producto.split("|");
          let id_producto = prod[0];
          let nombre = prod[1]+" "+prod[2];
          $("#id_producto").val(id_producto);
          new_producto(id_producto,nombre);
      }
  }
  if(proceso == "descarga")
  {
    //alert("aqui");
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
          data: {"query":q,"csrf_test_name":token,"id_sucursal": $("#sucursal").val()},
          url:  url+'/get_productos_stock',
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
  }

  $("#form_add_des").on('submit', function(e){
    e.preventDefault();
    $(this).parsley().validate();
    if ($(this).parsley().isValid()){
      $("#btn_add").prop("disabled",true)
      save_data_des();
    }
  });

  $("#form_edit_des").on('submit', function(e){
    e.preventDefault();
    $(this).parsley().validate();
    if ($(this).parsley().isValid()){
      $("#btn_edit").prop("disabled",true)
      edit_data_des();
    }
  });

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
        "url": base_url+ "assets/js/scripts/Spanish.json"
    },
    "pagingType": "full_numbers"
  }); // End of DataTable
  //dataTable.ajax.reload();
}

function new_producto(id_producto,nombre)
{
    $("#scrollable-dropdown-menu #producto").typeahead("val","");
    let distinto = true;
  /*
    if ($("#table_producto tr").length > 0)
    {
        $("#table_producto tr").each(function(){
            let id_p = $(this).find(".id_producto").val();
            if(id_producto == id_p)
            {
                distinto = false
            }
        });
    }
  */

    if(distinto){
        $.ajax({
            type: 'POST',
            url: url+'/detalle_producto',
            data: "id="+id_producto+"&csrf_test_name="+token,
            dataType: 'json',
            success: function (datax) {
        (datax.ocultar=="")?'':$("#total").hide();
                let fila = "<tr>";
                fila += "<td>"+id_producto+"</td>";
                fila += "<td><input type='hidden' class='id_producto' value='"+id_producto+"'><input type='hidden' class='nombre' value='"+nombre+"'>"+nombre+"</td>";
        fila += "<td>"+datax.colores+"</td>";
        fila += "<td><input type='text' class='form-control cantidad numeric' value='1' style='width:100%;'></td>";
                fila += "<td><input "+datax.ocultar+" type='text' class='form-control costo decimal' value='"+datax.costo+"' style='width:100%;'></td>";
        fila += "<td><input "+datax.ocultar+" type='text' class='form-control decimal precio_sugerido'  value='"+datax.precio_sugerido+"' style='width:100%;'></td>";
                fila += "<td><input "+datax.ocultar+" type='text' class='form-control subtotal' readonly value='"+datax.costo+"' style='width:100%;'></td>";
                fila += "<td class='text-center'><a class='btn btn-danger delete_tr' style='color: white'><i class='mdi mdi-trash-can'></i></a></td>";
                fila +="</tr>";
                $("#table_producto").prepend(fila);
                $(".numeric").numeric({negative:false, decimals:false});
                $(".decimal").numeric({negative:false, decimalPlaces:4});
                $(".sel").select2();
        $(".color").select2();
                $("#table_producto tr:first").find(".cantidad").focus();
            }
        });

    }else{
        notification("Error","Alerta","El producto ya fue agregado");
    }

}

/*keyups de movimiento entre casillas*/
$(document).on("keyup", ".cantidad", function(e){
    totales();
    var tr = $(this).parents("tr");
    if(e.keyCode == 13 && $(this).val()!="")
    {
        tr.find(".costo").focus();
    }
});
$(document).on("keyup", ".costo", function(e){
    totales();
  var tr = $(this).parents("tr");
    if(e.keyCode == 13 && $(this).val()!="")
    {
        tr.find(".precio_sugerido").focus();
    }
});
$(document).on("keyup", ".precio_sugerido", function(e){
    totales();
    if(e.keyCode == 13 && $(this).val()!="")
    {
        $("#producto").focus();
    }
});

function totales()
{
  var total = 0;
    $("#table_producto tr").each(function(){
        var tr  = $(this);
        var costo  = tr.find(".costo").val();
        var cantidad  = tr.find(".cantidad").val();
        if(costo == "")
        {
            costo = 0;
        }
        if(cantidad == "")
        {
            cantidad = 0;
        }
        var costo_iva = parseFloat(costo)*1.13;
        var subtotal = parseInt(cantidad)*parseFloat(costo);
    total+=subtotal;
        tr.find(".costo_iva").val(costo_iva.toFixed(2));
        tr.find(".subtotal").val(subtotal.toFixed(2));
    });
  $('#total').val(total.toFixed(2));
}
$(document).on("click", ".delete_tr", function(){
    $(this).parents("tr").remove();
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
    var precio_sugerido = parseFloat($(this).find('.precio_sugerido').val());
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
      url: url+'/cargar',
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


function edit_data(){
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
    var precio_sugerido = parseFloat($(this).find('.precio_sugerido').val());
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
    location.href = url+"/cargas";
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

/***********************************************************/
/***********************************************************/
/***********************************************************/
/***********************************************************/
/***********************************************************/
/***********************************************************/
/***********************************************************/



function new_producto_des(id_producto,nombre,id_color)
{
  //alert("aqui");
  $("#scrollable-dropdown-menu #producto_des").typeahead("val","");
  let distinto = true;
  var id_sucursal = $("#sucursal").val();
  $.ajax({
    type: 'POST',
    url: url+'/detalle_producto_stock',
    data: "id="+id_producto+"&csrf_test_name="+token+"&id_s="+id_color+"&id_sucursal="+id_sucursal,
    dataType: 'json',
    success: function (datax) {
      (datax.ocultar=="")?'':$("#total").hide();
      if(datax.stock>0)
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
          fila += "<td><input "+datax.ocultar+" type='text' class='form-control subtotald' readonly value='0' style='width:100%;'></td>";
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
    if(stock < parseInt(cantidad))
    {
      cantidad = stock;
      tr.find(".cantidadd").val(stock);
    }
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

function save_data_des(){
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


        if (!isNaN(cantidad) && cantidad>0)
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
    let form = $("#form_add_des");
    let formdata = false;
    if (window.FormData) {
      formdata = new FormData(form[0]);
    }
    $.ajax({
      type: 'POST',
      url: url+'/descargar',
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
          setTimeout("reload_des();", 1500);
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


function edit_data_des(){
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
    var color  = parseFloat($(this).find('.color').val());
        if (!isNaN(cantidad) && cantidad>0)
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
    let form = $("#form_edit_des");
    let formdata = false;
    if (window.FormData) {
      formdata = new FormData(form[0]);
    }
    $.ajax({
      type: 'POST',
      url: url+'/editar_descarga',
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
          setTimeout("reload_des();", 1500);
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

$(document).on("click",".delete_row_des", function(event)
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
                url: url+"/delete_descarga",
                data: dataString,
                dataType: 'json',
                success: function (data) {
                    notification(data.type,data.title,data.msg);
                    if (data.type == "success") {
                        setTimeout("reload_des();", 1500);
                    }
                }
            });
        }
    });
});
function reload_des() {
    location.href = url+"/descargas";
}

function reload_current_des() {
    location.reload()
}

$(document).on('click', '.detail', function(event) {
    $('#viewModal .modal-content').load(url+"/detalle/"+$(this).attr('data-id'));
});

$(document).on('click', '.detail_des', function(event) {
    $('#viewModal .modal-content').load(url+"/detalle_des/"+$(this).attr('data-id'));
});

$(document).on("click", "#descargarProducto", function(e){
  //alert("aqui");
  $.ajax({
      type: "POST",
      url: url+"/descargar_prod_esp",
      data: "csrf_test_name="+token,
      dataType: 'json',
      success: function (data) {
          notification(data.type,data.title,data.msg);
          if (data.type == "success") {
              //setTimeout("reload_des();", 1500);
          }
      }
  });
});
