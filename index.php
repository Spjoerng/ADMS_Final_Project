<?php 
session_start(); 
include "Procedures.php";
$proc = new Procedures();
$conn = $proc->getConnection();

if (isset($_POST['username']) && isset($_POST['password'])) {

	function validate($data): string{
       $data = trim($data);
	   $data = stripslashes($data);
	   $data = htmlspecialchars($data);
	   return $data;
	}

	$username = validate($_POST['username']);
	$password = validate($_POST['password']);

	if (empty($username)) {
		header("Location: loginpage.php?error=User Name is required");
	    exit();
	}
	else if(empty($password)){
        header("Location: loginpage.php?error=Password is required");
	    exit();
	}
	else{
		$sql = "SELECT * FROM lecturers WHERE username='$username' AND password='$password'";

		$result = mysqli_query($conn, $sql);

		if (mysqli_num_rows($result) === 1) {
			$row = mysqli_fetch_assoc($result);
            if ($row['username'] === $username && $row['password'] === $password) {
            	$_SESSION['username'] = $row['username'];
            	$_SESSION['name'] = $row['name'];
            	$_SESSION['id'] = $row['id'];
            	// Redirect to loading page
                header("Location: loading.php");
		        exit();
            }else{
				header("Location: loginpage.php?error=Incorect User name or password");
		        exit();
			}
		}
		else{
			header("Location: loginpage.php?error=Incorect User name or password");
            exit();
		}
	}
	
}else{
	header("Location: loginpage.php");
	exit();
}