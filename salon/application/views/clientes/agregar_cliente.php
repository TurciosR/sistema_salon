<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox" id="main_view">
                <div class="ibox-title">
                  <div class="row">
                    <div class="col-lg-6">
                      <h3 class="text-success font-weight-bold">
                        <i class="mdi mdi-plus"></i> Agregar cliente
                      </h3>
                      <small class="form-text text-muted">
                        <p><i class="mdi mdi-help-circle"></i> Los campos con <span class="text-danger">*</span> son requeridos</p>
                      </small>
                    </div>
                    <div class="col-lg-6">
                      <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="switchTaxpayer">
                        <label class="custom-control-label" for="switchTaxpayer">Contribuyente</label>
                        <small id="checboxHelp" class="form-text text-muted">
                          <i class="mdi mdi-help-circle"></i> De clic en el switch para activar los Campos de Contribuyente
                        </small>
                      </div>
                    </div>
                  </div> <!-- row -->
                </div>
                <div class="ibox-content">
                    <form id="form_add" novalidate>
                        <div class="row">
                            <div class="col-lg-6" hidden>
                                <div class="form-group single-line">
                                    <label for="nombre">Razon social<span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" id="nombre" class="form-control mayu"  placeholder="Ingrese un nombre"
                                           data-parsley-trigger="change">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="form-group single-line">
                                                            <label for="nombre_comercial">Nombre<span class="text-danger">*</span></label>
                                                            <input type="text" name="nombre_comercial" id="nombre_comercial" class="form-control mayu"  placeholder="Ingrese un nombre"
                                                                   required data-parsley-trigger="change">
                                                        </div>
                                                    </div>
                            <div class="col-lg-6">
                                <div class="form-group single-line">
                                    <label for="direccion">Dirección<span class="text-danger"></span></label>
                                    <input type="text" name="direccion" id="direccion" class="form-control"  placeholder="Ingrese una dirección" data-parsley-trigger="change">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="departamento">Departamento<span class="text-danger"></span></label>
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
                                    <label for="municipio">Municipio<span class="text-danger"></span></label>
                                    <select name="municipio" id="municipio" class="form-control select2" >
                                        <option value="0">Seleccione un municipio</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="dui">DUI</label>
                                    <input type="text" name="dui" id="dui" class="form-control dui" placeholder="Ingrese un no. DUI" pattern="[0-9]{8,}-[0-9]{1,}" maxlength="10" data-parsley-maxlength="10">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="nit">NIT<span class="text-danger"></span></label>
                                    <input type="text" name="nit" id="nit" class="form-control nit" placeholder="Ingrese un no. NIT" data-parsley-trigger="change" pattern="[0-9]{4,}-[0-9]{6,}-[0-9]{3,}-[0-9]{1,}" maxlength="17" data-parsley-maxlength="17" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="nrc">NRC <span class="text-danger"></span></label>
                                    <input type="text" name="nrc" id="nrc" class="form-control nrc" placeholder="Ingrese el NRC" data-parsley-trigger="change" pattern="[0-9]{6,}-[0-9]{1,}" maxlength="8" data-parsley-maxlength="8" disabled>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="giro">Giro<span class="text-danger"></span></label>
                                    <select name="giro" id="giro" class="form-control select2" disabled>
                                        <option value="0">Seleccione un giro</option>
                                        <?php foreach ($giro as $gir): ?>
                                            <option value="<?=$gir->id_giro?>"><?=$gir->descripcion?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3" hidden>
                                <div class="form-group single-line">
                                    <label for="categoria">Categoria <span class="text-danger">*</span></label>
                                    <select name="categoria" id="categoria" class="form-control select2" >
                                        <option value="0">Seleccione una categoría</option>
                                        <?php foreach ($categoria_cliente as $cat): ?>
                                            <option value="<?=$cat->id_categoria?>"><?=$cat->nombre?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="tipo">Tipo de cliente <span class="text-danger"></span></label>
                                    <select name="tipo" id="tipo" class="form-control select2" >
                                        <option value="0">Seleccione un tipo de cliente</option>
                                        <?php foreach ($tipo_cliente as $tipo): ?>
                                            <option value="<?=$tipo->id_tipo?>"><?=$tipo->descripcion?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                                                        <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="telefono1">Teléfono 1 <span class="text-danger"></span></label>
                                    <input type="text" name="telefono1" id="telefono1" class="form-control tel"  placeholder="Ingrese el número de teléfono 1" data-parsley-trigger="change" maxlength="9" data-parsley-maxlength="9">
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="telefono2">Teléfono 2</label>
                                    <input type="text" name="telefono2" id="telefono2" class="form-control tel"  placeholder="Ingrese el número de teléfono 2" maxlength="9" data-parsley-maxlength="9">
                                </div>
                            </div>
                            <div class="col-lg-3" hidden>
                                <div class="form-group single-line">
                                    <label for="fax">Fax </label>
                                    <input type="text" name="fax" id="fax" class="form-control tel"  placeholder="Ingrese el número de fax" maxlength="9" data-parsley-maxlength="9">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group single-line">
                                    <label for="correo">Correo electrónico </label>
                                    <input type="email" name="correo" id="correo" class="form-control"  placeholder="Ingrese un correo electrónico" data-parsley-type="email">
                                </div>
                            </div>
                                                        <div class="col-lg-3">
                                                             <div class="form-group single-line">
                                                                     <label for="tipo">Clasificación de cliente <span class="text-danger">*</span></label>
                                                                     <select name="clasifica" id="clasifica" class="form-control select2" >
                                                                             <option value="0">Seleccione clasificaci&oacute;n de cliente</option>
                                                                             <?php foreach ($clasifica_cliente as $clasifica): ?>
                                                                                     <option value="<?=$clasifica->id?>"><?=$clasifica->descripcion?></option>
                                                                             <?php endforeach; ?>
                                                                     </select>
                                                             </div>
                                                     </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3" hidden>
                                <div class="form-group single-line">
                                    <label for="descuento">Porcentaje de descuento<span class="text-danger">*</span></label>
                                    <select name="descuento" id="descuento" class="form-control select2" >
                                        <option value="0">Seleccione un porcentaje</option>
                                        <?php foreach ($porcentajes as $porc): ?>
                                            <option value="<?=$porc->id_porcentaje?>"><?=$porc->descripcion?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3" hidden>
                                <div class="form-group single-line">
                                    <label for="dias_credito">Días crédito</label>
                                    <input type="text" name="dias_credito" id="dias_credito" class="form-control "  placeholder="Ingrese los días crédito" >
                                </div>
                            </div>
                            <div class="col-lg-3" hidden>
                                <div class="form-group single-line">
                                    <label for="tipo_documento">Tipo de documento </label>
                                    <select name="tipo_documento" id="tipo_documento" class="form-control select2" >
                                        <option value="0">Seleccione un tipo de documento</option>
                                        <?php foreach ($tipo_doc as $tip): ?>
                                            <option value="<?=$tip->idtipodoc?>"><?=$tip->nombredoc?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- Collapse with multiple targets for opcional data -->
                        <div class="row">
                        <div class="accordion col-lg-12" id="accordionDataOptional">
                          <div class="card">
                            <div class="card-header" id="headingOne">
                              <h2 class="mb-0">
                                <button class="btn-link btn-for-accordion collapsed text-success h6 font-weight-bold" type="button" data-toggle="collapse" data-target="#collapseOne"
                                  aria-expanded="false" aria-controls="collapseOne">
                                  <i class="mdi mdi-plus"></i>
                                  Llene datos de contacto (Opcional)
                                </button>
                              </h2>
                            </div>

                            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionDataOptional">
                              <div class="card-body">
                                <div class="row"><!-- row -->
                                  <div class="col-lg-6">
                                    <div class="form-group single-line">
                                      <label for="contacto">Nombre de contacto</label>
                                      <input type="text" name="contacto" id="contacto" class="form-control "  placeholder="Ingrese el nombre de contacto" >
                                    </div>
                                  </div>
                                  <div class="col-lg-3">
                                    <div class="form-group single-line">
                                      <label for="contacto_telefono">Teléfono de contacto</label>
                                      <input type="text" name="contacto_telefono" id="contacto_telefono" class="form-control tel"  placeholder="Ingrese el número de teléfono"
                                        maxlength="9" data-parsley-maxlength="9">
                                    </div>
                                  </div>
                                  <div class="col-lg-3">
                                    <div class="form-group single-line">
                                      <label for="contacto_correo">Correo de contacto</label>
                                      <input type="email" name="contacto_correo" id="contacto_correo" class="form-control"  placeholder="Ingrese un correo electrónico" data-parsley-type="email">
                                    </div>
                                  </div>
                                </div> <!-- row close -->
                              </div> <!-- card-body close -->
                            </div> <!-- colappseOne close -->
                          </div> <!-- card close -->
                          <div class="card">
                            <div class="card-header" id="headingTwo">
                              <h2 class="mb-0">
                                <button class="btn-link btn-for-accordion collapsed text-success h6 font-weight-bold" type="button" data-toggle="collapse" data-target="#collapseTwo"
                                  aria-expanded="false" aria-controls="collapseTwo">
                                  <i class="mdi mdi-plus"></i>
                                  Introduzca observaciones (Opcional)
                                </button>
                              </h2>
                            </div>

                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionDataOptional">
                              <div class="card-body">
                                <div class="row"><!-- row -->
                                  <div class="col-lg-12">
                                    <div class="form-group single-line">
                                      <label for="observaciones">Observaciones</label>
                                      <textarea name="observaciones" id="observaciones" class="form-control" rows="3"></textarea>
                                    </div>
                                  </div>
                                </div><!-- row close -->
                              </div><!-- card-body close -->
                            </div><!-- collapseTwo close -->
                          </div><!-- card close -->
                        </div><!-- accordion close -->
                        </div><!-- row close for accordion -->
                        <div class="row" style="margin-top: 25px">
                            <div class="form-actions col-lg-12">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                                <button type="submit" id="btn_add" name="btn_add" class="btn btn-success m-t-n-xs float-right"><i class="mdi mdi-content-save"></i>
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
<div class='modal fade inmodal' id='viewModal' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog modal-md'>
        <div class='modal-content modal-md'>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
