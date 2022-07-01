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
    <?php if (isset($tipodoc)):?>
      <div class="col-lg-12">
        <h5>Concepto: <?php echo $rowcompra->concepto ?></h5>
        <h5>Documento: <?php echo $tipodoc->nombredoc ?>&nbsp; Proveedor: <?php echo $proveedor->nombre ?></h5>
      </div>
    <?php endif; ?>
      <div class="col-lg-12">
          <div class="form-group">
            <table class="table table-stripped table-bordered">
              <?php
              switch ($process) {
                case 'cargas':
                  ?>
                  <thead>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Costo</th>
                    <th>Subtotal+ IVA</th>
                  </thead>
                  <tbody>
                    <?php
                    $total=0;
                    foreach ($rows as $key):
                    ?>
                      <tr>
                        <td><?=$key->color ?></td>
                        <td style="text-align:right"><?=$key->cantidad ?></td>
                        <td style="text-align:right"><?=number_format($key->costo,2) ?></td>
                        <td style="text-align:right"><?=number_format($key->subtotal,2) ?></td>
                      </tr>
                    <?php
                    $total+=$key->subtotal;
                  endforeach; ?>
                    <tr>
                      <td>TOTAL</td>
                      <td colspan=3 style="text-align:right">$ <?=number_format($total,2) ?></td>
                    </tr>
                  </tbody>
                  <?php
                  break;
                case 'descarga':
                ?>
                <thead>
                  <th>Producto</th>
                  <th>Cantidad</th>
                  <th>Costo</th>
                  <th>Precio</th>
                  <th>Subtotal</th>
                </thead>
                <tbody>
                  <?php foreach ($rows as $key): ?>
                    <tr>
                      <td><?=$key->color ?></td>
                      <td style="text-align:right"><?=$key->cantidad ?></td>
                      <td style="text-align:right"><?=number_format($key->costo,2) ?></td>
                      <td style="text-align:right"><?=number_format($key->precio,2) ?></td>
                      <td style="text-align:right"><?=number_format($key->subtotal,2) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
                <?php
                  break;
                case 'venta':
                ?>
                <thead>
                  <th>Producto</th>
                  <th>Cantidad</th>
                  <th>Costo</th>
                  <th>Precio</th>
                  <th>Subtotal</th>
                </thead>
                <tbody>
                  <?php foreach ($rows as $key): ?>
                    <tr>
                      <td><?=$key->color ?></td>
                      <td style="text-align:right"><?=$key->cantidad ?></td>
                      <td style="text-align:right"><?=number_format($key->costo,2) ?></td>
                      <td style="text-align:right"><?=number_format($key->precio,2) ?></td>
                      <td style="text-align:right"><?=number_format($key->subtotal,2) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
                <?php
                  break;

                  case 'descarga':
                  ?>
                  <thead>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Costo</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                  </thead>
                  <tbody>
                    <?php foreach ($rows as $key): ?>
                      <tr>
                        <td><?=$key->nombre ?></td>
                        <td style="text-align:right"><?=$key->cantidad ?></td>
                        <td style="text-align:right"><?=number_format($key->costo,2) ?></td>
                        <td style="text-align:right"><?=number_format($key->precio,2) ?></td>
                        <td style="text-align:right"><?=number_format($key->subtotal,2) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                  <?php
                    break;
                  case 'traslado':
                  ?>
                  <thead>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Costo</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                  </thead>
                  <tbody>
                    <?php foreach ($rows as $key): ?>
                      <tr>
                        <td><?=$key->color ?></td>
                        <td style="text-align:right"><?=$key->cantidad ?></td>
                        <td style="text-align:right"><?=number_format($key->costo,2) ?></td>
                        <td style="text-align:right"><?=number_format($key->precio,2) ?></td>
                        <td style="text-align:right"><?=number_format($key->subtotal,2) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                  <?php
                    break;
                    case 'ajuste':
                    ?>
                    <thead>
                      <th>Producto</th>
                      <th>Sistema</th>
                      <th>Manual</th>
                      <th>Costo</th>
                      <th>Precio</th>
                      <th>Subtotal</th>
                    </thead>
                    <tbody>
                      <?php foreach ($rows as $key): ?>
                        <tr>
                          <td><?=$key->color ?></td>

                          <td style="text-align:right"><?=$key->stock_anterior ?></td>
                          <td style="text-align:right"><?=$key->cantidad ?></td>
                          <td style="text-align:right"><?=number_format($key->costo,2) ?></td>
                          <td style="text-align:right"><?=number_format($key->precio,2) ?></td>
                          <td style="text-align:right"><?=number_format($key->subtotal,2) ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                    <?php
                      break;

                default:
                  // code...
                  break;
              }
               ?>
            </table>
          </div>
      </div>
  </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
</div>
