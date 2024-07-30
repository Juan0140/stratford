<?php
require '../db/db.php';
require 'funciones.php';
session_start();


if($_SERVER['REQUEST_METHOD'] == "POST") {


    if($_POST['user']=="" || $_POST['pass'] == ""){
        sendAlert($conn, 3, "Rellena todos los campos", "../../login.php");
    }

    $user = $_POST["user"];
    $pass = $_POST["pass"];
    $user = $conn->real_escape_string($user);
    $pass = $conn->real_escape_string($pass);


    
    $query = "SELECT * FROM login WHERE user = '{$user}' && admin = '1'";
    $result=$conn->query($query);
    if($result->num_rows == 0) {
        sendAlert($conn,3, "El usuario no existe o no es administrador","../../login.php");
        return;
    }


    while($row = $result->fetch_assoc()) {
        $userDb = $row['user'];
        $passDb = $row['pass'];
        $nameDb = $row['name'];
    }


    if(password_verify($pass, $passDb)) {
        $_SESSION['auth']= true;
        $_SESSION['user']= $userDb;
        $_SESSION['name'] = $nameDb;
        $conn->close();
        header("Location: ../../horariosAdmin.php");
    }else{
        sendAlert($conn,3, "Contraseña incorrecta","../../login.php");
    }

}