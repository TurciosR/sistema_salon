<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox" id="main_view">
                <div class="ibox-title">
                    <h3 class="text-success"><b><i class="mdi mdi-plus"></i> Agregar usuario</b></h3>
                </div>
                <div class="ibox-content">
                    <form id="form_add" novalidate>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" name="nombre" id="nombre" class="form-control"  placeholder="Ingrese un nombre"
                                    required data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="usuario">Usuario</label>
                                    <input type="text" name="usuario" id="usuario" class="form-control"  placeholder="Ingrese un usuario"
                                    required data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <fieldset>
                                        <legend>
                                            Tipo usuario
                                        </legend>
                                        <div class="">
                                            <input class="radio_usuario" type="radio" id="tipo_usuario1" value="1" name="tipo_usuario">
                                            <label class="" for="tipo_usuario1"> Administrador </label>
                                        </div>
                                        <div class="">
                                            <input class="radio_usuario" type="radio" id="tipo_usuario2" value="0" name="tipo_usuario" checked>
                                            <label class="" for="tipo_usuario2"> Usuario normal </label>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line cont_rol">
                                    <label for="rol">Rol</label>
                                    <select class="form-control select2" name="rol" id="rol">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($roles as $rol): ?>
                                            <option value="<?=$rol->id_rol?>"><?=$rol->nombre?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>


                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="sucursal">Sucursal</label>
                                    <select class="form-control select2" name="sucursal" id="sucursal">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($sucursales as $sucursal): ?>
                                            <option value="<?=$sucursal->id_sucursal?>"><?=$sucursal->nombre?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="password">Contraseña</label>
                                    <input type="password" name="password" id="password" class="form-control"  placeholder="Ingrese una contraseña"
                                    required data-parsley-trigger="change">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-actions col-lg-12">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                                <button type="submit" id="btn_add" name="btn_add" class="btn btn-success m-t-n-xs pull-right"><i class="mdi mdi-content-save"></i>
                                    Guardar registro
                                </button>
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
