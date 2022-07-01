<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox" id="main_view">
				<div class="ibox-title">
					<h3 class="text-success"><b><i class="mdi mdi-plus"></i> Agregar Colores</b></h3>
				</div>
				<div class="ibox-content">
					<form id="form_add" novalidate>

						<div class="row">
							<div class="col-lg-12">
								<div class="form-group single-line">
									<label for="color">Color</label>
									<input type="text" name="color" id="color" class="form-control mayu"  placeholder="Ingrese un color"
										   required data-parsley-trigger="change">
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-actions col-lg-12">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
								<button type="submit" id="btn_add" name="btn_add" class="btn btn-success float-right"><i class="mdi mdi-content-save"></i>
									Guardar Registro
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>

		</div>
	</div>
</div>
