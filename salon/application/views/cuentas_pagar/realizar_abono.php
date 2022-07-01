<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox" id="main_view">
				<div class="ibox-title">
					<h3 class="text-success"><b><i class="mdi mdi-plus"></i> Realizar Abono</b></h3>
				</div>
				<div class="ibox-content">
					<form id="form_add" novalidate>
            <input type="text" name="id_cuentas" id="id_cuentas" value="<?=$cuenta->id_cuentas?>" class="form-control" hidden>
						<div class="row">
							<div class="col-lg-6">
								<div class="form-group single-line">
									<label for="saldo">Deuda Total</label>
									<input readonly type="text" name="saldo" id="saldo" value="<?=$cuenta->saldo?>" class="form-control mayu"  placeholder=""
										   required data-parsley-trigger="change">
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group single-line">
									<label for="abonos">Abonos</label>
									<input readonly type="text" name="abonos" id="abonos" value="<?=($cuenta->abono_total=='')?'0.00':$cuenta->abono_total?>" class="form-control mayu"  placeholder=""
										   required data-parsley-trigger="change">
								</div>
							</div>
              <div class="col-lg-6">
								<div class="form-group single-line">
									<label for="monto">Monto</label>
									<input type="text" name="monto" saldo="<?=$cuenta->saldo?>" id="monto" class="form-control"  placeholder=""
										   required data-parsley-trigger="change">
								</div>
							</div>
							<div class="form-actions col-lg-6"></div>
              <div class="form-actions col-lg-6">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
								<button type="submit" id="btn_add" name="btn_add" class="btn btn-success float-right"><i class="mdi mdi-content-save"></i>
									Guardar Registro
								</button>
							</div>
						</div>
						<br>
            <div class="table-responsive">
                <table class="table table-bordered table-hover datatable" id="">
                    <thead class="">
                        <tr>
                          <th>Fecha</th>
                          <th>Hora</th>
                          <th>Abono</th>
                          <th>Actualizar</th>
                          <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php
                        if ($detalleAbonos==0) {
                          // code...
                        }
                        else {
                          // code...
                          foreach ($detalleAbonos as $arrDetalle) {
                            // code...
                            ?>
                              <tr>
                                <td><?=$arrDetalle->fecha; ?></td>
                                <td><?=$arrDetalle->hora; ?></td>
                                <td><input class="form-control abono_monto decimal monto" montoa="<?=$arrDetalle->abono; ?>" saldo="<?=$arrDetalle->saldo_total?>" type="text" name="" value="<?=$arrDetalle->abono; ?>"></td>
                                <td class='text-center'><a class='btn btn-primary update_tr' monto="<?=$arrDetalle->abono; ?>" saldo="<?=$arrDetalle->saldo_total; ?>" abono="<?=$arrDetalle->abono_total; ?>" cuenta="<?=$arrDetalle->id_cuentas_por_pagar; ?>"  id="<?=$arrDetalle->id_abono; ?>" style='color: white'><i class='mdi mdi-folder-upload'></i></a></td>
                                <td class='text-center'><a class='btn btn-danger delete_tr' monto="<?=$arrDetalle->abono; ?>" saldo="<?=$arrDetalle->saldo_total; ?>" abono="<?=$arrDetalle->abono_total; ?>" cuenta="<?=$arrDetalle->id_cuentas_por_pagar; ?>"  id="<?=$arrDetalle->id_abono; ?>" style='color: white'><i class='mdi mdi-trash-can'></i></a></td>
                              </tr>
                            <?php
                          }
                        }
                       ?>
                    </tbody>
                </table>
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
