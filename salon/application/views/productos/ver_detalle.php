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
        <h3>Detalle de producto</h3>
        <div class="row">
            <div class="col-lg-6">
          <div class="col-lg-12">
            <div class="form-group single-line">
              <label for="nombre">Nombre del producto</label>
              <input type="text" name="nombre" id="nombre" class="form-control mayu" value="<?= $row->nombre ?>" placeholder="Ingrese descripcion" required data-parsley-trigger="change" readonly>
            </div>
          </div>


            <div class="col-lg-12">
          <div class="row">
            <?php if (isset($stock)) :?>
          <div class="col-lg-6">
            <div class="form-group single-line">
              <label for="modelo">Stock<span class="text-danger"></span></label>

                    <input type="text" name="st" id="st" class="form-control mayu" value="<?= $stock->cantidad ?>" required data-parsley-trigger="change" readonly>


            </div>
          </div>
        <?php  endif;  ?>
          <?php
          if (isset($reservado)) :?>
          <div class="col-lg-6">
            <div class="form-group single-line">
              <label for="modelo">Reservado<span class="text-danger"></span></label>

                    <input type="text" name="st" id="st" class="form-control mayu" value="<?= $reservado ?>" required data-parsley-trigger="change" readonly>


            </div>
          </div>
          <?php endif;  ?>
          </div>
        </div>
        </div>
          <div class="col-lg-6">

            <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-use-bootstrap-modal="false">
                <div class="slides"></div>
                <h3 class="title"></h3>
                <a class="prev">‹</a>
                <a class="next">›</a>
                <a class="close">×</a>
                <a class="play-pause"></a>
                <ol class="indicator"></ol>
            </div>
            <div id="links" class="links">
              <?php
              if (isset($imagen)) :
                foreach ($imagen as $m) : ?>
                <a href=<?= $m->url ?> title="<?= $row->nombre ?>" data-gallery="blueimp-gallery">
                  <!--  -->
                  <img alt="imagen" class="img-rounded img-thumbnail" src="<?=$m->url ?>" width="140px" height="80px" border="1">
                </a>
              <?php
                endforeach;
              endif;
              ?>
            </div>
          </div>

        </div>
        <div class="row">
          <div class="col-lg-12">
            <div class="form-group">
              <label for="descripcion">Descripción:</label>
              <textarea class="form-control mayu" id="descripcion" name="descripcion" rows="3" readonly><?= $row->descripcion ?></textarea>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-4">
            <div class="form-group single-line">
              <label for="codigo_barra">Código de Barras</label>
              <input type="text" name="codigo_barra" id="codigo_barra" class="form-control mayu" placeholder="Ingrese un código de barras" value="<?= $row->codigo_barra ?>" data-parsley-trigger="change" readonly>
            </div>
          </div>
          <div class="col-lg-3">
            <div class="form-group single-line">
              <label for="categoria">Categoría</label>
                <?php foreach ($categorias as $cat) : ?>
                  <?php if(($cat->id_categoria == $row->id_categoria)):?>
                    <input type="text" name="categoria" id="codigo_barra" class="form-control mayu" placeholder="" value="<?= $cat->nombre ?>" data-parsley-trigger="change" readonly>
                  <?php endif;?>
                <?php endforeach; ?>

            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group has-info single-line">
              <label>Costo sin IVA</label>
              <input type="text" placeholder="Costo" class="form-control ccos decimal" id="ultcosto" value="<?= number_format($row->costo_s_iva, 2, ".", "") ?>" readonly>
              <input type="hidden" id="costo_s_iva" name="costo_s_iva" value="0">
              <input type="hidden" id="costo_c_iva" name="costo_c_iva" value="0">
              <input type="hidden" id="precio_sugerido" name="precio_sugerido">
              <input type="hidden" id="preciosg" name="preciosg">
              <input type="hidden" id="porcentaje_iva" name="porcentaje_iva" value="<?= $config_impuestos->iva ?>">

            </div>
          </div>
          <div class="col-lg-2">
            <label for="">Exento</label>
            <div class="row">
              <div class="col-sm-6 float_left">
                <label for="">Sí</label><br>
                <input type="radio" class="exento_iva" name="exento_iva" value="1" <?= ($row->exento == 1) ? 'checked' : ''; ?> disabled>
              </div>
              <div class="col-sm-6 float_left">
                <label for="">No</label><br>
                <input type="radio" class="exento_iva" name="exento_iva" value="0" <?= ($row->exento == 1) ? '' : 'checked'; ?> disabled>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <table class="table table-striperd table-hover table-bordered">
              <thead>
                <tr>
                  <th class="" style="text-align: left">Descripción</th>
                  <th class="" style="text-align: center">Costo</th>
                  <th class="" style="text-align: center">IVA</th>
                  <th class="" style="text-align: center">Costo Total</th>
                  <th class="" style="text-align: center">Precio Venta</th>
                  <th class="" style="text-align: center">Ganancia $</th>
                </tr>
              </thead>
              <tbody id="precios">
                <?php
                $lista = "";
                if ($row->exento == 1) {
                  // si el producto es exento de iva...
                  $ivaPorc = 0;
                } else {
                  // code...
                  $ivaPorc = 0.13;
                }
                foreach ($precios as $row_por) {
                  $costo = $row_por->costo;
                  $iva = round($costo * $ivaPorc, 2);
                  $cesc = round($costo * 0.00, 2);
                  $ctotal = $costo + $iva + $cesc;
                  $id = $row_por->id_listaprecio;

                  $costo_iva = $row_por->costo_iva;
                  $detalle = $row_por->descripcion;

                  $resultado2 = $row_por->precio_venta;;
                  $gana = $resultado2 - $ctotal;
                  $lista .= "<tr>";

                  $lista .= "<td style='text-align: left' class='td_desc'><input type='hidden' class='form-control lista_pr' id='id_lista_pr' name='id_lista_pr' value='" . $id . "'>$detalle</td>";
                  $lista .= "<td style='text-align: right' class='td_costo'><input type='hidden' class='form-control costo_td' id='costo_td' name='costo_td' value='" . $costo . "'>$ " . number_format($costo, 2, '.', '') . "</td>";
                  $lista .= "<td style='text-align: right' class='td_costo_iva'><input type='hidden' class='form-control costo_td_iva' id='costo_td_iva' name='costo_td_iva' value='" . $iva . "'>$ " . number_format($iva, 2, '.', '') . "</td>";
                  $lista .= "<td style='text-align: right' class='td_precio_iva'><input type='hidden' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value='" . $ctotal . "'>$ " . number_format($ctotal, 2, '.', '') . "</td>";
                  $lista .= "<td style='text-align: right' class='td_preciolista'> " . number_format($resultado2, 2, '.', '') . " </td>";
                  $lista .= "<td style='text-align: right' class='td_ganancia'><input type='hidden' class='form-control ganancia_td' id='ganancia' name='ganancia' value='" . $gana . "'>$ " . number_format($gana, 2, '.', '') . "</td>";
                  $lista .= "</tr>";
                }

                echo $lista;
                ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12">
            <table class="table table-striperd table-hover table-bordered">
              <thead>
                <tr>
                  <th style="width: 80%; text-align: center">Color</th>
                </tr>
              </thead>
              <tbody id="colores">
                <?php
                $nc = 0;
                foreach ($colores as $color) {

                  echo "<tr id='" . $nc . "'>";
                  echo "<td class='colora'>" . $color->color . "</td>";
                  echo "</tr>";
                  $nc++;
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">

          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
</div>
