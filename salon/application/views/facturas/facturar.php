<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox" id="main_view">
        <div class="ibox-title">
          <h3 class="text-success"><b><i class="mdi mdi-plus"></i> Venta</b></h3>
        </div>

        <div class="ibox-content">
          <form id="form_add" novalidate>
            <input type="hidden" id="data_ingreso" name="data_ingreso" value="">
            <input type="hidden" id="proceso" name="proceso" value="carga">

            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <div id="scrollable-dropdown-menu">
                  <input type="text" name="cliente" id="cliente" class="form-control"
                  placeholder="Seleccione un cliente"
                  required data-parsley-trigger="change">
                  <input type="hidden" id="id_cliente" name="id_cliente">
                  <input type="hidden" id="porc_clasifica" name="porc_clasifica" value="">
                </div>
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group">
                  <input type="text" name="fecha" id="fecha" class="form-control datepicker"
                  placeholder="Seleccione una fecha" value="<?=date("d-m-Y")?>"
                  required data-parsley-trigger="change">
                </div>
              </div>


              <div class="col-lg-3">
                <div class="form-group">
                  <select data-parsley-trigger="change" style="width:100%" required class="select2" id="tipodoc" name="tipodoc">
                    <?php foreach ($tipodoc as $key): ?>
                      <option value="<?=$key->idtipodoc;?>"><?=$key->nombredoc?></option>

                    <?php endforeach; ?>
                  </select>
                </div>
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
                    <input type="text" id="producto" name="producto"  class="form-control" placeholder="Ingrese la Descripción de producto o Servicio" data-provide="typeahead">
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
              <div class="form-actions col-sm-3">
                  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                  value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                  <button style="width:100%;" type="submit" id="btn_add" name="btn_add" data-toggle="modal"  data-target="#viewModal"  class="btn btn-success float-right"><i class="mdi mdi-content-save"></i>
                  Guardar Registro
                </button>
              </div>
            </div>

            <div class="row  table-wrapper-scroll-y my-custom-scrollbar">
              <div class="col-lg-12">
                  <!--table class="table table-bordered table-hover table-striped"-->
                  <table class="table-striped">
                    <thead>
                      <tr>
                        <th style="width:5%;">No.</th>
                        <th style="width:25%;">Descripcion</th>
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
                  <td><strong>TOTAL VENTA:</strong></td>
                  <td class="total_final">$ 0.00</td>
              </tr>
              </tbody>
            </table>
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
      <div class="modal fade" id="viewModal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
         <div class="modal-dialog">
           <div class="modal-content modal-md">
           </div>
         </div>
       </div>
    </div>
  </div>
</div>
