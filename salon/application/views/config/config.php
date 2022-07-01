<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox" id="main_view">
                <div class="ibox-title">
                    <h3 class="text-navy"><b><i class="fa fa-pencil"></i> <?= $titulo; ?></b></h3>
                </div>
                <div class="ibox-content">
                    <form name="formulario" id="formulario" novalidate="novalidate">
                        <div class="row">
                            <div class="form-group col-lg-6"><label for="">Nombre empresa</label>
                                <input name="nombre" id="nombre" class="form-control" placeholder="Ingrese el nombre de la empresa" value="<?= $nombre_empresa ?>" type="text">
                            </div>
                            <div class="form-group col-lg-6"><label for="">Dirección</label>
                                <input name="direccion" id="direccion" class="form-control" placeholder="Ingrese la dirección de la empresa" value="<?= $direccion_empresa ?>" type="text">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4"><label for="">Teléfono</label>
                                <input name="telefono" id="telefono" class="form-control" placeholder="Ingrese el teléfono de la empresa" value="<?= $telefono_empresa ?>" type="text"></div>
                            <div class="form-group col-lg-4"><label for="">Correo electrónico</label>
                                <input name="email" id="email" class="form-control" placeholder="Ingrese el correo electrónico de la empresa" value="<?= $correo_empresa ?>" type="text"></div>
                            <div class="form-group col-lg-4"><label for="">Página Web</label>
                                <input name="web" id="web" class="form-control" placeholder="Ingrese la Página Web de la empresa" value="<?= $web_empresa ?>" type="text"></div>
                        </div>
            <div class="row">
                            <div class="col-lg-12">
                                <div class="mt-3">
                                    <label for="logo">Logo de la empresa</label>
                                    <input type="file" id="logo" name="logo" class="dropify" accept="image/*" data-default-file="<?=base_url($logo_empresa)?>"/>
                                    <p class="text-muted text-center mt-2 mb-0">Agrega logo</p>
                                </div>
                            </div>
                        </div>
                        <div class="row"><br>
                            <div class="form-actions col-lg-12">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">

                                <button type="submit" id="btn_edit" name="btn_edit" class="btn btn-success m-t-n-xs pull-right"><i class="mdi mdi-content-save"></i> Guardar cambios</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url(); ?>assets/admin/js/funciones/<?= $urljs; ?>"></script>
