let url = base_url+"stock";
let token = $("#csrf_token_id").val()

$(window).keydown(function(event) {
  if (event.keyCode == 13) {
    event.preventDefault();
    return false;
  }
});
$(document).on('change', '#sucursales', function(event) {
  generar();
});
$(document).ready(function () {
    generar();
});
function generar()
{
  dataTable = $('#editable').DataTable().destroy()
  dataTable = $('#editable').DataTable({
    "pageLength": 50,
    "serverSide": true,
    "order": [[0, "desc"]],
    "ajax": {
      url: url+"/get_data_stock",
      type: 'POST',
      data:{
        csrf_test_name:token,
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

$(document).on('click', '.detail', function(event) {
	$('#viewModal .modal-content').load(url+"/detalle/"+$(this).attr('data-id'));
});
