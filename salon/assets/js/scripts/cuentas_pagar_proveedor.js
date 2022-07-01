let url = base_url+"cuentas_pagar";
let id_proveedor = $("#hidden_id").val();

$(document).ready(function () {

    // Activamos el datable
    activar_datatable('#editable', url+'/get_data_proveedores/'+id_proveedor);

})
