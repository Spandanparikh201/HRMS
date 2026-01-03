<?php 
	session_start();
	error_reporting(0);
	include_once('includes/config.php');
    include_once("includes/functions.php");
	if(strlen($_SESSION['userlogin'])==0){
		header('location:login.php');
	}
    
    // Approval Logic
    if(isset($_GET['action']) && isset($_GET['id'])){
        $lid = intval($_GET['id']);
        $action = $_GET['action'];
        $status = ($action == 'approve') ? 1 : 2; // 1: Approved, 2: Rejected
        
        $sql = "UPDATE leaves SET Status=:status WHERE id=:lid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':status',$status,PDO::PARAM_STR);
        $query->bindParam(':lid',$lid,PDO::PARAM_STR);
        $query->execute();
        echo "<script>alert('Leave Status Updated');window.location.href='leaves-admin.php';</script>";
    }

    // Deletion Logic
    if(isset($_GET['delid'])){
        $rid=intval($_GET['delid']);
        $sql="DELETE from leaves where id=:rid";
        $query=$dbh->prepare($sql);
        $query->bindParam(':rid',$rid,PDO::PARAM_STR);
        $query->execute();
        echo "<script>alert('Employee Leave Has Been Deleted');</script>"; 
        echo "<script>window.location.href ='leaves-admin.php'</script>";
    }
 ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <title>Leave Management - Admin</title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/font-awesome.min.css">
        <link rel="stylesheet" href="assets/css/style.css">
        <link rel="stylesheet" href="assets/css/dark-theme.css">
        
        <!-- Select2 CSS -->
		<link rel="stylesheet" href="assets/css/select2.min.css">
		
		<!-- Datetimepicker CSS -->
		<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
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
						        <h3 class="page-title">Leave Applications (Admin)</h3>
                            </div>
                            <div class="col-auto float-right ml-auto">
								<a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_leave"><i class="fa fa-plus"></i> Add Leave</a>
							</div>
                        </div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table table-striped custom-table mb-0">
									<thead>
										<tr>
											<th>Employee</th>
											<th>From</th>
											<th>To</th>
											<th>Days</th>
											<th>Reason</th>
                                            <th>Status</th>
											<th class="text-right">Actions</th>
										</tr>
									</thead>
									<tbody>
                                    <?php 
                                        $sql = "SELECT * FROM leaves";
                                        $query = $dbh->prepare($sql);
                                        $query->execute();
                                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                                        if($query->rowCount() > 0){
                                            foreach($results as $row){
                                                $statusText = "Pending";
                                                if($row->Status == 1) $statusText = "Approved";
                                                if($row->Status == 2) $statusText = "Rejected";
                                    ?>
										<tr>
											<td><?php echo htmlentities($row->Employee);?></td>
											<td><?php echo htmlentities($row->Starting_At);?></td>
											<td><?php echo htmlentities($row->Ending_On);?></td>
											<td><?php echo htmlentities($row->Days);?></td>
											<td><?php echo htmlentities($row->Reason);?></td>
                                            <td><?php echo $statusText; ?></td>
											<td class="text-right">
                                                <?php if($row->Status == 0) { ?>
                                                    <a href="leaves-admin.php?action=approve&id=<?php echo $row->id; ?>" class="btn btn-success btn-sm">Approve</a>
                                                    <a href="leaves-admin.php?action=reject&id=<?php echo $row->id; ?>" class="btn btn-danger btn-sm">Reject</a>
                                                <?php } else { echo "Action Taken"; } ?>
                                                
                                                <div class="dropdown dropdown-action" style="display:inline-block; margin-left: 5px;">
													<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
													<div class="dropdown-menu dropdown-menu-right">
														<a class="dropdown-item" href="#" data-toggle="modal" data-target="#edit_leave"><i class="fa fa-pencil m-r-5"></i> Edit</a>
														<a class="dropdown-item" href="#" data-toggle="modal" data-target="#delete_approve"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
													</div>
												</div>
											</td>
										</tr>
                                    <?php } } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
                </div>
                
                <!-- Add Leave Modal -->
				<?php include_once 'includes/modals/leave/add_leave.php'; ?>
				<!-- /Add Leave Modal -->
				
				<!-- Edit Leave Modal -->
				<?php include_once 'includes/modals/leave/edit_leave.php'; ?>
				<!-- /Edit Leave Modal -->
				
				<!-- Delete Leave Modal -->
                <?php include_once 'includes/modals/leave/delete_leave.php'; ?>
				<!-- /Delete Leave Modal -->
                
            </div>
        </div>
        <script src="assets/js/jquery-3.2.1.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        
        <!-- Select2 JS -->
		<script src="assets/js/select2.min.js"></script>
		
		<!-- Datetimepicker JS -->
		<script src="assets/js/moment.min.js"></script>
		<script src="assets/js/bootstrap-datetimepicker.min.js"></script>
        
        <script src="assets/js/app.js"></script>
    </body>
</html>
