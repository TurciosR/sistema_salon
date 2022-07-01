<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox" id="main_view">
        <div class="ibox-title">
          <h3 class="text-success"><b><i class="mdi mdi-plus"></i>Edición de IMEI's</b></h3>
        </div>
        <div class="ibox-content">
          <form id="form_edit" novalidate>
            <input type="hidden" id="id_carga" name="id_carga" value="<?=$row->id_carga ?>">
            <input type="hidden" id="data_ingreso" name="data_ingreso" value="">
            <div class="row">
              <div class="col-lg-9">

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
            <div class="row" id="imeicontainer" style="margin-top:15px;">
              <?php $j=0; foreach ($detalles as $key): ?>
                 <?php
                   $chain=uniqid("",true);
                   ?>
                   <div class="col-lg-4 dat">
                     <div class="form-group single-line">
                       <label><?=$key['nombre'] ?></label>
                       <?php
                       foreach ($key['data'] as $key2) {
                          $spC = "co".($j+1);
                        ?>
                       <input id_imei='<?=$key2->id_imei ?>' id_producto='<?=$key['id_producto'] ?>' id_detalle='<?=$key['id_detalle'] ?>' chain='<?=$key['chain']?>' c='<?=$j+1?>' required data-parsley-trigger="change" type='text' placeholder="IMEI" class='form-control text-right imei <?=$spC?>' value='<?=$key2->imei?>' >
                     <?php $j++; }?>
                     </div>
                   </div>
              <?php endforeach; ?>
            </div>
          </form>
          <input type="hidden" id="max_co" name="max_co" value="<?=$j?>">
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
