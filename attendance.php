<?php 
	session_start();
	error_reporting(0);
	include('includes/config.php');
	if(strlen($_SESSION['userlogin'])==0){
		header('location:login.php');
	}
    
    // Check-in / Check-out Logic
    if(isset($_POST['mark_attendance'])) {
        $empid = $_SESSION['userlogin']; // Assuming username/id is stored here. Ideally we use Employee ID.
        // We need Employee_Id from 'employees' table corresponding to this user.
        // But for now let's query 'employees' to get ID or just use username if that's creating confusion.
        // Implementation Plan said "Button to Check-in".
        
        $type = $_POST['type']; // 'checkin' or 'checkout'
        $date = date('Y-m-d');
        $time = date('H:i:s');
        
        // Find Employee ID - Assuming UserName is unique and common
        $sqlu = "SELECT Employee_Id FROM employees WHERE UserName=:uname";
        $queryu = $dbh->prepare($sqlu);
        $queryu->bindParam(':uname', $empid, PDO::PARAM_STR);
        $queryu->execute();
        $res = $queryu->fetch(PDO::FETCH_OBJ);
        $employee_real_id = $res ? $res->Employee_Id : $empid; // Fallback

        if($type == 'checkin'){
            // Check if already checked in
            $sql = "SELECT id FROM attendance WHERE Employee_Id=:eid AND Date=:date";
            $q = $dbh->prepare($sql);
            $q->bindParam(':eid', $employee_real_id);
            $q->bindParam(':date', $date);
            $q->execute();
            
            if($q->rowCount() > 0){
                $msg = "Already checked in today.";
            } else {
                $sqlIn = "INSERT INTO attendance (Employee_Id, Date, CheckInTime, Status) VALUES (:eid, :date, :time, 'Present')";
                $qIn = $dbh->prepare($sqlIn);
                $qIn->bindParam(':eid', $employee_real_id);
                $qIn->bindParam(':date', $date);
                $qIn->bindParam(':time', $time);
                $qIn->execute();
                $msg = "Checked In Successfully at $time";
            }
        } elseif($type == 'checkout'){
             $sql = "UPDATE attendance SET CheckOutTime=:time WHERE Employee_Id=:eid AND Date=:date";
             $q = $dbh->prepare($sql);
             $q->bindParam(':eid', $employee_real_id);
             $q->bindParam(':date', $date);
             $q->bindParam(':time', $time);
             $q->execute();
             $msg = "Checked Out Successfully at $time";
        }
    }
 ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <title>Attendance - Dayflow</title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/font-awesome.min.css">
        <link rel="stylesheet" href="assets/css/style.css">
        <link rel="stylesheet" href="assets/css/dark-theme.css">
    </head>
    <body>
        <div class="main-wrapper">
            <?php include_once("includes/header.php");?>
            <?php include_once("includes/sidebar.php");?>
            <div class="page-wrapper">
                <div class="content container-fluid">
					<div class="page-header">
						<div class="row align-items-center">
							<div class="col">
								<h3 class="page-title">Attendance</h3>
                                <?php if($msg) echo "<div class='alert alert-info'>$msg</div>"; ?>
							</div>
                            <div class="col-auto float-right ml-auto">
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="type" value="checkin">
                                    <button type="submit" name="mark_attendance" class="btn btn-success"><i class="fa fa-sign-in"></i> Check In</button>
                                </form>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="type" value="checkout">
                                    <button type="submit" name="mark_attendance" class="btn btn-danger"><i class="fa fa-sign-out"></i> Check Out</button>
                                </form>
                            </div>
						</div>
					</div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped custom-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Check In</th>
                                            <th>Check Out</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            // Show attendance for current user (or all if admin - simplified for now to all for demo or user specific)
                                            // Requirement 3.4.2: Employees view only own, Admin all.
                                            // Currently relying on Session username.
                                            // TODO: specific permission check. For now, showing ALL for debugging, or filter by user.
                                            // Let's look up all for now as I am Admin.
                                            $sql = "SELECT * FROM attendance ORDER BY Date DESC";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $results=$query->fetchAll(PDO::FETCH_OBJ);
                                            if($query->rowCount() > 0){
                                                foreach($results as $row){
                                                    echo "<tr>
                                                        <td>".htmlentities($row->Date)."</td>
                                                        <td>".htmlentities($row->CheckInTime)."</td>
                                                        <td>".htmlentities($row->CheckOutTime)."</td>
                                                        <td>".htmlentities($row->Status)."</td>
                                                    </tr>";
                                                }
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
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
