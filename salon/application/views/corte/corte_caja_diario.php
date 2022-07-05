<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox" id="main_view">
        <div class="ibox-title">
          <h3 class="text-success"><b><i class="mdi mdi-plus"></i> Corte de Caja</b>&nbsp;&nbsp;<label>Usuario : <?= $usuario->nombre ?></label></h3>
        </div>
        <div class="ibox-content">

          <?php if ($hay_apertura != 0) : ?>
            <?php if ($ap_row->id_usuario == $usuario->id_usuario) : ?>
              <form id="form_corte_caja" novalidate>
                <input type="hidden" id="data_ingreso" name="data_ingreso" value="">
                <div class="row">
                  <input type="hidden" name="id_usuario" id="id_usuario" value="<?= $usuario->id_usuario ?>">
                  <input type="hidden" name="id_apertura" id="id_apertura" value="<?= $ap_row->id_apertura; ?>">
                  <input type="hidden" name="turno" id="turno" value="<?= $ap_row->turno; ?>">
                  <input type="hidden" name="caja" id="caja" value="<?= $ap_row->caja; ?>">
                  <div class="col-lg-6">
                    <div class="form-group single-line">
                      <label for="tipo_corte">Tipo de Corte<span class="text-danger">*</span></label>
                      <select id="tipo_corte" name="tipo_corte" class="form-control">
                        <option value="C">Corte de Caja</option>
                        <option value="X">Corte X</option>
                        <option value="Z">Corte Z</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-6 ">
                    <div class="form-group single-line">
                      <label for="serie">Fecha</label>
                      <input type="text" name="fecha" id="fecha" class="form-control mayu" value="<?= date("d-m-Y") ?>" readonly>
                    </div>
                  </div>

                </div>
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group single-line">
                      <div class='alert alert-success text-center' style='font-weight: bold;'>
                        <label style='font-size: 15px;'>Total Documentos</label>
                      </div>

                      <table class="table table-border">
                        <thead>
                          <tr>
                            <th>Tipo Documento</th>
                            <th>N° Inicio</th>
                            <th>N° Final</th>
                            <th>Total Documentos</th>
                            <th>Total Efectivo</th>
                          </tr>
                        </thead>
                        <tbody id='tabla_doc'>
                          <tr>
                            <td>TIQUETE</td>
                            <td><input type="hidden" id="t_min" name="t_min" value="<?= $ticket_rowmm_efectivo->minimo ?>"><?php if ($ticket_rowmm_efectivo !== NULL) {
                                                                                                                              echo   $ticket_rowmm_efectivo->minimo;
                                                                                                                            } ?></td>
                            <td><input type="hidden" id="t_max" name="t_max" value="<?= $ticket_rowmm_efectivo->maximo ?>"><?php if ($ticket_rowmm_efectivo !== NULL) {
                                                                                                                              echo  $ticket_rowmm_efectivo->maximo;
                                                                                                                            } ?></td>
                            <td><input type="hidden" id="t_count" name="t_count" value="<?= $count_rango_efectivo_tik ?>"><?= $count_rango_efectivo_tik; ?></td>
                            <td><input type="hidden" id="t_total" name="t_total" value="<?= $total_efectivo_tik ?>"><?php echo number_format($total_efectivo_tik, 2, ".", ","); ?></td>
                            <!--
                      "total_efectivo_tik"=>$total_efectivo_tik,
                      "total_efectivo_cof"=>$total_efectivo_cof,
                      "total_efectivo_ccf"=>$total_efectivo_ccf,
                      -->

                          </tr>
                          <tr>
                            <!--agregar los hidden para las 5 filas de esto porque van en la tabla controlcaja en un solo cmapo igual que el resto de cortes y validar cada uno C, X, Z-->
                            <td>FACTURA</td>
                            <td><input type="hidden" id="cof_min" name="cof_min" value="<?= $cof_rowmm_efectivo->minimo ?>"><?php if ($cof_rowmm_efectivo !== NULL) {
                                                                                                                              echo $cof_rowmm_efectivo->minimo;
                                                                                                                            } ?></td>
                            <td><input type="hidden" id="cof_max" name="cof_max" value="<?= $cof_rowmm_efectivo->maximo ?>"><?php if ($cof_rowmm_efectivo !== NULL) {
                                                                                                                              echo $cof_rowmm_efectivo->maximo;
                                                                                                                            } ?></td>
                            <td><input type="hidden" id="cof_count" name="cof_count" value="<?= $count_rango_efectivo_cof ?>"><?= $count_rango_efectivo_cof; ?></td>
                            <td><input type="hidden" id="cof_total" name="cof_total" value="<?= $total_efectivo_cof ?>"><?php echo number_format($total_efectivo_cof, 2, ".", ","); ?></td>

                          </tr>
                          <tr>
                            <td>CREDITO FISCAL</td>
                            <td><input type="hidden" id="ccf_min" name="ccf_min" value="<?= $ccf_rowmm_efectivo->minimo ?>"><?php if ($ccf_rowmm_efectivo !== NULL) {
                                                                                                                              echo $ccf_rowmm_efectivo->minimo;
                                                                                                                            } ?></td>
                            <td><input type="hidden" id="ccf_max" name="ccf_max" value="<?= $ccf_rowmm_efectivo->maximo ?>"><?php if ($ccf_rowmm_efectivo !== NULL) {
                                                                                                                              echo $ccf_rowmm_efectivo->maximo;
                                                                                                                            } ?></td>
                            <td><input type="hidden" id="ccf_count" name="ccf_count" value="<?= $count_rango_efectivo_ccf ?>"><?= $count_rango_efectivo_ccf; ?></td>
                            <td><input type="hidden" id="ccf_total" name="ccf_total" value="<?= $total_efectivo_ccf ?>"><?php echo number_format($total_efectivo_ccf, 2, ".", ","); ?></td>


                          </tr>
                          <tr>
                            <td>MONTO APERTURA</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><input type="hidden" id="monto_apertura" name="monto_apertura" value="<?= $ap_row->monto_apertura ?>"><label id="id_total1"><?php if ($ap_row !== NULL) {
                                                                                                                                                              echo number_format($ap_row->monto_apertura, 2, ".", ",");
                                                                                                                                                            } ?></label></td>
                          </tr>

                          <tr>
                            <td>(-RETENCION)</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><input type="hidden" id="total_retencion" name="total_retencion" value="<?= $total_row_ret->retencion ?>"><label id="id_totalre"><?php if ($total_row_ret !== NULL) {
                                                                                                                                                                    echo number_format($total_row_ret->retencion, 2, ".", ",");
                                                                                                                                                                  } ?></label></td>
                          </tr>
                          <tr>
                            <td>TOTAL</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><input type="hidden" id="total_efectivo_fin" name="total_efectivo_fin" value="<?= $total_efectivo_fin; ?>"><label id="id_total"><?php echo number_format($total_efectivo_fin, 2, ".", ","); ?></label></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group single-line">
                      <div class='alert alert-success text-center' style='font-weight: bold;'>
                        <label style='font-size: 15px;'>Total Documentos (tarjetas debito / credito)</label>
                      </div>

                      <table class="table table-border">
                        <thead>
                          <tr>
                            <th>Tipo Documento</th>
                            <th>N° Inicio</th>
                            <th>N° Final</th>
                            <th>Total Documentos</th>
                            <th>Total Ingresos</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>TIQUETE</td>
                            <td><?php if ($ticket_rowmm_tarjeta !== NULL) {
                                  echo   $ticket_rowmm_tarjeta->minimo;
                                } ?></td>
                            <td><?php if ($ticket_rowmm_tarjeta !== NULL) {
                                  echo  $ticket_rowmm_tarjeta->maximo;
                                } ?></td>
                            <td><?= $count_rango_tarjeta_tik; ?></td>
                            <td><?php echo number_format($total_tarjeta_tik, 2, ".", ","); ?></td>
                            <!--
                      "total_tarjeta_tik"=>$total_tarjeta_tik,
                      "total_tarjeta_cof"=>$total_tarjeta_cof,
                      "total_tarjeta_ccf"=>$total_tarjeta_ccf,
                      -->

                          </tr>
                          <tr>
                            <!--agregar los hidden para las 5 filas de esto porque van en la tabla controlcaja en un solo cmapo igual que el resto de cortes y validar cada uno C, X, Z-->
                            <td>FACTURA</td>
                            <td><?php if ($cof_rowmm_tarjeta !== NULL) {
                                  echo $cof_rowmm_tarjeta->minimo;
                                } ?></td>
                            <td><?php if ($cof_rowmm_tarjeta !== NULL) {
                                  echo $cof_rowmm_tarjeta->maximo;
                                } ?></td>
                            <td><?= $count_rango_tarjeta_cof; ?></td>
                            <td><?php echo number_format($total_tarjeta_cof, 2, ".", ","); ?></td>

                          </tr>
                          <tr>
                            <td>CREDITO FISCAL</td>
                            <td><?php if ($ccf_rowmm_tarjeta !== NULL) {
                                  echo $ccf_rowmm_tarjeta->minimo;
                                } ?></td>
                            <td><?php if ($ccf_rowmm_tarjeta !== NULL) {
                                  echo $ccf_rowmm_tarjeta->maximo;
                                } ?></td>
                            <td><?= $count_rango_tarjeta_ccf; ?></td>
                            <td><?php echo number_format($total_tarjeta_ccf, 2, ".", ","); ?></td>


                          </tr>
                          <tr>
                            <td>TOTAL</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><input type="hidden" id="total_tarjeta_fin" name="total_tarjeta_fin" value="<?= $total_tarjeta_fin; ?>"><label><?php echo number_format($total_tarjeta_fin, 2, ".", ","); ?></label></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="col-lg-6" id="caja_mov">
                    <div class="form-group single-line">
                      <div class='alert alert-success text-center' style='font-weight: bold;'>
                        <label style='font-size: 15px;'>Total Movimientos de Caja</label>
                      </div>
                      <table class="table table-border" id="table_mov">
                        <thead>
                          <tr>
                            <th class="col-md-11">Tipo Movimiento</th>
                            <th class="col-md-1">Total</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>ENTRADAS</td>
                            <td><input type="hidden" id="total_entrada_caja" name="total_entrada_caja" value="<?= $total_entrada_caja; ?>"><?php echo number_format($total_entrada_caja, 2, ".", ","); ?></td>
                          </tr>
                          <tr>
                            <td>SALIDAS</td>
                            <td><input type="hidden" id="total_entrada_caja" name="total_salida_caja" value="<?= $total_salida_caja; ?>"><?php echo number_format($total_salida_caja, 2, ".", ","); ?></td>
                          </tr>
                        </tbody>
                        <tfoot>


                          <tr>
                            <td>TOTAL</td>

                            <?php $total_mov_caja = $total_entrada_caja - $total_salida_caja ?>
                            <td><input type="hidden" id="total_mov_caja" name="total_mov_caja" value="<?= $total_mov_caja; ?>"><label id="id_total_mov_caja"><?php echo number_format($total_mov_caja, 2, ".", ","); ?></label></td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group single-line">
                      <div class='alert alert-success text-center' style='font-weight: bold;'>
                        <label style='font-size: 15px;'>Total Devoluciones</label>
                      </div>

                      <table class="table table-border" id="table_dev">
                        <thead>
                          <tr>
                            <th>N°</th>
                            <th>N° Documento</th>
                            <th>Documento Afecta</th>
                            <th>N° Afecta</th>
                            <th>Total</th>
                          </tr>
                        </thead>
                        <tbody id="tabla_devs">
                          <?php if ($rows_devs != NULL) :
                            $i = 1; ?>
                            <?php foreach ($rows_devs as $key) : ?>
                              <tr>
                                <td><input type="hidden" id="tipo_doc" name="tipo_doc" value="<?= $key->tipo_doc; ?>"><?= $i; ?></td>
                                <td><?= $key->correlativo; ?></td>
                                <td><?= $key->nombredoc; ?></td>
                                <td><?= $key->corr_afecta; ?></td>
                                <td><?= $key->total; ?></td>
                                <?php $i++; ?>
                              </tr>
                            <?php endforeach; ?>

                          <?php endif; ?>
                          <td colspan="4">TOTAL</td>
                          <td class="text-right"><input type="hidden" id="total_dev" name="total_dev" value="<?= $total_row_dev->total; ?>"><label id="id_total_dev"><?php if ($total_row_dev !== NULL) {
                                                                                                                                                                        echo number_format($total_row_dev->total, 2, ".", ",");
                                                                                                                                                                      } ?></label></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <!-- <div class="row">

                </div> -->
                <div class="row">


                <div class="col-lg-6">
                  <div class="form-group single-line">
                    <div class='alert alert-success text-center' style='font-weight: bold;'>
                      <label style='font-size: 15px;'>Total Cuentas</label>
                    </div>

                    <table class="table table-border" id="table_dev">
                      <thead>
                        <tr>
                          <th>Tipo de Cuenta</th>
                          <th>Total</th>
                        </tr>
                      </thead>
                      <tbody id="tabla_cuentas">
                        <?php if ($total_abonos_cuentas_cobrar != '') : ?>
                          <tr>
                            <td>CUENTAS POR COBRAR</td>
                            <td><?= number_format($total_abonos_cuentas_cobrar, 2, '.', ''); ?></td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div id="caja_dev">
                  
                  <div class="col-lg-12" id="caja_nc">
                    <div class="form-group single-line">
                      <div class='alert alert-success text-center' style='font-weight: bold;'>
                        <label style='font-size: 15px;'>Total Notas de Credito</label>
                      </div>

                      <table class="table table-border" id="table_nc">
                        <thead>
                          <tr>
                            <th>N°</th>
                            <th>N° Documento</th>
                            <th>Documento Afecta</th>
                            <th>N° Afecta</th>
                            <th>Total</th>
                          </tr>
                        </thead>
                        <tbody>
                          <td colspan="4">TOTAL</td>
                          <td class="text-right"><label id="id_total_nc"><?php $monto_nc = 0.0;
                                                                          echo number_format($monto_nc, 2, ".", ""); ?></label></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  
                </div>
                </div>
 
                <div class="row">
                  <table class="table table-border" id="table_t">
                    <thead>
                      <tr>
                        <th class="col-md-4">Total Efectivo en Caja $</th>
                        <th class="col-md-4" style="text-align: center">Total Corte Caja $</th>
                        <th class="col-md-4" style="text-align: center">Diferencia $</th>
                      </tr>
                    </thead>
                    <tbody id="table_data">
                      <tr>
                        <td>
                          <input type="text" id="total_efectivo" name="total_efectivo" value="" class="form-control decimal" required data-parsley-trigger="change">

                          <input type="hidden" id="total_fin" name="total_fin" value="<?= $total_efectivo_fin + $total_entrada_caja + $total_abonos_cuentas_cobrar - ($total_salida_caja/* + $total_row_dev->total*/) ?>" class="form-control decimal decimal">
                        </td>
                        <td style="text-align: center">
                          <label id="id_total_general"><?php echo number_format(($total_efectivo_fin + $total_entrada_caja + $total_abonos_cuentas_cobrar - ($total_salida_caja/* + $total_row_dev->total*/)), 2, ".", ""); ?></label>
                        </td>
                        <td style="text-align: center">
                          <label id="id_diferencia"></label>
                          <input type="hidden" id="diferencia_val" name="diferencia_val" value="" class="form-control decimal">

                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="row">
                  <div class="col-lg-12">
                    <div class="form-group">
                      <label>Observaciones </label><input type="text" id="observaciones" name="observaciones" placeholder="observaciones" value="" class="form-control ">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="form-actions col-lg-12">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                    <button type="submit" id="btn_corte_caja" name="btn_corte_caja" class="btn btn-success m-t-n-xs float-right"><i class="mdi mdi-content-save"></i>
                      Guardar Registro
                    </button>
                  </div>
                </div>
              </form>
            <?php else : ?>
              <div></div>
              <div class='alert alert-warning text-center' style='font-weight: bold;'>
                <label style='font-size: 15px;'>Ya existe una apertura de caja realizada por "<?= $usuario_ap->nombre ?>"!!</label>
                <br>
                <label style='font-size: 15px;'>Debe de realizar el corte con el usuario que hizo la apertura vigente, para poder hacer transacciones de caja.</label>

              </div>
            <?php endif; ?>
          <?php else : ?>
            <div class="row">
              <div class="col-lg-6">
                <h2 class="text-danger blink_me">No hay apertura de caja para hoy <?= date('d-m-Y'); ?></h2>

                <button role="button" id="btn_redirect" name="btn_redirect" onclick="location.href='<?= base_url(); ?>caja/apertura'" class="btn btn-success m-t-n-xs"><i class="mdi mdi-cash-register"></i>
                  Ir a Apertura
                </button>

              </div>
            </div>
          <?php endif; ?>


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