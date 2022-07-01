<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox" id="main_view">
                <div class="ibox-title">
                    <h3 class="text-navy"><b><i class="mdi mdi-truck"></i> PRODUCTO: <?=$row->nombre?></b> CODIGO DE BARRA: <?=$row->codigo_barra?></h3>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="id_proveedor">Proveedor</label>
                                <div id="scrollable-dropdown-menu">
                                    <input type="text" id="proveedor_search"  class=" form-control" placeholder="Ingrese nombre del proveedor" data-provide="typeahead">
                                    <input type="hidden" name="id_proveedor" id="id_proveedor" value="">
                                    <small class="text-muted">Buscar por nombre de proveedor</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="container-fluid">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th width="80%">Proveedor</th>
                                    <th width="20%" class="text-center">Accion</th>
                                </tr>
                                </thead>
                                <tbody id="table_proveedor">
                                <?php if(isset($proveedores)): ?>
                                    <?php foreach ($proveedores as $pro): ?>
                                        <tr>
                                        <td>
                                            <input type='hidden' class='id_pp' value='<?=$pro->id_pp?>'>
                                            <input type='hidden' class='id_proveedor' value='<?=$pro->id_proveedor?>'>
                                            <input type='hidden' class='nombre' value='<?=$pro->nombre?>'><?=$pro->nombre?></td>
                                        <td class='text-center'>
                                            <a class='btn btn-danger delete_proveedor' data-id="<?=$pro->id_pp?>" style='color: white'><i class='mdi mdi-trash-can'></i></a>
                                        </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
