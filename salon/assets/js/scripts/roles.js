let url = base_url+"roles";
let token = $("#csrf_token_id").val()
$(document).ready(function () {

    $('#editable').DataTable({
        pageLength: 50,
        serverSide: true,
        order: [[0, "asc"]],
        ajax: {
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

});
$(document).on("click","#btn_save",function (e) {
    e.preventDefault();
    $("#form_add").parsley().validate();
    if ($("#form_add").parsley().isValid()){
        $("#btn_save").prop("disabled",true)
        save_data();
    }
});

$(document).on("click","#btn_edit",function (e) {
    e.preventDefault();
    $("#form_edit").parsley().validate();
    if ($("#form_edit").parsley().isValid()){
        $("#btn_edit").prop("disabled",true)
        edit_data();
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
    let data = {
        modules: [],
        csrf_test_name:token,
        nombre:$("#nombre").val(),
        descripcion:$("#descripcion").val(),
    };
    $(':checkbox:checked').each(function(i){
        data.modules.push({"module":$(this).val()});
    });

    Swal.fire({
        title: 'Alerta!',
        text: "¿Seguro de guardar este rol?",
        type: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: "POST",
                url: url + "/agregar",
                data: data,
                dataType: 'json',
                success: function (data) {
                    $("#divh").hide();
                    $("#main_view").show();
                    notification(data.type, data.title, data.msg);
                    if (data.type == "success") {
                        setTimeout("reload();", 1500);
                    }
                }
            });
        }else{
            $("#divh").hide();
            $("#main_view").show();
        }
    });
}

function edit_data(){
    $("#divh").show();
    $("#main_view").hide();
    let data = {
        modules: [],
        csrf_test_name:token,
        nombre:$("#nombre").val(),
        descripcion:$("#descripcion").val(),
        id_rol:$("#id_rol").val(),
    };
    $(':checkbox:checked').each(function(i){
        data.modules.push({"module":$(this).val()});
    });

    Swal.fire({
        title: 'Warning!',
        text: "¿Seguro de guardar este rol?",
        type: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: "POST",
                url: url + "/editar",
                data: data,
                dataType: 'json',
                success: function (data) {
                    $("#divh").hide();
                    $("#main_view").show();
                    notification(data.type, data.title, data.msg);
                    if (data.type == "success") {
                        setTimeout("reload();", 1500);
                    }
                }
            });
        }else{
            $("#divh").hide();
            $("#main_view").show();
        }
    });
}

function reload() {
    location.href = url;
}
