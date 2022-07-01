<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox" id="main_view">
        <div class="ibox-title">
          <h3 class="text-success font-weight-bold">
            <i class="mdi mdi-plus"></i> Venta directa
          </h3>
        </div>

        <div class="ibox-content">
          <input type="hidden" id="id_usuario" name="id_usuario" value="<?= $id_usuario; ?>">
          <?php
          if ($row_ap != NULL) :
            if ($row_ap->vigente == 1) :
              if ($row_ap->id_usuario == $id_usuario) : ?>
                <form id="form_finref" novalidate>
                  <input type="hidden" id="id_venta" name="id_venta" value="-1">
                  <input type="hidden" id="data_ingreso" name="data_ingreso" value="">
                  <input type="hidden" id="proceso" name="proceso" value="carga">
                  <input type="hidden" id="tipo_pago_h" name="tipo_pago_h" value="">
                  <input type="hidden" id="tipo_doc_h" name="tipo_doc_h" value="">

                  <div class="row">

                  </div>
                  <div class="row">
                    <input type="hidden" id="id_cliente" name="id_cliente">
                    <input type="hidden" id="porc_clasifica" name="porc_clasifica" value="">
                    <div class='col-lg-3'>
                      <div class='form-group has-info'>
                        <label>Número de referencia</label>
                        <input type="text" class="form-control" id="referencia" name="referencia"
                          data-parsley-trigger="change">
                          <small id="referenciaHelp" class="form-text text-muted">
                            <i class="mdi mdi-help-circle"></i>
                            Ingrese el número luego presione Enter
                          </small>
                      </div>
                    </div>
                    <div class="col-lg-2">
                      <div class="form-group">
                        <label for="cliente">Cliente<span class="text-danger">*</span></label>
                        <?php if ($row_clientes != NULL) : ?>
                          <select class="form-group select usage clientes" name="client" id="client">

                            <?php foreach ($row_clientes as $rc) : ?>
                              <option value="<?= $rc->id_cliente ?>" <?php if ($rc->id_cliente == -1) echo "selected"; ?>>
                                <?= $rc->nombre ?></option>
                            <?php endforeach; ?>

                          </select>
                        <?php endif; ?>
                      </div>
                    </div>
                    <!--Agregar boton  clientes-->
                    <div class="col-lg-2">
                      <div class="form-group">
                          <label for="cliente">Cliente<span class="text-danger">*</span></label>
                        <button style="width:100%" type="button" id="btn_cliente" name="btn_cliente" class="btn btn-success float-right" data-toggle="modal" data-target="#viewModalCte">
                          <i class="mdi mdi-user"></i>
                          + Cliente
                        </button>
                      </div>
                    </div>
                    <div class="col-lg-2">
                      <div class="form-group">
                        <label for="fecha">Fecha venta<span class="text-danger">*</span></label>
                        <input type="text" name="fecha" id="fecha" class="form-control " placeholder="Seleccione una fecha" value="<?= date("d-m-Y") ?>" required readonly>
                      </div>
                    </div>


                    <div class="col-lg-2">
                      <div class="form-group">
                        <label for="tipodoc">Tipo venta<span class="text-danger">*</span></label>
                        <select data-parsley-trigger="change" style="width:100%" required class="select2" id="tipodoc" name="tipodoc">
                          <?php foreach ($tipodoc as $key) : ?>
                            <option value="<?= $key->idtipodoc; ?>"><?= $key->nombredoc ?></option>

                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>

                    <input type="hidden" id="id_sucursal" name="id_sucursal" value="<?php echo $id_sucursal; ?>">
                  </div>
                  <div class="row">

                    <div class="col-lg-3" hidden>
                      <div class="form-group">
                        <select class="form-control select2" name="sucursal" id="sucursal">
                          <?php foreach ($sucursal as $sucurale) : ?>
                            <option value="<?= $sucurale->id_sucursal ?>" <?php if ($sucurale->id_sucursal == $id_sucursal) {
                                                                            echo " selected ";
                                                                          } ?>><?= $sucurale->direccion ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>


                  </div>
                  <div class="row">
                    <div class="col-sm-4">
                      <div class="form-group has-info">
                        <div id="scrollable-dropdown-menu">
                          <input type="text" id="producto" name="producto" class="form-control" placeholder="Ingrese la Descripción de producto o Servicio" data-provide="typeahead">
                          <input type="hidden" id="id_producto" name="id_producto">
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-2" hidden>
                      <div class="form-group">
                        <input type="text" name="total1" id="total1" readonly class="form-control text-center" placeholder="" value="$0.00" data-parsley-trigger="change">
                        <input type="hidden" name="total" id="total">
                      </div>
                    </div>
                    <!--oculto -->
                    <div class="col-lg-2" hidden>
                      <div class="form-group">
                        <input type="text" name="items" id="items" readonly class="form-control text-center itemss" placeholder="items" value="" data-parsley-trigger="change">
                        <input type="hidden" name="item1" id="item1" class="itemss">
                      </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="tipo_pago">Tipo pago<span class="text-danger">*</span></label>
                            <select name="tipo_pago" id="tipo_pago" class="form-control" >
                                <?php foreach ($tipo_pago as $tp): ?>
                                  <?php if($tp->inactivo==0):?>
                                    <option value="<?=$tp->id_tipopago?>"
                                        <?php if($tp->id_tipopago==1) echo "selected"; ?>>
                                          <?=$tp->descripcion?></option>
                                        <?php endif; ?>
                                <?php endforeach; ?>

                            </select>
                        </div>
                    </div>
                    <!--div class="row"-->
                        <div class="col-lg-2  ">
                            <div class="form-group">
                                <label for="efectivo" id="lbl_efectivo">Efectivo  $<span class="text-danger  big-font">*</span></label>
                                <input type="text" name="efectivo" id="efectivo" class="form-control decimal  big-font-title"
                                 placeholder="Ingrese monto"  value="">
                            </div>
                        </div>
                      <div class="col-lg-2">
                          <div class="form-group ">
                              <label for="cambio" id="lbl_cambio">Cambio  $</label>
                              <input type="text" name="cambio" id="cambio" class="form-control cambiodinero  big-font-title"   value="" readOnly >
                          </div>
                      </div>
                    <!--/div-->
                    <div class="form-actions col-lg-2">
                      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                      <button style="width:100%" type="submit" id="btn_finref" name="btn_finref" class="btn btn-success float-right" data-toggle="modal" data-target="#viewModal">
                        <i class="mdi mdi-content-save"></i>
                        F2 guardar venta
                      </button>
                      <!--input style="width:100%" type="submit" id="btn_finref" name="btn_finref" class="btn btn-success float-right" data-toggle="modal" data-target="#viewModal" value="F2 Guardar Venta"-->

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
                            <!--th style="width:10%;">Estado</th-->
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
                      <tbody class="subt_iva">
                        <tr class="higrow">
                          <td><strong>SUBTOTAL SIN IVA $:</strong></td>
                          <td class="total_s_iva">0.00</td>
                        </tr>
                        <tr class="higrow">
                          <td><strong> IVA $:</strong></td>
                          <td class="total_iva">0.00</td>
                        </tr>
                      </tbody>
                      <tbody class="tot_fin">
                        <tr class="higrow">
                          <td><strong>TOTAL VENTA $:</strong></td>
                          <td class="total_final">0.00</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                </form>
              <?php else : ?>
                <div></div>
                <div class='alert alert-warning text-center' style='font-weight: bold;'>
                  <label style='font-size: 15px;'>¡¡Ya existe una apertura de caja realizada por "<?= $usuario_ap->nombre ?>"!!</label>
                  <br>
                  <label style='font-size: 15px;'>Debe de realizar el corte con el usuario que hizo la apertura vigente, para poder iniciar una nueva apertura de caja.</label>

                </div>
              <?php endif; ?>
          <?php
            else :
              redirect("caja/apertura");
            endif;
          else :
            redirect("caja/apertura");
          endif; ?>

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
      <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
          <div class="modal-content modal-md"></div>
        </div>
      </div>
      <div class="modal fade" id="viewModalCte" tabindex="-2" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
          <div class="modal-content modal-md">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  $(document).ready(function() {
    setTimeout(function() {
      $("#producto").focus();
    }, 1500);
  });
</script>
