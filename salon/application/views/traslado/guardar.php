<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox" id="main_view">
        <div class="ibox-title">
          <h3 class="text-success"><b><i class="mdi mdi-plus"></i> Traslado</b></h3>
        </div>
        <!--
        <select class='sel'>
          <option value='NUEVO'>NUEVO</option>
          <option value='USADO'>USADO</option>
        </select>
        -->
        <div class="ibox-content">
          <form id="form_add" novalidate>
            <input type="hidden" id="data_ingreso" name="data_ingreso" value="">
            <input type="hidden" id="proceso" name="proceso" value="carga">
            <div class="row">
              <div class="col-lg-4">
                <div class="form-group">
                  <label>Sucursal origen</label>
                  <select class="form-control select2" name="sucursal" id="sucursal">
                    <?php foreach ($sucursal_envio as $sucurale): ?>
                      <option value="<?= $sucurale->id_sucursal ?>" <?php if($sucurale->id_sucursal==$id_sucursal){ echo " selected "; } ?> ><?=$sucurale->nombre.", ".$sucurale->direccion ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="form-group">
                  <label>Sucursal destino</label>
                  <select class="form-control select2" name="sucursal_destino" id="sucursal_destino">
                    <?php $i=0;  ?>
                    <?php foreach ($sucursal as $sucurale): ?>
                      <option value="<?= $sucurale->id_sucursal ?>" <?php if($sucurale->id_sucursal!=$id_sucursal && $i==0){ echo " selected "; $i++;} ?> ><?=$sucurale->nombre.", ".$sucurale->direccion ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="form-group">
                  <label>Fecha</label>
                  <input type="text" name="fecha" id="fecha" class="form-control datepicker"
                  placeholder="Seleccione una fecha" value="<?=date("d-m-Y")?>"
                  required data-parsley-trigger="change">
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-lg-4">
                <div class="form-group">
                  <input type="text" name="concepto" id="concepto" class="form-control"
                  placeholder="Concepto"
                  required data-parsley-trigger="change" value="TRASLADO DE PRODUCTOS">
                </div>
              </div>
              <div class="col-lg-4">
                <div class="form-group">
                  <input type="text" name="instrucciones" id="instrucciones" class="form-control"
                  placeholder="Instrucciones especiales"
                  data-parsley-trigger="change">
                </div>
              </div>
              <div class="form-actions col-sm-4">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                <button style="width:100%;" type="submit" id="btn_add" name="btn_add"
                class="btn btn-success float-right"><i
                class="mdi mdi-content-save"></i>
                Guardar Registro
              </button>
              </div>
            </div>


            <div class="row">
              <div class="col-sm-8">
                <div class="form-group has-info">
                  <div id="scrollable-dropdown-menu">
                    <input type="text" id="producto" name="producto"  class="form-control" placeholder="Ingrese la descripción de producto" data-provide="typeahead">
                    <input type="hidden" id="id_producto" name="id_producto">
                  </div>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="form-group">
                  <input type="text" name="total" id="total" readonly class="form-control text-center"
                  placeholder="" value="0.00"
                  data-parsley-trigger="change">
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-lg-12">
                  <table class="table table-bordered table-hover table-striped">
                    <thead>
                      <tr>
                        <th style="width:5%;">No.</th>
                        <th style="width:25%;">Descripción</th>
                        <th style="width:10%;">Stock</th>
                        <th style="width:10%;">Cantidad</th>
                        <th style="width:10%;">Estado</th>
                        <th style="width:10%;">Precio</th>
                        <th style="width:10%;">Subtotal</th>
                        <th style="width:10%;">Acciones</th>
                      </tr>
                    </thead>
                    <tbody id="table_producto">

                    </tbody>
                  </table>
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
