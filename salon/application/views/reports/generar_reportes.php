<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox" id="main_view">
                <div class="ibox-title">
                    <h3 class="text-success"><b><i class="mdi mdi-plus"></i> Reportes</b></h3>
                </div>
                <div class="ibox-content">
                    <form id="form_add" novalidate>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="nombre">Reporte</label>
                                    <select class="select2" style="width:100%" name="" id="reportes">
                    <?php
                      foreach ($reportes as $arrReportes) {
                        // code...
                        ?>
                          <option value="<?=$arrReportes->id_reporte; ?>"><?=$arrReportes->nombre; ?></option>
                        <?php
                      }
                     ?>

                  </select>
                                    <br>
                                    <label for="nombre">Sucursal</label>
                                    <select data-parsley-trigger="change" style="width:100%" required class="select2" id="sucursal" name="sucursal">
                      <?php foreach ($sucursal as $key): ?>
                        <option <?php if($id_sucursal==$key->id_sucursal){echo "selected";} ?> value="<?=$key->id_sucursal ?>"><?=$key->nombre." ".$key->direccion ?></option>
                      <?php endforeach; ?>
                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group single-line">
                                    <fieldset>
                                        <legend>
                                            Tipo de reporte
                                        </legend>
                                        <div class="">
                                            <input class="radio_usuario" type="radio" id="tipoReporte1" value="0" name="tipoReporte" checked>
                                            <label class="" for="tipoReporte1">General</label>
                                        </div>
                                        <div class="">
                                            <input class="radio_usuario" type="radio" id="tipoReporte2" value="1" name="tipoReporte">
                                            <label class="" for="tipoReporte2">Específico</label>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
              <div class="col-lg-2">
                <div class="form-group">
                  <label for="">Fecha inicio</label>
                  <input readonly type="text" class="form-control datepicker fechaInicio" name="" value="<?=date("d-m-Y")?>">
                </div>
              </div>
              <div class="col-lg-2">
                <div class="form-group">
                  <label for="">Fecha fin</label>
                  <input readonly type="text" class="form-control datepicker fechaFin" name="" value="<?=date("d-m-Y")?>">
                </div>
              </div>
                            <div class="col-lg-1"></div>
              <div class="form-actions col-lg-2">
                <label for="">Acción</label><br>
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                <button type="button" id="generarReporte" name="btn_add" class="btn btn-success m-t-n-xs pull-right" style="width:100%;"><i class="mdi mdi-content-save"></i>
                  Generar reporte
                </button>
              </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <hr>
                                <br>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="nombre">Sucursal</label>
                                    <select data-parsley-trigger="change" style="width:100%" required class="select2" id="sucursalK" name="sucursalK">
                    <?php foreach ($sucursal as $key): ?>
                      <option <?php if($id_sucursal==$key->id_sucursal){echo "selected";} ?> value="<?=$key->id_sucursal ?>"><?=$key->nombre." ".$key->direccion ?></option>
                    <?php endforeach; ?>
                  </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="">Kardex</label>
                                    <select data-parsley-trigger="change" style="width:100%" required class="select2" id="selectProductos" name="selectProductos">
                                        <option value="">Seleccione...</option>
                    <?php foreach ($productos as $arrP): ?>
                      <option value="<?=$arrP->id_producto ?>" color="<?=$arrP->id_color ?>"><?=$arrP->codigo_barra." ".$arrP->nombre." ".$arrP->color; ?></option>
                    <?php endforeach; ?>
                  </select>
                                </div>
                            </div>
                            <div class="col-lg-2">
                <div class="form-group">
                  <label for="">Fecha inicio</label>
                  <input readonly type="text" class="form-control datepicker fechaInicioK" name="" value="<?=date("d-m-Y")?>">
                </div>
              </div>
              <div class="col-lg-2">
                <div class="form-group">
                  <label for="">Fecha fin</label>
                  <input readonly type="text" class="form-control datepicker fechaFinK" name="" value="<?=date("d-m-Y")?>">
                </div>
              </div>
              <div class="form-actions col-lg-2">
                <label for="">Acción</label><br>
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                <button type="button" id="generarReporteKardex" name="generarReporteKardex" class="btn btn-success m-t-n-xs pull-right" style="width:100%;"><i class="mdi mdi-content-save"></i>
                  Generar kardex
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
