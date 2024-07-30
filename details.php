<?php include_once('includes/config.php');
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>OFMS | Details</title>
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



     
  <div class="row">

                        <div class="col-lg-6">

                            <!-- Basic Card Example -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Personal Information (Reported by)</h6>
                                </div>
                                <div class="card-body">
   
                                <?php
class FireReport {
    private $connection;

    public function __construct($db) {
        $this->connection = $db->getConnection();
    }

    public function getReportById($id) {
        try {
            $query = "SELECT * FROM tblfirereport WHERE id = :id";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getAssignedDetails($id) {
        try {
            $query = "SELECT * FROM tblfirereport 
                      JOIN tblteams ON tblteams.id = tblfirereport.assignTo
                      WHERE tblfirereport.id = :id";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getRequestHistory($id) {
        try {
            $query = "SELECT * FROM tblfiretequesthistory WHERE requestId = :id";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}

$db = new Database();
$fireReport = new FireReport($db);

$rid = isset($_GET['requestid']) ? $_GET['requestid'] : 0;

// Fetch fire report by ID
$report = $fireReport->getReportById($rid);
$assignedDetails = $fireReport->getAssignedDetails($rid);
$requestHistory = $fireReport->getRequestHistory($rid);
?>

<!-- Fire Report Details -->
<?php if ($report): ?>
    <table class="table table-bordered" width="100%" cellspacing="0">
        <tr>
            <th>Full Name</th> 
            <td><?php echo htmlspecialchars($report['fullName']); ?></td>
        </tr>
        <tr>
            <th>Mobile Number</th> 
            <td><?php echo htmlspecialchars($report['mobileNumber']); ?></td>
        </tr>
        <tr>
            <th>Province</th> 
            <td><?php echo htmlspecialchars($report['province']); ?></td>
        </tr>
        <tr>
            <th>District</th> 
            <td><?php echo htmlspecialchars($report['district']); ?></td>
        </tr>
        <tr>
            <th>sector</th> 
            <td><?php echo htmlspecialchars($report['sector']); ?></td>
        </tr>
        <tr>
            <th>Message</th> 
            <td><?php echo htmlspecialchars($report['messgae']); ?></td>
        </tr>
        <tr>
            <th>Reporting Time</th> 
            <td><?php echo htmlspecialchars($report['postingDate']); ?></td>
        </tr>
    </table>
<?php endif; ?>

<!-- Assigned Details -->
<div class="col-lg-6">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Assigned Details</h6>
        </div>
        <div class="card-body">
            <?php if ($assignedDetails): ?>
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <tr>
                        <th>Team Name</th> 
                        <td><?php echo htmlspecialchars($assignedDetails['teamName']); ?></td>
                    </tr>
                    <tr>
                        <th>Team Leader Name</th> 
                        <td><?php echo htmlspecialchars($assignedDetails['teamLeaderName']); ?></td>
                    </tr>
                    <tr>
                        <th>TL Mobile No.</th> 
                        <td><?php echo htmlspecialchars($assignedDetails['teamLeadMobno']); ?></td>
                    </tr>
                    <tr>
                        <th>Team Members</th> 
                        <td><?php echo htmlspecialchars($assignedDetails['teamMembers']); ?></td>
                    </tr>
                    <tr>
                        <th>Assigned Time</th> 
                        <td><?php echo htmlspecialchars($assignedDetails['assignTme']); ?></td>
                    </tr>
                </table>
            <?php else: ?>
                <h4 style="color:red;" align="center">Not Assigned Yet</h4>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Request Tracking History -->
<?php if (!empty($requestHistory)): ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary" align="center">Request Track History</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <tr>
                            <th>Remark</th>
                            <th>Status</th>
                            <th>Remark Date</th>
                        </tr>
                        <?php foreach ($requestHistory as $history): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($history['remark']); ?></td>
                                <td><?php echo htmlspecialchars($history['status']); ?></td>
                                <td><?php echo htmlspecialchars($history['postingDate']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <h4 style="color:red;" align="center">No History Available</h4>
<?php endif; ?>
</div>
                            </div>

                        </div>
                    </div>

                      
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
