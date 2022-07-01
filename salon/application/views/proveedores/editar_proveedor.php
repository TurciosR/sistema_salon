<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox" id="main_view">
                <div class="ibox-title">
                    <h3 class="text-success"><b><i class="mdi mdi-account-edit"></i> Editar Proveedor</b></h3>
                </div>
                <div class="ibox-content">
                    <form id="form_edit" novalidate>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group single-line">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" name="nombre" id="nombre" class="form-control mayu"  placeholder="Ingrese un nombre" value="<?=$row->nombre?>"
                                           required data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="direccion">Direccion</label>
                                    <input type="text" name="direccion" id="direccion" class="form-control mayu"  placeholder="Ingrese una direccion" value="<?=$row->direccion?>"
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
                                            <input class="form-check-input" type="radio" id="tipo1" value="1" name="tipo" <?php if($row->tipo==1) echo "checked"; ?> >
                                            <label class="form-check-label" for="tipo1"> Nacional </label>
                                        </div>
                                        <div class="form-check abc-radio form-check-inline">
                                            <input class="form-check-input" type="radio" id="tipo2" value="0" name="tipo" <?php if($row->tipo==0) echo "checked"; ?>>
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
                                            <option value="<?=$dep->id_departamento?>"
                                            <?php if($dep->id_departamento==$row->departamento) echo "selected"; ?>
                                            ><?=$dep->nombre?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="municipio">Municipio</label>
                                    <select name="municipio" id="municipio" class="form-control select2" >
                                        <option value="0">Seleccione un municipi</option>
                                        <?php if($row->departamento!=0): ?>
                                            <?php foreach ($municipios as $mun): ?>
                                                <option value="<?=$mun->id_municipio?>"
                                                    <?php if($mun->id_municipio==$row->municipio) echo "selected"; ?>
                                                ><?=$mun->nombre?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="dui">DUI</label>
                                    <input type="text" name="dui" id="dui" class="form-control dui"  placeholder="Ingrese el numero de dui"  value="<?=$row->dui?>">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="nit">NIT</label>
                                    <input type="text" name="nit" id="nit" class="form-control nit"  placeholder="Ingrese el numero de nit"  value="<?=$row->nit?>">
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="nrc">NRC</label>
                                    <input type="text" name="nrc" id="nrc" class="form-control numeric"  placeholder="Ingrese un numero de nrc"  value="<?=$row->nrc?>">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="giro">Giro</label>
                                    <select name="giro" id="giro" class="form-control select2" >
                                        <option value="0">Seleccione un giro</option>
                                        <?php foreach ($giro as $gir): ?>
                                            <option value="<?=$gir->id_giro?>"
                                            <?php if($gir->id_giro==$row->giro) echo "selected"; ?>
                                            ><?=$gir->descripcion?></option>
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
                                            <option value="<?=$cat_pro->id_categoria?>"
                                            <?php if($cat_pro->id_categoria==$row->categoria) echo "selected"; ?>
                                            ><?=$cat_pro->nombre?></option>
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
                                            <option value="<?=$tip->id_tipo?>"
                                            <?php if($tip->id_tipo==$row->tipo_proveedor) echo "selected"; ?>
                                            ><?=$tip->nombre?></option>
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
                                    <input type="text" class="form-control mayu" name="nombre1" id="nombre1" placeholder="Ingrese un nombre"  value="<?=$row->nombre1?>">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="telefono1">Telefono 1</label>
                                    <input type="text" class="form-control" name="telefono1" id="telefono1" placeholder="Ingrese un telefono"  value="<?=$row->telefono1?>">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="correo1">Correo 1</label>
                                    <input type="text" class="form-control" name="correo1" id="correo1" placeholder="Ingrese un correo" value="<?=$row->correo1?>">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="comentario1">Comentario 1</label>
                                    <input type="text" class="form-control" name="comentario1" id="comentario1" placeholder="Ingrese un comentario" value="<?=$row->comentario1?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="nombre2">Nombre del contacto 2</label>
                                    <input type="text" class="form-control mayu" name="nombre2" id="nombre2" placeholder="Ingrese un nombre" value="<?=$row->nombre2?>">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="telefono2">Telefono 2</label>
                                    <input type="text" class="form-control" name="telefono2" id="telefono2" placeholder="Ingrese un telefono" value="<?=$row->telefono2?>">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="correo2">Correo 2</label>
                                    <input type="text" class="form-control" name="correo2" id="correo2" placeholder="Ingrese un correo" value="<?=$row->correo2?>">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="comentario2">Comentario 2</label>
                                    <input type="text" class="form-control" name="comentario2" id="comentario2" placeholder="Ingrese un comentario" value="<?=$row->comentario2?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-actions col-lg-12">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                                <input type="hidden" name="id_proveedor" value="<?=$row->id_proveedor?>">
                                <button type="submit" id="btn_edit" name="btn_edit" class="btn btn-success float-right"><i class="mdi mdi-content-save"></i>
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