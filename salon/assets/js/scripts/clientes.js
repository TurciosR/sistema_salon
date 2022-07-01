let url = base_url+"clientes";
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

    $("#departamento").change(function()
    {
        $("#municipio *").remove();
        $("#select2-municipio-container").text("");
        $.ajax({
            url:url+"/get_municipios",
            type: "POST",
            data: {
                id_departamento: $("#departamento").val(),
                csrf_test_name:token
            },
            success: function(opciones)
            {
                $("#select2-municipio-container").text("Seleccione");
                $("#municipio").html(opciones);
                $("#municipio").val("");
            }
        })
    });


    // Catch if user want to switch between taxpayer or not
    if ($("#switchTaxpayer").length) {
        $("#switchTaxpayer").change(function(){
            if ($("#switchTaxpayer").is(':checked')) {
                // Enable inputs nit, giro and nrc
                $("#nit").prop('disabled', false);
                $("#giro").prop('disabled', false);
                $("#nrc").prop('disabled', false);
                // Do inputs nit and nrc required
                $("#nit").attr('required', true);
                $("#nrc").attr('required', true);
            } else {
                $("#nit").prop('disabled', true);
                $("#giro").prop('disabled', true);
                $("#nrc").prop('disabled', true);
                // Remove required attribute from nit and nrc inputs
                $("#nit").removeAttr('required');
                $("#nrc").removeAttr('required');
            }
        });
    }

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

  var data = $('#clasifica').select2('data');
  var error_array = [];
  if(data) {
    if(data[0].id>0){
      errors = false;
    }
    else{
      errors = true;
    error_array.push('No ha seleccionado  Clasificación del Cliente');
    }
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
        }
    });
}
    else {
      notification("Error","Error en formulario",error_array.join(",<br>"));
      $("#divh").hide();
      $("#main_view").show();
          $("#btn_add").prop("disabled",false)
    }



}

function edit_data(){
    $("#divh").show();
    $("#main_view").hide();
  var data = $('#clasifica').select2('data');
  var error_array = [];
  if(data) {
    if(data[0].id>0){
      errors = false;
    }
    else{
      errors = true;
      error_array.push('No ha seleccionado  Clasificación del Cliente');
    }
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
        }
    });
}
    else {
      notification("Error","Error en formulario",error_array.join(",<br>"));
      $("#divh").hide();
      $("#main_view").show();
          $("#btn_edit").prop("disabled",false)
    }

}

function reload() {
    location.href = url;
}
