<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="modal-content">
<div class="modal-header">
  <div class="col-lg-1">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
  </div>
  <div class="col-lg-11">
    <h4 class="modal-title">Vales</h4>

  </div>
</div>
<div class="modal-body">
  <form id="form_add_vale" data-parsley-validate>

  <div class="row">
    <div class="col-md-12">
          <div class="form-group has-info single-line">
            <label>Monto </label> <input type='text'  class='form-control numeric' id='monto2' name='monto2'
             required data-parsley-trigger="change">
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group has-info single-line">
            <label>Concepto</label>
            <textarea class='form-control' id='concepto2' name='concepto2'></textarea>
          </div>
        </div>



        <div class="col-md-12 caja_iva">
          <div class="form-group has-info single-line">
            <label>Recibe </label> <input type='text'  class='form-control' id='recibe2' name='recibe2'>
          </div>
        </div>
    </div>
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">

</form>
</div>
<div class="modal-footer">
      <button type="submit" form="form_add_vale" class="btn btn-primary saveprint" id="btn_add_vale" >Guardar</button>
    <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
</div>
</div>
