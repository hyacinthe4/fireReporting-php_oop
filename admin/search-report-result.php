<?php
session_start();

include_once('includes/config.php');

class FireReport {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function searchReports($searchdata) {
        $query = "SELECT * FROM tblfirereport WHERE fullName LIKE :searchdata OR mobileNumber LIKE :searchdata OR location LIKE :searchdata";
        $stmt = $this->conn->prepare($query);
        $searchdata = "%$searchdata%";
        $stmt->bindParam(':searchdata', $searchdata);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// DB connection
$database = new Database();
$db = $database->getConnection();
$fireReport = new FireReport($db);

// Validating session
if (strlen($_SESSION['aid']) == 0) {
    header('location:logout.php');
} else {
    $searchdata = isset($_POST['serachdata']) ? $_POST['serachdata'] : '';
    $results = $fireReport->searchReports($searchdata);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>OFRS | Search Report</title>
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
                    <h1 class="h3 mb-2 text-gray-800">Search Result Against '<?php echo $searchdata; ?>'</h1>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Search Report Results</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <form name="assignto" method="post">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Sno.</th>
                                                <th>Name</th>
                                                <th>Mobile Number</th>
                                                <th>Location</th>
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
                                                <th>Location</th>
                                                <th>Message</th>
                                                <th>Reporting Time</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <?php
                                            $cnt = 1;
                                            foreach ($results as $row) {
                                            ?>
                                                <tr>
                                                    <td><?php echo $cnt; ?></td>
                                                    <td><?php echo $row['fullName']; ?></td>
                                                    <td><?php echo $row['mobileNumber']; ?></td>
                                                    <td><?php echo $row['location']; ?></td>
                                                    <td><?php echo $row['messgae']; ?></td>
                                                    <td><?php echo $row['postingDate']; ?></td>
                                                    <td>
                                                        <a href="request-details.php?requestid=<?php echo $row['id']; ?>" class="btn-sm btn-primary">View</a>
                                                    </td>
                                                </tr>
                                            <?php
                                                $cnt++;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </form>
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

<?php } ?>
