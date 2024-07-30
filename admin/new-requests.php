<?php
session_start();
error_reporting(0);

include_once('includes/config.php');
class FireReportManager {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function deleteTeam($teamId) {
        $con = $this->db->getConnection();
        $sql = "DELETE FROM tblteams WHERE id = :id";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':id', $teamId);
        $stmt->execute();
    }

    public function getFireReports() {
        $con = $this->db->getConnection();
        $query = $con->query("SELECT * FROM tblfirereport WHERE status IS NULL");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Check session and redirect if not logged in
if (strlen($_SESSION['aid']) == 0) {
    header('location:logout.php');
} else {
    $fireReportManager = new FireReportManager();

    // Handle team deletion
    if (isset($_GET['teamid'])) {
        $teamId = $_GET['teamid'];
        $fireReportManager->deleteTeam($teamId);
        echo "<script>alert('Data Deleted');</script>";
        echo "<script>window.location.href='manage-teams.php'</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Manage New Fire Reporting</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include_once('includes/sidebar.php'); ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include_once('includes/topbar.php'); ?>
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Manage New Fire Reporting</h1>
                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Fire Reporting Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Sno.</th>
                                            <th>Name</th>
                                            <th>Mobile Number</th>
                                            <th>Location </th>
                                            <th>Message</th>
                                            <th>Reporting Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Sno.</th>
                                            <th>Name</th>
                                            <th>Mobile Number</th>
                                            <th>Location </th>
                                            <th>Message</th>
                                            <th>Reporting Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$fireReportManager = new FireReportManager();
$fireReports = $fireReportManager->getFireReports();
$cnt = 1;
foreach ($fireReports as $report) {
?>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo $report['fullName']; ?></td>
                                            <td><?php echo $report['mobileNumber']; ?></td>
                                            <td><?php echo $report['location']; ?></td>
                                            <td><?php echo $report['messgae']; ?></td>
                                            <td><?php echo $report['postingDate']; ?></td>
                                            <td>
                                                <a href="request-details.php?requestid=<?php echo $report['id']; ?>" class="btn-sm btn-primary">View</a>
                                            </td>
                                        </tr>
<?php
    $cnt++;
}
?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once('includes/footer.php'); ?>
        </div>
    </div>
    <?php include_once('includes/footer2.php'); ?>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="js/demo/datatables-demo.js"></script>
</body>
</html>

