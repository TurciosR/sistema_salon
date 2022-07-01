<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2><?=$this->session->nombre; ?></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <strong>¡Bienvenido/a!</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">

    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
      <?php if($usuario_ap->admin==1 ||$usuario_ap->super_admin==1):?>
        <div class="col-lg-3">
            <a href="<?= base_url("productos") ?>">
                <div class="widget style1 lazur-bg">
                    <div class="row">
                        <div class="col-3">
                            <i class="mdi mdi-archive mdi-48px"></i>
                        </div>
                        <div class="col-9 text-right align-self-center">
                            <span>Gestionar</span>
                        </div>
                        <div class="col-12 text-right">
                            <h4 class="font-bold text-wrap text-break">Productos</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3">
            <a href="<?= base_url("servicios") ?>">
                <div class="widget style1 yellow-bg">
                    <div class="row">
                        <div class="col-3">
                            <i class="mdi mdi-format-list-bulleted mdi-48px"></i>
                        </div>
                        <div class="col-9 text-right align-self-center">
                            <span>Gestionar</span>
                        </div>
                        <div class="col-12 text-right">
                            <h4 class="font-bold text-wrap text-break">Servicios</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3">
            <a href="<?= base_url("categorias") ?>">
                <div class="widget style1 navy-bg">
                    <div class="row">
                        <div class="col-3">
                            <i class="mdi mdi-format-list-bulleted mdi-48px"></i>
                        </div>
                        <div class="col-9 text-right align-self-center">
                            <span>Gestionar</span>
                        </div>
                        <div class="col-12 text-right">
                            <h4 class="font-bold text-wrap text-break">Categorías</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif;?>
  <?php if($usuario_ap->admin==1 ||$usuario_ap->super_admin==1):?>

        <div class="col-lg-3">
            <a href="<?= base_url("configuracion") ?>">
                <div class="widget style1 navy-bg">
                    <div class="row">
                        <div class="col-3">
                            <i class="mdi mdi-archive mdi-48px"></i>
                        </div>
                        <div class="col-9 text-right align-self-center">
                            <span>Gestionar</span>
                        </div>
                        <div class="col-12 text-right">
                            <h4 class="font-bold text-wrap text-break">Configuración</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
          <?php endif;?>
    </div>

    <?php if($rol_usuario=="CAJERO" || $rol_usuario=="CAJERA" ):?>
      <div class="row">
      <div class="col-lg-3">
          <a href="<?= base_url("ventas/agregar") ?>">
              <div class="widget style1 lazur-bg">
                  <div class="row">
                      <div class="col-3">
                          <i class="mdi mdi-cart mdi-48px"></i>
                      </div>
                      <div class="col-9 text-right align-self-center">
                          <span>Venta</span>
                      </div>
                      <div class="col-12 text-right">
                          <h4 class="font-bold text-wrap text-break">Directa</h4>
                      </div>
                  </div>
              </div>
          </a>
      </div>
      <div class="col-lg-3">
          <a href="<?= base_url("corte") ?>">
              <div class="widget style1 yellow-bg">
                  <div class="row">
                      <div class="col-3">
                          <i class="mdi mdi-cash-multiple mdi-48px"></i>
                      </div>
                      <div class="col-9 text-right align-self-center">
                          <span>Corte</span>
                      </div>
                      <div class="col-12 text-right">
                          <h4 class="font-bold text-wrap text-break">Caja</h4>
                      </div>
                  </div>
              </div>
          </a>
      </div>
      <div class="col-lg-3">
          <a href="<?= base_url("caja/apertura") ?>">
              <div class="widget style1 navy-bg">
                  <div class="row">
                      <div class="col-3">
                          <i class="mdi mdi-cash-register mdi-48px"></i>
                      </div>
                      <div class="col-9 text-right align-self-center">
                          <span>Apertura</span>
                      </div>
                      <div class="col-12 text-right">
                          <h4 class="font-bold text-wrap text-break">Caja</h4>
                      </div>
                  </div>
              </div>
          </a>
      </div>
      <div class="col-lg-3">
          <a href="">
              <div class="widget style1 yellow-bg">
                  <div class="row">
                      <div class="col-3">
                          <i class="mdi mdi-cash-register mdi-48px"></i>
                      </div>
                      <div class="col-9 text-right align-self-center">
                          <span>Total Caja</span>
                      </div>
                      <div class="col-12 text-right">
                          <h4 class="font-bold text-wrap text-break">$<?=number_format($totalCaja, 2, '.', '');?></h4>
                      </div>
                  </div>
              </div>
          </a>
      </div>
      </div>
      <div class="row">
        <div class="col-lg-12" style="background:#fff; margin-bottom:25px; max-height:330px; overflow:scroll;">
          <br>
          <label style="float:right; font-weight:bold; font-size:16px; color: #23c6c8;">Movimientos de Caja</label>
          <br>
          <div class="table-responsive">
            <table class="table table-hover datatable">
              <thead style="background: #23c6c8 !important; color: #fff; font-size:14px;">
                <th>Responsable</th>
                <th>Movimiento</th>
                <th>Tipo</th>
                <th>Monto</th>
              </thead>
              <tbody>
                <?php
                if ($datosMovimientos==0) {
                  ?>
                  <tr>
                    <td>Aún no se han ingreado movimientos...</td>
                  </tr>
                  <?php
                }
                else {
                  foreach ($datosMovimientos as $arrMov) {
                    ?>
                      <tr>
                        <td><?=$arrMov->nombre_recibe ?></td>
                        <td><?=$arrMov->concepto ?></td>
                        <td><?=$arrMov->tipo ?></td>
                        <td>$<?=number_format($arrMov->valor, 2,'.',''); ?></td>
                      </tr>
                    <?php
                  }
                  ?>
                  <tr>
                    <td>Total Caja</td>
                    <td></td>
                    <td></td>
                    <td>$<?=number_format($totalCaja, 2,'.',''); ?></td>
                  </tr>
                  <?php
                }
                 ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endif;?>

  <?php if($usuario_ap->admin==1 ||$usuario_ap->super_admin==1):?>
    <div class="row">
      <?php $contSuc = 0;
        foreach ($data as $key): ?>
        <?php
         //validacion de icono y color
         $icono="mdi mdi-archive mdi-48px";
         $color="lazur-bg";

         if (array_key_exists($key->id_estado,$visual)) {
           // code...
           $icono = $visual[$key->id_estado]['icon'];
           $color = $visual[$key->id_estado]['color'];
         }
         ?>
        <div class="col-lg-3">
            <a href="<?= base_url("dashboard/estado/".$key->id_estado) ?>">
                <div class="widget style1 <?=$color ?>">
                    <div class="row">
                        <div class="col-3">
                            <i class="<?=$icono?>"></i>
                        </div>
                        <div class="col-9 text-right align-self-center">
                            <span> <?=$key->descripcion ?> </span>
                        </div>
                        <div class="col-12 text-right">
                            <h4 class="font-bold text-wrap text-break"><?="$ ".number_format($key->total_ventas,2) ?></h4>
                        </div>
                        <?php
                        //var_dump($datosSuc[$contSuc]);
                          foreach ($datosSuc[$contSuc] as $arrDatSuc) {
                            // code...
                            //var_dump($arrDatSuc);
                            foreach ($arrDatSuc as $arrDatos) {
                              // code...
                              ?>
                                <label style="width:100%;"><?=$arrDatos['sucursal']; ?> <strong style="float:right"><?="$".number_format($arrDatos['total'], 2, '.', '')?></strong> </label>
                              <?php
                            }
                          }
                         ?>
                    </div>
                </div>
            </a>
        </div>
      <?php $contSuc++;
      endforeach; ?>
      <div class="col-lg-3">
          <a href="">
              <div class="widget style1 navy-bg">
                  <div class="row">
                      <div class="col-3">
                          <i class="mdi mdi-cash-register mdi-48px"></i>
                      </div>
                      <div class="col-9 text-right align-self-center">
                          <span>Caja</span>
                      </div>
                      <div class="col-12 text-right">
                          <h4 class="font-bold text-wrap text-break">$<?=number_format($totalCaja, 2, '.', '');?></h4>
                      </div>
                  </div>
              </div>
          </a>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6" style="background:#fff; margin-bottom:25px; max-height:330px; overflow:scroll;">
        <br>
        <label style="float:right; font-weight:bold; font-size:16px; color: #23c6c8;">Movimientos de Caja</label>
        <br>
        <div class="table-responsive">
          <table class="table table-hover datatable">
            <thead style="background: #23c6c8 !important; color: #fff; font-size:14px;">
              <tr>
                <th>Responsable</th>
                <th>Movimiento</th>
                <th>Tipo</th>
                <th>Monto</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($datosMovimientos==0) {
                // code...
                ?>
                <tr>
                  <td>Aún no se han ingreado movimientos...</td>
                </tr>
                <?php
              }
              else {
                // code...
                foreach ($datosMovimientos as $arrMov) {
                  // code...
                  ?>
                    <tr>
                      <td><?=$arrMov->nombre_recibe ?></td>
                      <td><?=$arrMov->concepto ?></td>
                      <td><?=$arrMov->tipo ?></td>
                      <td>$<?=number_format($arrMov->valor, 2,'.',''); ?></td>
                    </tr>
                  <?php
                }
                ?>
                <tr>
                  <td>Total Caja</td>
                  <td></td>
                  <td></td>
                  <td>$<?=number_format($totalCaja, 2,'.',''); ?></td>
                </tr>
                <?php
              }
               ?>

            </tbody>
          </table>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="ibox float-e-margins">
          <div class="ibox-title">
            <h5 style="color:#000;">Estado de Ventas</h5>
            <div class="ibox-tools">
              <a class="collapse-link">
                <i class="fa fa-chevron-up" style="color:#000;"></i>
              </a>
            </div>
          </div>
          <div class="ibox-content" style="margin-top: 1.8px;">
            <div>

              <canvas id="myChart" style="width: 495px; height: 250px;"></canvas>

              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
              value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif;?>
