<!DOCTYPE html>
<html lang="en">
<head>
<title>FINGUER - Control párking</title>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="inc/style.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
</head>

<body>

<div class="container-fluid">
    <div class="container-fluid text-center" style="background-color:#02164f;padding-top:35px;padding-bottom:20px;margin-bottom:20px">
    <a href="<?php APP_SERVER;?>/inici">
            <img alt="Finguer" src="inc/img/logo.png"
            width="150" height="70">
        </a>
    </div>

    <div class="container-fluid text-center">
    <div class="row">

        <div class="col-sm">
        <a href="<?php APP_SERVER;?>/inici" class="btn btn-warning menuBtn" role="button" aria-disabled="false">Estat 1: pendent</a>
        </div>

        <div class="col-sm">
        <a href="<?php APP_SERVER;?>/reserves-parking" class="btn btn-danger menuBtn" role="button" aria-disabled="false">Estat 2: al parking</a>
        </div>
        <div class="col-sm">
        <a href="<?php APP_SERVER;?>/reserves-completades" class="btn btn-success menuBtn" role="button" aria-disabled="false">Estat 3: completades</a>
        </div>
    </div>

    <div class="row" style="margin-top:20px;margin-bottom:20px">
        <div class="col-sm">
        <a href="cercador-reserva.php" class="btn btn-secondary menuBtn" role="button" aria-disabled="false">Cercador reserva</a>
        </div>

        <div class="col-sm">
        <a href="calendari-entrades.php" class="btn btn-secondary menuBtn" role="button" aria-disabled="false">Calendari entrades</a>
        </div>

        <div class="col-sm">
        <a href="calendari-sortides.php" class="btn btn-secondary menuBtn" role="button" aria-disabled="false">Calendari sortides</a>
        </div>

        <div class="col-sm">
        <a href="reserves-cercadors.php" class="btn btn-secondary menuBtn" role="button" aria-disabled="false">Buscadors</a>
        </div>

        <div class="col-sm">
        <a href="reserves-anuals-index.php" class="btn btn-secondary menuBtn" role="button" aria-disabled="false">Clients anuals</a>
        </div>

    </div>

    </div>

    <hr>

    <?php