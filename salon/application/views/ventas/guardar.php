<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox" id="main_view">
        <div class="ibox-title">
          <h3 class="text-success ont-weight-bold">
            <i class="mdi mdi-plus"></i> Venta
          </h3>
        </div>

        <div class="ibox-content">
          <?php
          if($row_ap!=NULL && $row_ap->vigente=='1' ):
          ?>
          <form id="form_add" novalidate>
            <input type="hidden" id="data_ingreso" name="data_ingreso" value="">
            <input type="hidden" id="proceso" name="proceso" value="carga">

            <div class="row">
              <input type="hidden" id="id_cliente" name="id_cliente">
              <input type="hidden" id="porc_clasifica" name="porc_clasifica" value="">

              <div class="col-lg-6">
                <div class="form-group">
                    <label for="cliente">Cliente<span class="text-danger">*</span></label>
                    <?php if($row_clientes!=NULL):?>
                  <select class="form-group select usage clientes" name="client" id="client">

                        <?php foreach ($row_clientes as $rc): ?>
                            <option value="<?=$rc->id_cliente?>"
                                <?php if($rc->id_cliente==$row_client_select->id_cliente) echo "selected"; ?>>
                                  <?=$rc->nombre?></option>
                        <?php endforeach; ?>

                    </select>
                      <?php endif; ?>
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group">
                  <label for="fecha">Fecha venta<span class="text-danger">*</span></label>
                  <input type="text" name="fecha" id="fecha" class="form-control datepicker"
                  placeholder="Seleccione una fecha" value="<?=date("d-m-Y")?>"
                  required data-parsley-trigger="change" disabled>
                </div>
              </div>
              <div class="form-actions col-sm-3">
                  <label for="fecha">Guardar venta</label>
                  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                  value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                  <button style="width:100%;" type="submit" id="btn_add_new" name="btn_add_new"
                  class="btn btn-success float-right"><i
                  class="mdi mdi-content-save"></i>
                  F2 guardar venta
                </button>
              </div>
            <input type="hidden" id="id_sucursal" name="id_sucursal" value="<?php echo $id_sucursal;?>">
            </div>
            <div class="row">

              <div class="col-lg-3" hidden>
                <div class="form-group">
                  <select class="form-control select2" name="sucursal" id="sucursal">
                    <?php foreach ($sucursal as $sucurale): ?>
                      <option value="<?= $sucurale->id_sucursal ?>" <?php if($sucurale->id_sucursal==$id_sucursal){ echo " selected "; } ?> ><?= $sucurale->direccion ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group has-info">
                  <div id="scrollable-dropdown-menu">
                    <input type="text" id="producto" name="producto"  class="form-control" placeholder="Ingrese la descripción de producto o servicio" data-provide="typeahead">
                    <input type="hidden" id="id_producto" name="id_producto">
                  </div>
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group">
                  <input type="text" name="total1" id="total1" readonly class="form-control text-center"
                  placeholder="" value="$0.00"
                  data-parsley-trigger="change">
                  <input type="hidden" name="total" id="total">
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group">
                  <input type="text" name="items" id="items" readonly class="form-control text-center itemss"
                  placeholder="items" value=""
                  data-parsley-trigger="change">
                  <input type="hidden" name="item1" id="item1" class="itemss">
                </div>
              </div>

            </div>

            <div class="row  table-wrapper-scroll-y my-custom-scrollbar">
              <div class="col-lg-12">
                  <!--table class="table table-bordered table-hover table-striped"-->
                  <table class="table-striped">
                    <thead>
                      <tr>
                        <th style="width:5%;">No.</th>
                        <th style="width:25%;">Descripción</th>
                        <th style="width:10%;">Stock</th>
                        <th style="width:10%;">Cantidad</th>
                        <th style="width:10%;">Estado</th>
                        <th style="width:10%;">Precio</th>
                        <th style="width:10%;">Descto</th>
                        <th style="width:10%;">Subtotal</th>
                        <th style="width:10%;">Acciones</th>
                      </tr>
                    </thead>
                    <tbody id="table_producto"></tbody>
                    <tbody id="table_servicio"></tbody>
                  </table>
              </div>
            </div>
            <div class="row" id="totals">
              <table class="table invoice-total">
              <tbody>
              <tr class="higrow">
                  <td><strong>TOTAL VENTA $:</strong></td>
                  <td class="total_final">0.00</td>
              </tr>
              </tbody>
            </table>
            </div>

          </form>
        <!--?php  else:?>
          <div></div>
            <div class='alert alert-warning text-center' style='font-weight: bold;'>
              <label style='font-size: 15px;'>Ya existe una apertura de caja realizada por "<!--?=$usuario_ap->nombre?>"!!</label>
              <br>
              <label style='font-size: 15px;'>Debe de realizar el corte con el usuario que hizo la  apertura vigente, para poder iniciar una nueva apertura de caja.</label>

            </div-->
         <!--?php endif;?-->
        <?php
       else:
         redirect("caja/apertura");
       endif;?>
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
<script>
$(document).ready(function () {
  setTimeout(function ()
  {$("#producto").focus();}, 500);
});
</script>
