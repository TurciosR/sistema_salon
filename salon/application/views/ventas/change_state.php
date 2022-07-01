<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="modal-header">
  <div class="col-lg-1">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
  </div>
  <div class="col-lg-11">
    <h4 class="modal-title">Cambiar estado</h4>
    <small class="font-bold"></small>
  </div>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-lg-12">
          <div class="form-group">
            <label>Estado</label>
            <select class="estado" name="">
              <?php foreach ($rows as $key): ?>
                <option value="<?=$key->id_estado ?>" <?php if($row->id_estado==$key->id_estado){ echo "selected";} ?>><?=$key->descripcion ?></option>
              <?php endforeach; ?>
            </select>

          </div>
      </div>
  </div>
</div>
<div class="modal-footer">
    <button type="button" id_v="<?=$row->id_venta?>" class="btn btn-success change_s">Cambiar</button>
    <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
</div>

<script type="text/javascript">
$(document).ready(function() {
  $(".estado").select2({
     dropdownParent: $('.modal-body')
   });
 });
</script>
