<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox" id="main_view">
        <div class="ibox-title">
          <h3 class="text-success"><b><i class="mdi mdi-plus"></i> Agregar Servicio</b></h3>
        </div>
        <div class="ibox-content">
          <form id="form_add" novalidate>
            <div class="row">
            <div class="col-lg-8">
              <div class="form-group single-line">
               <label for="nombre">Nombre</label>
                  <input type="text" name="nombre" id="nombre" class="form-control mayu"
                  placeholder="Ingrese un nombre"
                required data-parsley-trigger="change">
              </div>
            </div>
            <div class="col-lg-4 preciosug" >
              <div class="form-group single-line">
                <label for="precio_sug">Precio</label>
                <input type="text" name="precio_sug" id="precio_sug"
                class="form-control decimal" placeholder="Ingrese un precio sugerido"
                required ata-parsley-trigger="change">
              </div>
            </div>
          

          </div>
          <div class="row">
            <div class="col-sm-3" hidden>
              <div class="form-group has-info single-line">
                <label>Costo</label>
                <input type="text" placeholder="Costo" class="form-control ccos decimal" id="ultcosto"  name="ultcosto">
                <input type="hidden" id="costo_s_iva" name="costo_s_iva" value="0">
                <input type="hidden" id="costo_c_iva" name="costo_c_iva" value="0">
                <input type="hidden" id="precio_sugerido" name="precio_sugerido">
                <input type="hidden" id="preciosg" name="preciosg">

              </div>
            </div>
            <div class="col-lg-4" hidden>
              <div class="form-group single-line">
                <label for="dias_garantia">Dias de Garantia</label>
                <input type="text" name="dias_garantia" id="dias_garantia"
                class="form-control numeric" placeholder="Ingrese los dias de garantia"
                required data-parsley-trigger="change" value="0">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-4" hidden>
              <div class="form-group single-line">
                <label for="dias_garantia">Precio Mínimo</label>
                <input type="text" name="precio_min" id="precio_min"
                class="form-control decimal" placeholder="Ingrese Precio Minimo"
                 data-parsley-trigger="change" value="">
              </div>
            </div>
          </div>


              <div class="row">
            <div class="form-actions col-lg-12">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
              value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
              <button type="submit" id="btn_add" name="btn_add"
              class="btn btn-success m-t-n-xs float-right"><i
              class="mdi mdi-content-save"></i>
              Guardar Registro
            </button>
          </div>
          </div>
      </form>
    </div>

  </div>
  <div class="ibox" style="display: none;" id="divh">
    <div class="ibox-content text-center">
      <div class="row">
        <div class="col-lg-12">
          <h2 class="text-danger blink_me">Espere un momento, procesando su solicitud!</h2>
          <section class="sect">
            <div id="loader">
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
