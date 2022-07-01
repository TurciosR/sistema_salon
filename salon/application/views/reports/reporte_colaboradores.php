<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-2">
    </div>
</div>
<div class="wrapper wrapper-content  animated fadeInRight">
     <div class="row">
         <div class="col-lg-12">
             <div class="ibox ">
                 <div class="ibox-title">
                     <h3 class="text-navy"><b><i class="fa fa-1x<?=$icono?>"></i> <?=$nombre_archivo?></b></h3>
                 </div>
                <div class="ibox-content">
                    <form method="POST" action="<?php echo base_url($urlpost);?>" target="_blank">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group has-info single-line">
                                    <label>Gerencia</label>
                                    <select id="gerencia" name="gerencia" class="form-control select">
                                        <option value="0" selected>GENERAL</option>
                                        <?php foreach ($gerencia as $option):?>
                                            <option value="<?=$option->id_coordinacion ?>"> <?= $option->nombre ?> </option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group has-info single-line">
                                    <label>Coordinación</label>
                                    <select id="coordinacion" name="coordinacion" class="form-control select">
                                        <!-- <?php foreach ($unidades as $option):?>
                                            <option value="<?=$option->id_unidad ?>"> <?= $option->nombre ?> </option>
                                        <?php endforeach;?> -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="submit" id="btn_col" name="btn_col" value="Imprimir" class="btn btn-primary m-t-n-xs pull-right">
                                </div>
                            </div>
                        </div>
                    </form>
                 </div>
             </div>
         </div>
     </div>
</div>
<script src="<?= base_url(); ?>assets/js/funciones/<?=$urljs; ?>"></script>