</div>

<script type="text/javascript">
$(document).ready(function() {
  grafica();
});

function grafica() {
  tok = $("#csrf_token_id").val();
  $.ajax({
    url: base_url+"Dashboard/getGrafica",
    data:
    {
      csrf_test_name:tok,
    },
    method: "POST",
    success: function(data) {
      var mes = [];
      var total = [];
      var obj = jQuery.parseJSON(data);

      for (var i in obj) {
        mes.push(obj[i].mes);
        total.push(obj[i].total);
      }

      var chartdata = {
        labels: mes,
        datasets: [{
          label: 'NUMERO DE VENTAS',
          backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(95, 172, 136, 0.2)',
            'rgba(95, 105, 136, 0.2)',
            'rgba(255, 57, 218, 0.2)',
            'rgba(0, 255, 0, 0.2)'
          ],
          borderColor: [
            'rgba(255,99,132,1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(95, 172, 136, 1)',
            'rgba(95, 105, 136, 1)',
            'rgba(255, 57, 218, 1)',
            'rgba(0, 255, 0, 1)'
          ],
          //backgroundColor:'rgba(54, 162, 235, 0.2)',
          //borderColor:'rgba(54, 162, 235, 1)',
          borderWidth: 1.2,
          data: total,
        }]
      };

      var ctx = $("#myChart");

      var barGraph = new Chart(ctx, {
        type: 'pie',
        data: chartdata,
        options: {
          title: {
            display: true,
            text: 'Estado de Ventas Mensual'
          },
          responsive: true,
        },
      });
    },
    error: function(data) {
      console.log(data);
    }
  });
}
</script>
