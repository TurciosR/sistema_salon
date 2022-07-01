let url = base_url+"reportes";
let token = $("#csrf_token_id").val()

$(document).ready(function () {
    $('.select').select2();
    $("#generarReporte").click(function(){
        //alert("hola");
        var valor = $('#reportes').val();
        var tipoReporte = $('input[name=tipoReporte]:checked').val();
        var sucursal = $('#sucursal').val();
    //alert(valor);
        if (tipoReporte==undefined) {
            notification("warning","Error","debe seleccionar un tipo de reporte");
        }
        else{
            if (valor==undefined) {
                //alert("no es un numero");
            }
            else {
                //alert("aqui");
                var fechaInicio = $(".fechaInicio").val();
                var fechaFin = $(".fechaFin").val();
                window.open(url + "/generar/"+valor+"/"+tipoReporte+"/"+fechaInicio+"/"+fechaFin+"/"+sucursal);

            }
        }

    });

    $("#generarReporteKardex").click(function(){
        //alert("hola");
        var sucursal = $('#sucursalK').val();
        var idP = $('#selectProductos').val();
        var color = $('#selectProductos option:selected').attr("color");
        //alert(valor);
        //alert("aqui");
        var fechaInicio = $(".fechaInicioK").val();
        var fechaFin = $(".fechaFinK").val();
        if (idP=="") {
            notification("warning","Alerta","debe seleccionar un producto");
        }
        else {
            window.open(url + "/generar_kardex/"+fechaInicio+"/"+fechaFin+"/"+sucursal+"/"+idP+"/"+color);
        }
    });

    $("#generarReporteExist").click(function(){
        //alert("hola");
        var valor = $('#reportes').val();
        var sucursal = $('#sucursal').val();

        if (valor==undefined) {
            //alert("no es un numero");
        }
        else {
            //alert("aqui");
            var fechaInicio = $(".fechaInicio").val();
            var fechaFin = $(".fechaFin").val();
            window.open(url + "/generarExist/"+valor+"/"+sucursal);
        }
    });
});

$("#sucursalK").on('change', function(e){
    var sucursal = $(this).val();
    let dataString = "id=" + sucursal+"&csrf_test_name="+token;
    $.ajax({
        type: "POST",
        url: url+"/get_stock_sucursal",
        data: dataString,
        //dataType: 'json',
        success: function (data) {
            $("#selectProductos").html("");
            $("#selectProductos").html(data);
        }
    });
});
