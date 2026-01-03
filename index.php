<?php
	session_start();
	error_reporting(0);
	include('includes/config.php');
	if(strlen($_SESSION['userlogin'])==0){
		header('location:login.php');
	}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <meta name="description" content="Dayflow - HR Management System">
		<meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
        <meta name="author" content="Dayflow - HR Management System">
        <meta name="robots" content="noindex, nofollow">
        <title>Dashboard - Dayflow HR Management</title>
		
		<!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
		
		<!-- Bootstrap CSS -->
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
		
		<!-- Fontawesome CSS -->
        <link rel="stylesheet" href="assets/css/font-awesome.min.css">
		
		<!-- Lineawesome CSS -->
        <link rel="stylesheet" href="assets/css/line-awesome.min.css">
		
		<!-- Chart CSS -->
		<link rel="stylesheet" href="assets/plugins/morris/morris.css">
		
		<!-- Main CSS -->
        <link rel="stylesheet" href="assets/css/style.css">
		
		<!-- Dark Theme CSS -->
        <link rel="stylesheet" href="assets/css/dark-theme.css">
		
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="assets/js/html5shiv.min.js"></script>
			<script src="assets/js/respond.min.js"></script>
		<![endif]-->
    </head>
	
    <body>
		<!-- Main Wrapper -->
        <div class="main-wrapper">
		
			<!-- Header -->
            <?php include_once("includes/header.php"); ?>
			<!-- /Header -->
			
			<!-- Sidebar -->
            <?php include_once("includes/sidebar.php");?>
			<!-- /Sidebar -->
			
			<!-- Page Wrapper -->
            <div class="page-wrapper">
			
				<!-- Page Content -->
                <div class="content container-fluid">
				
					<!-- Page Header -->
					<div class="page-header">
						<div class="row">
							<div class="col-sm-12">
								<h3 class="page-title">Welcome <?php echo htmlentities(ucfirst($_SESSION['userlogin']));?>!</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item active">Dashboard</li>
								</ul>
							</div>
						</div>
					</div>
					<!-- /Page Header -->
				
					<div class="row">
						<div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
							<div class="card dash-widget">
								<div class="card-body">
									<span class="dash-widget-icon"><i class="fa fa-users"></i></span>
									<div class="dash-widget-info">
                                        <?php 
                                            $sql = "SELECT COUNT(*) as count from employees";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $result = $query->fetch(PDO::FETCH_OBJ);
                                            $emp_count = $result->count;
                                        ?>
										<h3><?php echo $emp_count; ?></h3>
										<span>Employees</span>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
							<div class="card dash-widget">
								<div class="card-body">
									<span class="dash-widget-icon"><i class="fa fa-envelope"></i></span>
									<div class="dash-widget-info">
                                        <?php 
                                            $sql = "SELECT COUNT(*) as count from leaves WHERE Status=0";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $result = $query->fetch(PDO::FETCH_OBJ);
                                            $leave_count = $result->count;
                                        ?>
										<h3><?php echo $leave_count; ?></h3>
										<span>Pending Leaves</span>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
							<div class="card dash-widget">
								<div class="card-body">
									<span class="dash-widget-icon"><i class="fa fa-clock-o"></i></span>
									<div class="dash-widget-info">
                                         <?php 
                                            // Today's date
                                            $today = date('Y-m-d');
                                            $sql = "SELECT COUNT(*) as count from attendance WHERE Date=:today AND Status='Present'";
                                            $query = $dbh->prepare($sql);
                                            $query->bindParam(':today',$today,PDO::PARAM_STR);
                                            $query->execute();
                                            $result = $query->fetch(PDO::FETCH_OBJ);
                                            $present_count = $result->count;
                                        ?>
										<h3><?php echo $present_count; ?></h3>
										<span>Present Today</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-12 d-flex">
							<div class="card card-table flex-fill">
								<div class="card-header">
									<h3 class="card-title mb-0">Pending Leave Approvals</h3>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table class="table custom-table mb-0">
											<thead>
												<tr>
													<th>Employee</th>
													<th>Reason</th>
                                                    <th>Days</th>
													<th class="text-right">Action</th>
												</tr>
											</thead>
											<tbody>
                                                <?php 
                                                    $sql = "SELECT * FROM leaves WHERE Status=0 ORDER BY id DESC LIMIT 5";
                                                    $query = $dbh->prepare($sql);
                                                    $query->execute();
                                                    $results=$query->fetchAll(PDO::FETCH_OBJ);
                                                    if($query->rowCount() > 0){
                                                        foreach($results as $row){
                                                ?>
												<tr>
													<td>
														<h2><a href="#"><?php echo htmlentities($row->Employee);?></a></h2>
													</td>
													<td><?php echo htmlentities($row->Reason);?></td>
                                                    <td><?php echo htmlentities($row->Days);?></td>
													<td class="text-right">
                                                        <form method="POST" style="display:inline;">
                                                            <input type="hidden" name="action" value="approve">
                                                            <input type="hidden" name="leave_id" value="<?php echo $row->id; ?>">
                                                            <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                                        </form>
                                                        <form method="POST" style="display:inline;">
                                                            <input type="hidden" name="action" value="reject">
                                                            <input type="hidden" name="leave_id" value="<?php echo $row->id; ?>">
                                                            <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                                        </form>
													</td>
												</tr>
                                                <?php } } else { ?>
                                                    <tr><td colspan="4">No pending leaves.</td></tr>
                                                <?php } ?>
											</tbody>
										</table>
									</div>
								</div>
								<div class="card-footer">
									<a href="leaves-admin.php">View all Leave Requests</a>
								</div>
							</div>
						</div>
					</div>
				
				</div>
				<!-- /Page Content -->

   </div>
			<!-- /Page Wrapper -->
			
        </div>
		<!-- /Main Wrapper -->
		
		<!-- javascript links starts here -->
		<!-- jQuery -->
        <script src="assets/js/jquery-3.2.1.min.js"></script>
		
		<!-- Bootstrap Core JS -->
        <script src="assets/js/popper.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
		
		<!-- Slimscroll JS -->
		<script src="assets/js/jquery.slimscroll.min.js"></script>
		
		<!-- Chart JS -->
		<script src="assets/plugins/morris/morris.min.js"></script>
		<script src="assets/plugins/raphael/raphael.min.js"></script>
		<script src="assets/js/chart.js"></script>
		
		<!-- Custom JS -->
		<script src="assets/js/app.js"></script>
		
		<!-- Theme Toggle JS -->
		<script src="assets/js/theme-toggle.js"></script>
		<!-- javascript links ends here  -->
    </body>
</html>
