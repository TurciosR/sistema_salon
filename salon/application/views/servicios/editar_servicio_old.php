<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox" id="main_view">
                <div class="ibox-title">
                    <h3 class="text-success"><b><i class="mdi mdi-square-edit-outline"></i> Editar Producto</b></h3>
                </div>
                <div class="ibox-content">
                    <form id="form_edit" novalidate>
                        <div class="row">

                            <div class="col-lg-6">
                                <div class="form-group single-line">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" name="nombre" id="nombre" class="form-control mayu"  placeholder="Ingrese un nombre" value="<?=$row->nombre?>"
                                            data-parsley-trigger="change">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group single-line">
                                    <label for="categoria">Categoria</label>
                                    <select name="categoria" id="categoria" class="form-control select2" required data-parsley-trigger="change">
                                        <?php foreach ($categorias as $cat): ?>
                                            <option value="<?=$cat->id_categoria?>"
                                            <?php if($cat->id_categoria==$row->id_categoria) echo "selected"; ?>
                                            ><?=$cat->nombre?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                              <div class="form-group has-info single-line">
                                <label>Costo sin IVA</label>
                                <input type="text" placeholder="Costo" class="form-control ccos decimal" id="ultcosto" value="<?=number_format($row->costo_s_iva,2,".","") ?>">
                                <input type="hidden" id="costo_s_iva" name="costo_s_iva" value="0">
                                <input type="hidden" id="costo_c_iva" name="costo_c_iva" value="0">
                                <input type="hidden" id="precio_sugerido" name="precio_sugerido">
                                <input type="hidden" id="preciosg" name="preciosg">
                              </div>
                            </div>

                              <div class="col-lg-3"  hidden >
                                <div class="form-group single-line">
                                    <fieldset>
                                        <legend>
                                            Cesc
                                        </legend>
                                        <div class="form-check abc-radio abc-radio form-check-inline">
                                            <input class="form-check-input" type="radio" id="cesc1" value="1" name="cesc" <?php if ($row->cesc==1) echo "checked"; ?>>
                                            <label class="form-check-label" for="cesc1"> SI </label>
                                        </div>
                                        <div class="form-check abc-radio form-check-inline">
                                            <input class="form-check-input" type="radio" id="cesc2" value="0" name="cesc" <?php if ($row->cesc==0) echo "checked"; ?>>
                                            <label class="form-check-label" for="cesc2"> NO </label>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group single-line">
                                    <label for="dias_garantia">Dias de Garantia</label>
                                    <input type="text" name="dias_garantia" id="dias_garantia" class="form-control numeric"  placeholder="Ingrese los dias de garantia" value="<?=$row->dias_garantia?>"
                                           required data-parsley-trigger="change">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                          <div class="col-lg-3">
                            <div class="form-group single-line">
                              <label for="dias_garantia">Precio Mínimo</label>
                              <input type="text" name="precio_min" id="precio_min"
                              class="form-control decimal" placeholder="Ingrese Precio Minimo"
                              required data-parsley-trigger="change" value="<?=number_format($row->precio_minimo,2,".","")?>">
                            </div>
                          </div>
                          <div class="col-lg-3 preciosug" >
                            <div class="form-group single-line">
                              <label for="precio_sug">Precio Sugerido</label>
                              <input type="text" name="precio_sug" id="precio_sug"
                              class="form-control decimal" placeholder="Ingrese un precio sugerido"
                              data-parsley-trigger="change" value="<?=number_format($row->precio_sugerido,2,".","") ?>">
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-lg-12">
                            <table class="table table-striperd table-hover table-bordered">
                              <thead>
                                <tr>
                                  <th class="" style="text-align: center">Descripción</th>
                                  <th class="" style="text-align: center">Costo</th>
                                  <th class="" style="text-align: center">IVA</th>
                                  <th class="" style="text-align: center">CESC</th>
                                  <th class="" style="text-align: center">Costo Total</th>
                                  <th class="" style="text-align: center">Ganancia Mínima $</th>
                                  <th class="" style="text-align: center">Ganancia Precio Sug. $</th>
                                </tr>
                              </thead>
                              <tbody id="precios">
                                <?php
                                $costo=$row->costo_s_iva;
                                $imp_iva=$impuestos->iva/100;
                                $imp_cesc=$impuestos->cesc/100;
                                $iva = round($costo * $imp_iva, 2);
                                $cesc = round($costo * 0.00, 2);
                                $ctotal = $costo+$iva+$cesc;

                                $precio_min=$row->precio_minimo;
                                $precio_sug=$row->precio_sugerido;

                                  $detalle = 'DETALLE DE SERVICIO';
                                  //$resultado = round($costo * ($porcentaje / 100) , 2);
                                  $gana1 = round($precio_min - $ctotal,2) ;
                                  if($gana1<0)
                                    $gana1 =0.00;
                                  $gana2 = round( $precio_sug - $ctotal , 2);
                                  if($gana2<0)
                                    $gana2 =0.00;
                                  $lista = "";
                                  $lista .= "<tr>";
                                  $lista .= "<td style='text-align: right' class='td_desc'><input type='text' style='width:350px;' class='form-control desc_td' id='desc_td' name='desc_td' value='".$detalle."' readonly></td>";
                                  $lista .= "<td style='text-align: right' class='td_costo'><input type='hidden' class='form-control costo_td' id='costo_td' name='costo_td' value='".$costo."'>$ ".number_format($costo,2)."</td>";
                                  $lista .= "<td style='text-align: right' class='td_costo_iva'><input type='hidden' class='form-control costo_td_iva' id='costo_td_iva' name='costo_td_iva' value='".$iva."'>$ ".number_format($iva,2)."</td>";
                                  $lista .= "<td style='text-align: right' class='td_precio'><input type='hidden' class='form-control precio_td' id='precio_td' name='precio_td' value='".$cesc."'>$ ".number_format($cesc, 2)."</td>";
                                  $lista .= "<td style='text-align: right' class='td_precio_iva'><input type='hidden' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value='".$ctotal."'>$ ".number_format($ctotal,2)."</td>";
                                  $lista .= "<td style='text-align: right' class='td_porcentaje'><input type='hidden' class='form-control ganancia_min_td' id='ganancia_min_td' name='ganancia_min_td' value='".$gana1."'>$ ".number_format($gana1,2)."</td>";
                                  $lista .= "<td style='text-align: right' class='td_ganancia'><input type='hidden' class='form-control ganancia_td' id='ganancia_td' name='ganancia_td' value='".$gana2."'>$ ".number_format($gana2, 2)."</td>";
                                  $lista .= "</tr>";
                                  echo $lista;
                                ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                        <div class="row">
                            <div class="form-actions col-lg-12">
                                <input type="hidden" name="id_servicio" id="id_servicio" value="<?=$row->id_servicio?>">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                                <button type="submit" id="btn_edit" name="btn_edit" class="btn btn-success m-t-n-xs float-right"><i class="mdi mdi-content-save"></i>
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
<script>
    let preloaded = [];
    let token1 = $("#csrf_token_id").val()
    $.ajax({
        type: "POST",
        url: base_url+"productos/get_images",
        data: {id :$("#id_producto").val(),csrf_test_name:token1},
        dataType: 'json',
        success: function (data) {
            $.each(data, function(index, item) {
                preloaded.push({id:item['id'],src:item['imagen']})
            });
        },
        complete:function () {
            $('.input-images-edit').imageUploader({
                preloaded: preloaded,
                imagesInputName: 'photos',
                preloadedInputName: 'old'
            });
        }
    });


</script>
