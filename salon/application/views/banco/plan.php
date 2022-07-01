<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox" id="main_view">
        <div class="ibox-title">
          <h3 class="text-success"><b><i class="mdi mdi-plus"></i> Editar planes</b></h3>
        </div>
        <div class="ibox-content">
            <input type="hidden" id="proceso" name="proceso" value="ajuste">
            <div class="row">
              <div class="col-lg-5">
                <div class="form-group single-line">
                  <label for="nrc">Numero de cuotas</label>
                  <input type="text" name="numero" id="numero" class="form-control numeric" placeholder="Ingrese el numero de cuotas ej: 12">
                </div>
              </div>
              <div class="col-lg-5">
                <div class="form-group single-line">
                  <label for="nrc">Porcentaje de aumento</label>
                  <input type="text" name="porcentaje" id="porcentaje" class="form-control decimal" placeholder="Ingrese un porcentaje de division ej: 0.9">
                </div>
              </div>
              <div class="col-lg-2">
                  <br>
                  <button  id="btn_pp" name="btn_pp"
                  class="btn btn-success"><i
                  class="mdi mdi-plus"></i>
                  Agregar Plan
                </button>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-12">
                <table class="table table-bordered table-hover table-striped" style="width:100%">
                  <thead>
                    <td class="col-lg-5">Cuotas</td>
                    <td class="col-lg-5">Porcentaje</td>
                    <td>Acciones</td>
                  </thead>
                  <tbody class='plan'>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="row">
              <div class="form-actions col-sm-12">
                <input type="hidden" id="id_banco" name="id_banco" value="<?= $row->id ?>">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
              </div>
            </div>


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
