<?php
session_start();
error_reporting(0);
include_once('includes/config.php');
class TeamManager {
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

    public function getTeams() {
        $con = $this->db->getConnection();
        $query = $con->query("SELECT * FROM tblteams");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Check session and redirect if not logged in
if (strlen($_SESSION['aid']) == 0) {
    header('location:logout.php');
} else {
    $teamManager = new TeamManager();

    // Handle team deletion
    if (isset($_GET['teamid'])) {
        $teamId = $_GET['teamid'];
        $teamManager->deleteTeam($teamId);
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
    <title>Manage Teams</title>
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
                        <h1 class="h3 mb-0 text-gray-800">Manage Teams</h1>
                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Team Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Sno.</th>
                                            <th>Team Name</th>
                                            <th>Team Leader Name</th>
                                            <th>TL Mobile Number</th>
                                            <th>Team Members</th>
                                            <th>Regd Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Sno.</th>
                                            <th>Team Name</th>
                                            <th>Team Leader Name</th>
                                            <th>TL Mobile Number</th>
                                            <th>Team Members</th>
                                            <th>Regd Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$teamManager = new TeamManager();
$teams = $teamManager->getTeams();
$cnt = 1;
foreach ($teams as $team) {
?>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo $team['teamName']; ?></td>
                                            <td><?php echo $team['teamLeaderName']; ?></td>
                                            <td><?php echo $team['teamLeadMobno']; ?></td>
                                            <td><?php echo $team['teamMembers']; ?></td>
                                            <td><?php echo $team['postingDate']; ?></td>
                                            <td>
                                                <a href="edit-team.php?teamid=<?php echo $team['id']; ?>&tname=<?php echo $team['teamName']; ?>" class="btn-sm btn-primary">Edit</a>
                                                <a href="manage-teams.php?teamid=<?php echo $team['id']; ?>" onclick="return confirm('Do you really want to delete the team?');" class="btn-sm btn-danger">Delete</a>
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

