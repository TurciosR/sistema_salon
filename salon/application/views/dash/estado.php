<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2><?=$this->session->nombre; ?></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <strong>Bienvenido!</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">

    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row">
      <div class="col-lg-12">
        <div class="ibox float-e-margins">
          <div class="ibox-title">
            <h5 style="color:#000;">Total productos estado: <?= $rows->descripcion ?> por año  </h5>
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
              <input type="hidden" id="tipo" name="tipo" value="<?= $rows->id_estado ?>">
            </div>
          </div>
        </div>
      </div>
    </div>

</div>

<script type="text/javascript">
$(document).ready(function() {
  grafica();
});

function grafica() {
  tok = $("#csrf_token_id").val();
  tipo = $("#tipo").val();
  $.ajax({
    url: base_url+"Dashboard/getGraficaEstado",
    data:
    {
      csrf_test_name:tok,
      tipo:tipo,

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
        type: 'bar',
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
