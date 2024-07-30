<?php 
session_start();
include_once('includes/config.php');
error_reporting(0);

// Team class
class Team {
    private $connection;

    public function __construct($db) {
        $this->connection = $db->getConnection();
    }

    public function createTeam($tname, $tlname, $mobno, $tmember) {
        $query = "INSERT INTO tblteams (teamName, teamLeaderName, teamLeadMobno, teamMembers) VALUES (:teamName, :teamLeaderName, :teamLeadMobno, :teamMembers)";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':teamName', $tname);
        $stmt->bindParam(':teamLeaderName', $tlname);
        $stmt->bindParam(':teamLeadMobno', $mobno);
        $stmt->bindParam(':teamMembers', $tmember);

        return $stmt->execute();
    }

    public function deleteTeam($tid) {
        $query = "DELETE FROM tblteams WHERE id = :id";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':id', $tid);
        return $stmt->execute();
    }
}

if (strlen($_SESSION['aid']) == 0) {
    header('location:logout.php');
} else {
    $db = new Database();
    $team = new Team($db);

    if (isset($_POST['submit'])) {
        $tname = $_POST['teamname'];
        $tlname = $_POST['teamleadname'];
        $mobno = $_POST['mobilenumber'];
        $tmember = $_POST['teammember'];

        if ($team->createTeam($tname, $tlname, $mobno, $tmember)) {
            echo '<script>alert("Team Created.")</script>';
            echo "<script>window.location.href ='manage-teams.php'</script>";
        } else {
            echo '<script>alert("Something Went Wrong. Please try again.")</script>';
        }
    }

    if ($_GET['teamid']) {
        $tid = $_GET['teamid'];
        if ($team->deleteTeam($tid)) {
            echo "<script>alert('Data Deleted');</script>";
            echo "<script>window.location.href='manage-teams.php'</script>";
        } else {
            echo "<script>alert('Unable to delete the team');</script>";
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
    <title>Team Creation and Fire Reporting Management</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
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
                    <h1 class="h3 mb-4 text-gray-800">Fire Safety Team Creation</h1>
                    <form method="post" name="adminprofile">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="card shadow mb-4">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Team Name</label>
                                            <input type="text" class="form-control" name="teamname" value="" required='true'>
                                        </div>
                                        <div class="form-group">
                                            <label>Team Leader name</label>
                                            <input type="text" class="form-control" name="teamleadname" value="" required='true'> 
                                        </div>
                                        <div class="form-group">
                                            <label>Team Lead Contact Number</label>
                                            <input type="text" class="form-control" name="mobilenumber" value="" required='true' maxlength='10'>
                                        </div>
                                        <div class="form-group">
                                            <label>Team Member (Separated by Comma)</label>
                                            <input type="text" class="form-control" name="teammember" value="" required='true'>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-primary btn-user btn-block" name="submit" id="submit" value="Submit">                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Manage Assigned Fire Reporting</h1>
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
                                        $query = $db->getConnection()->prepare("SELECT * FROM tblfirereport ");
                                        $query->execute();
                                        $cnt = 1;
                                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
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
                                        <?php $cnt++; } ?>
                                    </tbody>
                                </table>
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
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="js/demo/datatables-demo.js"></script>
</body>
</html>
<?php } ?>
