<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox" id="main_view">
        <div class="ibox-title">
          <h3 class="text-success"><b><i class="mdi mdi-plus"></i> Editar Descarga de Inventario</b></h3>
        </div>
        <div class="ibox-content">
          <form id="form_edit_des" novalidate>
            <input type="hidden" id="id_carga" name="id_carga" value="<?=$row->id_descarga ?>">
            <input type="hidden" id="data_ingreso" name="data_ingreso" value="">
            <input type="hidden" id="proceso" name="proceso" value="descarga">
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <input type="text" name="concepto" id="concepto" class="form-control mayu"
                  placeholder="Ingrese un concepto"
                  required data-parsley-trigger="change" value="<?=$row->concepto ?>">
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group">
                  <input type="text" name="fecha" id="fecha" class="form-control datepicker"
                  placeholder="Seleccione una fecha" value="<?=d_m_y($row->fecha)?>"
                  required data-parsley-trigger="change">
                </div>
              </div>
              <div class="form-actions col-sm-3">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                <button type="submit" id="btn_add" name="btn_add"
                class="btn btn-success float-right"><i
                class="mdi mdi-content-save"></i>
                Guardar Registro
              </button>
              </div>
            </div>


            <div class="row">
              <div class="col-sm-6">
                <div class="form-group has-info">
                  <div id="scrollable-dropdown-menu">
                    <input type="text" id="producto_des" name="producto_des"  class="form-control" placeholder="Ingrese la Descripción de producto" data-provide="typeahead">
                    <input type="hidden" id="id_producto" name="id_producto">
                  </div>
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group">
                  <input type="text" name="total" id="total" readonly class="form-control text-center"
                  placeholder="" value="<?=round($row->total,2) ?>"
                  data-parsley-trigger="change">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-12">
                  <table class="table table-bordered table-hover table-striped">
                    <thead>
                      <tr>
                        <th style="width:10%;">No.</th>
                        <th style="width:30%;">Descripcion</th>
                        <th style="width:10%;">Stock</th>
                        <th style="width:10%;">Cantidad</th>
                        <!--<th style="width:10%;">Costo</th>-->
                        <th style="width:10%;">Precio</th>
                        <th style="width:10%;">Subtotal</th>
                        <th style="width:10%;">Acciones</th>
                      </tr>
                    </thead>
                    <tbody id="table_producto">
                      <?php foreach ($detalles as $key): ?>
                        <tr>
                				<td><?=$key->id_producto ?></td>
                				<td><input type='hidden' class='id_producto' value='<?=$key->id_producto ?>'> <input type='hidden' class='id_s' value='<?=$key->id_stock ?>'> <input type='hidden' class='nombre' value='<?=$key->modelo ?>'><?=$key->modelo." ".$key->color ?></td>
                				<td><input type='hidden' class='color' value='<?=$key->id_color ?>'><input type='text' class='form-control stockd numeric' value='<?=$key->stock+$key->cantidad ?>' style='width:100%;' readonly>
                				<td><input type='text' class='form-control cantidadd numeric' value='<?=$key->cantidad ?>' style='width:100%;'>
                				<input type='hidden' class='form-control costod decimal' value='<?=$key->costo ?>' style='width:100%;'></td>
                				<td><?= $key->precios ?></td>
                				<td><input type='text' class='form-control subtotald' readonly value='<?=$key->subtotal ?>' style='width:100%;'></td>
                				<td class='text-center'><a class='btn btn-danger delete_trd' style='color: white'><i class='mdi mdi-trash-can'></i></a></td>
                			  </tr>
                      <?php endforeach; ?>
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
