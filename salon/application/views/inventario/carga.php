<?php
defined('BASEPATH') or exit('No direct script access allowed');

if($this->session->admin==1 || $this->session->super_admin==1){
  $validarCostos = "";
}
else {
  ?>
  <style media="screen">

    .table tr th:nth-child(5)
    {
      display: none;
    }
    .table tr th:nth-child(6)
    {
      display: none;
    }
    .table tr th:nth-child(7)
    {
      display: none;
    }

    .table tr td:nth-child(5)
    {
      display: none;
    }
    .table tr td:nth-child(6)
    {
      display: none;
    }
    .table tr td:nth-child(7)
    {
      display: none;
    }

  </style>
  <?php
}

?>
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox" id="main_view">
        <div class="ibox-title">
          <h3 class="text-success"><b><i class="mdi mdi-plus"></i> Carga de inventario</b></h3>
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
              <div class="col-sm-4">
                <div class="form-group has-info">
                  <div id="scrollable-dropdown-menu">
                    <input type="text" id="producto" name="producto"  class="form-control" placeholder="Ingrese la descripci??n de producto" data-provide="typeahead">
                    <input type="hidden" id="id_producto" name="id_producto">
                  </div>
                </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group has-info">
                  <div id="scrollable-dropdown-menu">
                    <input type="text" id="barcore" name="barcore"  class="form-control" placeholder="Ingrese c??digo de barras">
                  </div>
                </div>
              </div>
              <div class="col-lg-2">
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
                        <th style="width:25%;">Descripci??n</th>
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

          </form>
        </div>

      </div>
      <div class="ibox" style="display: none;" id="divh">
        <div class="ibox-content text-center">
          <div class="row">
            <div class="col-lg-12">
              <h2 class="text-danger blink_me">??Espere un momento, procesando su solicitud!</h2>
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
