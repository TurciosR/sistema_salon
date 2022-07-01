<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="modal-header">
  <div class="col-lg-1">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
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
            <h3>Precios Contado</h3>
            <table class="table table-stripped">
              <thead>
                <td>Descripción</td>
                <td>Precio</td>
              </thead>
              <tbody>
                <?php foreach ($precios as $key): ?>
                  <tr>
                    <td><?=$key->descripcion ?></td>
                    <td style="text-align:right"><?=$key->total_iva ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <?php if ($exis_p): ?>
              <h3>Precios Credito</h3>
              <table class="table table-stripped">
                <thead>
                  <td>Banco</td>
                  <td>Cuotas</td>
                  <td>Cuota</td>
                  <td>Tipo Precio</td>
                  <td>Precio</td>
                </thead>
                <tbody>
                  <?php foreach ($planes as $key ): ?>
                    <?php foreach ($precios as $keys): ?>
                      <tr>
                        <td><?=$key['banco'] ?></td>
                        <td><?=$key['cuotas'] ?></td>

                        <td style="text-align:right"><?=number_format(($keys->total_iva/$key['porcentaje'])/$key['cuotas'],2) ?></td>
                        <td><?=$keys->descripcion ?></td>
                        <td style="text-align:right"><?=number_format($keys->total_iva/$key['porcentaje'],2) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>

          </div>
      </div>
  </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
</div>
