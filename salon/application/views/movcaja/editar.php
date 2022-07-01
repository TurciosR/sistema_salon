<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="modal-header">
  <div class="col-lg-1">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
  </div>
  <div class="col-lg-11">
    <h4 class="modal-title">Vales</h4>

  </div>
</div>
<div class="modal-body">
  <form id="form_edit_vale" data-parsley-validate>
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
    <input type="hidden" name="id_mov" value="<?=$row->id_mov?>">
  <div class="row">
    <div class="col-md-12">
          <div class="form-group has-info single-line">
            <label>Monto </label> <input type='text'  class='form-control decimal' id='monto2' name='monto2'
             required data-parsley-trigger="change"  value="<?=$row->valor?>">
             <!--
             "concepto"=>$concepto,
             "valor"=>$valor,
             "fecha"=>$fecha,
             "hora"=>$hora,
             "nombre_recibe"=>$recibe,
             -->
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group has-info single-line">
            <label>Concepto</label>
            <textarea id='concepto2' name='concepto2'  class='form-control'  rows="4" required data-parsley-trigger="change"><?=$row->concepto?></textarea>
            <!--input type='text'  class='form-control' id='concepto2' name='concepto2'
           required data-parsley-trigger="change"-->
          </div>
        </div>



        <div class="col-md-12 caja_iva">
          <div class="form-group has-info single-line">
            <label>Recibe </label>
            <input type='text'  class='form-control' id='recibe2' name='recibe2'
            required data-parsley-trigger="change" value="<?=$row->nombre_recibe?>">
          </div>
        </div>
    </div>
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">

</form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
    <button type="submit" form="form_edit_vale" class="btn btn-primary saveprint" id="btn_edit_vale" >Guardar</button>
</div>
<script>
  $("#monto2").numeric({negative:false, decimalPlaces:4});
//  $(".decimal").numeric({negative:false, decimalPlaces:4});
</script>
