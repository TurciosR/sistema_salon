<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="modal-content">
<div class="modal-header">
  <div class="col-lg-1">
    <!--<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>-->
  </div>
  <div class="col-lg-11">
    <h4 class="modal-title">Impresión documento</h4>
    <!--small class="font-bold">Documento Num:<?=$row->id_venta."-".$rowcte->id_cliente?></small-->
  </div>
</div>
<div class="modal-body">
  <form id="form_edit_cte" data-parsley-validate>
  <input type="hidden" name="id_vta" id="id_vta" value="<?=$row->id_venta?>">
  <input type="hidden" name="process" id="process" value="">
    <input type="hidden" name="id_client" id="id_client" value="<?=$rowcte->id_cliente?>">
    <input type="hidden" name="clasifica" id="clasifica" value="<?=$rowcte->clasifica?>">
    <div class="row">
        <div class="col-lg-12 big-font">
            <div class="form-group single-line">
                <label for="nombre">Nombre Cliente<span class="text-danger">*</span></label>
                <input type="text" name="nombre_cliente" id="nombre_cliente" class="form-control mayu big-font-title"  placeholder="Ingrese un nombre" value="<?=$rowcte->nombre?>"
                       required data-parsley-trigger="change">
            </div>
        </div>

    </div>
<?php if($row->tipo_doc==3):?>
    <div class="row ">
    <div class="col-lg-6 big-font">
        <div class="form-group single-line">
            <label for="nit">NIT<span class="text-danger">*</span></label>
            <input type="text" name="nit" id="nit" class="form-control nit big-font-title"  placeholder="Ingrese NIT" pattern="[0-9]{4,}-[0-9]{6,}-[0-9]{3,}-[0-9]{1,}" value="<?=$rowcte->nit?>"
                   required data-parsley-trigger="change">
        </div>
    </div>

      <div class="col-lg-6 big-font">
          <div class="form-group single-line">
              <label for="nrc">NRC <span class="text-danger">*</span></label>
              <input type="text" name="nrc" id="nrc" class="form-control big-font-title"  placeholder="Ingrese el NRC" value="<?=$rowcte->nrc?>"
                     required data-parsley-trigger="change">
          </div>
      </div>
    </div>

  <?php endif; ?>
    <div class="row">

      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">

      <div class="col-lg-12  big-font">
          <div class="form-group single-line">
              <label for="nrc">TOTAL $<span class="text-danger">*</span></label>
              <input type="text" name="totfin" id="totfin" class="form-control  big-font-title"   value="<?=$row->total?>"  readOnly
                     required data-parsley-trigger="change">
          </div>
      </div>
    </div>
    <!--div class="row">
      <div class="col-lg-12">
          <div class="form-group single-line">
              <label for="tipo_pago">Tipo pago<span class="text-danger">*</span></label>
              <select name="tipo_pago" id="tipo_pago" class="form-control" >
                  <!--?php foreach ($tipo_pago as $tp): ?>
                    <!--?php if($tp->inactivo==0):?>
                      <option value="<?=$tp->id_tipopago?>"
                          <!--?php if($tp->id_tipopago==1) echo "selected"; ?>>
                            <!--?=$tp->descripcion?></option>
                          <!--?php endif; ?>
                  <!--?php endforeach; ?>

              </select>
          </div>
      </div>
    </div-->
    <!--div class="row">
        <div class="col-lg-6  big-font">
            <div class="form-group single-line">
                <label for="efectivo" id="lbl_efectivo">Efectivo  $<span class="text-danger  big-font">*</span></label>
                <input type="text" name="efectivo" id="efectivo" class="form-control decimal  big-font-title"  placeholder="Ingrese monto"  value=""
                       required data-parsley-trigger="change"  autofocus>
            </div>
        </div>
      <div class="col-lg-6 big-font">
          <div class="form-group single-line">
              <label for="cambio" id="lbl_cambio">Cambio  $</label>
              <input type="text" name="cambio" id="cambio" class="form-control cambiodinero  big-font-title"   value="" readOnly >
          </div>
      </div>
    </div-->
    <div class="row">
        <div class="col-lg-6 big-font">
            <div class="form-group single-line">
                <label for="correlativo">Correlativo<span class="text-danger">*</span></label>
                <input type="text" name="numero_doc" id="numero_doc" class="form-control numeric big-font-title"  readOnly  value="<?=$row->correlativo?>"
                       required data-parsley-trigger="change">
            </div>
        </div>
        <div class="col-lg-6 big-font">
            <div class="form-group single-line">
                <label for="referencia">Referencia<span class="text-danger">*</span></label>
                <input type="text" name="referencia" id="referencia" class="form-control numeric big-font-title"  placeholder="Referencia diaria"  value="<?=$row->referencia?>"
                       required data-parsley-trigger="change" readonly>
            </div>
        </div>
    </div>

</form>
</div>
<div class="modal-footer">
      <button type="submit" form="form_edit_cte" class="btn btn-primary saveprint" id="btn_edit_cte" >  <i class="mdi mdi-printer"></i>Imprimir</button>
  <button type="button" id='close_fin' class="btn btn-white" >  <i class="mdi mdi-close"></i>Cerrar</button>
    <!--<button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>-->
</div>
</div>
