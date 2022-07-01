<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="modal-content">
<div class="modal-header">
  <div class="col-lg-1">
  </div>
  <div class="col-lg-11">
    <h4 class="modal-title">Cliente nuevo</h4>

  </div>
</div>
<div class="modal-body">
  <form id="form_save_newcte" class="form-horizontal" data-parsley-validate>
    <input type="hidden" name="process" id="process" value="new_client">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">

    <div class="row">
      <div class="col-lg-12 big-font single-line">
        <label for="">Tipo cliente</label>
        <div class="row">
          <div class="col-lg-6 float_left">
            <label for="">No. contribuyente</label>
            <input type="radio" class="contribuyente" id='contrib0' name="contribuyente" value=0 checked>
          </div>
          <div class="col-lg-6 float_left">
            <label for="">Contribuyente</label>
            <input type="radio" class="contribuyente" id='contrib1' name="contribuyente" value=1 >
          </div>
        </div>
      </div>
    </div>


    <div class="row divnombre">
        <div class="col-lg-12 big-font">
                    <div class="form-group row">
            <label for="nombre" class="col-form-label col-lg-4">Nombre<span class="text-danger">*</span></label>
              <div class="col-lg-8">
                <input type="text" name="nombre_cliente" id="nombre_cliente" class="form-control mayu big-font-title"  placeholder="Ingrese un nombre" value=""
                       required data-parsley-trigger="change">
               </div>
             </div>
         </div>
    </div>

    <div class="row divnit">
        <div class="col-lg-12 big-font">
          <div class="form-group row">
            <label for="nit" class="col-form-label col-lg-4">NIT<span class="text-danger">*</span></label>
              <div class="col-lg-8">
                  <input type="text" name="nit" id="nit" class="form-control nit big-font-title"  value=""
                  placeholder="Ingrese NIT" pattern="[0-9]{4,}-[0-9]{6,}-[0-9]{3,}-[0-9]{1,}"   data-parsley-trigger="change"  maxlength="17" data-parsley-maxlength="17" required >
               </div>
            </div>
         </div>
    </div>
<div class="row divdui">
  <div class="col-lg-12 big-font">
      <div class="form-group row">
            <label for="dui"  class="col-form-label col-lg-4">DUI</label>
            <div class="col-lg-8">
                <input type="text" name="dui" id="dui" class="form-control dui big-font-title"  value=""
                placeholder="Ingrese DUI"  pattern="[0-9]{8,}-[0-9]{1,}"  data-parsley-trigger="change" maxlength="10" data-parsley-maxlength="10">
          </div>
        </div>
    </div>
  </div>
  <div class="row divnrc">
    <div class="col-lg-12 big-font">
        <div class="form-group row">
            <label for="nrc" class="col-form-label col-lg-4">NRC <span class="text-danger"></span></label>
            <div class="col-lg-8">
              <input type="text" name="nrc" id="nrc" class="form-control nrc Big-font-title" placeholder="Ingrese el NRC"
              data-parsley-trigger="change" pattern="[0-9]{6,}-[0-9]{1,}" maxlength="8" data-parsley-maxlength="8">
            </div>
          </div>
      </div>
  </div>
  <div class="row">
    <div class="col-lg-12 big-font">
        <div class="form-group row">
                  <label for="clasifica"  class="col-form-label col-lg-4">Clasificación<span class="text-danger">*</span></label>
                   <div class="col-lg-8">
             <select name="clasifica" id="clasifica" class="form-control select2" >

                           <?php foreach ($clasifica_cliente as $clasifica): ?>
                                   <option value="<?=$clasifica->id?>"><?=$clasifica->descripcion?></option>
                           <?php endforeach; ?>
                   </select>
         </div>
       </div>
   </div>
  </div>
</form>
</div>
<div class="modal-footer">
    <button type="submit" form="form_save_newcte" class="btn btn-primary savenewcte" id="btn_save_newcte"  name="btn_save_newcte" >  <i class="mdi mdi-content-save"></i>Guardar</button>
    <button type="button" id='close_newcte' class="btn btn-white" >  <i class="mdi mdi-close"></i>Cerrar</button>
</div>
</div>
