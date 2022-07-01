<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox" id="main_view">
				<div class="ibox-title">
					<h3 class="text-success"><b><i class="mdi mdi-account-edit"></i> Editar Categoria</b></h3>
				</div>
				<div class="ibox-content">
					<form id="form_edit" novalidate>
						<div class="row">
							<div class="col-lg-6">
								<div class="form-group single-line">
									<label for="nombre">Nombre</label>
									<input type="text" name="nombre" id="nombre" class="form-control mayu"  placeholder="Ingrese un nombre" value="<?=$row->nombre?>"
										   required data-parsley-trigger="change">
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group single-line">
									<label for="descripcion">Descripcion</label>
									<input type="text" name="descripcion" id="descripcion" class="form-control mayu"  placeholder="Ingrese una descripcion" value="<?=$row->descripcion?>"
                                    required data-parsley-trigger="change">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="mt-3">
									<label for="foto">Foto de la categoria</label>
									<input type="file" id="foto" name="foto" class="dropify" accept="image/*" data-default-file="<?=base_url($row->imagen)?>"/>
									<p class="text-muted text-center mt-2 mb-0">Agrega foto de la categoria</p>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-actions col-lg-12">
								<input type="hidden" name="id_categoria" value="<?=$row->id_categoria?>">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
								<button type="submit" id="btn_edit" name="btn_add" class="btn btn-success float-right"><i class="mdi mdi-content-save"></i>
									Guardar Registro
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
