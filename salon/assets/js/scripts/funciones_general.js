$(document).ready(function () {

    $(".dropify").dropify({
        messages: {
            default: "Arrastra una imagen o click aqui",
            replace: "Arrastra y suelta, o click para reemplazar",
            remove: "Remover",
            error: "Ooops, algo salio mal."
        },
        error: {
            fileSize: "El archivo es muy grande(1M max)."
        }
    });
    $("#formulario").on('submit', function(e){
        e.preventDefault();
        $(this).parsley().validate();
        if ($(this).parsley().isValid()){
            senddata();
        }
    });

});

function senddata() {
    var form = $("#formulario");
    var formdata = false;
    if (window.FormData) {
        formdata = new FormData(form[0]);
    }
    $.ajax({
        type: 'POST',
        url: base_url+'configuracion/save_data',
        cache: false,
        data: formdata ? formdata : form.serialize(),
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (datax) {
            notification(datax.type,datax.title, datax.msg);
            if (datax.type == "success") {
                setTimeout("reload();", 1000);
            }
        }
    });
}

function reload() {
    location.href = base_url+'configuracion';
}
