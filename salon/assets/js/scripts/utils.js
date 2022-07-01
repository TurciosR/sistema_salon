$(document).ready(function()
{
    $(".numeric").numeric({
        negative: false,
        decimal: false
    });
    $(".decimal").numeric({
        negative: false,
        decimalPlaces: 2
    });

    $('.tel').mask('0000-0000');
    $('.nit').mask("0000-000000-000-0");
    $('.dui').mask("00000000-0");
    $('.nrc').mask("000000-0");
    $('.select2').select2();
    $(".upper").blur(function() {
        $(this).val($(this).val().toUpperCase())
    });
    $(".lower").blur(function() {
        $(this).val($(this).val().toLowerCase())
    });
    $('.mayu').blur(function() {
        this.value = this.value.toLocaleUpperCase();
    });
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
    $(".datepicker").datepicker({
        format: 'dd-mm-yyyy',
        language: 'es',
        orientation: "bottom auto",
        autoclose: true
    });
    /*var fecha = new Date();
    fecha.setDate(fecha.getDate()+10);

    $('.datepicker1').datepicker({
        format: 'dd-mm-yyyy',
        language: 'es',
        startDate: fecha,
    });*/
    /*$('.timepicker').mdtimepicker({
        timeFormat: 'hh:mm:ss.000',
        format: 'h:mm tt',
        theme: 'blue',
        readOnly: true,
        hourPadding: false
    });*/
});

function notification(type, title, message){
    if(type=="success" || type=="Success"){
        iziToast.success({
            title: title,
            message: message,
            position: 'topRight',
        });
    }
    else if(type=="info" || type=="Info"){
        iziToast.info({
            title: title,
            message: message,
            position: 'topRight',
        });
    }
    else if(type=="warning" || type=="Warning"){
        iziToast.warning({
            title: title,
            message: message,
            position: 'topRight',
        });
    }
    else if(type=="error" || type=="Error"){
        iziToast.error({
            title: title,
            message: message,
            position: 'topRight',
        });
    }
}

function calcularDias(fecha1,fecha2){
    var dia1= fecha1.substr(0,2);
    var mes1= fecha1.substr(3,2);
    var anyo1= fecha1.substr(6);

    var dia2= fecha2.substr(0,2);
    var mes2= fecha2.substr(3,2);
    var anyo2= fecha2.substr(6);

    var nuevafecha1= new Date(anyo1+","+mes1+","+dia1);
    var nuevafecha2= new Date(anyo2+","+mes2+","+dia2);

    var diasDif = nuevafecha2.getTime() - nuevafecha1.getTime();

    var dias = Math.round(diasDif/(1000 * 60 * 60 * 24));

    return dias;
}

/**
 * Inicializa una tabla con datatable que utiliza serverSide
 * 
 * @param {string} tabla el identificador de la tabla
 * @param {string} url la url a la que apunta
 * @
 */
function activar_datatable(tabla, url){

    // Obtenemos el token 
    let token = $("#csrf_token_id").val()
    
    $(tabla).DataTable({
        "pageLength": 50,
        "serverSide": true,
        "order": [[0, "asc"]],
        "ajax": {
            url: url,
            type: 'GET',
            data:{
                csrf_test_name:token
            }
        },
        "language": {
            "url": base_url+ "assets/js/scripts/Spanish.json"
        },
        "pagingType": "full_numbers"
    });
}
