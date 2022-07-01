<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox" id="main_view">
                <div class="ibox-title">
                    <h3 class="text-success"><b><i class="mdi mdi-plus"></i> Ticket de auditoría</b></h3>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group single-line">
                                <label for="nombre">Sucursal</label>
                                <select data-parsley-trigger="change" style="width:100%" required class="select2" id="sucursalK" name="sucursalK">
                                    <?php foreach ($sucursal as $key): ?>
                                        <option <?php if($id_sucursal==$key->id_sucursal){echo "selected";} ?> value="<?=$key->id_sucursal ?>"><?=$key->nombre." ".$key->direccion ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="">Fecha inicio</label>
                                <input readonly type="text" class="form-control datepicker fechaInicioK" name="" value="<?=date("d-m-Y")?>">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="">Fecha fin</label>
                                <input readonly type="text" class="form-control datepicker fechaFinK" name="" value="<?=date("d-m-Y")?>">
                            </div>
                        </div>
                        <div class="form-actions col-lg-2">
                            <label for="">Acción</label><br>
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                            <button type="button" id="generar" name="generar" class="btn btn-success m-t-n-xs pull-right" style="width:100%;"><i class="mdi mdi-content-save"></i>
                                Generar
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

$(document).on('click', '#generar', function(event) {
    id_sucursal  = $("#sucursalK").val();
    fini  = $(".fechaInicioK").val();
    ffin  = $(".fechaFinK").val();
    var cadena = base_url+"Reportecorte/tikets/"+id_sucursal+"/"+fini+"/"+ffin;
    window.open(cadena, '', '');
});
</script>
