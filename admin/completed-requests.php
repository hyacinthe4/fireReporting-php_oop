<?php
class Data {
    public $con;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->con = new mysqli('localhost', 'root', '', 'ofrsdb');

        if ($this->con->connect_error) {
            die('Connection failed: ' . $this->con->connect_error);
        }
    }
}

class Session {
    // Start session if not already started
    public static function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Validate session
    public static function validateSession() {
        self::startSession();
        if (empty($_SESSION['aid'])) {
            header('location: logout.php');
            exit;
        }
    }
}

class AdminControl {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Change password method
    public function changePassword($currentPassword, $newPassword) {
        Session::startSession();
        $adminid = $_SESSION['aid'];
        $cpassword = md5($currentPassword);
        $newpassword = md5($newPassword);

        $query = "SELECT ID FROM tbladmin WHERE ID='$adminid' AND Password='$cpassword'";
        $result = $this->db->con->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $uname = $_SESSION['uname'];
            $uip = $_SERVER['REMOTE_ADDR'];
            $link = $_SERVER['REQUEST_URI'];
            $action = 'Password Updation';

            $status = 1;
            $this->db->con->query("INSERT INTO tbllogs(userName, userIp, userAction, actionUrl, actionStatus) VALUES ('$uname', '$uip', '$action', '$link', '$status')");

            $this->db->con->query("UPDATE tbladmin SET Password='$newpassword' WHERE ID='$adminid'");
            echo '<script>alert("Your password successfully changed.")</script>';
        } else {
            $status = 0;
            $this->db->con->query("INSERT INTO tbllogs(userName, userIp, userAction, actionUrl, actionStatus) VALUES ('$uname', '$uip', '$action', '$link', '$status')");
            echo '<script>alert("Your current password is wrong.")</script>';
        }
    }
}

class FireReport {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Fetch fire reports between dates
    public function fetchFireReports($fromDate, $toDate) {
        $query = "SELECT * FROM tblfirereport WHERE status='Completed' AND DATE(postingDate) BETWEEN '$fromDate' AND '$toDate'";
        $result = $this->db->con->query($query);
        return $result;
    }
}

// Usage example
$db = new Data();
Session::validateSession();

$result = null; // Initialize the result variable

// Handle change password form submission
if (isset($_POST['submit'])) {
    $admin = new AdminControl($db);
    $admin->changePassword($_POST['currentpassword'], $_POST['newpassword']);
}

// Handle fire report data fetching
if (isset($_POST['fromdate'], $_POST['todate'])) {
    $fireReport = new FireReport($db);
    $fromDate = $_POST['fromdate'];
    $toDate = $_POST['todate'];
    $result = $fireReport->fetchFireReports($fromDate, $toDate);
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
    <title>Manage Completed Fire Reporting</title>
    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <?php include_once('includes/sidebar.php'); ?>
        <!-- End of Sidebar -->
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <?php include_once('includes/topbar.php'); ?>
                <!-- End of Topbar -->
                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Manage Completed Fire Reporting</h1>
                    </div>
                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Fire Reporting Information</h6>
                        </div>
                        <div class="card-body">
                            <form method="post" action="">
                                <div class="form-group">
                                    <label for="fromdate">From Date:</label>
                                    <input type="date" class="form-control" id="fromdate" name="fromdate" required>
                                </div>
                                <div class="form-group">
                                    <label for="todate">To Date:</label>
                                    <input type="date" class="form-control" id="todate" name="todate" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Fetch Reports</button>
                            </form>
                            <div class="table-responsive mt-4">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Sno.</th>
                                            <th>Name</th>
                                            <th>Mobile Number</th>
                                            <th>province</th>
                                            <th>District</th>
                                            <th>Sector</th>
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
                                            <th>province</th>
                                            <th>District</th>
                                            <th>Sector</th>
                                            <th>Message</th>
                                            <th>Reporting Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php
                                        $cnt = 1;
                                        if ($result && $result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                        ?>
                                                <tr>
                                                    <td><?php echo $cnt; ?></td>
                                                    <td><?php echo $row['fullName']; ?></td>
                                                    <td><?php echo $row['mobileNumber']; ?></td>
                                                    <td><?php echo $row['province']; ?></td>
                                                    <td><?php echo $row['district']; ?></td>
                                                    <td><?php echo $row['sector']; ?></td>
                                                    <td><?php echo $row['messgae']; ?></td>
                                                    <td><?php echo $row['postingDate']; ?></td>
                                                    <td>
                                                        <a href="request-details.php?requestid=<?php echo $row['id']; ?>" class="btn-sm btn-primary">View</a>
                                                    </td>
                                                </tr>
                                        <?php $cnt++;
                                            }
                                        } else {
                                            echo "<tr><td colspan='7'>No records found</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->
            <!-- Footer -->
            <?php include_once('includes/footer.php'); ?>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->
    <!-- Scroll to Top Button-->
    <?php include_once('includes/footer2.php'); ?>
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>
    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>
</body>

</html>
