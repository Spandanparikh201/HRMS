<?php
    session_start();
    error_reporting(0);
    include_once("includes/config.php");

    if(isset($_POST['register'])){
        $fname = htmlspecialchars($_POST['firstname']);
        $lname = htmlspecialchars($_POST['lastname']);
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);
        $empid = htmlspecialchars($_POST['empid']);
        $role = htmlspecialchars($_POST['role']); // employee or admin
        
        // Hash password
        $options = ['cost' => 12];
        $hashed_pass = password_hash($password, PASSWORD_BCRYPT, $options);

        // Check if email already exists
        $sql = "SELECT Email FROM users WHERE Email=:email";
        $query = $dbh->prepare($sql);
        $query->bindParam(':email',$email,PDO::PARAM_STR);
        $query->execute();
        
        if($query->rowCount() > 0){
             $error="Email already registered.";
        } else {
            // Insert into Users (for login)
            // Note: DB schema separates users and employees/admins somewhat ambiguously in current setup, 
            // but login checks 'users' table. We will insert into 'users'.
            // Also need to insert into 'employees' if it's an employee role for profile management consistency, 
            // but for authentication 'users' is key.
            // Let's stick to 'users' for Auth and 'employees' for HR data?
            // The existing schema has both. We should probably insert into both to be safe or just 'users'.
            // 'login.php' checks 'users' table. 
            // 'profile.php' joins employees? No, let's check profile.php later.
            // For MVP, insert into 'users'.

            $sql = "INSERT INTO users (FirstName, LastName, UserName, Email, Password) VALUES (:fname, :lname, :uname, :email, :pass)";
            // using Firstname as Username for now or just part of email
            $uname = explode('@', $email)[0];
            
            $query = $dbh->prepare($sql);
            $query->bindParam(':fname',$fname,PDO::PARAM_STR);
            $query->bindParam(':lname',$lname,PDO::PARAM_STR);
            $query->bindParam(':uname',$uname,PDO::PARAM_STR);
            $query->bindParam(':email',$email,PDO::PARAM_STR);
            $query->bindParam(':pass',$hashed_pass,PDO::PARAM_STR);
            
            if($query->execute()){
                // Insert into employees table as well for HRMS consistency
                $sqlE = "INSERT INTO employees (FirstName, LastName, UserName, Email, Password, Employee_Id, Joining_Date) VALUES (:fname, :lname, :uname, :email, :pass, :empid, CURDATE())";
                $queryE = $dbh->prepare($sqlE);
                $queryE->bindParam(':fname',$fname,PDO::PARAM_STR);
                $queryE->bindParam(':lname',$lname,PDO::PARAM_STR);
                $queryE->bindParam(':uname',$uname,PDO::PARAM_STR);
                $queryE->bindParam(':email',$email,PDO::PARAM_STR);
                $queryE->bindParam(':pass',$hashed_pass,PDO::PARAM_STR); // Using same hashed pass
                $queryE->bindParam(':empid',$empid,PDO::PARAM_STR);
                $queryE->execute();

                 echo "<script>alert('Registration Successful');window.location.href='login.php';</script>";
            } else {
                 $error="Something went wrong. Please try again";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <meta name="description" content="Dayflow - HR Management System">
        <title>Register - Dayflow Admin</title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/font-awesome.min.css">
        <link rel="stylesheet" href="assets/css/style.css"><link rel="stylesheet" href="assets/css/dark-theme.css">
    </head>
    <body class="account-page">
        <div class="main-wrapper">
            <div class="account-content">
                <div class="container">
                    <div class="account-logo">
                        <a href="index.php"><img src="assets/img/logo2.png" alt="Company Logo"></a>
                    </div>
                    <div class="account-box">
                        <div class="account-wrapper">
                            <h3 class="account-title">Register</h3>
                            <form method="POST">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input class="form-control" name="firstname" required type="text">
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input class="form-control" name="lastname" required type="text">
                                </div>
                                <div class="form-group">
                                    <label>Employee ID</label>
                                    <input class="form-control" name="empid" required type="text">
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input class="form-control" name="email" required type="email">
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input class="form-control" name="password" required type="password">
                                </div>
                                <div class="form-group">
                                    <label>Role</label>
                                    <select class="form-control" name="role">
                                        <option value="employee">Employee</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                
                                <?php if($error){ echo '<div class="alert alert-danger">'.$error.'</div>'; } ?>
                                
                                <div class="form-group text-center">
                                    <button class="btn btn-primary account-btn" name="register" type="submit">Register</button>
                                </div>
                                <div class="account-footer">
                                    <p>Already have an account? <a href="login.php">Login</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="assets/js/jquery-3.2.1.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/app.js"></script>
    </body>
</html>
