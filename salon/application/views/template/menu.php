<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<body class="skin-1">
<?php

 ?>

<div id="wrapper">

    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                        <img alt="image" class="img-fluid" src="<?=base_url(getLogo())?>">
                    </div>
                    <div class="logo-element">
                        OSS
                    </div>
                </li>

                <li class="active">
                    <a href="<?=base_url("")?>"><i class="mdi mdi-home"></i> <span class="nav-label">Inicio</span></a>
                </li>
                <?php if(isset($menus)): ?>
                    <?php foreach ($menus as $menu): ?>
                        <li>
                            <a>
                                <i class="<?= $menu->icono; ?>"></i> <span class='nav-label'><?= $menu->nombre; ?></span> <span class='fa arrow'></span></a>
                            <ul class='nav nav-second-level collapse'>
                                <?php foreach ($modulos[$menu->id_menu] as $modulo):?>
                                    <li><a href="<?= base_url() . $modulo->filename; ?>"><?= $modulo->nombre; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>

            </ul>

        </div>
    </nav>

    <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#">
                        <i class="mdi mdi-menu"></i>
                    </a>
                </div>
                <ul class="nav navbar-top-links navbar-right">
                    <li>
                        <span class="m-r-sm text-muted welcome-message"><?=$usuario?></span>
                    </li>
                    <li>
                        <a href="<?=base_url("logout")?>">
                            <i class="mdi mdi-logout"></i> Cerrar Sesión
                        </a>
                    </li>
                </ul>

            </nav>
        </div>
