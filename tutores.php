<!DOCTYPE html>
<?php
require 'functions.php';
//Define queienes tienen permiso en este archivo
$permisos = ['Administrador','Profesor'];
permisos($permisos);
//consulta las secciones
$secciones = $conn->prepare("select * from secciones");
$secciones->execute();
$secciones = $secciones->fetchAll();

//consulta de grados
$grados = $conn->prepare("select * from grados");
$grados->execute();
$grados = $grados->fetchAll();
?>
<html>
<head>
<title>Inicio | Registro de Notas</title>
    <meta name="description" content="Registro de Notas del Centro Escolar " />
    <link rel="stylesheet" href="css/style.css" />

</head>
<body>
<div class="header">
        <h1>Registro de Notas  </h1>
        <h3>Usuario:  <?php echo $_SESSION["username"] ?></h3>
</div>
<nav>
    <ul>
        <li><a href="inicio.view.php">Inicio</a> </li>
        <li class="active"><a href="alumnos.view.php">Registro de Alumnos</a> </li>
        <li><a href="listadoalumnos.view.php">Listado de Alumnos</a> </li>
        <li><a href="notas.view.php">Registro de Conducta</a> </li>
        <li><a href="listadonotas.view.php">Consulta de Conducta</a> </li>
        <li><a href="condicionsalud.php">Condicion de Salud</a> </li>
        <li><a href="tutores.php">Tutores</a> </li>
        
        <li class="right"><a href="logout.php">Salir</a> </li>

    </ul>
</nav>

<div class="body">
    <div class="panel">
            <h4>Registro de Alumno</h4>
            <form method="post" class="form" action="procesartutor.php">
                <label>Nombre del alumno</label><br>
                <input type="text" required name="nombre_alumno" maxlength="45">
                <br>
                <h4>Registro de Tutores</h4>
                <label>Nombre del tutor</label><br>
                <input type="text" required name="nombres_tutor" maxlength="45">
                <br>
                <label>Apellidos</label><br>
                <input type="text" required name="apellidos_tutor" maxlength="45">
                <br>
                <label>Numero telefonico</label><br>
                <input type="text" required name="numerotelefonico" maxlength="45">
                <br><br>
                <label>Domicilio</label><br>
                <input type="text" required name="Domicilio" maxlength="45">
                <br>

            

                <br><br>
                <button type="submit" name="insertar">Guardar</button> <button type="reset">Limpiar</button> <a class="btn-link" href="listadoalumnos.view.php">Ver Listado</a>
                <br><br>
                <!--mostrando los mensajes que recibe a traves de los parametros en la url-->
                <?php
                if(isset($_GET['err']))
                    echo '<span class="error">Error al almacenar el registro</span>';
                if(isset($_GET['info']))
                    echo '<span class="success">Registro almacenado correctamente!</span>';
                ?>

            </form>
        <?php
        if(isset($_GET['err']))
            echo '<span class="error">Error al guardar</span>';
        ?>
        </div>
</div>

<footer>
    <p>Derechos reservados &copy; 2020</p>
</footer>

</body>

</html>