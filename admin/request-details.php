<?php
session_start();
include_once('includes/config.php');

class FireReport {
    private $conn;
    private $table_name = "tblfirereport";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function assignRequest($requestId, $assignTo) {
        $assignTime = date('Y-m-d H:i:s');
        $query = "UPDATE " . $this->table_name . " SET assignTo = :assignTo, assignTme = :assignTime, status = 'Assigned' WHERE id = :requestId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':assignTo', $assignTo);
        $stmt->bindParam(':assignTime', $assignTime);
        $stmt->bindParam(':requestId', $requestId);
        return $stmt->execute();
    }

    public function updateStatus($requestId, $status, $remark) {
        $this->addHistory($requestId, $status, $remark);
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :requestId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':requestId', $requestId);
        return $stmt->execute();
    }

    public function addHistory($requestId, $status, $remark) {
        $query = "INSERT INTO tblfiretequesthistory(requestId, status, remark) VALUES(:requestId, :status, :remark)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':requestId', $requestId);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':remark', $remark);
        return $stmt->execute();
    }

    public function getReportDetails($requestId) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :requestId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':requestId', $requestId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAssignedDetails($requestId) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  JOIN tblteams ON tblteams.id = " . $this->table_name . ".assignTo
                  WHERE " . $this->table_name . ".id = :requestId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':requestId', $requestId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTrackingHistory($requestId) {
        $query = "SELECT * FROM tblfiretequesthistory WHERE requestId = :requestId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':requestId', $requestId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (strlen($_SESSION['aid']) == 0) {
    header('location:logout.php');
} else {
    $database = new Database();
    $db = $database->getConnection();
    $fireReport = new FireReport($db);

    if (isset($_POST['submit'])) {
        $requestId = $_GET['requestid'];
        $assignTo = $_POST['assignto'];
        if ($fireReport->assignRequest($requestId, $assignTo)) {
            echo '<script>alert("Request has been assigned to the team.")</script>';
            echo "<script>window.location.href ='assigned-requests.php'</script>";
        } else {
            echo '<script>alert("Something Went Wrong. Please try again.")</script>';
        }
    }

    if (isset($_POST['takeaction'])) {
        $requestId = $_GET['requestid'];
        $status = $_POST['status'];
        $remark = $_POST['remark'];
        if ($fireReport->updateStatus($requestId, $status, $remark)) {
            echo '<script>alert("Request has been updated.")</script>';
            echo "<script>window.location.href ='all-requests.php'</script>";
        } else {
            echo '<script>alert("Something Went Wrong. Please try again.")</script>';
        }
    }

    $requestId = $_GET['requestid'];
    $reportDetails = $fireReport->getReportDetails($requestId);
    $assignedDetails = $fireReport->getAssignedDetails($requestId);
    $trackingHistory = $fireReport->getTrackingHistory($requestId);

    // Fetch teams from the database
    $teamsQuery = "SELECT id, teamName, teamLeaderName FROM tblteams";
    $teamsStmt = $db->prepare($teamsQuery);
    $teamsStmt->execute();
    $teams = $teamsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Fire Reporting Details</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style type="text/css">
        label {
            font-size: 16px;
            font-weight: bold;
            color: #000;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include_once('includes/sidebar.php'); ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include_once('includes/topbar.php'); ?>
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Fire Reporting Details</h1>
                    <form method="post" name="adminprofile">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Personal Information (Reported by)</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($reportDetails) { ?>
                                            <table class="table table-bordered" width="100%" cellspacing="0">
                                                <tr>
                                                    <th>Full Name</th>
                                                    <td><?php echo $reportDetails['fullName']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Mobile Number</th>
                                                    <td><?php echo $reportDetails['mobileNumber']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>province</th>
                                                    <td><?php echo $reportDetails['province']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>district</th>
                                                    <td><?php echo $reportDetails['district']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>sector</th>
                                                    <td><?php echo $reportDetails['sector']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Message</th>
                                                    <td><?php echo $reportDetails['messgae']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Reporting Time</th>
                                                    <td><?php echo $reportDetails['postingDate']; ?></td>
                                                </tr>
                                            </table>
                                            <?php if (empty($reportDetails['assignTo'])) { ?>
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#assignto">Assign To</button>
                                                </div>
                                            <?php } elseif (in_array($reportDetails['status'], ['Assigned', 'Team On the Way', 'Fire Relief Work in Progress'])) { ?>
                                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#takeaction">Take Action</button>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Assigned Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($assignedDetails) { ?>
                                            <table class="table table-bordered" width="100%" cellspacing="0">
                                                <tr>
                                                    <th>Team Name</th>
                                                    <td><?php echo $assignedDetails['teamName']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Team Leader Name</th>
                                                    <td><?php echo $assignedDetails['teamLeaderName']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>TL Mobile No.</th>
                                                    <td><?php echo $assignedDetails['teamLeadMobno']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Team Members</th>
                                                    <td><?php echo $assignedDetails['teamMembers']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Assignment Time</th>
                                                    <td><?php echo $assignedDetails['assignTme']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Status</th>
                                                    <td><?php echo $assignedDetails['status']; ?></td>
                                                </tr>
                                            </table>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Tracking History</h6>
                                </div>
                                <div class="card-body">
                                    <?php if ($trackingHistory) { ?>
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <tr>
                                                <th>#</th>
                                                <th>Remark</th>
                                                <th>Status</th>
                                                <th>Time</th>
                                            </tr>
                                            <?php foreach ($trackingHistory as $index => $history) { ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><?php echo $history['remark']; ?></td>
                                                    <td><?php echo $history['status']; ?></td>
                                                    <td><?php echo $history['postingDate']; ?></td>
                                                </tr>
                                            <?php } ?>
                                        </table>
                                    <?php } else { ?>
                                        <p>No tracking history found for this report.</p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assign To Modal -->
                    <div class="modal fade" id="assignto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Assign To</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <select name="assignto" id="assignto-select" class="form-control" required>
                                            <option value="">Select Team</option>
                                            <?php foreach ($teams as $team) { ?>
                                                <option value="<?php echo $team['id']; ?>" data-teamleader="<?php echo $team['teamLeaderName']; ?>"><?php echo $team['teamName']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <div class="form-group mt-3">
                                            <label for="team-leader-name">Team Leader Name</label>
                                            <input type="text" class="form-control" id="team-leader-name" readonly>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="submit" class="btn btn-primary">Assign</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Take Action Modal -->
                    <div class="modal fade" id="takeaction" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Take Action</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="status" required class="form-control">
                                                <option value="Team On the Way">Team On the Way</option>
                                                <option value="Fire Relief Work in Progress">Fire Relief Work in Progress</option>
                                                <option value="Completed">Completed</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Remark</label>
                                            <textarea class="form-control" required name="remark" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="takeaction" class="btn btn-primary">Update</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once('includes/footer.php'); ?>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script>
        document.getElementById('assignto-select').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var teamLeaderName = selectedOption.getAttribute('data-teamleader');
            document.getElementById('team-leader-name').value = teamLeaderName;
        });
    </script>
</body>
</html>
<?php } ?>
