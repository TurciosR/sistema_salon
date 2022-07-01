<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox" id="main_view">
        <div class="ibox-title">
          <h3 class="text-success"><b><i class="mdi mdi-plus"></i> Editar Banco</b></h3>
        </div>
        <div class="ibox-content">
          <form id="form_edit" novalidate>
            <input type="hidden" id="proceso" name="proceso" value="ajuste">
            <div class="row">
              <div class="col-lg-9">
                <div class="form-group">
                  <input type="text" name="banco" id="banco" class="form-control mayu"
                  placeholder="Ingrese el nombre del banco"
                  required data-parsley-trigger="change" value="<?=$row->nombre?>">
                </div>
              </div>
              <div class="form-actions col-sm-3">
                <input type="hidden" id="id" name="id" value="<?=$row->id?>">
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
							<div class="col-lg-12">
								<div class="mt-3">
									<label for="foto">Foto del Banco</label>
									<input type="file" id="foto" name="foto" class="dropify" accept="image/*"/ data-default-file="<?=base_url($row->imagen)?>">
									<p class="text-muted text-center mt-2 mb-0">Agrega foto del banco</p>
								</div>
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
