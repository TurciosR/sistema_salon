<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox" id="main_view">
        <div class="ibox-title">
          <h3 class="text-success font-weight-bold">
            <i class="mdi mdi-plus"></i> Apertura de caja
          </h3>
        </div>
        <div class="ibox-content">
          <form id="form_add_apert" novalidate>
              <?php if (isset($caja)):?>
            
              <div class="row">
                <input type="hidden" name="id_usuario" id="id_usuario" value="<?=$usuario->id_usuario?>">
                <div class="col-lg-6 ">
                  <div class="form-group single-line">
                    <label for="serie">Fecha</label>
                    <input type="text" name="fecha" id="fecha" class="form-control datepicker"
                    placeholder="Seleccione una fecha" value="<?=date("d-m-Y")?>"
                    required data-parsley-trigger="change">
                  </div>
                </div>
                <div class="col-lg-6">
                <div class="form-group single-line">
                 <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="form-control mayu"
                    placeholder="Ingrese un nombre de usuario"  value="<?=$usuario->nombre?>" readonly
                  required data-parsley-trigger="change">
                </div>
              </div>
              </div>
              <div class="row">
                  <div class="col-lg-6">
                      <div class="form-group single-line">
                          <label for="caja">Caja apertura<span class="text-danger">*</span></label>
                          <select name="caja" id="caja" class="form-control" >
                            <?php if (isset($caja)):?>
                              <option value="0">Seleccione </option>
                              <?php foreach ($caja as $cashdraw): ?>
                                  <option value="<?=$cashdraw->id_caja?>"
                                  ><?=$cashdraw->nombre?></option>
                              <?php endforeach; ?>
                            <?php else:?>
                              <option value="-1">Ya están aperturadas todas las cajas</option>
                            <?php endif; ?>
                          </select>
                      </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group has-info single-line">
                        <label>Monto apertura <span style="color:red;">*</span></label>
                        <input type="text" class="form-control numeric" id="monto_apertura" name="monto_apertura"
                          required data-parsley-trigger="change">
                    </div>
                </div>
              </div>
                <div class="row">
                    <div class="form-actions col-lg-12">
                      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                      value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                      <button type="submit" id="btn_add_apert" name="btn_add_apert"
                      class="btn btn-success m-t-n-xs float-right"><i
                      class="mdi mdi-content-save"></i>
                      Guardar registro
                      </button>
                    </div>
                  </div>

            <?php else:?>
              <div class="row">

                <div class='alert alert-warning text-center' style='font-weight: bold;'>
                  <label style='font-size: 15px;'>¡¡No hay cajas disponibles para apertura!!</label>
                  <br>
                  <label style='font-size: 15px;'>Debe de realizar un corte para poder iniciar una nueva apertura de caja.</label>

                </div>
            </div>
            <?php endif;?>
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
