<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="modal-header">
    <div class="col-lg-1">
      <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
    </div>
    <div class="col-lg-11">
      <h4 class="modal-title">Agregar porcentaje</h4>
      <small class="font-bold">Rellena los datos campos requeridos</small>
    </div>
</div>
<div class="modal-body">
    <form id="form_add" data-parsley-validate>
        <div class="row">
            <div class="col-lg-12">
                <div class="form-group">
                    <label for="descripcion">Descripción<span class="text-danger">*</span></label>
                    <input type="text" name="descripcion" id="descripcion" class="form-control"  placeholder="Ingrese un nombre" required data-parsley-trigger="change">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="form-group">
                    <label for="porcentaje">Porcentaje<span class="text-danger">*</span></label>
                    <input type="text" name="porcentaje" id="porcentaje" class="form-control numeric"  placeholder="Ingrese un porcentaje"  required data-parsley-trigger="change">
                </div>
            </div>
        </div>
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
    <button type="submit" form="form_add" class="btn btn-primary" id="btn_add" >Guardar</button>
</div>
<script>
    $(".numeric").numeric({
        negative: false,
        decimal: false
    });
</script>
