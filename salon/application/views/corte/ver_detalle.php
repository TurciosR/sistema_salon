<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="modal-header">
  <div class="col-lg-1">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
  </div>
  <div class="col-lg-11">
    <h4 class="modal-title">Detalles</h4>
    <small class="font-bold"></small>
  </div>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-lg-12">
          <div class="form-group">

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
                    <td><input type="hidden" id="t_min" name="t_min" value="<?=$rowcorte->tinicio;?>"><?=$rowcorte->tinicio;?></td>
                    <td><input type="hidden" id="t_max" name="t_max" value="<?=$rowcorte->tinicio;?>"><?=$rowcorte->tfinal;?></td>
                    <td><input type="hidden" id="t_count" name="t_count" value="<?=$rowcorte->tinicio;?>"><?=$rowcorte->totalnot;?></td>
                    <td><input type="hidden" id="t_total" name="t_total"value="<?=	$rowcorte->tinicio;?>"><?=$rowcorte->totalt;?></td>

                  </tr>
                  <tr>
                    <td>FACTURA</td>
                    <td><input type="hidden" id="cof_min" name="cof_min" value="<?=$rowcorte->tinicio;?>"><?=$rowcorte->finicio;?></td>
                    <td><input type="hidden" id="cof_max" name="cof_max" value="<?=$rowcorte->tinicio;?>"><?=$rowcorte->ffinal;?></td>
                    <td><input type="hidden" id="cof_count" name="cof_count" value="<?=$rowcorte->tinicio;?>"><?=$rowcorte->totalnof;?></td>
                    <td><input type="hidden" id="cof_total" name="cof_total" value="<?=$rowcorte->tinicio?>"><?php echo number_format($rowcorte->totalf,2,".",","); ?></td>

                  </tr>
                  <tr>
                    <td>CREDITO FISCAL</td>
                    <td><input type="hidden" id="ccf_min" name="ccf_min" value="<?=$rowcorte->tinicio;?>"><?=$rowcorte->cfinicio;?></td>
                    <td><input type="hidden" id="ccf_max" name="ccf_max" value="<?=$rowcorte->tinicio;?>"><?=$rowcorte->cffinal;?></td>
                    <td><input type="hidden" id="ccf_count" name="ccf_count" value="<?=$rowcorte->tinicio;?>"><?=$rowcorte->totalnocf;?></td>
                    <td><input type="hidden" id="ccf_total" name="ccf_total" value="<?=$rowcorte->tinicio?>"><?php echo number_format($rowcorte->totalcf,2,".",","); ?></td>


                  </tr>
                  <tr>
                    <td>MONTO APERTURA</td>
                    <td></td><td></td><td>$</td>
                    <td><input type="hidden" id="monto_apertura" name="monto_apertura" value="<?=$ap_row->monto_apertura?>"><label id="id_total1"><?php if($ap_row!==NULL){ echo number_format($ap_row->monto_apertura,2,".",",");} ?></label></td>
                  </tr>
                  <tr>
                    <td>OTROS INGRESOS(ENTRADAS)</td>
                      <td></td><td></td><td>$</td>
                    <td><input type="hidden" id="total_entrada_caja" name="total_entrada_caja" value="<?= $rowcorte->ingresos;?>"><?php echo number_format($rowcorte->ingresos,2,".",","); ?></td>
                  </tr>
                  <tr>
                    <td>(-)SALIDAS(VALES)</td>
                      <td></td><td></td><td>$</td>
                    <td><input type="hidden" id="total_entrada_caja" name="total_salida_caja" value="<?= $rowcorte->vales;?>"><?php echo number_format($rowcorte->vales,2,".",","); ?></td>
                  </tr>

                  <tr>
                    <td>TOTAL</td>
                      <td></td><td></td><td>$</td>
                      <td><input type="hidden" id="total_efectivo_fin" name="total_efectivo_fin" value="<?=$total_tmp;?>"><label id="id_total"><?php echo number_format($total_tmp,2,".",","); ?></label></td>
                    </tr>
                </tbody>
              </table>


          </div>
          </div>
      </div>
  </div>
</div>
<div class="modal-footer">

  <input type="hidden" name="id_corte" id="id_corte" value="<?=$id;?>">
  <button type="button" class="btn btn-success printicket" id="btn_printicket" name="btn_printicket"><i class="mdi mdi-printer"></i> Reimprimir</button>

    <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
</div>
