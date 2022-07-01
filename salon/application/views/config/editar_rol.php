<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox" id="main_view">
                <div class="ibox-title">
                    <h3 class="text-navy"><b><i class="mdi mdi-account-edit"></i> Editar rol</b></h3>
                </div>
                <div class="ibox-content">
                    <form id="form_edit" novalidate>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group single-line">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" name="nombre" id="nombre" required data-parsley-trigger="change" value="<?=$row->nombre?>">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group single-line">
                                    <label>Descripción</label>
                                    <input type="text" class="form-control" name="descripcion" id="descripcion" required data-parsley-trigger="change" value="<?=$row->descripcion?>">
                                </div>
                            </div>
                        </div>
                        <div class="row m-t-10">
                            <?php $n=0; ?>
                            <?php foreach($menus as $rows):?>
                            <?php if($n%4==0):?>
                        </div>
                        <div class="row m-t-10">
                            <?php endif;?>
                            <div class="col-lg-3">
                                <div class="panel panel-success">
                                    <div class="panel-heading"><i class="<?=$rows->icono?>"></i> <?=$rows->nombre?></div>
                                    <div class="panel-body">
                                        <?php foreach ($controller[$rows->id_menu] as $control):?>
                                            <div class="checkbox m-r-xs">
                                                <input type="checkbox" id="chbController<?=$control->id_modulo?>" name="chbController" value="<?=$control->id_modulo?>" class="checkboxes"
                                               <?php
                                               $has=false;
                                               if(isset($roles)){
                                                   foreach ($roles as $us){
                                                       if($us->id_modulo==$control->id_modulo) $has=true;
                                                   }
                                                   if($has==true)echo "checked='true'";
                                                   $has=false;
                                               }
                                               ?>
                                                >
                                                <label for="chbController<?=$control->id_modulo?>"> <?=$control->nombre?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <?php $n++ ?>
                            <?php endforeach;?>

                        </div>

                        <div class="row">
                            <div class="form-actions col-lg-12">
                                <input type="hidden" value="<?=$row->id_rol?>" id="id_rol">
                                <button type="button" id="btn_edit" class="btn btn-success m-t-n-xs float-right"><i class="mdi mdi-content-save"></i>
                                    Guardar registro
                                </button>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <div class="ibox" style="display: none;" id="divh">
                <div class="ibox-content text-center">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2 class="text-danger blink_me">¡Espere un momento, procesando su solicitud!</h2>
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
