<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox" id="main_view">
                <div class="ibox-title">
                    <h3 class="text-navy"><b><i class="mdi mdi-cogs"></i> Administrar modulos</b></h3>
                </div>
                <div class="ibox-content">
                    <form name="formulario" id="formulario" novalidate="novalidate">
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label class="text-dark font-bold" for="">Nombre Empresa</label>
                                <input name="nombre" id="nombre" class="form-control" placeholder="Ingrese el nombre de la empresa" type="text">
                            </div>
                            <div class="form-group col-lg-6"><label class="text-dark font-bold" for="">Direccion</label>
                                <input name="direccion" id="direccion" class="form-control" placeholder="Ingrese la direccion de la empresa"  type="text">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-actions col-lg-12">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                                <button type="submit" id="btn_edit" name="btn_edit" class="btn btn-primary mt-3 float-right"><i class="mdi mdi-content-save"></i> Guardar Cambios</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>