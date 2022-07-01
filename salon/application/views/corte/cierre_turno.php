<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="modal-header">
    <div class="col-lg-1">
      <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
    </div>
    <div class="col-lg-11">
      <h4 class="modal-title">Cierre turno</h4>
      <h3 class="text-success">&nbsp;&nbsp;<label>Usuario : <?=$usuario->nombre?></label></h3>

      <small class="font-bold">Rellena los datos campos requeridos</small>
    </div>
</div>
<div class="modal-body">
    <?php if($hay_apertura!=0):?>
    <form id="form_cierre" data-parsley-validate>
      <div class="row">
        <input type="hidden" name="id_usuario" id="id_usuario" value="<?=$usuario->id_usuario?>">
        <input type="hidden" name="id_apertura" id="id_apertura" value="<?= $ap_row->id_apertura;?>">
        <input type="hidden" name="turno" id="turno" value="<?=$ap_row->turno;?>">
        <input type="hidden" name="caja" id="caja" value="<?= $ap_row->caja;?>">

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
              <input type="text" id="total_efectivo" name="total_efectivo" value=""  class="form-control decimal"
                required data-parsley-trigger="change">

              <input type="hidden" id="total_fin" name="total_fin" value="<?=$total_corte + $total_entrada_caja  - ($total_salida_caja+$total_row_dev->total)?>"  class="form-control decimal decimal">
            </td>
            <td style="text-align: center">
              <label id="id_total_general"><?php echo number_format(($total_corte + $total_entrada_caja  - ($total_salida_caja+$total_row_dev->total)),2,".","");?></label></td>
              <td style="text-align: center">
                <label id="id_diferencia"></label>
                <input type="hidden" id="diferencia_val" name="diferencia_val" value=""  class="form-control decimal">

              </td>
            </tr>
          </tbody>
        </table>
      </div>
        <!--Valores para cierre Facturacion -->
        <input type="hidden" id="t_min" name="t_min" value="<?=$ticket_rowmm_efectivo->minimo?>">
        <input type="hidden" id="t_max" name="t_max" value="<?=$ticket_rowmm_efectivo->maximo?>">
        <input type="hidden" id="t_count" name="t_count" value="<?=$count_rango_efectivo_tik?>">
        <input type="hidden" id="t_total" name="t_total" value="<?=$total_efectivo_tik?>">
        <input type="hidden" id="cof_min" name="cof_min" value="<?=$cof_rowmm_efectivo->minimo?>">
        <input type="hidden" id="cof_max" name="cof_max" value="<?=$cof_rowmm_efectivo->maximo?>">
        <input type="hidden" id="cof_count" name="cof_count" value="<?=$count_rango_efectivo_cof?>">
        <input type="hidden" id="cof_total" name="cof_total" value="<?=$total_efectivo_cof?>">
        <input type="hidden" id="ccf_min" name="ccf_min" value="<?=$ccf_rowmm_efectivo->minimo?>">
        <input type="hidden" id="ccf_max" name="ccf_max" value="<?=$ccf_rowmm_efectivo->maximo?>">
        <input type="hidden" id="ccf_count" name="ccf_count" value="<?=$count_rango_efectivo_ccf?>">
        <input type="hidden" id="ccf_total" name="ccf_total" value="<?=$total_efectivo_ccf?>">
        <input type="hidden" id="monto_apertura" name="monto_apertura" value="<?=$ap_row->monto_apertura?>">
        <input type="hidden" id="total_retencion" name="total_retencion" value="<?=$total_row_ret->retencion?>">
        <input type="hidden" id="total_efectivo_fin" name="total_efectivo_fin" value="<?=$total_corte;?>"><label id="id_total">
          <!-- Mov caja-->
        <input type="hidden" id="total_entrada_caja" name="total_entrada_caja" value="<?= $total_entrada_caja;?>">
        <input type="hidden" id="total_entrada_caja" name="total_salida_caja" value="<?= $total_salida_caja;?>">
        <!--Total devoluciones -->
        <input type="hidden" id="total_dev" name="total_dev" value="<?=$total_row_dev->total;?>">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
    </form>
  <?php else:?>
    <div class="row">
  <div class="col-lg-6">
    <h2 class="text-danger blink_me">No hay apertura de caja para hoy <?= date('d-m-Y');?></h2>

    <button  role="button" id="btn_redirect" name="btn_redirect" onclick="location.href='<?= base_url();?>caja/apertura'"
    class="btn btn-success m-t-n-xs"><i
    class="mdi mdi-cash-register"></i>
    Ir a apertura
  </button>

</div>
  </div>
<?php endif;?>

</div>
<div class="modal-footer">
    <?php if($hay_apertura!=0):?>
    <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
    <button type="submit" form="form_cierre" class="btn btn-primary" id="btn_cierre" >Guardar</button>
    <?php endif;?>
</div>


<script>
    $(".numeric").numeric({
        negative: false,
        decimal: true,
    });
</script>
