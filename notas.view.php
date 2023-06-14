<!DOCTYPE html>
<?php
require 'functions.php';
//arreglo de permisos
$permisos = ['Administrador','Profesor'];
permisos($permisos);

//consulta las materias
$materias = $conn->prepare("select * from materias");
$materias->execute();
$materias = $materias->fetchAll();

//consulta de grados
$grados = $conn->prepare("select * from grados");
$grados->execute();
$grados = $grados->fetchAll();

//consulta las secciones
$secciones = $conn->prepare("select * from secciones");
$secciones->execute();
$secciones = $secciones->fetchAll();
?>
<html>
<head>
<title>Notas | Registro de Conducta</title>
    <meta name="description" content="Registro de Notas del Centro Escolar Profesor Lennin" />
    <link rel="stylesheet" href="css/style.css" />

</head>
<body>
<div class="header">
        <h1>Registro de Notas</h1>
        <h3>Usuario:  <?php echo $_SESSION["username"] ?></h3>
</div>
<nav>
    <ul>
        <li><a href="inicio.view.php">Inicio</a> </li>
        <li><a href="alumnos.view.php">Registro de Alumnos</a> </li>
        <li><a href="listadoalumnos.view.php">Listado de Alumnos</a> </li>
        <li class="active"><a href="notas.view.php">Registro de Conducta</a> </li>
        <li><a href="listadonotas.view.php">Consulta de Conducta</a> </li>
        <li><a href="condicionsalud.php">Condicion de Salud</a> </li>
        <li><a href="tutores.php">Tutores</a> </li>
        <li class="right"><a href="logout.php">Salir</a> </li>

    </ul>
</nav>

<div class="body">
    <div class="panel">
            <h3>Registro y Modificación Conducta</h3>
           <?php
           if(!isset($_GET['revisar'])){
               ?>

            <form method="get" class="form" action="notas.view.php">
                <label>Seleccione el Grado</label><br>
                <select name="grado" required>
                    <?php foreach ($grados as $grado):?>
                        <option value="<?php echo $grado['id'] ?>"><?php echo $grado['nombre'] ?></option>
                    <?php endforeach;?>
                </select>
               

                <br><br>
                <label>Seleccione el grupo</label><br>
                <select name="grupo" required>
                <?php foreach ($secciones as $seccion):?>
                    <option value="<?php echo $seccion['id'] ?>"><?php echo $seccion['nombre'] ?></option>
                <?php endforeach;?>
                <br><br>
                </select>
                <br><br>
                <div class="mb-3">
                    <label for="exampleFormControlTextarea1" class="form-label">
                        Conducta<br><br>
                </select>
            </label>
                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                    </div>

                <br><br>
                <button type="submit" name="revisar" value="1">Ingresar Notas</button> <a class="btn-link" href="listadonotas.view.php">Consultar Conducta</a>
                <br><br>
            </form>
        <?php
           }
        ?>
        <hr>

        <?php
        if(isset($_GET['revisar'])){
            $id_materia = $_GET['materia'];
            $id_grado = $_GET['grado'];
            $id_seccion = $_GET['seccion'];

            //extrayendo el numero de evaluaciones para esa materia seleccionada
            $num_eval = $conn->prepare("select num_evaluaciones from materias where id = ".$id_materia);
            $num_eval->execute();
            $num_eval = $num_eval->fetch();
            $num_eval = $num_eval['num_evaluaciones'];


            //mostrando el cuadro de notas de todos los alumnos del grado seleccionado
            $sqlalumnos = $conn->prepare("select a.id, a.num_lista, a.apellidos, a.nombres, b.nota, avg(b.nota) as promedio, b.observaciones from alumnos as a left join notas as b on a.id = b.id_alumno
 where id_grado = ".$id_grado." and id_seccion = ".$id_seccion." group by a.id");
            $sqlalumnos->execute();
            $alumnos = $sqlalumnos->fetchAll();
            $num_alumnos = $sqlalumnos->rowCount();

            ?>
            <br>
            <a href="notas.view.php"><strong><< Volver</strong></a>
            <br>
            <br>
                <form action="procesarnota.php" method="post">

            <table class="table" cellpadding="0" cellspacing="0">
                <tr>
                    <th>fecha de nacimiento</th><th>Apellidos</th><th>Nombres</th>
                    <?php
                        for($i = 1; $i <= $num_eval; $i++){
                           echo '<th>Nota '.$i .'</th>';
                        }
                    ?>
                    
                    <th>Observaciones</th>
                    <th>Eliminar</th>
                </tr>
                <?php foreach ($alumnos as $index => $alumno) :?>
                    <!-- campos ocultos necesarios para realizar el insert-->
                    <input type="hidden" value="<?php echo $num_alumnos ?>" name="num_alumnos">
                    <input type="hidden" value="<?php echo $alumno['id'] ?>" name="<?php echo 'id_alumno'.$index ?>">
                    <input type="hidden" value="<?php echo $num_eval ?>" name="num_eval">
                     <!-- campos para devolver los parametros en el get y mantener los mismos datos al hacer el header location-->
                    <input type="hidden" value="<?php echo $id_materia ?>" name="id_materia">
                    <input type="hidden" value="<?php echo $id_grado ?>" name="id_grado">
                    <input type="hidden" value="<?php echo $id_seccion ?>" name="id_seccion">
                    <tr>
                        <td align="center"><?php echo $alumno['fecha_nacimiento'] ?></td><td><?php echo $alumno['apellidos'] ?></td>
                        <td><?php echo $alumno['nombres'] ?></td>
                        <?php
                           if(existeNota($alumno['id'],$id_materia,$conn) > 0){
                                //ya tiene notas registradas
                               


                            }else {
                                //extrayendo el numero de evaluaciones para esa materia seleccionada
                                for($i = 0; $i < $num_eval; $i++) {
                                    echo '<td><input type="text" maxlength="5" name="evaluacion' . $i . 'alumno' . $index . '" class="txtnota"></td>';
                                }
                            }

                            echo '<td align="center">'.number_format($alumno['promedio'], 2).'</td>';

                            if(existeNota($alumno['id'],$id_materia,$conn) > 0){
                                echo '<td><input type="text" maxlength="100" value="'.$alumno['observaciones'].'" name="observaciones' . $index . '" class="txtnota"></td>';
                            }else {
                                echo '<td><input type="text" name="observaciones' . $index . '" class="txtnota"></td>';
                            }

                        if(existeNota($alumno['id'],$id_materia,$conn) > 0){
                            echo '<td><a href="notadelete.php?idalumno='.$alumno['id'].'&idmateria='.$id_materia.'">Eliminar</a> </td>';
                        }else{
                            echo '<td>Sin notas</td>';
                        }
                        ?>
                    </tr>
                <?php endforeach;?>
                <tr></tr>
            </table>
                <br>
                <button type="submit" name="insertar">Guardar</button> <button type="reset">Limpiar</button> <a class="btn-link" href="listadonotas.view.php">Consultar Notas</a>
                <br>
            </form>


        <?php }

        ?>
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