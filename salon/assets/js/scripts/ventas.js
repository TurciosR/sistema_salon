let url = base_url + "ventas";
let token = $("#csrf_token_id").val();
var com = "";
id_venta = 0;
let duration=500;
$(window).keydown(function (event) {

  if (event.keyCode == 13) {
    event.preventDefault();
    return false;
  }
  if (event.keyCode === 113) {
    event.preventDefault();
    if (($("button[name='btn_add_fact']").attr('id'))) {
      if ($('.total_final').text() != "0.00" || parseFloat(!isNaN($('.total_final').text()))) {
        $("[name='btn_add_fact']")[0].click();
        $("#btn_add_fact").focus();
        $("#btn_add_fact").removeClass('btn-success').addClass('btn-dark');
      }
    }
    if (($("button[name='btn_add_new']").attr('id'))) {
      if ($('.total_final').text() != "0.00" || parseFloat(!isNaN($('.total_final').text()))) {
        $("[name='btn_add_new']")[0].click();
        $("#btn_add_new").focus();
        $("#btn_add_new").removeClass('btn-success').addClass('btn-dark');
      }
    }
    if (($("button[name='btn_add']").attr('id'))) {
      if ($('.total_final').text() != "0.00" || parseFloat(!isNaN($('.total_final').text()))) {
        $("[name='btn_add']")[0].click();
        $("#btn_add").focus();
        $("#btn_add").removeClass('btn-success').addClass('btn-dark');
      }
    }
    if (($("button[name='btn_finref']").attr('id'))) {
      if ($('.total_final').text() != "0.00" || parseFloat(!isNaN($('.total_final').text()))) {
        $("[name='btn_finref']")[0].click();
        $("#btn_finref").focus();
        $("#btn_finref").removeClass('btn-success').addClass('btn-dark');
      }
    }
  }
});

$(document).on('change', '#sucursal', function (event) {
  $("#table_producto").html("");
  $("#total").val("0.00");
  $("#total1").val("$0.00");
});

$(document).on('change', '#tipodoc', function (event) {
  var data = $('#tipodoc').select2('data');
  if (data) {
  }
  totales();
});
$(document).on('change', '#tipo_pago', function (event) {
  var data = $('#tipo_pago').select2('data');
  var totalfinal = $("#total").val();
  let tipopago=$('#tipo_pago').val();
  $("#tipo_pago_h").val(tipopago)
  if (data) {
    if (data[0].id == 1) {
      $('#lbl_efectivo').text('Efectivo');
      $('#lbl_cambio').text('Cambio');
      $("#cambio").prop("readonly", true)
      $("#efectivo").val("");
    }
    if (data[0].id == 2) {
      $('#lbl_efectivo').text('Monto');
      $('#lbl_cambio').text('No. Transacción');
      $("#cambio").prop("readonly", false);
      $("#cambio").val("");
      $("#efectivo").val("");
      $("#efectivo").focus();

    }

    if (data[0].id == 3) {
      let c=$("#client").text().trim()
      if (c === 'MOSTRADOR') {
        notification("Error", "Seleccione otro cliente", "El cliente no puede ser MOSTRADOR");
      } else {
        $('#lbl_efectivo').text('Monto Abono');
        $('#lbl_cambio').text('Días Crédito');
        $("#cambio").prop("readonly", false)
        $("#cambio").val("");
        $("#efectivo").focus();
        $("#efectivo").val("");
      }

    }
    }
  setTimeout(function () { $("#efectivo").focus(); }, 500);
});

