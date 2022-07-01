<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox" id="main_view">
        <div class="ibox-title">
          <h3 class="text-success"><b><i class="mdi mdi-plus"></i> Compra a proveedor</b></h3>
        </div>
        <div class="ibox-content">
          <form id="form_add" novalidate>
            <input type="hidden" id="data_ingreso" name="data_ingreso" value="">
            <input type="hidden" id="proceso" name="proceso" value="carga">
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <input type="text" name="concepto" id="concepto" class="form-control mayu"
                  placeholder="Ingrese un concepto"
                  required data-parsley-trigger="change">
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group">
                  <input type="text" name="fecha" id="fecha" class="form-control datepicker"
                  placeholder="Seleccione una fecha" value="<?=date("d-m-Y")?>"
                  required data-parsley-trigger="change">
                </div>
              </div>
              <div class="form-actions col-sm-3">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                <button style="width:100%" type="submit" id="btn_add" name="btn_add"
                class="btn btn-success float-right"><i
                class="mdi mdi-content-save"></i>
                Guardar Registro
              </button>
              </div>
            </div>
            <div class="row">
              <div class='col-lg-3'>
                <div class='form-group has-info'>
                  <label>Proveedor</label>

                    <select class="form-group select " id="id_proveedor" name="id_proveedor"
                    required data-parsley-trigger="change">
                    <option value="">Seleccione</option>
                    <?php
                    foreach ($proveedores as $prov):  ?>
                    <option value="<?=$prov->id_proveedor; ?>"><?= $prov->nombre;?></option>
                    <?php
                  endforeach;
                        ?>
                  </select>
                </div>
              </div>
              <div class='col-lg-3'>
                <div class='form-group has-info'>
                  <label>Documento</label>

                    <select class="form-control select " id="tipo_doc" name="tipo_doc"
                    required data-parsley-trigger="change">
                    <option value="">Seleccione</option>
                    <?php
                    foreach ($tipodoc as $td):  ?>
                    <option value="<?= $td->alias; ?>"><?= $td->nombredoc;?></option>
                    <?php
                  endforeach;
                        ?>
                  </select>
                </div>
              </div>
              <div class='col-lg-2'>
                <div class='form-group has-info'>
                  <label>Número de documento</label>
                  <input type="text" class="form-control" id="numero_doc" name="numero_doc"
                  required  data-parsley-trigger="change">
                </div>
              </div>
              <div class="col-lg-2">
                <label>Días crédito</label>
                <input type="text" class="form-control" id="numero_dias" name="numero_dias">
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6">
                <div class="form-group has-info">
                  <div id="scrollable-dropdown-menu">
                    <input type="text" id="producto" name="producto"  class="form-control" placeholder="Ingrese la descripción de producto" data-provide="typeahead">
                    <input type="hidden" id="id_producto" name="id_producto">
                  </div>
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group">
                  <input type="text" name="total" id="total" readonly class="form-control text-center"
                  placeholder="" value="0.00"
                  data-parsley-trigger="change">
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group">
                  <select data-parsley-trigger="change" style="width:100%" required class="select2" name="sucursal">
                    <?php foreach ($sucursal as $key): ?>
                      <option <?php if($id_sucursal==$key->id_sucursal){echo "selected";} ?> value="<?=$key->id_sucursal ?>"><?=$key->nombre." ".$key->direccion ?></option>
                    <?php endforeach; ?>
                  </select>
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
                        <th style="width:10%;">Color</th>
                        <th style="width:10%;">Cantidad</th>
                        <th style="width:10%;">Costo</th>
                        <th style="width:10%;">Precio sugerido</th>
                        <th style="width:10%;">Subtotal</th>
                        <th style="width:10%;">Acciones</th>
                      </tr>
                    </thead>
                    <tbody id="table_producto">

                    </tbody>
                  </table>
                </div>
              </div>
              <div class="row" id="totals">
                <table class="table invoice-total">
                <tbody class="subt_iva">
                  <tr class="higrow">
                      <td><strong>SUBTOTAL SIN IVA $:</strong></td>
                      <td><input readonly type="text" name="" class="total_s_iva form-control" value="0.00"></td>
                  </tr>
                  <tr class="higrow">
                      <td><strong> IVA $:</strong></td>
                      <td> <input type="text" name="total_iva" class="total_iva form-control" value="0.00"></td>
                  </tr>
                  </tbody>
                    <tbody class="tot_fin">
                      <tr class="higrow">
                    <td><strong>TOTAL VENTA $:</strong></td>
                    <td><input type="text" name="total_final" class="total_final form-control" value="0.00"></td>
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
