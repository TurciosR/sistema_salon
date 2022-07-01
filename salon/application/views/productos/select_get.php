<?php

if (isset($colores)) {
    foreach ($colores as $arrColor) {
  ?>
    <option value="<?=$arrColor->id_color; ?>"><?=$arrColor->color; ?></option>
  <?php
  }
} else if (isset($marcas)) {
    foreach ($marcas as $arrMarcas) {
  ?>
    <option value="<?=$arrMarcas->id_marca; ?>"><?=$arrMarcas->nombre; ?></option>
  <?php
  }
} else if (isset($modelos)){
  foreach ($modelos as $arrModelos) {
    ?>
      <option value="<?=$arrModelos->id_modelo; ?>"><?=$arrModelos->nombre; ?></option>
    <?php
    }
}
 ?>
