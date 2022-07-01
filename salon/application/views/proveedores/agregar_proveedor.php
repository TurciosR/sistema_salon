<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox" id="main_view">
                <div class="ibox-title">
                    <h3 class="text-success"><b><i class="mdi mdi-plus"></i> Agregar Proveedor</b></h3>
                </div>
                <div class="ibox-content">
                    <form id="form_add" novalidate>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group single-line">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" name="nombre" id="nombre" class="form-control mayu"  placeholder="Ingrese un nombre"
                                           required data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="direccion">Direccion</label>
                                    <input type="text" name="direccion" id="direccion" class="form-control mayu"  placeholder="Ingrese una direccion"
                                           required data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <fieldset>
                                        <legend>
                                            Localizacion
                                        </legend>
                                        <div class="form-check abc-radio abc-radio form-check-inline">
                                            <input class="form-check-input" type="radio" id="tipo1" value="1" name="tipo" checked>
                                            <label class="form-check-label" for="tipo1"> Nacional </label>
                                        </div>
                                        <div class="form-check abc-radio form-check-inline">
                                            <input class="form-check-input" type="radio" id="tipo2" value="0" name="tipo">
                                            <label class="form-check-label" for="tipo2"> Internacional </label>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="departamento">Departamento</label>
                                    <select name="departamento" id="departamento" class="form-control select2">
                                        <option value="0">Seleccione un departamento</option>
                                        <?php foreach ($departamentos as $dep): ?>
                                            <option value="<?=$dep->id_departamento?>"><?=$dep->nombre?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="municipio">Municipio</label>
                                    <select name="municipio" id="municipio" class="form-control select2" >
                                        <option value="0">Seleccione un municipio</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="dui">DUI</label>
                                    <input type="text" name="dui" id="dui" class="form-control dui"  placeholder="Ingrese el numero de dui">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="nit">NIT</label>
                                    <input type="text" name="nit" id="nit" class="form-control nit"  placeholder="Ingrese el numero de nit">
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="nrc">NRC</label>
                                    <input type="text" name="nrc" id="nrc" class="form-control numeric"  placeholder="Ingrese un numero de nrc">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="giro">Giro</label>
                                    <select name="giro" id="giro" class="form-control select2" >
                                        <option value="0">Seleccione un giro</option>
                                        <?php foreach ($giro as $gir): ?>
                                            <option value="<?=$gir->id_giro?>"><?=$gir->descripcion?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="categoria">Categoria</label>
                                    <select name="categoria" id="categoria" class="form-control select2" >
                                        <option value="0">Seleccione una categoria</option>
                                        <?php foreach ($categoria_proveedor as $cat_pro): ?>
                                            <option value="<?=$cat_pro->id_categoria?>"><?=$cat_pro->nombre?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="tipo_proveedor">Tipo</label>
                                    <select name="tipo_proveedor" id="tipo_proveedor" class="form-control select2" >
                                        <option value="0">Seleccione un tipo</option>
                                        <?php foreach ($tipo_proveedor as $tip): ?>
                                            <option value="<?=$tip->id_tipo?>"><?=$tip->nombre?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="alert alert-info">
                                    DATOS DE CONTACTO
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="nombre1">Nombre del contacto 1</label>
                                    <input type="text" class="form-control mayu" name="nombre1" id="nombre1" placeholder="Ingrese un nombre">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="telefono1">Telefono 1</label>
                                    <input type="text" class="form-control" name="telefono1" id="telefono1" placeholder="Ingrese un telefono">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="correo1">Correo 1</label>
                                    <input type="text" class="form-control" name="correo1" id="correo1" placeholder="Ingrese un correo">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="comentario1">Comentario 1</label>
                                    <input type="text" class="form-control" name="comentario1" id="comentario1" placeholder="Ingrese un comentario">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="nombre2">Nombre del contacto 2</label>
                                    <input type="text" class="form-control mayu" name="nombre2" id="nombre2" placeholder="Ingrese un nombre">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="telefono2">Telefono 2</label>
                                    <input type="text" class="form-control" name="telefono2" id="telefono2" placeholder="Ingrese un telefono">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="correo2">Correo 2</label>
                                    <input type="text" class="form-control" name="correo2" id="correo2" placeholder="Ingrese un correo">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="comentario2">Comentario 2</label>
                                    <input type="text" class="form-control" name="comentario2" id="comentario2" placeholder="Ingrese un comentario">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-actions col-lg-12">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                                <button type="submit" id="btn_add" name="btn_add" class="btn btn-success float-right"><i class="mdi mdi-content-save"></i>
                                    Guardar Registro
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
                            <h2 class="text-danger blink_me">Espere un momento, procesando su solicitud!</h2>
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