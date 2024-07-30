<?php include_once('includes/config.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>OFMS | Reporting</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <!-- Responsive navbar-->
    <?php include_once('includes/header.php') ?>
    <!-- Page Content-->
    <div class="container px-4 px-lg-5">
        <!-- Heading Row-->
        <!-- Content Row-->
        <div class="row gx-4 gx-lg-5">
            <div class="col-md-12 mb-5">
                <div class="card h-100">
                    <div class="card-body">

                        <?php
                        $searchdata = isset($_POST['searchdata']) ? $_POST['searchdata'] : '';

                        class FireReport {
                            private $connection;

                            public function __construct($db) {
                                $this->connection = $db->getConnection();
                            }

                            public function searchReports($searchdata) {
                                try {
                                    $query = "SELECT * FROM tblfirereport WHERE fullName LIKE :searchdata OR mobileNumber LIKE :searchdata";
                                    $stmt = $this->connection->prepare($query);
                                    $searchTerm = "%$searchdata%";
                                    $stmt->bindParam(':searchdata', $searchTerm);
                                    $stmt->execute();
                                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    return [];
                                }
                            }
                        }

                        // Create a database connection
                        $db = new Database();
                        $fireReport = new FireReport($db);

                        // Fetch fire reports
                        $reports = $fireReport->searchReports($searchdata);
                        ?>

                        <h2 class="card-title">Search Result Against '<?php echo htmlspecialchars($searchdata); ?>'</h2>

                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Sno.</th>
                                    <th>Name</th>
                                    <th>Mobile Number</th>
                                    <th>Status</th>
                                    <th>Reporting Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Sno.</th>
                                    <th>Name</th>
                                    <th>Mobile Number</th>
                                    <th>Status</th>
                                    <th>Reporting Time</th>
                                    <th>Action</th>

                                </tr>
                            </tfoot>
                            <tbody>
                                <?php
                                $cnt = 1;
                                foreach ($reports as $row) {
                                ?>
                                    <tr>
                                        <td><?php echo $cnt; ?></td>
                                        <td><?php echo htmlspecialchars($row['fullName']); ?></td>
                                        <td><?php echo htmlspecialchars($row['mobileNumber']); ?></td>
                                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                                        <td><?php echo htmlspecialchars($row['postingDate']); ?></td>
                                        <td>
                                            <a href="details.php?requestid=<?php echo $row['id']; ?>" class="btn-sm btn-primary">View</a>
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
    <!-- Footer-->
    <?php include_once('includes/footer.php') ?>
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="js/scripts.js"></script>
</body>
</html>
