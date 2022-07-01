<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox" id="main_view">
				<div class="ibox-title">
					<h3 class="text-success"><b><i class="mdi mdi-plus"></i> Agregar Carrier</b></h3>
				</div>
				<div class="ibox-content">

						<div class="row">
							<div class="col-lg-3">
								<div class="form-group single-line">
									<label for="nombre">Nombre</label>
									<input type="text" name="nombre" id="nombre" class="form-control mayu"  placeholder="Ingrese un nombre" required data-parsley-trigger="change">
								</div>
							</div>
							<div class="col-lg-3">
								<div class="form-group single-line">
									<label for="telefono">Teléfono</label>
									<input type="text" name="telefono" id="telefono" class="form-control mayu"  placeholder="Ingrese un teléfono" required data-parsley-trigger="change">
								</div>
							</div>
							<div class="col-lg-3">
								<div class="form-group single-line">
									<label for="contacto">Contacto</label>
									<input type="text" name="contacto" id="contacto" class="form-control mayu"  placeholder="Ingrese un contacto" required data-parsley-trigger="change">
								</div>
							</div>
							<div class="col-lg-3">
								<div class="form-group single-line">
									<label for="contacto">Sucursal</label>
									<select class="form-control select2" name="sucursal" id="sucursal">
                    <?php foreach ($sucursal as $sucurale): ?>
                      <option value="<?= $sucurale->id_sucursal ?>" <?php if($sucurale->id_sucursal==$id_sucursal){ echo " selected "; } ?> ><?= $sucurale->direccion ?></option>
                    <?php endforeach; ?>
                  </select>
								</div>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-lg-3">
								<div class="form-group single-line">
									<label for="nombre">Tipo</label>
									<input type="text" name="tipo" id="tipo" class="form-control mayu"  placeholder="Ingrese un tipo" required data-parsley-trigger="change">
								</div>
							</div>
							<div class="col-lg-3">
								<div class="form-group single-line">
									<label for="nombre">Descripción</label>
									<input type="text" name="descripcion" id="descripcion" class="form-control mayu"  placeholder="Ingrese una descripcion" required data-parsley-trigger="change">
								</div>
							</div>
							<div class="col-lg-3">
								<div class="form-group single-line">
									<label for="nombre">Monto</label>
									<input type="text" name="monto" id="monto" class="form-control decimal"  placeholder="Ingrese una monto" required data-parsley-trigger="change">
								</div>
							</div>
							<div class="col-lg-3">
									<button type="submit" id="btn_add_tipo" name="btn_add_tipo" class="btn btn-success float-right" style="margin-top:25px;"><i class="mdi mdi-plus"></i>Agregar</button>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<table class="table table-bordered table-hover datatable no-footer">
									<thead>
										<tr>
											<th>Tipo</th>
											<th>Descripción</th>
											<th>Monto</th>
											<th>Accion</th>
										</tr>
									</thead>
									<tbody class="lista_tipos">

									</tbody>
								</table>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="form-actions col-lg-12">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
								<button type="submit" id="btn_add" name="btn_add" class="btn btn-success float-right"><i class="mdi mdi-content-save"></i>
									Guardar Registro
								</button>
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
