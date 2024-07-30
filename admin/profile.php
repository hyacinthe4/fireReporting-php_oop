<?php
session_start();

// Single file containing both Database and AdminManager classes

include_once('includes/config.php');

class AdminManager {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function updateAdminProfile($adminId, $adminName, $mobileNumber, $email) {
        $con = $this->db->getConnection();
        $sql = "UPDATE tbladmin SET AdminName = :adminName, MobileNumber = :mobileNumber, Email = :email WHERE ID = :adminId";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':adminName', $adminName);
        $stmt->bindParam(':mobileNumber', $mobileNumber);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':adminId', $adminId);
        return $stmt->execute();
    }

    public function logAction($userName, $userIp, $userAction, $actionUrl, $actionStatus) {
        $con = $this->db->getConnection();
        $sql = "INSERT INTO tbllogs (userName, userIp, userAction, actionUrl, actionStatus) VALUES (:username, :userIp, :userAction, :actionUrl, :actionStatus)";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':username', $userName);
        $stmt->bindParam(':userIp', $userIp);
        $stmt->bindParam(':userAction', $userAction);
        $stmt->bindParam(':actionUrl', $actionUrl);
        $stmt->bindParam(':actionStatus', $actionStatus);
        return $stmt->execute();
    }

    public function getAdminProfile($adminId) {
        $con = $this->db->getConnection();
        $sql = "SELECT * FROM tbladmin WHERE ID = :adminId";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':adminId', $adminId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Check session and redirect if not logged in
if (strlen($_SESSION['aid']) == 0) {
    header('location:logout.php');
} else {
    // Ensure username is set
    if (!isset($_SESSION['username'])) {
        // Retrieve and set username from the database or login session
        // This is an example, adjust it based on your login implementation
        $adminManager = new AdminManager();
        $adminProfile = $adminManager->getAdminProfile($_SESSION['aid']);
        $_SESSION['username'] = $adminProfile['AdminuserName'];
    }

    $adminManager = new AdminManager();

    if (isset($_POST['update'])) {
        $adminId = $_SESSION['aid'];
        $adminName = $_POST['adminname'];
        $mobileNumber = $_POST['mobilenumber'];
        $email = $_POST['email'];
        $userName = $_SESSION['username'];
        $userIp = $_SERVER['REMOTE_ADDR'];
        $actionUrl = $_SERVER['REQUEST_URI'];
        $userAction = 'Profile Updation';

        if ($adminManager->updateAdminProfile($adminId, $adminName, $mobileNumber, $email)) {
            $adminManager->logAction($userName, $userIp, $userAction, $actionUrl, 1);
            echo '<script>alert("Profile has been updated")</script>';
        } else {
            $adminManager->logAction($userName, $userIp, $userAction, $actionUrl, 0);
            echo '<script>alert("Something Went Wrong. Please try again.")</script>';
        }
    }

    $adminProfile = $adminManager->getAdminProfile($_SESSION['aid']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title> Admin Profile</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
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
    <div id="wrapper">
        <?php include_once('includes/sidebar.php'); ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include_once('includes/topbar.php'); ?>
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Admin Profile</h1>
                    <form method="post" name="adminprofile">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Registration Date: <?php echo $adminProfile['AdminRegdate']; ?></h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Admin Name</label>
                                            <input type="text" class="form-control" name="adminname" value="<?php echo $adminProfile['AdminName']; ?>" required='true'>
                                        </div>
                                        <div class="form-group">
                                            <label>User Name</label>
                                            <input type="text" class="form-control" name="username" value="<?php echo $adminProfile['AdminuserName']; ?>" readonly='true'>
                                        </div>
                                        <div class="form-group">
                                            <label>Email Id</label>
                                            <input type="email" class="form-control" name="email" value="<?php echo $adminProfile['Email']; ?>" required='true'>
                                        </div>
                                        <div class="form-group">
                                            <label>Contact Number</label>
                                            <input type="text" class="form-control" name="mobilenumber" value="<?php echo $adminProfile['MobileNumber']; ?>" required='true' maxlength='10'>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-primary btn-user btn-block" name="update" id="update" value="Update">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
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
</body>
</html>
<?php } ?>
