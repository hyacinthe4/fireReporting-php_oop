<?php
// Start session and include necessary files
session_start();


// Create PDO instance
$dsn = 'mysql:host=localhost;dbname=ofrsdb';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

class ReportSelection
{
    public $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function renderPage()
    {
        // Check session validity
        if (strlen($_SESSION['aid']) == 0) {
            header('location:logout.php');
            exit(); // Stop further execution
        }

        // Display HTML content
        ?>
        <!DOCTYPE html>
        <html lang="en">
        
        <head>
        
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <meta name="description" content="">
            <meta name="author" content="">
        
            <title>OFRS | B/w Dates Report Date Selection</title>
        
            <!-- Custom fonts for this template-->
            <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
            <link
                href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
                rel="stylesheet">
        
            <!-- Custom styles for this template-->
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
        
            <!-- Page Wrapper -->
            <div id="wrapper">
        
                <?php include_once('includes/sidebar.php'); ?>
        
                <!-- Content Wrapper -->
                <div id="content-wrapper" class="d-flex flex-column">
        
                    <!-- Main Content -->
                    <div id="content">
        
                        <!-- Topbar -->
                        <?php include_once('includes/topbar.php'); ?>
        
                        <!-- Begin Page Content -->
                        <div class="container-fluid">
        
                            <!-- Page Heading -->
                            <h1 class="h3 mb-4 text-gray-800">B/w Dates Report Date Selection</h1>
        
                            <!-- Form for Date Selection -->
                            <form method="post">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <!-- Basic Card Example -->
                                        <div class="card shadow mb-4">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label>From Date</label>
                                                    <input type="date" class="form-control" id="fromdate" name="fromdate" required="true">
                                                </div>
                                                <div class="form-group">
                                                    <label>To Date</label>
                                                    <input type="date" class="form-control" id="todate" name="todate" required="true">
                                                </div>
                                                <div class="form-group">
                                                    <input type="submit" class="btn btn-primary btn-user btn-block" name="submit" value="Submit">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
        
                            <!-- Handle Form Submission -->
                            <?php
                            if (isset($_POST['submit'])) {
                                $fromDate = isset($_POST['fromdate']) ? htmlspecialchars(strip_tags($_POST['fromdate'])) : null;
                                $toDate = isset($_POST['todate']) ? htmlspecialchars(strip_tags($_POST['todate'])) : null;
        
                                // Validate dates if needed
        
                                // Perform database query using PDO prepared statements
                                try {
                                    $stmt = $this->pdo->prepare("SELECT * FROM tblfirereport WHERE postingDate BETWEEN :fromDate AND :toDate");
                                    $stmt->bindParam(':fromDate', $fromDate, PDO::PARAM_STR);
                                    $stmt->bindParam(':toDate', $toDate, PDO::PARAM_STR);
                                    $stmt->execute();
        
                                    // Display results
                                    if ($stmt->rowCount() > 0) {
                                        echo '<div class="card shadow mb-4">
                                                <div class="card-body">
                                                    <div class="table-responsive">
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
                                                            <tbody>';
        
                                        $cnt = 1;
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            echo '<tr>
                                                    <td>' . $cnt . '</td>
                                                    <td>' . $row['name'] . '</td>
                                                    <td>' . $row['mobile'] . '</td>
                                                    <td>' . $row['province'] . '</td>
                                                    <td>' . $row['district'] . '</td>
                                                    <td>' . $row['sector'] . '</td>
                                                    <td>' . $row['message'] . '</td>
                                                    <td>' . $row['reporting_time'] . '</td>
                                                    <td><a href="request-details.php?requestid=' . $row['id'] . '" class="btn-sm btn-primary">View</a></td>
                                                  </tr>';
                                            $cnt++;
                                        }
        
                                        echo '</tbody></table></div></div></div>';
                                    } else {
                                        echo '<div class="alert alert-info">No records found.</div>';
                                    }
                                } catch (PDOException $e) {
                                    die('ERROR: ' . $e->getMessage());
                                }
                            }
                            ?>
        
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
        
        </body>
        
        </html>
        <?php
    }
}

// Usage of ReportSelection class
$reportSelection = new ReportSelection($pdo);
$reportSelection->renderPage();
?>
