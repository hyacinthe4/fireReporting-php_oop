<?php
include_once('includes/config.php');

class Session {
    public static function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function validateSession() {
        self::startSession();
        if (empty($_SESSION['aid'])) {
            header('location: logout.php');
            exit;
        }
    }
}

class Team {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function updateTeamDetails($teamid, $tname, $tlname, $mobno, $tmember) {
        $query = "UPDATE tblteams SET teamName=:teamName, teamLeaderName=:teamLeaderName, teamLeadMobno=:teamLeadMobno, teamMembers=:teamMembers WHERE id=:id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(':teamName', $tname);
        $stmt->bindParam(':teamLeaderName', $tlname);
        $stmt->bindParam(':teamLeadMobno', $mobno);
        $stmt->bindParam(':teamMembers', $tmember);
        $stmt->bindParam(':id', $teamid);
        return $stmt->execute();
    }

    public function fetchTeamDetails($teamid) {
        $query = "SELECT * FROM tblteams WHERE id=:id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(':id', $teamid);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Database connection


// Initialize database connection
$db = new Database();
Session::validateSession();

// Handle update form submission
if (isset($_POST['update'])) {
    $team = new Team($db);
    $teamid = $_GET['teamid'];
    $tname = $_POST['teamname'];
    $tlname = $_POST['teamleadname'];
    $mobno = $_POST['mobilenumber'];
    $tmember = $_POST['teammember'];

    if ($team->updateTeamDetails($teamid, $tname, $tlname, $mobno, $tmember)) {
        echo '<script>alert("Team details updated.")</script>';
        echo "<script>window.location.href ='manage-teams.php'</script>";
    } else {
        echo '<script>alert("Something went wrong. Please try again.")</script>';
    }
}

// Fetch team details
$team = new Team($db);
$teamid = $_GET['teamid'];
$row = $team->fetchTeamDetails($teamid);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Update Team Details</title>
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
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
                    <h1 class="h3 mb-4 text-gray-800">Edit <?php echo htmlspecialchars($_GET['tname']); ?>'s Details</h1>
                    <form method="post" name="adminprofile">
                        <?php if ($row): ?>
                            <div class="row">
                                <div class="col-lg-8">
                                    <!-- Basic Card Example -->
                                    <div class="card shadow mb-4">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>Team Name</label>
                                                <input type="text" class="form-control" name="teamname" value="<?php echo htmlspecialchars($row['teamName']); ?>" required='true'>
                                            </div>
                                            <div class="form-group">
                                                <label>Team Leader name</label>
                                                <input type="text" class="form-control" name="teamleadname" value="<?php echo htmlspecialchars($row['teamLeaderName']); ?>" required='true'>
                                            </div>
                                            <div class="form-group">
                                                <label>Team Lead Contact Number</label>
                                                <input type="text" class="form-control" name="mobilenumber" value="<?php echo htmlspecialchars($row['teamLeadMobno']); ?>" required='true' maxlength='10'>
                                            </div>
                                            <div class="form-group">
                                                <label>Team Member (Separated by Comma)</label>
                                                <input type="text" class="form-control" name="teammember" value="<?php echo htmlspecialchars($row['teamMembers']); ?>" required='true'>
                                            </div>
                                            <div class="form-group">
                                                <input type="submit" class="btn btn-primary btn-user btn-block" name="update" id="update" value="Update">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <p>Team details not found.</p>
                        <?php endif; ?>
                    </form>
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
