<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="modal-content">
<div class="modal-header">
  <div class="col-lg-1">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
  </div>
  <div class="col-lg-11">
    <h4 class="modal-title">Impresión Documento</h4>
    <small class="font-bold">Documento Num:<?=$row->id_venta."-".$rowcte->id_cliente?></small>
  </div>
</div>
<div class="modal-body">
  <form id="form_edit_cte" data-parsley-validate>
  <input type="hidden" name="id_vta" id="id_vta" value="<?=$row->id_venta?>">
    <input type="hidden" name="id_client" id="id_client" value="<?=$rowcte->id_cliente?>">
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group single-line">
                <label for="nombre">Razon Social<span class="text-danger">*</span></label>
                <input type="text" name="nombre_cliente" id="nombre_cliente" class="form-control mayu"  placeholder="Ingrese un nombre" value="<?=$rowcte->nombre?>"
                       required data-parsley-trigger="change">
            </div>
        </div>

          <div class="col-lg-6">
              <div class="form-group single-line">
                  <label for="nombre_comercial">Nombre Comercial<span class="text-danger">*</span></label>
                  <input type="text" name="nombre_comercial" id="nombre_comercial mayu" class="form-control" value="<?=$rowcte->nombre_comercial?>"  placeholder="Ingrese un nombre comercial"
                         required data-parsley-trigger="change">
              </div>
          </div>
    </div>
<?php if($row->tipo_doc!=1):?>
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group single-line">
                <label for="direccion">Dirección<span class="text-danger">*</span></label>
                <input type="text" name="direccion" id="direccion" class="form-control"  placeholder="Ingrese una direccion" value="<?=$rowcte->direccion?>"
                       required data-parsley-trigger="change">
            </div>
        </div>

    <div class="col-lg-6">
        <div class="form-group single-line">
            <label for="dui">DUI</label>
            <input type="text" name="dui" id="dui" class="form-control dui"  placeholder="Ingrese una direccion" value="<?=$rowcte->dui?>">
        </div>
    </div>
    </div>
    <div class="row">
    <div class="col-lg-6">
        <div class="form-group single-line">
            <label for="nit">NIT<span class="text-danger">*</span></label>
            <input type="text" name="nit" id="nit" class="form-control nit"  placeholder="Ingrese una direccion" value="<?=$rowcte->nit?>"
                   required data-parsley-trigger="change">
        </div>
    </div>

      <div class="col-lg-6">
          <div class="form-group single-line">
              <label for="nrc">NRC <span class="text-danger">*</span></label>
              <input type="text" name="nrc" id="nrc" class="form-control"  placeholder="Ingrese el nrc" value="<?=$rowcte->nrc?>"
                     required data-parsley-trigger="change">
          </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
          <div class="form-group single-line">
              <label for="giro">Giro<span class="text-danger">*</span></label>
              <select name="giro" id="giro" class="form-control" >
                  <option value="0">Seleccione un giro</option>
                  <?php foreach ($giro as $gir): ?>
                      <option value="<?=$gir->id_giro?>"
                          <?php if($gir->id_giro==$rowcte->giro) echo "selected"; ?>
                      ><?=$gir->descripcion?></option>
                  <?php endforeach; ?>
              </select>
          </div>
      </div>
      </div>
  <?php endif; ?>
    <div class="row">

      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">

      <div class="col-lg-4">
          <div class="form-group single-line">
              <label for="nrc">TOTAL $<span class="text-danger">*</span></label>
              <input type="text" name="totfin" id="totfin" class="form-control"   value="<?=$row->total?>"  readOnly
                     required data-parsley-trigger="change">
          </div>
      </div>

        <div class="col-lg-4">
            <div class="form-group single-line">
                <label for="efectivo">Efectivo<span class="text-danger">*</span></label>
                <input type="text" name="efectivo" id="efectivo" class="form-control decimal"  placeholder="Ingrese cantidad Efectivo"  value=""
                       required data-parsley-trigger="change">
            </div>
        </div>
      <div class="col-lg-4">
          <div class="form-group single-line">
              <label for="cambio">Cambio</label>
              <input type="text" name="cambio" id="cambio" class="form-control cambiodinero"   value="" readOnly >
          </div>
      </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group single-line">
                <label for="efectivo">Numero Documento<span class="text-danger">*</span></label>
                <input type="text" name="numero_doc" id="numero_doc" class="form-control numeric"  placeholder="Ingrese Numero de Documento"  value="<?=$row->correlativo?>"
                       required data-parsley-trigger="change">
            </div>
        </div>

    </div>

</form>
</div>
<div class="modal-footer">
      <button type="submit" form="form_edit_cte" class="btn btn-primary saveprint" id="btn_edit" >Guardar</button>
    <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
  //alert("iniciado..")
  /*
  $(".estado").select2({
     dropdownParent: $('.modal-body')
   });
   /**
* @Desc: Convert str1,str2,str3,str4,str5,....... to fix table columns
* @param string, delimiter, number of column
* @return Table markup fix columns
*/
 /**
function pre_table($str, $del = ',', $numCols = '3'){
        $cnt = 0;
        $arr = explode($del,$str);
        $out_str = "<table width='100%'>";
             $cnt=0;
             for ($cnt=0;$cnt<count($arr); $cnt++)
             {
                  if ($cnt % $numCols == 0)
                  {
                          if ($cnt > 0) $out_str .= "</tr>";
                          $out_str .= "<tr valign='top'>";
                  }
                  $out_str .= "<td>".$arr[$cnt]."</td>";
             }
             while ($cnt % $numCols != 0)
             {
                     $out_str .= "<td>&nbsp;</td>";
                     $cnt++;
             }
        $out_str .= "</tr></table>";
return $out_str;
}
   */

 });
</script>
