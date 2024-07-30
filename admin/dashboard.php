<?php
session_start();
include_once('includes/config.php');

if (strlen($_SESSION['aid']) == 0) {
    header('location:logout.php');
} else {

    class FireReport {
        private $connection;

        public function __construct($db) {
            $this->connection = $db->getConnection();
        }

        public function getTotalReportings() {
            $query = "SELECT COUNT(id) AS total FROM tblfirereport";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        }

        public function getReportingsByStatus($status) {
            $query = "SELECT COUNT(id) AS total FROM tblfirereport WHERE status = :status";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        }

        public function getNewRequests() {
            $query = "SELECT COUNT(id) AS total FROM tblfirereport WHERE status IS NULL";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        }
    }

    class logs {
        private $connection;

        public function __construct($db) {
            $this->connection = $db->getConnection();
        }

        public function getAdminName($adminId) {
            $query = "SELECT AdminName FROM tbladmin WHERE ID = :id";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':id', $adminId);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['AdminName'];
        }
    }

    class mine {
        private $connection;

        public function __construct($db) {
            $this->connection = $db->getConnection();
        }

        public function getSiteInfo() {
            $query = "SELECT siteLogo, siteTitle FROM tblsite";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    // Create a database connection
    $db = new Database();

    // Fetch site information
    $site = new mine($db);
    $siteInfo = $site->getSiteInfo();
    $logo = $siteInfo['siteLogo'];
    $wtitle = $siteInfo['siteTitle'];

    // Fetch admin information
    $adid = $_SESSION['aid'];
    $admin = new logs($db);
    $adminName = $admin->getAdminName($adid);

    // Fetch fire report statistics
    $fireReport = new FireReport($db);
    $totalReportings = $fireReport->getTotalReportings();
    $requestCompleted = $fireReport->getReportingsByStatus('Completed');
    $assignedRequests = $fireReport->getReportingsByStatus('Assigned');
    $teamOnTheWay = $fireReport->getReportingsByStatus('Team On the Way');
    $workInProgress = $fireReport->getReportingsByStatus('Fire Relief Work in Progress');
    $newRequests = $fireReport->getNewRequests();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Dashboard</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <?php include_once('includes/sidebar.php'); ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include_once('includes/topbar.php'); ?>
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                    </div>
                    <div class="row">
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <a href="new-requests.php">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">New Fire Requests</div>
                                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $newRequests; ?></div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fa fa-fire fa-2x text-gray"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <a href="all-requests.php">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Fire Reportings</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalReportings; ?></div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-fire fa-2x text-red-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <a href="completed-requests.php">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Fire Request Completed</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $requestCompleted; ?></div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-fire fa-2x text-red-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <a href="assigned-requests.php">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Assigned Fire Requests</div>
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col-auto">
                                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $assignedRequests; ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-fire fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <a href="team-ontheway-requests.php">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Team On the Way Requests</div>
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col-auto">
                                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $teamOnTheWay; ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-fire fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <a href="workin-progress-requests.php">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Fire Relief Work in Progress</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $workInProgress; ?></div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fa fa-fire fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include_once('includes/footer.php'); ?>
            </div>
        </div>
    </div>
    <?php include_once('includes/footer2.php'); ?>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="vendor/chart.js/Chart.min.js"></script>
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>
</body>
</html>
<?php } ?>
