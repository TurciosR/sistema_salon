let url = base_url+"inventario";
let token = $("#csrf_token_id").val();

$(window).keydown(function(event) {
    if (event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
});

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
    $("#btn_add").prop("disabled",true)
    edit_data();
  }
});

function save_data(){

  var array_json = [];
  $(".imei").each(function(index) {
    id_producto = $(this).attr('id_producto');
    imei = $(this).val();
    id_detalle = $(this).attr('id_detalle');
    chain = $(this).attr('chain');
    var obj = new Object();
    obj.id_producto = id_producto;
    obj.imei = imei;
    obj.id_detalle = id_detalle;
    obj.chain = chain;
    //convert object to json string
    text = JSON.stringify(obj);
    array_json.push(text);

  });

  var json_arr = '[' + array_json + ']';
	$("#data_ingreso").val(json_arr);

  let form = $("#form_add");
  let formdata = false;
  if (window.FormData) {
    formdata = new FormData(form[0]);
  }
  $.ajax({
    type: 'POST',
    url: url+'/imei',
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

  var array_json = [];
  $(".imei").each(function(index) {
    id_imei = $(this).attr('id_imei');
    imei = $(this).val();
    var obj = new Object();
    obj.id_imei = id_imei;
    obj.imei = imei;
    //convert object to json string
    text = JSON.stringify(obj);
    array_json.push(text);

  });

  var json_arr = '[' + array_json + ']';
	$("#data_ingreso").val(json_arr);

  let form = $("#form_edit");
  let formdata = false;
  if (window.FormData) {
    formdata = new FormData(form[0]);
  }
  $.ajax({
    type: 'POST',
    url: url+'/editarimei',
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

/*movimientos entre imeis*/

$(document).on("keyup", ".imei", function(e){
	var c = parseInt($(this).attr('c'));
  var max = parseInt($("#max_co").val());
	if(e.keyCode == 13 && $(this).val()!="")
	{
    if (c==max) {
      $(".co1").focus();
    }
    else {
      c++;
      $(".co"+c).focus();
    }

	}
});

function reload() {
	location.href = url+"/cargas";
}
