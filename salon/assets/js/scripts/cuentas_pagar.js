let url = base_url+"cuentas_pagar";

let token = $("#csrf_token_id").val()


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


});

$(document).on("click",".delete_tr", function(event)
{
    event.preventDefault()
    let id_row = $(this).attr("id");
  let id_cuenta = $(this).attr("cuenta");
  let saldo = $(this).attr("saldo");
  let abono = $(this).attr("abono");
  let monto = $(this).attr("monto");
    let dataString = "id=" + id_row+"&id_cuenta=" + id_cuenta+"&monto=" + monto+"&saldo=" + saldo+"&abono=" + abono+"&csrf_test_name="+token;
    Swal.fire({
        title: 'Alerta!!',
        text: "Estas seguro de eliminar este abono?!",
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

$(document).on("click",".update_tr", function(event)
{
    event.preventDefault()
    let id_row = $(this).attr("id");
  let id_cuenta = $(this).attr("cuenta");
  let saldo = $(this).attr("saldo");
  let abono = $(this).attr("abono");
  let monto = $(this).attr("monto");

    var objAbono = $(this).closest('tr');
    var montoNuevo = (objAbono.find(".abono_monto")).val();
    //alert(montoNuevo);
    //console.log(montoAnterior.val());
    let dataString = "id=" + id_row+"&id_cuenta=" + id_cuenta+"&monto=" + monto+"&saldo=" + saldo+"&abono=" + abono +"&montoNuevo=" + montoNuevo+"&csrf_test_name="+token;
    Swal.fire({
        title: 'Alerta!!',
        text: "Estas seguro de actualizar este abono?!",
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
                url: url+"/update",
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

function save_data(){
    $("#divh").show();
    $("#main_view").hide();
    let form = $("#form_add");
    let formdata = false;
    if (window.FormData) {
        formdata = new FormData(form[0]);
    }
    $.ajax({
        type: 'POST',
        url: url+'/realizar_abono',
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
        }
    });
}

function edit_data(){
    $("#divh").show();
    $("#main_view").hide();
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
        }
    });
}

function reload() {
    location.href = url;
}

$(document).on("keyup", "#monto", function()
{
  setTimeout(validarMonto, 100);
});
$(document).on("keyup", ".monto", function()
{
    var monto = parseFloat($(this).val());
      var saldoMax = parseFloat($(this).attr("saldo"));
  //alert(monto +" - "+saldoMax);
  if (monto>saldoMax) {
    $(this).val(saldoMax);
  }
  else{

  }
});
function validarMonto(){
  var monto = parseFloat($("#monto").val());
  var saldoMax = parseFloat($("#monto").attr("saldo"));
  //alert(monto +" - "+saldoMax);
  if (monto>saldoMax) {
    $("#monto").val(saldoMax);
  }
  else{

  }
  return monto;
}
$(document).on("click", "#editable > tbody tr", function()
{
  //alert("aqui");
//   var id_cuenta = $(this).find('.cc').val();
  var id_proveedor = $(this).find('.id_p').val();
  var url1=base_url+'cuentas_pagar/cuentas_proveedor/'+id_proveedor;
  window.location =url1;
});
