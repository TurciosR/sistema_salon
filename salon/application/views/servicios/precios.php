<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox" id="main_view">
                <div class="ibox-title">
                    <h3 class="text-success"><b><i class="mdi mdi-cash"></i> PRODUCTO:</b> <?=$row->nombre?> <b>CODIGO DE BARRA:</b> <?=$row->codigo_barra?></h3>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="form-group">
                                <label for="costo_referencia">Costo de referencia</label>
                                <input type="text" id="costo_referencia"  class="form-control decimal" placeholder="Ingrese el costo de referencia">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <button id="more_price" class="btn btn-primary float-right mt-3"> <i class="mdi mdi-plus"></i> Agregar precio</button>
                            </div>
                        </div>

                    </div>
                    <div class="row mt-3">
                        <div class="container-fluid">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th width="30%">Precio</th>
                                    <th width="10%">Costo</th>
                                    <th width="10%">Costo + IVA</th>
                                    <th width="10%">Precio</th>
                                    <th width="10%">Precio + IVA</th>
                                    <th width="10%">Utilidad %</th>
                                    <th width="10%">Ganancia $</th>
                                    <th width="10%" class="text-center">Acción</th>
                                </tr>
                                </thead>
                                <tbody id="table_proveedor">
                                    <tr>
                                        <td style="text-align: right" class="td_desc">
                                            <input type="text" style="width:350px;" class="form-control desc_td" id="desc_td" name="desc_td" value="Normal" disabled>
                                        </td>
                                        <td style="text-align: left" class="td_costo">
                                            <input type="hidden" class="form-control costo_td" id="costo_td" name="costo_td" value="54">
                                            $ 54.00</td>
                                        <td style="text-align: left" class="td_costo_iva">
                                            <input type="hidden" class="form-control costo_td_iva" id="costo_td_iva" name="costo_td_iva" value="61.02">$ 61.02</td>
                                        <td style="text-align: left" class="td_precio">$ 67.50
                                            <input type="hidden" class="form-control precio_td" id="precio_td" name="precio_td" value="67.50"></td>
                                        <td style="text-align: left" class="td_precio_iva">$ 76.27
                                            <input type="hidden" class="form-control precio_td_iva" id="precio_td_iva" name="precio_td_iva" value="76.27">
                                        </td>
                                        <td style="text-align: left" class="td_porcentaje">25.00%
                                            <input type="hidden" class="form-control porcentaje_td" id="porcentaje_td" name="porcentaje_td" value="25.00">
                                        </td>
                                        <td style="text-align: left" class="td_ganancia">$ 13.50
                                            <input type="hidden" class="form-control ganancia_td" id="ganancia_td" name="ganancia_td" value="13.50">
                                        </td>
                                        <td style="text-align: center">
                                            <button id="delete" type="button" class="btn btn-danger fa delete"><i class="mdi mdi-trash-can"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-actions col-lg-12">
                            <input type="hidden" name="id_producto" id="id_producto" value="<?=$row->id_producto?>">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                            <button type="button" id="btn_proveedor" name="btn_proveedor" class="btn btn-primary float-right"><i class="mdi mdi-content-save"></i>
                                Guardar
                            </button>
                        </div>
                    </div>
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
