<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox" id="main_view">
        <div class="ibox-title">
          <h3 class="text-success font-weight-bold">
          <i class="mdi mdi-plus"></i>Editar caja
        </h3>
        </div>
        <div class="ibox-content">
          <form id="form_edit" novalidate>
            <input type="hidden" name="id_caja" id="id_caja" value="<?=$row->id_caja?>">
            <div class="row">
            <div class="col-lg-4">
              <div class="form-group single-line">
               <label for="nombre">Nombre</label>
                  <input type="text" name="nombre" id="nombre" class="form-control mayu"
                  placeholder="Ingrese un nombre de Caja"
                required data-parsley-trigger="change"  value="<?=$row->nombre?>">
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group single-line">
                <label for="serie">Serie</label>
                <input type="text" name="serie" id="serie" class="form-control mayu"
                placeholder="Ingrese la serie de Caja"
              required data-parsley-trigger="change" value="<?=$row->serie?>">
            </div>
            </div>
            <div class="col-lg-4 single-line">
              <div class="form-group">
                <label for="serie">Fecha de Resoluci&oacute;n</label>
                <input type="text" name="fecha" id="fecha" class="form-control datepicker"
                placeholder="Seleccione una fecha"  value="<?=$row->fecha?>"
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
              required data-parsley-trigger="change"  value="<?=$row->desde?>">
            </div>
          </div>
          <div class="col-lg-4">
            <div class="form-group single-line">
              <label for="hasta">Hasta</label>
              <input type="text" name="hasta" id="hasta" class="form-control mayu"
              placeholder="Ingrese número de finalizacion"
            required data-parsley-trigger="change"  value="<?=$row->hasta?>">
          </div>
          </div>
          <div class="col-lg-4">
              <div class="form-group single-line">
                  <label for="sucursal">Sucursal para Caja<span class="text-danger">*</span></label>
                  <select name="sucursal" id="sucursal" class="form-control" >
                    <?php if (isset($sucursales)):?>

                      <?php foreach ($sucursales as $suc): ?>
                        <option <?php if(	$row->id_sucursal==$suc->id_sucursal){echo "selected";} ?> value="<?=$suc->id_sucursal;?>"> <?=$suc->nombre;?></option>
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
             data-parsley-trigger="change"   value="<?=$row->correlativo_dispo?>">
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group single-line">
            <label for="resolucion"> Resolución</label>
            <input type="text" name="resolucion" id="resolucion" class="form-control mayu"
            placeholder="Ingrese número de finalizacion"
          required data-parsley-trigger="change"  value="<?=$row->resolucion?>">

        </div>
        </div>
      </div>

              <div class="row">
            <div class="form-actions col-lg-12">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
              value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
              <button type="submit" id="btn_edit" name="btn_edit"
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
