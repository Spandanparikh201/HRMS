<?php
	session_start();
	error_reporting(0);
	include('includes/config.php');

    // Helper function for status badge
    function getStatusBadge($status) {
        if ($status == 1) return '<span class="badge badge-success">Active/Approved</span>';
        if ($status == 2) return '<span class="badge badge-danger">Rejected</span>';
        return '<span class="badge badge-warning">Pending</span>';
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>System Verification - Dayflow HRMS</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .card-header { font-weight: bold; font-size: 1.1em; }
        .success-text { color: green; font-weight: bold; }
        .error-text { color: red; font-weight: bold; }
        .section-title { margin-top: 30px; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <?php include_once("includes/header.php");?>
        <?php include_once("includes/sidebar.php");?>
        
        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="page-title">System Activity Verification Dashboard</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Verification</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- System Health Section -->
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="section-title">1. System Health Checks</h4>
                    </div>
                    
                    <!-- DB Check -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">Database Connectivity</div>
                            <div class="card-body">
                                <?php if($dbh): ?>
                                    <p class="success-text"><i class="fa fa-check-circle"></i> Connected to Database</p>
                                <?php else: ?>
                                    <p class="error-text"><i class="fa fa-times-circle"></i> Connection Failed</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- File Check -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">Critical Files Check</div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                <?php 
                                    $files = [
                                        'includes/config.php',
                                        'leaves-admin.php',
                                        'employees.php',
                                        'users.php'
                                    ];
                                    foreach($files as $file){
                                        if(file_exists($file)){
                                            echo "<li><i class='fa fa-check text-success'></i> $file found</li>";
                                        } else {
                                            echo "<li><i class='fa fa-times text-danger'></i> $file NOT FOUND</li>";
                                        }
                                    }
                                    // Check if deleted file is gone
                                    if(!file_exists('leaves-employee.php')){
                                        echo "<li><i class='fa fa-check text-success'></i> leaves-employee.php successfully deleted</li>";
                                    } else {
                                        echo "<li><i class='fa fa-times text-danger'></i> leaves-employee.php STILL EXISTS (Should be deleted)</li>";
                                    }
                                ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Latest Data Section -->
                 <div class="row">
                    <div class="col-md-12">
                        <h4 class="section-title">2. Recent User Activities (Latency Verification)</h4>
                        <p class="text-muted">Use this section to verify that your actions (Add User, Apply Leave, etc.) are being recorded in the database.</p>
                    </div>

                    <!-- Latest Leaves -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                Latest 5 Leaves Applied 
                                <a href="leaves-admin.php" class="btn btn-sm btn-primary float-right">Manage Leaves</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Employee</th>
                                                <th>Reason</th>
                                                <th>Dates</th>
                                                <th>Status</th>
                                                <th>Time Added</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $sql = "SELECT * FROM leaves ORDER BY id DESC LIMIT 5";
                                                $query = $dbh->prepare($sql);
                                                $query->execute();
                                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                if($query->rowCount() > 0):
                                                    foreach($results as $row):
                                            ?>
                                            <tr>
                                                <td><?php echo $row->id; ?></td>
                                                <td><?php echo htmlentities($row->Employee); ?></td>
                                                <td><?php echo htmlentities($row->Reason); ?></td>
                                                <td><?php echo htmlentities($row->Starting_At) . ' to ' . htmlentities($row->Ending_On); ?></td>
                                                <td><?php echo getStatusBadge($row->Status); ?></td>
                                                <td><?php echo htmlentities($row->Time_Added); ?></td>
                                            </tr>
                                            <?php endforeach; else: ?>
                                                <tr><td colspan="6" class="text-center">No leaves found</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Latest Employees -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                Latest 5 Employees Added
                                <a href="employees.php" class="btn btn-sm btn-primary float-right">Manage Employees</a>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                <?php 
                                    $sql = "SELECT FirstName, LastName, Designation, DateTime FROM employees ORDER BY id DESC LIMIT 5";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                    if($query->rowCount() > 0):
                                        foreach($results as $row):
                                ?>
                                    <li class="list-group-item">
                                        <strong><?php echo htmlentities($row->FirstName . ' ' . $row->LastName); ?></strong>
                                        <span class="text-muted list-divider">|</span>
                                        <?php echo htmlentities($row->Designation); ?>
                                        <small class="text-muted float-right"><?php echo htmlentities($row->DateTime); ?></small>
                                    </li>
                                <?php endforeach; else: ?>
                                    <li class="list-group-item text-center">No employees found</li>
                                <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Latest Users -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                Latest 5 Users Added
                                <a href="users.php" class="btn btn-sm btn-primary float-right">Manage Users</a>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                <?php 
                                    $sql = "SELECT UserName, Email, dateTime FROM users ORDER BY id DESC LIMIT 5";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                    if($query->rowCount() > 0):
                                        foreach($results as $row):
                                ?>
                                    <li class="list-group-item">
                                        <strong><?php echo htmlentities($row->UserName); ?></strong>
                                        <br>
                                        <small><?php echo htmlentities($row->Email); ?></small>
                                        <small class="text-muted float-right"><?php echo htmlentities($row->dateTime); ?></small>
                                    </li>
                                <?php endforeach; else: ?>
                                    <li class="list-group-item text-center">No users found</li>
                                <?php endif; ?>
                                </ul>
                            </div>
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
