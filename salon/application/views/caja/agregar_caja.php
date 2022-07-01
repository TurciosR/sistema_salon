<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox" id="main_view">
        <div class="ibox-title">
          <h3 class="text-success font-weight-bold">
            <i class="mdi mdi-plus"></i> Agregar caja
          </h3>
        </div>
        <div class="ibox-content">
          <form id="form_add" novalidate>
            <div class="row">
            <div class="col-lg-4">
              <div class="form-group single-line">
               <label for="nombre">Nombre</label>
                  <input type="text" name="nombre" id="nombre" class="form-control mayu"
                  placeholder="Ingrese un nombre de Caja"
                required data-parsley-trigger="change">
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group single-line">
                <label for="serie">Serie</label>
                <input type="text" name="serie" id="serie" class="form-control mayu"
                placeholder="Ingrese la serie de Caja"
              required data-parsley-trigger="change">
            </div>
            </div>
            <div class="col-lg-4 single-line">
              <div class="form-group">
                <label for="serie">Fecha de Resoluci&oacute;n</label>
                <input type="text" name="fecha" id="fecha" class="form-control datepicker"
                placeholder="Seleccione una fecha" value="<?=date("d-m-Y")?>"
                required data-parsley-trigger="change">
              </div>
            </div>
          </div>
          <!--	//id_caja, nombre, serie, desde, hasta, correlativo_dispo, resolucion, fecha, id_sucursal, activa-->
          <div class="row">
          <div class="col-lg-4">
            <div class="form-group single-line">
             <label for="desde">Desde</label>
                <input type="text" name="desde" id="desde" class="form-control mayu"
                placeholder="Ingrese número de inicio"
              required data-parsley-trigger="change">
            </div>
          </div>
          <div class="col-lg-4">
            <div class="form-group single-line">
              <label for="hasta">Hasta</label>
              <input type="text" name="hasta" id="hasta" class="form-control mayu"
              placeholder="Ingrese número de finalización"
              required data-parsley-trigger="change">
            </div>
          </div>
          <div class="col-lg-4">
              <div class="form-group single-line">
                  <label for="sucursal">Sucursal para Caja<span class="text-danger">*</span></label>
                  <select name="sucursal" id="sucursal" class="form-control" >
                    <?php if (isset($sucursales)):
                    	$id_sucursal=$this->session->id_sucursal;?>
                      <?php foreach ($sucursales as $suc): ?>
                        <option <?php if(	$id_sucursal==$suc->id_sucursal){echo "selected";} ?> value="<?=$suc->id_sucursal;?>"> <?=$suc->nombre;?></option>
                      <?php endforeach; ?>

                    <?php endif; ?>
                  </select>
              </div>
          </div>

        </div>
        <!--	// correlativo_dispo, resolucion, fecha, id_sucursal, activa-->
        <div class="row">
        <div class="col-lg-6">
          <div class="form-group single-line">
           <label for="correlativo_dispo">correlativo disponible</label>
              <input type="text" name="correlativo_dispo" id="correlativo_dispo" class="form-control mayu"
              placeholder="Ingrese número de inicio"
             data-parsley-trigger="change">
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group single-line">
            <label for="resolucion"> Resolución</label>
            <input type="text" name="resolucion" id="resolucion" class="form-control mayu"
            placeholder="Ingrese número de finalización"
          required data-parsley-trigger="change">
        </div>
        </div>
      </div>

              <div class="row">
            <div class="form-actions col-lg-12">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"

              value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
              <button type="submit" id="btn_add" name="btn_add_caja"
              class="btn btn-success m-t-n-xs float-right"><i
              class="mdi mdi-content-save"></i>
              Guardar registro
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
          <h2 class="text-danger blink_me">¡Espere un momento, procesando su solicitud!</h2>
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