$(document).ready(function () {
  $("#producto").focus();
  $(".sel").select2();
  $(".est").select2();
  $(".clientes").select2();

  $(".numeric").numeric({ negative: false, decimals: false });
  $(".decimal").numeric({ negative: false, decimalPlaces: 4 });
  $('.cant_dev').numeric({ negative: false, decimal: false });
  $("#tipo_pago").select2();
  $("#tipo_pago option[value='3']").remove();
  $('#clasifica').select2();
  //}

  var complemento = '/get_data';
  com = complemento;
  generar();

  setTimeout(function () { $("#producto").focus(); }, 500);
  if ($("#proceso").val() != "devolver" && $("#proceso").length > 0) {

    var clasifica = $('#client').select2('data');
    if (clasifica != undefined) {
      get_porcent_client(clasifica[0].id);
    }

  }
  $("#form_add").on('submit', function (e) {
    e.preventDefault();
    $(this).parsley().validate();
    if ($(this).parsley().isValid()) {
      $("#btn_add_new").prop("disabled", true)
      save_data();
    }
  });

  $("#form_edit").on('submit', function (e) {
    e.preventDefault();
    $(this).parsley().validate();
    if ($(this).parsley().isValid()) {
      $("#btn_edit").prop("disabled", true)
      edit_data();
    }
  });
  $("#form_add_fact").on('submit', function (e) {
    e.preventDefault();
    $(this).parsley().validate();
    if ($(this).parsley().isValid()) {
      //$("#btn_add_fact").prop("disabled",true)
      save_data_fact();
    }
  });

  $("#form_fin").on('submit', function (e) {
    e.preventDefault();

    $(this).parsley().validate();
    if ($(this).parsley().isValid()) {
      $("#btn_edit #btn_add").prop("disabled", true)
      fin_data();
    }

  });

  $("#form_finref").on('submit', function (e) {
    e.preventDefault();

    let cuantos=0;
      $("#table_servicio tr").each(function (index) {
        cuantos++
      });
      $("#table_producto tr").each(function (index) {
        cuantos++
    });
    /*
    if ($('#efectivo').val().length === 0){
      let total =$("#total").val();
      $('#efectivo').val(total);
    }*/
    $(this).parsley().validate();
    if ($(this).parsley().isValid() ) {
      $("#btn_edit #btn_add #btn_finref").prop("disabled", true)
      let efect =  $("#efectivo").val();
        fin_data_ref();

    }
  //}
  });
  //devolucion
  $("#form_devolver").on('submit', function (e) {
    e.preventDefault();
    $(this).parsley().validate();
    if ($(this).parsley().isValid()) {
      $("#btn_devolver").prop("disabled", true)
      devolver_data();
    }
  });
  $("#scrollable-dropdown-menu #producto").typeahead({
    highlight: true,
  },
    {
      limit: 100,
      name: 'producto',
      display: function (data) {
        prod = data.producto.split("|");
        return prod[1];
      },
      source: function show(q, cb, cba) {
        $.ajax({
          type: "POST",
          data: { "query": q, "csrf_test_name": token, id_sucursal: $("#id_sucursal").val() },
          url: url + '/get_productos',
        }).done(function (res) {
          if (res) cba(JSON.parse(res));
        });
      },
      templates: {
        suggestion: function (data) {
          var prod = data.producto.split("|");
          return '<div class="tt-suggestion tt-selectable">' + prod[1] + '</div>';
        }
      }
    }).on('typeahead:selected', onAutocompleted_producto);
  function onAutocompleted_producto($e, datum) {
    let prod = datum.producto.split("|");
    let id_producto = prod[0];
    let nombre = prod[1];
    let id_color = prod[2];
    $("#id_producto").val(id_producto);
    new_producto(id_producto, nombre, id_color);
  }

  $("#scrollable-dropdown-menu #cliente").typeahead({
    highlight: true,
  },
    {
      limit: 100,
      name: 'cliente',
      display: function (data) {
        cli = data.cliente.split("|");
        return cli[1];
      },
      source: function show(q, cb, cba) {
        $.ajax({
          type: "POST",
          data: { "query": q, "csrf_test_name": token },
          url: url + '/get_clientes',
        }).done(function (res) {
          if (res) cba(JSON.parse(res));
        });
      },
      templates: {
        suggestion: function (data) {
          var cli = data.cliente.split("|");
          return '<div class="tt-suggestion tt-selectable">' + cli[1] + '</div>';
        }
      }
    }).on('typeahead:selected', onAutocompleted_cliente);
  function onAutocompleted_cliente($e, datum) {
    let cli = datum.cliente.split("|");
    let id_cliente = cli[0];
    let nombre = cli[1];
    let clasifica = cli[2];
    //$("#cliente").attr("readonly", true);
    $("#id_cliente").val(id_cliente);

    setTimeout(function () {
      get_porcent_client(clasifica);
      //totales();
    }, 1000);

  }


});
function get_porcent_client(clasifica) {
  let dataString = "clasifica=" + clasifica;

  $.ajax({
    type: "POST",
    url: url + "/get_porcent_cliente",
    //  data: dataString,
    data: { "clasifica": clasifica, "csrf_test_name": token },
    dataType: 'json',
    success: function (data) {
      notification(data.type, data.title, data.msg);
      if (data.type == "success") {
        $("#porc_clasifica").val(data.porc_clasifica);
        if (data.mostrador == 1) {
          $("#tipo_pago").val(1).trigger('change');
          //disable the field
          //$("#tipo_pago").prop( "disabled", true );

          $("#tipo_pago_h").val(1);
          let tipodoc=$('#tipodoc').val();
          $("#tipo_doc_h").val(tipodoc);
          $('#tipodoc').prop( "disabled", true );

        }else{
          let tipopago=$('#tipo_pago').val();
          $("#tipo_pago_h").val(tipopago);
          $("#tipo_pago").prop( "disabled", false );
          $('#tipodoc').prop( "disabled", false );
        }
        totales();
      } else {
        $("#porc_clasifica").val('0');
        totales();
      }
    }
  });
}
$(document).on('change', '#sucursales', function (event) {
  generar();
});
function generar() {
  dataTable = $('#editable').DataTable().destroy()
  dataTable = $('#editable').DataTable({
    "pageLength": 50,
    "serverSide": true,
    "order": [[0, "desc"]],
    "ajax": {
      url: url + com,
      type: 'POST',
      data: {
        csrf_test_name: token,
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

function new_producto(id_producto, nombre, id_color, qty = 1) {
  $('#totals').show();
  $("#scrollable-dropdown-menu #producto").typeahead("val", "");

  monto_ini = 0;

  if ($("#proceso").val() == "editar") {
    id_venta = $("#id_venta").val();
  }
  let porc_clasifica = -1
  if ($("#porc_clasifica").val() != "") {
    porc_clasifica = $("#porc_clasifica").val();
  }

  if (id_color == "SERVICIO") {
    monto_ini = 0;
    $.ajax({
      type: 'POST',
      url: url + '/detalle_servicio',
      data: "id=" + id_producto + "&csrf_test_name=" + token + "&id_s=" + id_color + "&id_venta=" + id_venta,
      dataType: 'json',
      success: function (datax) {
        let stockk = "-";
        let subt = datax.precio_sugerido * qty;
        let fila = "<tr>";
        fila += "<td style='width:5%;'>"+"<input type='hidden' class='form-control costo decimal' value='" + datax.costo + "'><input type='hidden' class='form-control precio_minimo decimal' value='" + datax.precio_minimo + "'>" + id_producto + "</td>";
        fila += "<td style='width:25%;'><input type='hidden' class='id_producto' value='" + id_producto + "'><input type='hidden' class='id_s' value='" + datax.id_s + "'><input type='hidden' class='nombre' value='" + nombre + "'>" + nombre + "</td>";
        fila += "<td style='width:10%;'><input type='hidden' class='color' value='" + id_color + "'><input type='hidden' class='stock' value='" + stockk + "' style='width:100%;'>" + stockk + "</td>";
        fila += "<td style='width:10%;'><input type='text' id='qty' class='form-control cantidad numeric' value='" + qty + "' style='width:100%;'></td>";
      //fila += "<td style='width:10%;'>-</td>";
        fila += "<td style='width:10%;'><input type='hidden' class='form-control precio_sugerido decimal' value='" + datax.precio_sugerido + "' style='width:100%;'><input type='text' class='form-control precio_base decimal' value='" + datax.precio_sugerido + "' style='width:100%;'></td>";
        fila += "<td style='width:10%;'><input type='text' class='form-control descuento' readonly value='" + monto_ini + "' style='width:100%;'></td>";
        fila += "<td style='width:10%;'><input type='text' class='form-control subtotal' readonly value='" + subt + "' style='width:100%;'></td>";
        fila += "<td style='width:10%;' class='text-center'><a class='btn btn-danger delete_tr' style='color: white'><i class='mdi mdi-trash-can'></i></a></td>";
        fila += "</tr>";
        $("#table_servicio").prepend(fila);
        $(".numeric").numeric({ negative: false, decimals: false });
        $(".decimal").numeric({ negative: false, decimalPlaces: 4 });
        $(".sel").select2();
        $(".est").select2();
        $("#table_servicio tr:first").find(".cantidad").focus();
        totales();
      }
    });
    totales();

  }

  if (id_color != "SERVICIO") {
    $.ajax({
      type: 'POST',
      url: url + '/detalle_producto',
      data:
        "id=" + id_producto
        + "&csrf_test_name=" + token
        + "&id_s=" + id_color
        + "&id_venta=" + id_venta
        + "&clasifica=" + porc_clasifica,
      dataType: 'json',
      success: function (datax) {
        let subt = datax.precio_ini * qty;
        let fila = "<tr>";
        fila += "<td style='width:5%;'>" + id_producto + "</td>";
        fila += "<td style='width:25%;'><input type='hidden' class='id_producto' value='" + id_producto + "'><input type='hidden' class='id_s' value='" + datax.id_s + "'><input type='hidden' class='nombre' value='" + nombre + "'>" + nombre + "</td>";
        fila += "<td style='width:10%;'><input type='hidden' class='color' value='" + id_color + "'><input type='hidden' class='stock' value='" + datax.stock + "' style='width:100%;'>" + datax.stock + "</td>";
        fila += "<td style='width:10%;'><input type='text' id='qty'  class='form-control cantidad numeric' value='" + qty + "' style='width:100%;'></td>";
        //fila += "<td style='width:10%;'><select class='est'><option value='NUEVO'>NUEVO</option><option value='USADO'>USADO</option></select></td>";
        fila += "<td style='width:10%;'><input type='hidden' class='form-control costo decimal' value='" + datax.costo + "' >" + datax.precios + "</td>";
        fila += "<td style='width:10%;'><input type='text' class='form-control descuento' readonly value='" + monto_ini + "' style='width:100%;'></td>";

        fila += "<td style='width:10%;'><input type='text' class='form-control subtotal' readonly value='" + subt + "' style='width:100%;'></td>";
        fila += "<td  style='width:10%;' class='text-center'><a class='btn btn-danger delete_tr' style='color: white'><i class='mdi mdi-trash-can'></i></a></td>";
        fila += "</tr>";
        if (datax.stock > 0) {
          $("#table_producto").prepend(fila);
        }
        $(".numeric").numeric({ negative: false, decimals: false });
        $(".decimal").numeric({ negative: false, decimalPlaces: 4 });
        $(".sel").select2();
        $(".est").select2();
        $("#table_producto tr:first").find(".cantidad").focus();
        totales();
      }

    });
    totales();
  }

}

/*keyups de movimiento entre casillas*/
$(document).on("keyup", ".cantidad", function (e) {
  var errors = false;
  var error_array = [];
  if ($("#id_cliente").val() == "") {
    errors = true;
    error_array.push('Seleccione un cliente');
  }
  else {
    let id_cliente = $("#id_cliente").val();
    var errors = false;
    var error_array = [];
  }

  let porc_clasifica = -1
  if ($("#porc_clasifica").val() != "") {
    porc_clasifica = $("#porc_clasifica").val();
  }
  tr = $(this).closest('tr');
  stock = parseInt(tr.find('.stock').val());
  id_producto = parseInt(tr.find('.id_s').val());

  fila = tr;
  existencia = parseInt(fila.find('.stock').val());

  a_cant = parseInt($(this).val());

  if (isNaN(a_cant)) {
    a_cant = 0;
  }
  a_asignar = 0;

  $('#table_producto tr').each(function (index) {

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
    setTimeout(function () {
      totales();
    }, 1000);
  } else {
    totales();
  }

});
$(document).on("keydown", ".cantidad", function (e) {
  if (e.keyCode == 13 && $(this).val() != "") {
    var tr = $(this).parents("tr");
    var stock = tr.find(".stock").val();
    setTimeout(function () {
      if (stock != "-") {
        tr.find(".est").select2("open");
      } else {
        tr.find(".precio_base").focus();
      }
    }, 1000);
  }
});

//keyup para cantidades en table_servicio
$(document).on("keyup", ".precio_base", function (e) {
  var tr = $(this).parents("tr");
  var precio_base = tr.find(".precio_base").val();
  var precio_sugerido = tr.find(".precio_sugerido").val();//oculto
  var precio_minimo = tr.find(".precio_minimo").val();

  totales();
  if (e.keyCode == 13 && $(this).val() != "") {
    setTimeout(function () {

      $("#producto").focus();
    }, 500);
  }

});
$(document).on("select2:close", ".est", function () {
  var tr = $(this).parents("tr");
  tr.find(".sel").select2("open");
});

$(document).on("select2:close", ".sel", function () {
  totales();
  $("#producto").focus();
});
function totales() {
  var total = 0, total_producto = 0, total_servicio = 0, subtotal1 = 0, subtotal2 = 0;
  var total_items = 0;
  $("#table_producto tr").each(function () {
    var tr = $(this);
    var servicio = tr.find(".color").val();
    var costo = tr.find(".precios").val();
    var cantidad = tr.find(".cantidad").val();
    var stock = parseInt(tr.find(".stock").val());
    if (costo == "") {
      costo = 0;
    }
    if (cantidad == "") {
      cantidad = 0;
    }
    if (stock < parseInt(cantidad)) {
      cantidad = stock;
      tr.find(".cantidad").val(stock);
    }
    let porc_clasifica = 0;
    if ($("#porc_clasifica").val() != "") {
      porc_clasifica = $("#porc_clasifica").val();
    }
    var costo_iva = parseFloat(costo) * 1.13;
    var descto = 0.00;

    if (servicio == "SERVICIO") {
      descto = 0.00;
    }
    var subtotal1 = parseInt(cantidad) * parseFloat(costo) - descto;
    if (isNaN(subtotal1)) {
      subtotal1 = 0.00;
    }

    total_producto += subtotal1;
    tr.find(".costo_iva").val(costo_iva.toFixed(2));
    tr.find(".subtotal").val(subtotal1.toFixed(2));
    tr.find(".descuento").val(descto.toFixed(2));
    total_items++;
  });
  $("#table_servicio tr").each(function () {
    var tr = $(this);
    var servicio = tr.find(".color").val();
    var precio_sugerido = tr.find(".precio_sugerido").val();//oculto
    var precio_base = tr.find(".precio_base").val();
    var precio_minimo = tr.find(".precio_minimo").val();
    var cantidad = tr.find(".cantidad").val();
    if (precio_sugerido == "") {
      precio_sugerido = 0;
    }
    if (cantidad == "") {
      cantidad = 0;
    }
    let porc_clasifica = 0;
    var costo_iva = parseFloat(precio_sugerido) * 1.13;
    var descto = 0;
    if (descto < 0)
      descto = 0;

    if (parseFloat(precio_base) < parseFloat(precio_sugerido) && parseFloat(precio_base) >= parseFloat(precio_minimo)) {
      descto = parseFloat(cantidad) * (parseFloat(precio_sugerido) - parseFloat(precio_base));
    }
    if (parseInt(cantidad) <= 0) {
      descto = 0;
    }
    subtotal2 = parseInt(cantidad) * parseFloat(precio_base);
    if (isNaN(subtotal2)) {
      subtotal2 = 0.00;
    }
    total_servicio += subtotal2;
    tr.find(".costo_iva").val(costo_iva.toFixed(2));
    tr.find(".subtotal").val(subtotal2.toFixed(2));
    tr.find(".descuento").val(descto.toFixed(2));
    total_items++;
  });
  total = total_producto + total_servicio;
  $('#total').val(total.toFixed(2));
  $('#total1').val("$" + total.toFixed(2));
  $('.itemss').val(total_items);
  $('#totals .total_final').text(total.toFixed(2));


  var data = $('#tipodoc').select2('data');
  if (data) {
    if (data[0].id == 3) {
      total_s_iva = total - total * 0.13 //falta traer el impuesto iva del controller Ventas y  asu vez pasar a cada formulario agregar, editar y finalizar venta pendiente !!!!
      total_iva = total * 0.13
      $('.total_s_iva').text(+total_s_iva.toFixed(2));
      $('.total_iva').text(total_iva.toFixed(2));
    }
    else {
      $('.total_s_iva').text("0.00");
      $('.total_iva').text("0.00");
    }
  }



}
function total_dev() {
  var total = 0, total_producto = 0, total_servicio = 0, subtotal1 = 0, subtotal2 = 0;
  $("#table_producto tr").each(function () {
    var tr = $(this);
    var costo = tr.find(".precio_base").val();
    var cantidad = tr.find(".cant_dev").val();
    var stock = parseInt(tr.find(".stock").val());
    if (cantidad == "") {
      cantidad = 0;
    }
    if (stock < parseInt(cantidad)) {
      cantidad = stock;
      tr.find(".cant_dev").val(stock);
    }
    var subtotal1 = parseInt(cantidad) * parseFloat(costo);
    total_producto += subtotal1;

    tr.find(".subtotal").val(subtotal1.toFixed(2));

  });
  $("#table_servicio tr").each(function () {
    var tr = $(this);
    var servicio = tr.find(".color").val();
    var costo = tr.find(".precio_base").val();
    var cantidad = tr.find(".cant_dev").val();
    var stock = parseInt(tr.find(".stock").val());
    if (cantidad == "") {
      cantidad = 0;
    }
    var subtotal2 = parseInt(cantidad) * parseFloat(costo);
    total_servicio += subtotal2;

    tr.find(".subtotal").val(subtotal2.toFixed(2));

  });
  total = total_producto + total_servicio;
  $('#total').val(total.toFixed(2));
  $('#total1').val("$" + total.toFixed(2));

  $('#totals .total_final').text("$ " + total.toFixed(2));


  var data = $('#tipodoc').select2('data');
  if (data) {
    if (data[0].id == 3) {
      total_s_iva = total - total * 0.13 //falta traer el impuesto iva del controller Ventas y  asu vez pasar a cada formulario agregar, editar y finalizar venta pendiente !!!!
      total_iva = total * 0.13
      $('.total_s_iva').text("$ " + total_s_iva.toFixed(2));
      $('.total_iva').text("$ " + total_iva.toFixed(2));
    }
    else {
      $('.total_s_iva').text("$ 0.00");
      $('.total_iva').text("$ 0.00");
    }
  }



}
$(document).on("click", ".cerrar", function () {
  $("#btn_add_fact").removeAttr("disabled");
  $("#btn_add_fact").prop("disabled", false)
});
$(document).on("click", ".delete_tr", function () {
  $(this).parents("tr").remove();
  totales();
});

$(document).on("click", ".delete", function () {
  $(this).parents("tr").remove();
});

function save_data() {
  $("#divh").show();
  $("#main_view").hide();

  var errors = false;
  var error_array = [];
  var array_json = [];
  var cuantos = 0;
  var total = $("#total").val();
  $("#table_producto tr").each(function (index) {
    var id_producto = $(this).find('.id_producto').val();
    var cantidad = parseInt($(this).find('.cantidad').val());
    var costo = parseFloat($(this).find('.costo').val());
    var est = $(this).find('.est').val();
    var precio_sugerido = parseFloat($(this).find('.precios').val());
    var id_precio = $(this).find('.precios>option:selected').attr("id_precio");

    var descuento = parseFloat($(this).find('.descuento').val());
    var precio_final = precio_sugerido - descuento;
    var subtotal = parseFloat($(this).find('.subtotal').val());
    var color = parseFloat($(this).find('.color').val());

    if (isNaN(costo)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un costo vacio');
    }
    if (isNaN(precio_sugerido)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un precio sugerido vacio');
    }
    if ($("#client").val() == "") {
      errors = true;
      error_array.push('Seleccione un cliente');
    }
    if (!isNaN(cantidad) && cantidad > 0) {
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
      obj.tipo = 0;//"PRODUCTO"
      obj.id_precio = id_precio;
      //convert object to json string
      text = JSON.stringify(obj);
      array_json.push(text);
      cuantos++;
    }
    else {
      errors = true;
      error_array.push('No. ' + id_producto + ' hay una cantidad con valor cero o vacia');
    }
  });
  $("#table_servicio tr").each(function (index) {
    var id_producto = $(this).find('.id_producto').val();
    var cantidad = parseInt($(this).find('.cantidad').val());
    var costo = parseFloat($(this).find('.costo').val());
    var est = $(this).find('.est').val();
    var precio_sugerido = parseFloat($(this).find('.precio_sugerido').val());
    var descuento = parseFloat($(this).find('.descuento').val());
    var precio_final = parseFloat($(this).find('.precio_base').val());
    var subtotal = parseFloat($(this).find('.subtotal').val());
    var color = parseFloat($(this).find('.color').val());
    var est = "SERVICIO";
    var color = -1;
    if (isNaN(costo)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un costo vacio');
    }
    if (isNaN(precio_sugerido)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un precio sugerido vacio');
    }

    if (!isNaN(cantidad) && cantidad > 0) {
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
      obj.tipo = "1";//"SERVICIO"
      //convert object to json string
      text = JSON.stringify(obj);
      array_json.push(text);
      cuantos++;
    }
    else {
      errors = true;
      error_array.push('No. ' + id_producto + ' hay una cantidad con valor cero o vacia');
    }
  });
  var json_arr = '[' + array_json + ']';
  $("#data_ingreso").val(json_arr);
  if (cuantos == 0) {
    errors = true;
    error_array.push('Llene los datos de al menos un producto');
    $("#main_view").show();
    $("#divh").hide();
    $("#btn_add_new").prop("disabled", false)
  }

  if (errors == false) {
    let form = $("#form_add");
    let formdata = false;
    if (window.FormData) {
      formdata = new FormData(form[0]);
    }
    $.ajax({
      type: 'POST',
      url: url + '/agregar',
      cache: false,
      data: formdata ? formdata : form.serialize(),
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function (data) {
        $("#divh").hide();
        $("#main_view").show();
        notification(data.type, data.title, data.msg);
        if (data.type == "success") {
          var msg = "Presione Enter para continuar"
          show_reference(data.type, data.title, msg, data.referencia, total);
          //setTimeout("reload();", 1500);
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
    notification("Error", "Error en formulario", error_array.join(",<br>"));

    $("#btn_add").removeAttr("disabled");
    $("#divh").hide();
    $("#main_view").show();
    $('#viewModal').modal('close');
  }
}
function show_reference(type, title, msg, referencia, total) {

  Swal.fire({

    title: "<b>Referencia <i># " + referencia + "</i><br>$ " + total + "</b>",
    type: type,
    text: msg,
    showCancelButton: false,
    confirmButtonColor: '#283593',
    confirmButtonText: 'Continuar',
  }).then((result) => {
    setTimeout("reload()", 500);
  });
}
function edit_data() {
  $("#divh").show();
  $("#main_view").hide();

  var errors = false;
  var error_array = [];
  var array_json = [];
  var cuantos = 0;
  var total1 = $('#total1').val();
  var total = $('#total').val();


  $("#table_producto tr").each(function (index) {
    var id_producto = $(this).find('.id_producto').val();
    var cantidad = parseInt($(this).find('.cantidad').val());
    var costo = parseFloat($(this).find('.costo').val());
    var est = $(this).find('.est').val();
    var precio_sugerido = parseFloat($(this).find('.precios').val());
    var id_precio = $(this).find('.precios>option:selected').attr("id_precio");
    var descuento = parseFloat($(this).find('.descuento').val());
    var precio_final = precio_sugerido - descuento;
    var subtotal = parseFloat($(this).find('.subtotal').val());
    var color = parseFloat($(this).find('.color').val());

    if (isNaN(costo)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un costo vacio');
    }
    if (isNaN(precio_sugerido)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un precio sugerido vacio');
    }

    if (!isNaN(cantidad) && cantidad > 0) {
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
      obj.tipo = "0";//"PRODUCTO"
      obj.id_precio = id_precio;
      //convert object to json string
      text = JSON.stringify(obj);
      array_json.push(text);
      cuantos++;
    }
    else {
      errors = true;
      error_array.push('No. ' + id_producto + ' hay una cantidad con valor cero o vacia');
    }
  });
  $("#table_servicio tr").each(function (index) {
    var id_producto = $(this).find('.id_producto').val();
    var cantidad = parseInt($(this).find('.cantidad').val());
    var costo = parseFloat($(this).find('.costo').val());
    var est = $(this).find('.est').val();
    var precio_sugerido = parseFloat($(this).find('.precio_sugerido').val());
    var descuento = parseFloat($(this).find('.descuento').val());
    var precio_final = parseFloat($(this).find('.precio_base').val());
    var subtotal = parseFloat($(this).find('.subtotal').val());
    var color = parseFloat($(this).find('.color').val());
    var est = "SERVICIO";
    var color = -1;
    if (isNaN(costo)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un costo vacio');
    }
    if (isNaN(precio_sugerido)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un precio sugerido vacio');
    }

    if (!isNaN(cantidad) && cantidad > 0) {
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
      obj.tipo = "1";//"SERVICIO"
      //convert object to json string
      text = JSON.stringify(obj);
      array_json.push(text);
      cuantos++;
    }
    else {
      errors = true;
      error_array.push('No. ' + id_producto + ' hay una cantidad con valor cero o vacia');
    }
  });
  var json_arr = '[' + array_json + ']';
  $("#data_ingreso").val(json_arr);
  if (cuantos == 0) {
    errors = true;
    error_array.push('Llene los datos de al menos un producto');
  }

  if ($("#id_cliente").val() == "") {
    error_array.push('Seleccione un cliente');
  }

  if (errors == false) {
    let form = $("#form_edit");
    let formdata = false;
    if (window.FormData) {
      formdata = new FormData(form[0]);
    }
    $.ajax({
      type: 'POST',
      url: url + '/editar',
      cache: false,
      data: formdata ? formdata : form.serialize(),
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function (data) {
        $("#divh").hide();
        $("#main_view").show();
        notification(data.type, data.title, data.msg);
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
    notification("Error", "Error en formulario", error_array.join(",<br>"));
    $("#btn_add").removeAttr("disabled");
    $("#divh").hide();
    $("#main_view").show();
  }
}

function fin_data() {
  var id_venta = $("#id_venta").val();
  var errors = false;
  var error_array = [];
  var array_json = [];
  var cuantos = 0;
  var total1 = $('#total1').val();
  var total = $('#total').val();

  $("#table_producto tr").each(function (index) {
    var id_producto = $(this).find('.id_producto').val();
    var cantidad = parseInt($(this).find('.cantidad').val());
    var costo = parseFloat($(this).find('.costo').val());
    var est = $(this).find('.est').val();
    var precio_sugerido = parseFloat($(this).find('.precios').val());
    var id_precio = $(this).find('.precios>option:selected').attr("id_precio");

    var descuento = parseFloat($(this).find('.descuento').val());
    var precio_final = precio_sugerido - descuento;
    var subtotal = parseFloat($(this).find('.subtotal').val());
    var color = parseFloat($(this).find('.color').val());

    if (isNaN(costo)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un costo vacio');
    }
    if (isNaN(precio_sugerido)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un precio sugerido vacio');
    }

    if (!isNaN(cantidad) && cantidad > 0) {
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
      obj.tipo = "0";//"PRODUCTO"
      obj.id_precio = id_precio;
      //convert object to json string
      text = JSON.stringify(obj);
      array_json.push(text);
      cuantos++;
    }
    else {
      errors = true;
      error_array.push('No. ' + id_producto + ' hay una cantidad con valor cero o vacia');
    }
  });
  $("#table_servicio tr").each(function (index) {
    var id_producto = $(this).find('.id_producto').val();
    var cantidad = parseInt($(this).find('.cantidad').val());
    var costo = parseFloat($(this).find('.costo').val());
    var est = $(this).find('.est').val();
    var precio_sugerido = parseFloat($(this).find('.precio_sugerido').val());
    var descuento = parseFloat($(this).find('.descuento').val());
    var precio_final = parseFloat($(this).find('.precio_base').val());
    var subtotal = parseFloat($(this).find('.subtotal').val());
    var color = parseFloat($(this).find('.color').val());
    var est = "SERVICIO";
    var color = -1;
    if (isNaN(costo)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un costo vacio');
    }
    if (isNaN(precio_sugerido)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un precio sugerido vacio');
    }

    if (!isNaN(cantidad) && cantidad > 0) {
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
      obj.tipo = "1";//"SERVICIO"
      //convert object to json string
      text = JSON.stringify(obj);
      array_json.push(text);
      cuantos++;
    }
    else {
      errors = true;
      error_array.push('No. ' + id_producto + ' hay una cantidad con valor cero o vacia');
    }
  });
  var json_arr = '[' + array_json + ']';
  $("#data_ingreso").val(json_arr);
  if (cuantos == 0) {
    errors = true;
    error_array.push('Llene los datos de al menos un producto');
  }

  if ($("#id_cliente").val() == "") {
    error_array.push('Seleccione un cliente');
  }

  if (errors == false) {
    let form = $("#form_fin");
    let formdata = false;
    if (window.FormData) {
      formdata = new FormData(form[0]);
    }
    var urls = url + "/get_data_cliente/" + id_venta;
    $.ajax({
      type: 'POST',
      url: url + '/finalizar',
      cache: false,
      data: formdata ? formdata : form.serialize(),
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function (data) {
        //  $("#divh").hide();
        //$("#main_view").show();
        notification(data.type, data.title, data.msg);
        if (data.type == "success") {
          //Open the model

          $('#viewModal .modal-content').load(urls);
          setTimeout(setselect, 1000);
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
    notification("Error", "Error en formulario", error_array.join(",<br>"));
    $("#btn_add").removeAttr("disabled");
    $("#divh").hide();
    $("#main_view").show();
  }
}
function devolver_data() {
  $("#divh").show();
  $("#main_view").hide();

  var errors = false;
  var error_array = [];
  var array_json = [];
  var cuantos = 0;
  var total1 = $('#total1').val();
  var total = $('#total').val();


  $("#table_producto tr").each(function (index) {
    var id_producto = $(this).find('.id_producto').val();
    var id_detalle = $(this).find('.id_det').val();
    var cantidad = parseInt($(this).find('.cantidad').val());
    var cant_dev = parseInt($(this).find('.cant_dev').val());
    var devante = parseInt($(this).find('.devante').val());
    var costo = parseFloat($(this).find('.costo').val());
    var tipo_prod = $(this).find('.tipo_prod').val();
    var precio_final = parseFloat($(this).find('.precio_base').val());
    var subtotal = parseFloat($(this).find('.subtotal').val());
    var color = parseFloat($(this).find('.color').val());
    var est = "PRODUCTO";
    if (isNaN(costo)) {
      costo = 0;
    }
    if (isNaN(devante)) {
      devante = 0;
    }
    if (isNaN(cant_dev)) {
      cant_dev = 0;
    }
    if (!isNaN(cantidad) && !isNaN(cant_dev)) {

      var obj = new Object();
      obj.id_producto = id_producto;
      obj.cantidad = cantidad;
      obj.costo = costo;
      obj.precio_final = precio_final;
      obj.subtotal = subtotal;
      obj.color = color;
      obj.est = est;
      obj.tipo_prod = tipo_prod
      obj.id_detalle = id_detalle;
      obj.cant_dev = cant_dev;
      obj.devante = devante;
      //convert object to json string
      text = JSON.stringify(obj);
      if (cant_dev > 0) {
        cuantos++;
        array_json.push(text);
      }
    }
    else {
      errors = true;
      error_array.push('No. ' + id_producto + ' hay una cantidad con valor cero o vacia');
    }
  });
  $("#table_servicio tr").each(function (index) {
    var id_producto = $(this).find('.id_producto').val();
    var id_detalle = $(this).find('.id_det').val();
    var cantidad = parseInt($(this).find('.cantidad').val());
    var cant_dev = parseInt($(this).find('.cant_dev').val());
    var devante = parseInt($(this).find('.devante').val());
    var costo = parseFloat($(this).find('.costo').val());
    var tipo_prod = $(this).find('.tipo_prod').val();
    var precio_final = parseFloat($(this).find('.precio_base').val());
    var subtotal = parseFloat($(this).find('.subtotal').val());

    var est = "SERVICIO";
    var color = -1;
    if (isNaN(costo)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un costo vacio');
    }
    if (isNaN(devante)) {
      devante = 0;
    }
    if (isNaN(cant_dev)) {
      cant_dev = 0;
    }
    if (!isNaN(cantidad) && !isNaN(cant_dev)) {
      var obj = new Object();
      obj.id_producto = id_producto;
      obj.cantidad = cantidad;
      obj.costo = costo;
      obj.precio_final = precio_final;
      obj.subtotal = subtotal;
      obj.color = color;
      obj.est = est;
      obj.tipo_prod = tipo_prod
      obj.id_detalle = id_detalle;
      obj.cant_dev = cant_dev;
      obj.devante = devante;
      //convert object to json string
      text = JSON.stringify(obj);
      if (cant_dev > 0) {
        cuantos++;
        array_json.push(text);
      }

    }
    else {
      errors = true;
      error_array.push('No. ' + id_producto + ' hay una cantidad con valor  vacia');
    }
  });
  var json_arr = '[' + array_json + ']';
  $("#data_ingreso").val(json_arr);
  if (cuantos == 0) {
    errors = true;
    error_array.push('Llene los datos de al menos un producto');
  }

  if ($("#id_cliente").val() == "") {
    error_array.push('Seleccione un cliente');
  }

  if (errors == false) {
    let form = $("#form_devolver");
    let formdata = false;
    if (window.FormData) {
      formdata = new FormData(form[0]);
    }
    var urls = url + '/devolver';
    $.ajax({
      type: 'POST',
      url: urls,
      cache: false,
      data: formdata ? formdata : form.serialize(),
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function (data) {
        $("#divh").hide();
        $("#main_view").show();
        notification(data.type, data.title, data.msg);
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
    notification("Error", "Error en formulario", error_array.join(",<br>"));
    $("#btn_add").removeAttr("disabled");
    $("#divh").hide();
    $("#main_view").show();
  }
}
function setselect() {
  $('.modal-body #process').val("finalizar")
}
$(document).on('submit', '#form_edit_cte', function (e) {
  e.preventDefault();
  $(this).parsley().validate();
  if ($(this).parsley().isValid()) {
    $("#btn_edit_cte").prop("disabled", true)
    save_data_cte();
  }
});
$('#form_edit_cte').on('keypress', function (e) {
  if (e.keyCode === 13) {
    e.preventDefault();
  }
});
function save_data_cte() {
  var id_factura = $("#id_vta").val();
  var id_cliente = $("#id_client").val();
  var efectivo = parseFloat($('#efectivo').val());
  var cambio = parseFloat($('#cambio').val());
  var process = $("#process").val();
  var datapago = $('#tipo_pago').select2('data');
  var cambiar = $("#cambio").val();
  var errors = false;
  var error_array = [];
  if (datapago) {
    if (datapago[0].id == 2) {
      efectivo = '0.00';
      cambio = '0.00';
    }
    if (datapago[0].id == 3) {
      if (isNaN(cambiar) || cambiar == 0) {
        errors = true;
        error_array.push('dias de Credito debe ser mayor que cero!');
      }
      cambio = '0.00';
    }

  }
  if (errors == false) {
    let form = $("#form_edit_cte");
    let formdata = false;
    if (window.FormData) {
      formdata = new FormData(form[0]);
    }
    $.ajax({
      type: 'POST',
      url: url + '/up_data_client',
      cache: false,
      data: formdata ? formdata : form.serialize(),

      contentType: false,
      processData: false,
      dataType: 'json',
      success: function (data) {

        notification(data.type, data.title, data.msg);

        if (data.type == "success") {
          if (data.opsys == 'Linux') {
            if (data.tipodoc == '1') {
              $.post("http://" + data.dir_print + "printpos1.php", {
                totales: data.totales,
                total_letras: data.total_letras,
                efectivo: efectivo,
                cambio: cambio,
                encabezado: data.encabezado,
                cuerpo: data.cuerpo,
                pie: data.pie,
                img:data.img,
              });
            }
            if (data.tipodoc == '2') {
              $.post("http://" + data.dir_print + "printcof1.php", {
                totales: data.totales,
                total_letras: data.total_letras,
                encabezado: data.encabezado,
                cuerpo: data.cuerpo,
                pie: data.pie,
              });
            }
            if (data.tipodoc == '3') {
              $.post("http://" + data.dir_print + "printccf1.php", {
                totales: data.totales,
                total_letras: data.total_letras,
                encabezado: data.encabezado,
                cuerpo: data.cuerpo,
                pie: data.pie,
              });
            }
          }
          else {
            var direc = "http://" + data.dir_print + "printposwin1.php";
            if (data.tipodoc == '1') {
              $.post(direc, {
                totales: data.totales,
                total_letras: data.total_letras,
                efectivo: efectivo,
                cambio: cambio,
                encabezado: data.encabezado,
                cuerpo: data.cuerpo,
                pie: data.pie,
                shared_printer_pos: data.dir_print_pos,
                img:data.img,
              })
            }
          }
          if (process == "facturar") {
            setTimeout("reload_current();", 1500);
          }
          else {
            setTimeout("reload();", 2000);
          }
        }

      }
    });
  }
  else {
    notification("Error", "Error en formulario", error_array.join(",<br>"));
    $("#btn_edit_cte").prop("disabled", false)
  }
}
$(document).on("keyup", "#efectivo", function (e) {
  if ($("#tipo_pago").val() == 1) {
    var efectivo = parseFloat($('#efectivo').val());
    var totalfinal = parseFloat($('#total').val());

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
          notification("warning", "Advertencia","El valor a entregar no puede ser menor que el total");
      }
    }
    //if (e.keyCode == 13 && $(this).val()!=""){
    if (e.which == 13 && $(this).val() != "" && efectivo>=totalfinal) {
      save_data_cte();
    }
  }
});
//Si el text cambio se usa para el voucher_pago
$(document).on("keyup", "#cambio", function (e) {
  var tipo_pago = $("#tipo_pago").val();
  var efectivo = parseFloat($('#efectivo').val());
  if (tipo_pago != 1) {
    if (e.which == 13) {
      if (!isNaN(parseFloat(efectivo))) {
        save_data_cte();
      }
    }
  }

});
//function to round 2 decimal places
function round(value, decimals) {
  return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
}
function actualiza_client() {

}

function reload() {
  location.href = url;
}

function reload_current() {
  location.reload()
}

$(document).on("click", ".delete_row", function (event) {
  event.preventDefault()
  let id_row = $(this).attr("id");
  let dataString = "id=" + id_row + "&csrf_test_name=" + token;
  Swal.fire({
    title: 'Alerta!!',
    text: "Estas seguro de eliminar este registro?!",
    type: 'error',
    target: '#page-top',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Aceptar',
    cancelButtonText: 'Cancelar',
  }).then((result) => {
    if (result.value) {
      $.ajax({
        type: "POST",
        url: url + "/delete",
        data: dataString,
        dataType: 'json',
        success: function (data) {
          notification(data.type, data.title, data.msg);
          if (data.type == "success") {
            setTimeout("reload();", 1500);
          }
        }
      });
    }
  });
});


$(document).on("click", ".delete_trd", function () {
  $(this).parents("tr").remove();
});


function reload_current_des() {
  location.reload()
}

$(document).on('click', '.detail', function (event) {
  $('#viewModal .modal-content').load(url + "/detalle/" + $(this).attr('data-id'));
});

$(document).on('click', '.status_change', function (event) {
  $('#viewModal .modal-content').load(url + "/change_state/" + $(this).attr('data-id'));
});

$(document).on('click', '.change_s', function (event) {
  let id_row = $(this).attr("id_v");
  var id_estado = $(".estado").val();
  let dataString = "id=" + id_row + "&csrf_test_name=" + token + "&id_estado=" + id_estado;
  $.ajax({
    type: "POST",
    url: url + "/change",
    data: dataString,
    dataType: 'json',
    success: function (data) {
      notification(data.type, data.title, data.msg);
      if (data.type == "success") {
        setTimeout("reload();", 1500);
      }
    }
  });
});
;
//facturacion directa
function save_data_fact() {
  var errors = false;
  var error_array = [];
  var array_json = [];
  var cuantos = 0;
  $("#table_producto tr").each(function (index) {
    var id_producto = $(this).find('.id_producto').val();
    var cantidad = parseInt($(this).find('.cantidad').val());
    var costo = parseFloat($(this).find('.costo').val());
    var est = $(this).find('.est').val();
    var precio_sugerido = parseFloat($(this).find('.precios').val());
    var descuento = parseFloat($(this).find('.descuento').val());
    var precio_final = precio_sugerido - descuento;
    var subtotal = parseFloat($(this).find('.subtotal').val());
    var color = parseFloat($(this).find('.color').val());
    if (isNaN(costo)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un costo vacio');
    }
    if (isNaN(precio_sugerido)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un precio sugerido vacio');
    }
    if (!isNaN(cantidad) && cantidad > 0) {
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
      obj.tipo = 0;//"PRODUCTO"
      text = JSON.stringify(obj);
      array_json.push(text);
      cuantos++;
    }
    else {
      errors = true;
      error_array.push('No. ' + id_producto + ' hay una cantidad con valor cero o vacia');
    }
  });
  $("#table_servicio tr").each(function (index) {
    var id_producto = $(this).find('.id_producto').val();
    var cantidad = parseInt($(this).find('.cantidad').val());
    var costo = parseFloat($(this).find('.costo').val());
    var est = $(this).find('.est').val();
    var precio_sugerido = parseFloat($(this).find('.precio_sugerido').val());
    var descuento = parseFloat($(this).find('.descuento').val());
    var precio_final = parseFloat($(this).find('.precio_base').val());
    var subtotal = parseFloat($(this).find('.subtotal').val());
    var color = parseFloat($(this).find('.color').val());
    var est = "SERVICIO";
    var color = -1;
    if (isNaN(costo)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un costo vacio');
    }
    if (isNaN(precio_sugerido)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un precio sugerido vacio');
    }
    if (!isNaN(cantidad) && cantidad > 0) {
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
      obj.tipo = "1";//"SERVICIO"
      text = JSON.stringify(obj);
      array_json.push(text);
      cuantos++;
    }
    else {
      errors = true;
      error_array.push('No. ' + id_producto + ' hay una cantidad con valor cero o vacia');
    }
  });
  var json_arr = '[' + array_json + ']';
  $("#data_ingreso").val(json_arr);
  if (cuantos == 0) {
    errors = true;
    error_array.push('Llene los datos de al menos un producto');
  }
  if ($("#id_cliente").val() == "") {
    error_array.push('Seleccione un cliente');
  }
  if (errors == false) {
    let form = $("#form_add_fact");
    let formdata = false;
    if (window.FormData) {
      formdata = new FormData(form[0]);
    }
    $.ajax({
      type: 'POST',
      url: url + '/facturar',
      cache: false,
      data: formdata ? formdata : form.serialize(),
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function (data) {
        notification(data.type, data.title, data.msg);
        if (data.type == "success") {
          var urls = url + "/get_data_cliente/" + data.id_factura;

          $('.modal-content').load(urls, function (result) {
            $('#viewModal').modal({
              show: true,
              backdrop: 'static',
              keyboard: false
            });

            $('.modal-body #process').val("facturar")
          });
        }
        else {
          $("#btn_add_fact").removeAttr("disabled");
        }
      }
    });
  }
  else {
    notification("Error", "Error en formulario", error_array.join(",<br>"));
    $("#btn_add_fact").removeAttr("disabled");
    $("#btn_add_fact").prop("disabled", false)
    $("#main_view").show();
  }
}
$('#viewModal').on('shown.bs.modal', function () {
  /*
  setTimeout(function () {
    $('#efectivo').focus();
    $("#tipo_pago").select2();
  }, 1000);*/

  $({to:0}).animate({to:1}, duration, function() {
    if($('#efectivo').length>0){
      $('#efectivo').focus();
      $("#tipo_pago").select2();
    }
    });
});
/*
$("#viewModal").on('hidden.bs.modal', function () {
  setTimeout("reload_current();", 1500);
});
*/
//devoluciones
$(document).on('keyup', '#cant_devol', function (event) {
  var valor = parseInt($(this).val());

  if (isNaN(valor)) {
    valor = 0;
  }
  var suma = 0;
  var devant = 0;
  var cantvend = 0;
  var precio_venta = 0;
  var subtotal = 0;

  precio_venta = parseFloat($(this).closest('tr').find('#pv').val());
  cantvend = parseInt($(this).closest('tr').find('.cantidad').val());

  console.log(precio_venta);
  devant = parseInt($(this).closest('tr').find('#dev_ant').val());
  if (isNaN(devant)) {
    devant = 0;
  }
  suma = devant + valor;


  if (suma > cantvend) {

    valor = cantvend - (suma - valor);
    $(this).val(valor);
    subtotal = valor * precio_venta;
    subtotal = round(subtotal, 4);
    $(this).closest('tr').find('.subtotal').val(subtotal);
  }
  else {
    subtotal = valor * precio_venta;
    subtotal = round(subtotal, 4);
    $(this).closest('tr').find('.subtotal').val(subtotal);
  }

  total_dev();
});
//anular venta no finalizada
$(document).on("click", ".state_change", function (event) {
  event.preventDefault()
  let id_row = $(this).attr("id");
  let dataString = "id=" + id_row + "&csrf_test_name=" + token;
  Swal.fire({
    title: 'Alerta!!',
    text: "Estas seguro de cambiar estado este registro?!",
    type: 'error',
    target: '#page-top',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Aceptar',
    cancelButtonText: 'Cancelar',
  }).then((result) => {
    if (result.value) {
      $.ajax({
        type: "POST",
        url: url + "/state_change",
        data: dataString,
        dataType: 'json',
        success: function (data) {
          notification(data.type, data.title, data.msg);
          if (data.type == "success") {
            setTimeout("reload();", 1500);
          }
        }
      });
    }
  });
});
//finalizar por referencia
$(document).on('keyup', '#referencia', function (event) {
  if (event.which === 13) {
    return carga_venta();
  } else {
    event.preventDefault();
  }
});

function carga_venta() {
  var referencia = $("#referencia").val();
  let dataString = "referencia=" + referencia + "&csrf_test_name=" + token;
  $("#table_producto").empty();
  $("#table_servicio").empty();
  $.ajax({
    type: "POST",
    url: url + "/cargar_venta",
    data: dataString,
    dataType: 'json',
    success: function (data) {

      notification(data.type, data.title, data.msg);
      if (data.type == "success") {
        id_venta = data.id_venta;
        $("#btn_finref").prop("disabled", false);
        $("#btn_finref").removeClass("disabled");
        $("#id_venta").val(data.id_venta);
        $("#client").val(data.id_cliente).trigger('change');
        $("#fecha").val(data.fecha);
        $('#total1').val(data.total);
        $("#table_producto").empty();
        $("#table_servicio").empty();
        llenar_prods(data.detprod)
        llenar_serv(data.detserv);
        totales();
      } else {
        id_venta = 0;
        $("#table_producto").empty();
        $("#table_servicio").empty();
      }
    }
  });
}

function llenar_prods(arr_prod) {
  $.each(arr_prod, function (i, item) {
    nombre = item.nombre + " " + item.color;
    let fila = "<tr>";
    fila += "<td style='width:5%;'>" + item.id_producto + "</td>";
    fila += "<td style='width:25%;'><input type='hidden' class='id_producto' value='" + item.id_producto + "'><input type='hidden' class='id_s' value='" + item.id_s + "'><input type='hidden' class='nombre' value='" + nombre + "'>" + nombre + "</td>";
    fila += "<td style='width:10%;'><input type='hidden' class='color' value='" + item.id_color + "'><input type='hidden' class='stock' value='" + item.stock + "' style='width:100%;'>" + item.stock + "</td>";
    fila += "<td style='width:10%;'><input type='text' class='form-control cantidad numeric' value='" + item.cantidad + "' style='width:100%;'></td>";
    fila += "<td style='width:10%;'><input type='hidden' class='form-control costo decimal' value='" + item.costo + "' style='width:100%;'><select class='est'><option value='NUEVO'>NUEVO</option><option value='USADO'>USADO</option></select></td>";
    fila += "<td style='width:10%;'>" + item.precios + "</td>";
    fila += "<td style='width:10%;'><input type='text' class='form-control descuento' readonly value='" + item.descuento + "' style='width:100%;'></td>";
    fila += "<td style='width:10%;'><input type='text' class='form-control subtotal' readonly value='" + item.subtotal + "' style='width:100%;'></td>";
    fila += "<td  style='width:10%;' class='text-center'><a class='btn btn-danger delete_tr' style='color: white'><i class='mdi mdi-trash-can'></i></a></td>";
    fila += "</tr>";
    $("#table_producto").prepend(fila);
    $(".sel").select2();
    $(".est").select2();
    $(".numeric").numeric({ negative: false, decimals: false });
    $(".decimal").numeric({ negative: false, decimalPlaces: 4 });
  });
}
function llenar_serv(arr_ser) {
  $.each(arr_ser, function (i, item) {
    nombre = item.nombre + " (SERVICIO)";
    let fila = "<tr>";
    fila += "<td style='width:5%;'>" + item.id_producto + "</td>";
    fila += "<td style='width:25%;'><input type='hidden' class='id_producto' value='" + item.id_producto + "'><input type='hidden' class='nombre' value='" + nombre + "'>" + nombre + "</td>";
    fila += "<td style='width:10%;'><input type='hidden' class='color' value='-1'><input type='hidden' class='stock' value='-1' style='width:100%;'>-</td>";
    fila += "<td style='width:10%;'><input type='text' class='form-control cantidad numeric' value='" + item.cantidad + "' style='width:100%;'></td>";
    fila += "<td style='width:10%;'><input type='hidden' class='form-control costo decimal' value='" + item.costo + "' style='width:100%;'><input type='hidden' class='form-control precio_minimo decimal' value='" + item.precio_minimo + "' style='width:100%;'>$ " + item.precio_sugerido + "</td>";
    fila += "<td style='width:10%;'><input type='hidden' class='form-control precio_sugerido decimal' value='" + item.precio_sugerido + "' style='width:100%;'><input type='text' class='form-control precio_base decimal' value='" + item.precio_fin + "' style='width:100%;'></td>";
    fila += "<td style='width:10%;'><input type='text' class='form-control descuento' readonly value='" + item.descuento + "' style='width:100%;'></td>";
    fila += "<td style='width:10%;'><input type='text' class='form-control subtotal' readonly value='" + item.subtotal + "' style='width:100%;'></td>";
    fila += "<td style='width:10%;' class='text-center'><a class='btn btn-danger delete_tr' style='color: white'><i class='mdi mdi-trash-can'></i></a></td>";
    fila += "</tr>";
    $("#table_servicio").prepend(fila);
    $(".numeric").numeric({ negative: false, decimals: false });
    $(".decimal").numeric({ negative: false, decimalPlaces: 4 });
  });
}

//finalizar por referencia o venta directa !!!
function fin_data_ref() {
  var id_venta = $("#id_venta").val();
  var errors = false;
  var error_array = [];
  var array_json = [];
  var cuantos = 0;
  var total1 = $('#total1').val();
  var total = $('#total').val();
  var datapago = $('#tipo_pago').select2('data');
  var cambiar = $("#cambio").val();
  var errors = false;
  let efect =  $("#efectivo").val();
  var error_array = [];

  if (datapago) {
    if (datapago[0].id == 1) {
      if ( efect ==0 ||efect =='' || isNaN(efect)) {
        errors = true;
        error_array.push('Debe registrar el monto efectivo !');
      }
      if ( parseFloat(efect) <parseFloat(total)) {
        errors = true;
        error_array.push('el monto efectivo no debe ser menor que total!');
      }
    }
    if (datapago[0].id == 2) {
      efectivo = '0.00';
      cambio = '0.00';
      if ( efect.trim()==='' || isNaN(efect)) {
        errors = true;
        error_array.push('Debe registrar el valor de la transaccion !');
      }
      if(cambiar==""){
        errors = true;
        error_array.push('Debe registrar el voucher de la transaccion !');
      }
    }
    if (datapago[0].id == 3) {
      if ( efect =='' || isNaN(efect)) {
        errors = true;
        error_array.push('Debe registrar el valor del abono( incluye 0) !');
      }
      if (isNaN(cambiar) || cambiar == 0) {
        errors = true;
        error_array.push('dias de Credito debe ser mayor que cero!');
      }
      cambio = '0.00';
    }

  }
  //fin tipo_pago
  $("#table_producto tr").each(function (index) {
    var id_producto = $(this).find('.id_producto').val();
    var cantidad = parseInt($(this).find('.cantidad').val());
    var costo = parseFloat($(this).find('.costo').val());
    var est = $(this).find('.est').val();
    var precio_sugerido = parseFloat($(this).find('.precios').val());
    var id_precio = $(this).find('.precios>option:selected').attr("id_precio");

    var descuento = parseFloat($(this).find('.descuento').val());
    var precio_final = precio_sugerido - descuento;
    var subtotal = parseFloat($(this).find('.subtotal').val());
    var color = parseFloat($(this).find('.color').val());

    if (isNaN(costo)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un costo vacio');
    }
    if (isNaN(precio_sugerido)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un precio sugerido vacio');
    }

    if (!isNaN(cantidad) && cantidad > 0) {
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
      obj.tipo = "0";//"PRODUCTO"
      obj.id_precio = id_precio;
      //convert object to json string
      text = JSON.stringify(obj);
      array_json.push(text);
      cuantos++;
    }
    else {
      errors = true;
      error_array.push('No. ' + id_producto + ' hay una cantidad con valor cero o vacia');
    }
  });
  $("#table_servicio tr").each(function (index) {
    var id_producto = $(this).find('.id_producto').val();
    var cantidad = parseInt($(this).find('.cantidad').val());
    var costo = parseFloat($(this).find('.costo').val());
    var est = $(this).find('.est').val();
    var precio_sugerido = parseFloat($(this).find('.precio_sugerido').val());
    var descuento = parseFloat($(this).find('.descuento').val());
    var precio_final = parseFloat($(this).find('.precio_base').val());
    var subtotal = parseFloat($(this).find('.subtotal').val());
    var color = parseFloat($(this).find('.color').val());
    var est = "SERVICIO";
    var color = -1;
    if (isNaN(costo)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un costo vacio');
    }
    if (isNaN(precio_sugerido)) {
      costo = 0;
      errors = true;
      error_array.push('No. ' + id_producto + ' hay un precio sugerido vacio');
    }

    if (!isNaN(cantidad) && cantidad > 0) {
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
      obj.tipo = "1";//"SERVICIO"
      //convert object to json string
      text = JSON.stringify(obj);
      array_json.push(text);
      cuantos++;
    }
    else {
      errors = true;
      error_array.push('No. ' + id_producto + ' hay una cantidad con valor cero o vacia');
    }
  });
  var json_arr = '[' + array_json + ']';
  $("#data_ingreso").val(json_arr);
  if (cuantos == 0) {
    errors = true;
    error_array.push('Llene los datos de al menos un producto');
  }

  if ($("#id_cliente").val() == "") {
    error_array.push('Seleccione un cliente');
  }

  if (errors == false) {
    let form = $("#form_finref");
    let formdata = false;
    if (window.FormData) {
      formdata = new FormData(form[0]);
    }

    if (parseInt(id_venta) < 0 || id_venta == "") {
      id_venta = -1;
    }

    var url_tipo = url + '/fin_fact';
    $.ajax({
      type: 'POST',
      url: url_tipo,
      cache: false,
      data: formdata ? formdata : form.serialize(),
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function (data) {
        //  $("#divh").hide();
        //$("#main_view").show();
        notification(data.type, data.title, data.msg);
        if (data.type == "success") {
          //Open the model
          var urls = url + "/get_data_cliente/" + data.id_factura;

          $('.modal-content').load(urls, function (result) {
            $('#viewModal').modal({
              show: true,
              backdrop: 'static',
              keyboard: false
            });

            $('.modal-body #process').val("facturar")
          });
          setTimeout(setselect, 1000);
        }
        else {
          $("#divh").hide();
          $("#main_view").show();
          $("#btn_finref").removeAttr("disabled");
        }
      }
    });
  }
  else {

    notification("Error", "Error en formulario", error_array.join(",<br>"));
    $("#btn_finref").removeAttr("disabled");
    $("#divh").hide();
    $("#main_view").show();
    $({to:0}).animate({to:1}, duration, function() {
      $("#viewModal").modal("toggle");

      });

  }
}

$(document).on('change', '#client', function (event) {
  var clasifica = $('#client').select2('data');
  $('.precios').val(null).trigger('change');
  $(".precios").empty();
  let c = $("#client option:selected").text();
  let client =c.trim();
  if (client === 'MOSTRADOR') {
    $("#tipo_pago option[value='3']").remove();
  }
  else{
    $("#tipo_pago").append('<option value="3">CREDITO</option>');
  }
  get_porcent_client(clasifica[0].id);

  $("#table_producto tr").each(function (index) {
    var id_producto = $(this).find('.id_producto').val();
    var nombre = $(this).find('.nombre').val();
    var id_color = parseFloat($(this).find('.color').val());
    var cantidad = parseFloat($(this).find('.cantidad').val());
    $(this).remove();
    setTimeout(function () {
      new_producto(id_producto, nombre, id_color, cantidad);
      $('.cantidad').bind('keyup keypress blur', function () {
        totales();
      });

    }, 500);

    totales();
  });
  //fin cambio para ventas
});
//reimpresion de ticket venta
$(document).on("click", ".printicket", function () {

  var id_venta = $('#id_vta').val();
  $.ajax({
    type: 'POST',
    url: url + '/printdoc',
    data: "id_venta=" + id_venta + "&csrf_test_name=" + token + "&process=print",
    dataType: 'json',
    success: function (data) {
      notification(data.type, data.title, data.msg);
      var efectivo = data.totales;
      var cambio = 0.0;
      if (data.opsys == 'Linux') {

        if (data.tipodoc == '1') {
          $.post("http://" + data.dir_print + "printpos1.php", {
            totales: data.totales,
            total_letras: data.total_letras,
            efectivo: efectivo,
            cambio: cambio,
            encabezado: data.encabezado,
            cuerpo: data.cuerpo,
            pie: data.pie,
            img:data.img,
          });
        }
        if (data.tipodoc == '2') {
          $.post("http://" + data.dir_print + "printcof1.php", {
            totales: data.totales,
            total_letras: data.total_letras,
            encabezado: data.encabezado,
            cuerpo: data.cuerpo,
            pie: data.pie,
          });
        }
        if (data.tipodoc == '3') {
          $.post("http://" + data.dir_print + "printccf1.php", {
            totales: data.totales,
            total_letras: data.total_letras,
            encabezado: data.encabezado,
            cuerpo: data.cuerpo,
            pie: data.pie,
          });
        }
      }
      else {
        var direc = "http://" + data.dir_print + "printposwin1.php";
        if (data.tipodoc == '1') {
          $.post(direc, {
            totales: data.totales,
            total_letras: data.total_letras,
            efectivo: efectivo,
            cambio: cambio,
            encabezado: data.encabezado,
            cuerpo: data.cuerpo,
            pie: data.pie,
            shared_printer_pos: data.dir_print_pos,
              img:data.img,
          })
        }
      }
    }
  });


});
//cliente

$(document).on("click", "#btn_cliente", function () {
  //  e.preventDefault();
  var urls = url + "/new_data_client/";

  $('.modal-content').load(urls, function (result) {
    $('#viewModalCte').modal({
      show: true,
      backdrop: 'static',
      keyboard: false
    });
  });

  var valor=0;
  $({to:0}).animate({to:1},1000, function() {
      $('.modal-body #clasifica').select2();
      $('.modal-body s#nombre_cliente').focus();

      if( $(".modal-body input[name='contribuyente']:radio").is(':checked')) {
				 valor=$(".modal-body input[name='contribuyente']:checked").val();
			}
      if (valor==0){
        $("#viewModalCte .modal-body .divnit").hide();
        $("#viewModalCte .modal-body .divnrc").hide();
      }else{
        $("#viewModalCte .modal-body .divnit").show();
        $("#viewModalCte .modal-body .divnrc").show();
      }
		});

  //  });
});

//validar on change para radio
$(document).on('change','input[type=radio][name=contribuyente]', function () {
      if (this.value == 0) {
        $("#viewModalCte .modal-body .divnit").hide();
        $("#viewModalCte .modal-body .divnrc").hide();
      }
      else if (this.value ==1) {
        $("#viewModalCte .modal-body .divnit").show();
        $("#viewModalCte .modal-body .divnrc").show();
      }
  });
$(document).on("click", "#close_newcte", function () {
    $("#viewModalCte").modal('hide');
    reload_current();

});
$(document).on("click", "#close_fin", function () {
  $({to:0}).animate({to:1}, duration, function() {
    $('#viewModal').modal().hide();
    reload_current();
    });
});
$(document).on("click", "#btn_save_newcte", function (e) {
//$(document).on("submit", "#form_save_newcte", function (e) {
  e.preventDefault();
  $(this).parsley().validate();
  if ($(this).parsley().isValid()) {
    $("#btn_save_newcte").prop("disabled", true)
    save_new_cte();

  }
});
//guardar datos cliente nuevo
function save_new_cte() {
  //activar parsley para todos los input
  $('#viewModalCte .modal-body input').parsley();
  var error_array = [];
  var isValid = true;
  let token = $("#viewModalCte .modal-body #csrf_token_id").val();
  var clasifica=$("#viewModalCte .modal-body  #clasifica").val();
  var no=$("#viewModalCte .modal-body #nombre_cliente");
  var nombre=no.val();
  var du=$("#viewModalCte .modal-body #dui");
  var dui=du.val();
  var nit="";
  var nrc="";
   //validar cada input
  if( $("#viewModalCte .modal-body input[name='contribuyente']:radio").is(':checked')) {
     valor=$("#viewModalCte .modal-body input[name='contribuyente']:checked").val();
  }
  if (no.parsley().validate() !== true){
    isValid = false;
    error_array.push('Verificar nombre!');
  }
  if (du.parsley().validate() !== true) isValid = false;


  if (valor==1){
    var nr=$("#viewModalCte .modal-body #nrc");
    var ni=$("#viewModalCte .modal-body #nit");
    if (nr.parsley().validate() !== true) isValid = false;
    if (ni.parsley().validate() !== true){
      isValid = false;
      error_array.push('Verificar NIT');
    }
    nit=ni.val();
    nrc=nr.val();
  }
    var dataString={ nombre:nombre, nit:nit, dui:dui, nrc:nrc, clasifica:clasifica,csrf_test_name: token };

  if (isValid == true ) {
    $.ajax({
      type: 'POST',
      url: url + '/save_data_client',
      data: dataString,
      dataType: 'json',
      success: function (data) {
        notification(data.type, data.title, data.msg);
        if (data.type == "success") {
          id_cliente = data.id_cliente;
          nomcte= data.nomcte;
          if (id_cliente!=-1){
            $({to:0}).animate({to:1}, 1000, function() {
                var option = new Option(data.nomcte, data.id_cliente, true, true);
                 $('#client').append(option).trigger('change');
                 // manually trigger the `select2:select` event
                 $('#client').trigger({
                     type: 'select2:select',
                     params: { data: data}
                    })
            })
          }
          $('#viewModalCte').modal('hide');
        }
      }
    });
  }
  else {
    notification("Error", "Error en formulario", error_array.join(",<br>"));
    $("#btn_save_newcte").prop("disabled", false)
  }
}
$(document).on("click", "#close_vd", function () {
  $("#viewModal").modal('hide');
});
