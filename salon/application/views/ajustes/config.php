<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox" id="main_view">
                <div class="ibox-title">
                    <h3 class="text-navy"><b><i class="mdi mdi-cogs"></i> <?= $titulo; ?></b></h3>
                </div>
                <div class="ibox-content">
                    <form name="formulario" id="formulario" novalidate="novalidate">
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label class="text-dark font-bold" for="">Nombre Empresa</label>
                                <input name="nombre" id="nombre" class="form-control" placeholder="Ingrese el nombre de la empresa" value="<?= $nombre_empresa ?>" type="text">
                            </div>
                            <div class="form-group col-lg-6"><label class="text-dark font-bold" for="">Direccion</label>
                                <input name="direccion" id="direccion" class="form-control" placeholder="Ingrese la direccion de la empresa" value="<?= $direccion_empresa ?>" type="text">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="text-dark font-bold" for="">Telefono</label>
                                <input name="telefono" id="telefono" class="form-control tel" placeholder="Ingrese el telefono de la empresa" value="<?= $telefono_empresa ?>" type="text">
                            </div>
                            <div class="form-group col-lg-4"><label class="text-dark font-bold" for="">Correo Electronico</label>
                                <input name="email" id="email" class="form-control" placeholder="Ingrese el Correo electronico de la empresa" value="<?= $correo_empresa ?>" type="text"></div>
                            <div class="form-group col-lg-4"><label class="text-dark font-bold" for="">Pagina Web</label>
                                <input name="web" id="web" class="form-control" placeholder="Ingrese la pagina web de la empresa" value="<?= $web_empresa ?>" type="text"></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <label for="logo">Imagen principal</label>
                                <input type="file" id="logo" name="logo" class="dropify" accept="image/*" data-default-file="<?php if($logo_empresa!="") echo base_url($logo_empresa)?>"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-actions col-lg-12">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                                <input id="proccess" name="proccess" value="edited" type="hidden">
                                <button type="submit" id="btn_edit" name="btn_edit" class="btn btn-primary mt-3 float-right"><i class="mdi mdi-content-save"></i> Guardar Cambios</button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</div>