<?php
    session_start();
    $query=new mysqli('localhost', 'root', '', 'inerior');

    $username= $_POST['username'];
    $password=$_POST['password'];
    $level=$_POST['level'];

    $data=mysqli_query($query, "select * from user where username='$username' and password='$password' and level='$level'")
    or die(mysqli_error($query));

    $cek=mysqli_num_rows($data);

    if($cek>0){
        $_SESSION['username']=$username;
        $_SESSION['password']=$password;
        switch ($level) {
            case 'admin':
                header("Location: admin_dashboard.php");
                break;
            case 'mandor':
                header("Location: mandor_dashboard.php");
                break;
            case 'desainer':
                header("Location: desainer_dashboard.php");
                break;
        }
        
    }else{
        header("location:login.php?pesan=gagal");
    }
?>