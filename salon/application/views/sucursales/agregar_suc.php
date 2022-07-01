<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox" id="main_view">
                <div class="ibox-title">
                    <h3 class="text-success"><b><i class="mdi mdi-office-building"></i> Agregar datos generales de la sucursal</b></h3>
                </div>
                <div class="ibox-content">
                    <form id="form_add" novalidate>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group single-line">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" name="nombre" id="nombre" class="form-control mayu"  placeholder="Ingrese un nombre" value=""
                                        required data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group single-line">
                                    <label for="direccion">Dirección</label>
                                    <input type="text" name="direccion" id="direccion" class="form-control mayu"  placeholder="Ingrese una dirección" value=""
                                    required data-parsley-trigger="change">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group single-line">
                                    <label for="telefono">Teléfono</label>
                                    <input type="text" name="telefono" id="telefono" class="form-control"  placeholder="Ingrese un teléfono" value=""
                                                                        required data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group single-line">
                                    <label for="correo">Correo electrónico</label>
                                    <input type="text" name="correo" id="correo" class="form-control"  placeholder="Ingrese un correo electrónico" value=""
                                                                        required data-parsley-trigger="change">
                                </div>
                            </div>
                        </div>
                        <div class="row"><!--Para encabezados y pie de documentos a imprimir -->
    <div class="col-lg-12">
        <div class="panel-body">
                <div class="panel-group" id="accordion">
                        <div class="panel panel-default">
                                <div class="panel-heading">
                                        <h3 class="panel-title"><i class="mdi mdi-plus"></i>
                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" class="collapsed" aria-expanded="false">Encabezados y pie de documento: Ticket</a>
                                        </h3>
                                </div>
                                <div id="collapseOne" class="panel-collapse in collapse" style="">
                                        <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-6">

                        <div class="col-lg-12">
                            <div class="form-group single-line">
                                <label for="h1">Encabezado 1</label>
                                <input type="text" name="h1" id="h1" class="form-control mayu"  placeholder="Ingrese una descripción"
                                data-parsley-trigger="change">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group single-line">
                                <label for="h2">Encabezado 2</label>
                                <input type="text" name="h2" id="h2" class="form-control mayu"  placeholder="Ingrese una descripción"
                                data-parsley-trigger="change">
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group single-line">
                                <label for="h3">Encabezado 3</label>
                                <input type="text" name="h3" id="h3" class="form-control mayu"  placeholder="Ingrese una descripción"
                                data-parsley-trigger="change">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group single-line">
                                <label for="h4">Encabezado 4</label>
                                <input type="text" name="h4" id="h4" class="form-control mayu"  placeholder="Ingrese una descripción"
                                data-parsley-trigger="change">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group single-line">
                                <label for="h5">Encabezado 5</label>
                                <input type="text" name="h5" id="h5" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                    data-parsley-trigger="change">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group single-line">
                                <label for="h6">Encabezado 6</label>
                                <input type="text" name="h6" id="h6" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                    data-parsley-trigger="change">
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group single-line">
                                <label for="h7">Encabezado 7</label>
                                <input type="text" name="h7" id="h7" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                    data-parsley-trigger="change">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group single-line">
                                <label for="h8">Encabezado 8</label>
                                <input type="text" name="h8" id="h8" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                    data-parsley-trigger="change">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group single-line">
                                <label for="h9">Encabezado 9</label>
                                <input type="text" name="h9" id="h9" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                    data-parsley-trigger="change">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group single-line">
                                <label for="h10">Encabezado 10</label>
                                <input type="text" name="h10" id="h10" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                    data-parsley-trigger="change">
                            </div>
                        </div>
                    </div><!--encabezados -->
                    <div class="col-lg-6">

                        <div class="col-lg-12">
                            <div class="form-group single-line">
                                <label for="f1">Pie 1</label>
                                <input type="text" name="f1" id="f1" class="form-control mayu"  placeholder="Ingrese una descripción"
                                data-parsley-trigger="change">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group single-line">
                                <label for="f2">Pie 2</label>
                                <input type="text" name="f2" id="f2" class="form-control mayu"  placeholder="Ingrese una descripción"
                                data-parsley-trigger="change">
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group single-line">
                                <label for="f3">Pie 3</label>
                                <input type="text" name="f3" id="f3" class="form-control mayu"  placeholder="Ingrese una descripción"
                                data-parsley-trigger="change">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group single-line">
                                <label for="f4">Pie 4</label>
                                <input type="text" name="f4" id="f4" class="form-control mayu"  placeholder="Ingrese una descripción"
                                data-parsley-trigger="change">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group single-line">
                                <label for="f5">Pie 5</label>
                                <input type="text" name="f5" id="f5" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                    data-parsley-trigger="change">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group single-line">
                                <label for="f6">Pie 6</label>
                                <input type="text" name="f6" id="f6" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                    data-parsley-trigger="change">
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group single-line">
                                <label for="f7">Pie 7</label>
                                <input type="text" name="f7" id="f7" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                    data-parsley-trigger="change">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group single-line">
                                <label for="f8">Pie 8</label>
                                <input type="text" name="f8" id="f8" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                    data-parsley-trigger="change">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group single-line">
                                <label for="f9">Pie 9</label>
                                <input type="text" name="f9" id="f9" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                    data-parsley-trigger="change">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group single-line">
                                <label for="f10">Pie 10</label>
                                <input type="text" name="f10" id="f10" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                    data-parsley-trigger="change">
                            </div>
                        </div>
                        </div><!-- pie-->
                    </div>
                                </div>
                        </div>
                         </div>
                        <div class="panel panel-default">
                                <div class="panel-heading">
                                        <h3 class="panel-title"><i class="mdi mdi-plus"></i>
                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" class="collapsed" aria-expanded="false">Encabezados y pie de documento: Vale</a>
                                        </h3>
                                </div>
                                <div id="collapseTwo" class="panel-collapse in collapse" style="">
                            <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-6">

                            <div class="col-lg-12">
                                <div class="form-group single-line">
                                    <label for="hv1">Encabezado 1</label>
                                    <input type="text" name="hv1" id="hv1" class="form-control mayu"  placeholder="Ingrese una descripción"
                                    data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group single-line">
                                    <label for="hv2">Encabezado 2</label>
                                    <input type="text" name="hv2" id="hv2" class="form-control mayu"  placeholder="Ingrese una descripción"
                                    data-parsley-trigger="change">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group single-line">
                                    <label for="hv3">Encabezado 3</label>
                                    <input type="text" name="hv3" id="hv3" class="form-control mayu"  placeholder="Ingrese una descripción"
                                    data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group single-line">
                                    <label for="hv4">Encabezado 4</label>
                                    <input type="text" name="hv4" id="hv4" class="form-control mayu"  placeholder="Ingrese una descripción"
                                    data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group single-line">
                                    <label for="hv5">Encabezado 5</label>
                                    <input type="text" name="hv5" id="hv5" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                        data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group single-line">
                                    <label for="hv6">Encabezado 6</label>
                                    <input type="text" name="hv6" id="hv6" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                        data-parsley-trigger="change">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group single-line">
                                    <label for="hv7">Encabezado 7</label>
                                    <input type="text" name="hv7" id="hv7" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                        data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group single-line">
                                    <label for="hv8">Encabezado 8</label>
                                    <input type="text" name="hv8" id="hv8" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                        data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group single-line">
                                    <label for="hv9">Encabezado 9</label>
                                    <input type="text" name="hv9" id="hv9" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                        data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group single-line">
                                    <label for="hv10">Encabezado 10</label>
                                    <input type="text" name="hv10" id="hv10" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                        data-parsley-trigger="change">
                                </div>
                            </div>
                        </div><!--encabezados -->
                        <div class="col-lg-6">

                            <div class="col-lg-12">
                                <div class="form-group single-line">
                                    <label for="fv1">Pie 1</label>
                                    <input type="text" name="fv1" id="fv1" class="form-control mayu"  placeholder="Ingrese una descripción"
                                    data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group single-line">
                                    <label for="fv2">Pie 2</label>
                                    <input type="text" name="fv2" id="fv2" class="form-control mayu"  placeholder="Ingrese una descripción"
                                    data-parsley-trigger="change">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group single-line">
                                    <label for="fv3">Pie 3</label>
                                    <input type="text" name="fv3" id="fv3" class="form-control mayu"  placeholder="Ingrese una descripción"
                                    data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group single-line">
                                    <label for="fv4">Pie 4</label>
                                    <input type="text" name="fv4" id="fv4" class="form-control mayu"  placeholder="Ingrese una descripción"
                                    data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group single-line">
                                    <label for="fv5">Pie 5</label>
                                    <input type="text" name="fv5" id="fv5" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                        data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group single-line">
                                    <label for="fv6">Pie 6</label>
                                    <input type="text" name="fv6" id="fv6" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                        data-parsley-trigger="change">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group single-line">
                                    <label for="fv7">Pie 7</label>
                                    <input type="text" name="fv7" id="fv7" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                        data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group single-line">
                                    <label for="fv8">Pie 8</label>
                                    <input type="text" name="fv8" id="fv8" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                        data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group single-line">
                                    <label for="fv9">Pie 9</label>
                                    <input type="text" name="fv9" id="fv9" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                        data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group single-line">
                                    <label for="fv10">Pie 10</label>
                                    <input type="text" name="fv10" id="fv10" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                        data-parsley-trigger="change">
                                </div>
                            </div>
                            </div><!-- pie-->
                        </div>
                         </div>
                     </div>
                        </div>
                        <div class="panel panel-default">
                                <div class="panel-heading">
                                        <h3 class="panel-title"><i class="mdi mdi-plus"></i>
                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree" class="collapsed" aria-expanded="false">Encabezados y pie de documento: Corte de caja</a>
                                        </h3>
                                </div>
                                <div id="collapseThree" class="panel-collapse collapse">
                                    <div class="panel-body">
                                            <div class="row">
                                                <div class="col-lg-6">

                                                <div class="col-lg-12">
                                                    <div class="form-group single-line">
                                                        <label for="hc1">Encabezado 1</label>
                                                        <input type="text" name="hc1" id="hc1" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                        data-parsley-trigger="change">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group single-line">
                                                        <label for="hc2">Encabezado 2</label>
                                                        <input type="text" name="hc2" id="hc2" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                        data-parsley-trigger="change">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="form-group single-line">
                                                        <label for="hc3">Encabezado 3</label>
                                                        <input type="text" name="hc3" id="hc3" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                        data-parsley-trigger="change">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group single-line">
                                                        <label for="hc4">Encabezado 4</label>
                                                        <input type="text" name="hc4" id="hc4" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                        data-parsley-trigger="change">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group single-line">
                                                        <label for="hc5">Encabezado 5</label>
                                                        <input type="text" name="hc5" id="hc5" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                                            data-parsley-trigger="change">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group single-line">
                                                        <label for="hc6">Encabezado 6</label>
                                                        <input type="text" name="hc6" id="hc6" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                                            data-parsley-trigger="change">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="form-group single-line">
                                                        <label for="hc7">Encabezado 7</label>
                                                        <input type="text" name="hc7" id="hc7" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                                            data-parsley-trigger="change">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group single-line">
                                                        <label for="hc8">Encabezado 8</label>
                                                        <input type="text" name="hc8" id="hc8" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                                            data-parsley-trigger="change">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group single-line">
                                                        <label for="hc9">Encabezado 9</label>
                                                        <input type="text" name="hc9" id="hc9" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                                            data-parsley-trigger="change">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group single-line">
                                                        <label for="hc10">Encabezado 10</label>
                                                        <input type="text" name="hc10" id="hc10" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                                            data-parsley-trigger="change">
                                                    </div>
                                                </div>
                                            </div><!--encabezados -->
                                            <div class="col-lg-6">

                                                <div class="col-lg-12">
                                                    <div class="form-group single-line">
                                                        <label for="fc1">Pie 1</label>
                                                        <input type="text" name="fc1" id="fc1" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                        data-parsley-trigger="change">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group single-line">
                                                        <label for="fc2">Pie 2</label>
                                                        <input type="text" name="fc2" id="fc2" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                        data-parsley-trigger="change">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="form-group single-line">
                                                        <label for="fc3">Pie 3</label>
                                                        <input type="text" name="fc3" id="fc3" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                        data-parsley-trigger="change">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group single-line">
                                                        <label for="fc4">Pie 4</label>
                                                        <input type="text" name="fc4" id="fc4" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                        data-parsley-trigger="change">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group single-line">
                                                        <label for="fc5">Pie 5</label>
                                                        <input type="text" name="fc5" id="fc5" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                                            data-parsley-trigger="change">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group single-line">
                                                        <label for="fc6">Pie 6</label>
                                                        <input type="text" name="fc6" id="fc6" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                                            data-parsley-trigger="change">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="form-group single-line">
                                                        <label for="fc7">Pie 7</label>
                                                        <input type="text" name="fc7" id="fc7" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                                            data-parsley-trigger="change">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group single-line">
                                                        <label for="fc8">Pie 8</label>
                                                        <input type="text" name="fc8" id="fc8" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                                            data-parsley-trigger="change">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group single-line">
                                                        <label for="fc9">Pie 9</label>
                                                        <input type="text" name="fc9" id="fc9" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                                            data-parsley-trigger="change">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group single-line">
                                                        <label for="fc10">Pie 10</label>
                                                        <input type="text" name="fc10" id="fc10" class="form-control mayu"  placeholder="Ingrese una descripción"
                                                                                            data-parsley-trigger="change">
                                                    </div>
                                                </div>
                                                </div><!-- pie-->
                                            </div>
                                             </div>


                                </div>
                        </div><!--PANEL CORTES-->
                        <!--PANEL CONFIG RUTAS EQUIPOS IMPRESION -->
                        <div class="panel panel-default">
                         <div class="panel-heading">
                               <h3 class="panel-title"><i class="mdi mdi-plus"></i>
                                   <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour" class="collapsed" aria-expanded="false">RUTAS CONFIG EQUIPOS IMPRESION</a>
                               </h3>
                         </div>
                         <div id="collapseFour" class="panel-collapse in collapse" style="">
                             <div class="panel-body">
                                 <div class="row">
                                     <div class="col-lg-12">
                                         <div class="form-group single-line">
                                             <label for="l1">Directorio Ruta Impresion Sistema Operativo</label>
                                             <input type="text" name="l1" id="l1" class="form-control"  placeholder="Ingrese una descripción" value="localhost/impresion/"
                                             data-parsley-trigger="change">
                                         </div>
                                     </div>
                                     <div class="col-lg-12">
                                         <div class="form-group single-line">
                                             <label for="l2">Ruta Impresor Matricial(Para Facturas)</label>
                                             <input type="text" name="l2" id="l2" class="form-control"  placeholder="Ingrese una descripción" value="//localhost/facturacion"
                                             data-parsley-trigger="change">
                                         </div>
                                     </div>
                                     <div class="col-lg-12">
                                         <div class="form-group single-line">
                                             <label for="l3">Ruta Impresor Ticket(POS)</label>
                                             <input type="text" name="l3" id="l3" class="form-control"  placeholder="Ingrese una descripción" value="//localhost/ticket"
                                             data-parsley-trigger="change">
                                         </div>
                                     </div>
                                     <div class="col-lg-12">
                                         <div class="form-group single-line">
                                             <label for="l4">Ruta Impresor BARCODES)</label>
                                             <input type="text" name="l4" id="l4" class="form-control"  placeholder="Ingrese una descripción" value="//localhost/barcode"
                                             data-parsley-trigger="change">
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                    </div><!--FIN PANEL CONFIG EQUIPOS IMPRESION -->
                </div>
    </div>
</div>
</div><!--fin Para encabezados y pie de documentos a imprimir -->


                        <div class="row">
                            <div class="form-actions col-lg-12">
                                <input type="hidden" name="id_sucursal" id="id_sucursal" value="">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                                <button type="submit" id="btn_add" name="btn_add" class="btn btn-success float-right"><i class="mdi mdi-content-save"></i>
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
