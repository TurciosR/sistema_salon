let url = base_url+"banco";
let token = $("#csrf_token_id").val()

$(document).ready(function() {
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
    $("#btn_edit").prop("disabled",true)
    edit_data();
  }
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

function reload() {
  location.href = url+"/admin";
}
