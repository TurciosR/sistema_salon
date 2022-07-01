let url = base_url+"banco";
let token = $("#csrf_token_id").val()

$(document).ready(function() {
  get_data();
});

function get_data() {
  var id_banco = $("#id_banco").val();

  $.ajax({
    url: url+"/get",
    type: 'POST',
    dataType: 'json',
    data: {
      csrf_test_name: token,
      id_banco: id_banco,
    },
    success: function(data) {
      $(".plan").html(data.data);
      $(".nux").numeric({negative:false,decimal:false})
      $(".dex").numeric({negative:false,decimalPlaces:2})
    }
  });

}
$(document).on('click', '#btn_pp', function(event) {
  event.preventDefault();
  var numero = valnum($('#numero').val());
  var porcentaje = valnum($('#porcentaje').val());
  var id_banco = $("#id_banco").val();
  if (numero!=0&&porcentaje!=0)
  {
    $.ajax({
      url: url+"/planes",
      type: 'POST',
      dataType: 'json',
      data: {
        csrf_test_name: token,
        id_banco: id_banco,
        numero: numero,
        porcentaje: porcentaje
      },
      success: function(data)
      {
              notification(data.type,data.title,data.msg);
        get_data();
      }
    });

  }
  else
  {
    notification("Error","Datos Incorrectos","Numero y porcentaje deben ser mayores a cero");
  }
});

$(document).on('click', '.delp', function(event) {
  event.preventDefault();
  var id_banco = $("#id_banco").val();
  var det = $(this).attr('det');
  $.ajax({
    url: url+"/delp",
    type: 'POST',
    dataType: 'json',
    data: {
      csrf_test_name: token,
      id_banco: id_banco,
      det: det,
    },
    success: function(data)
    {
      notification(data.type,data.title,data.msg);
      get_data();
    }
  });
});

$(document).on('click', '.editp', function(event) {
  event.preventDefault();
  var id_banco = $("#id_banco").val();
  var det = $(this).attr('det');

  var numero = valnum($(this).closest('tr').find(".nux").val());
  var porcentaje = valnum($(this).closest('tr').find(".dex").val());
  if (numero!=0&&porcentaje!=0)
  {
    $.ajax({
      url: url+"/editp",
      type: 'POST',
      dataType: 'json',
      data: {
        csrf_test_name: token,
        id_banco: id_banco,
        det: det,
        numero: numero,
        porcentaje: porcentaje,
      },
      success: function(data)
      {
        notification(data.type,data.title,data.msg);
        get_data();
      }
    });
  }
});


function valnum(numero) {
  num = parseFloat(numero);
  if (isNaN(num))
  {
    return 0;
  }
  else
  {
    return num;
  }
}


$("#form_edit").on('submit', function(e){
  e.preventDefault();
  $(this).parsley().validate();
  if ($(this).parsley().isValid()){
    $("#btn_edit").prop("disabled",true)
    edit_data();
  }
});

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
  location.href = url+"/admin";
}
