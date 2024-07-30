<?php
session_start();
error_reporting(E_ALL); // Enable error reporting for debugging
ini_set('display_errors', 1);

include_once('includes/config.php');

class SiteManager {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function updateSiteDetails($webTitle, $currentPhoto, $imgFile, $imgTmpName, $uname, $uip, $link) {
        if (empty($uname)) {
            echo "<script>alert('User not logged in.');</script>";
            return false;
        }

        $con = $this->db->getConnection();
        $currentPath = "uploadeddata" . "/" . $currentPhoto;
        $extension = substr($imgFile, strlen($imgFile) - 4, strlen($imgFile));
        $allowedExtensions = array(".jpg", "jpeg");

        if (!in_array($extension, $allowedExtensions)) {
            $status = 0;
            $this->logAction($uname, $uip, 'Manage Site', $link, $status);
            echo "<script>alert('Invalid format. Only jpg / jpeg format allowed');</script>";
            return false;
        } else {
            $imgNewFile = md5($imgFile) . $extension;
            if (!move_uploaded_file($imgTmpName, "uploadeddata/" . $imgNewFile)) {
                echo "<script>alert('Failed to upload image');</script>";
                return false;
            }

            $sql = "UPDATE tblsite SET siteLogo=:siteLogo, siteTitle=:siteTitle";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':siteLogo', $imgNewFile);
            $stmt->bindParam(':siteTitle', $webTitle);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                if (file_exists($currentPath)) {
                    unlink($currentPath);
                }
                $status = 1;
                $this->logAction($uname, $uip, 'Manage Site', $link, $status);
                echo "<script>alert('Website Details Updated');</script>";
                return true;
            } else {
                echo "<script>alert('No changes made');</script>";
                return false;
            }
        }
    }

    public function getSiteDetails() {
        $con = $this->db->getConnection();
        $query = $con->query("SELECT * FROM tblsite");
        return $query->fetch(PDO::FETCH_ASSOC);
    }

 

    private function logAction($userName, $userIp, $userAction, $actionUrl, $actionStatus) {
        $con = $this->db->getConnection();
        $sql = "INSERT INTO tbllogs (userName, userIp, userAction, actionUrl, actionStatus) VALUES (:userName, :userIp, :userAction, :actionUrl, :actionStatus)";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':userName', $userName);
        $stmt->bindParam(':userIp', $userIp);
        $stmt->bindParam(':userAction', $userAction);
        $stmt->bindParam(':actionUrl', $actionUrl);
        $stmt->bindParam(':actionStatus', $actionStatus);
        $stmt->execute();
    }
}

// Check session and redirect if not logged in
if (strlen($_SESSION['aid']) == 0) {
    header('location:logout.php');
    exit();
} else {
    if (isset($_POST['submit'])) {
        if (!isset($_SESSION['uname'])) {
            echo "<script>alert('User not logged in.');</script>";
        } else {
            $webTitle = $_POST['webtitle'];
            $currentPhoto = $_POST['currentphoto'];
            $imgFile = $_FILES["image"]["name"];
            $imgTmpName = $_FILES["image"]["tmp_name"];
            $uname = $_SESSION['uname'];
            $uip = $_SERVER['REMOTE_ADDR'];
            $link = $_SERVER['REQUEST_URI'];

            $siteManager = new SiteManager();
            $siteManager->updateSiteDetails($webTitle, $currentPhoto, $imgFile, $imgTmpName, $uname, $uip, $link);
        }
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
    <title> Manage Website</title>
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
                    <h1 class="h3 mb-4 text-gray-800">Manage Website</h1>
                    <form method="post" enctype="multipart/form-data">
                        <?php
                        $siteManager = new SiteManager();
                        $siteDetails = $siteManager->getSiteDetails();
                        ?>
                        <div class="row">
                            <div class="col-lg-10">
                                <div class="card shadow mb-4">
                                    <input type="hidden" name="currentphoto" value="<?php echo $siteDetails['siteLogo']; ?>">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="inputSubject">Current Logo</label>
                                            <img src="uploadeddata/<?php echo $siteDetails['siteLogo']; ?>" width="250">
                                        </div>
                                        <div class="form-group">
                                            <label for="inputSubject">Website Title</label>
                                            <input class="form-control" id="webtitle" name="webtitle" required="true" value="<?php echo $siteDetails['siteTitle']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="inputSubject">Website Logo</label>
                                            <input type="file" name="image" required class="form-control" />
                                            <small style="color:red;">Only jpg / jpeg.</small>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-primary btn-user btn-block" name="submit" id="submit" value="Update">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- New User Form -->
                    <!-- <h1 class="h3 mb-4 text-gray-800">Add New User</h1>
                    <form method="post">
                        <div class="row">
                            <div class="col-lg-10">
                                <div class="card shadow mb-4">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="username">Username</label>
                                            <input class="form-control" id="username" name="username" required="true" placeholder="Enter username">
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input class="form-control" id="email" name="email" required="true" placeholder="Enter email">
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input class="form-control" id="password" name="password" type="password" required="true" placeholder="Enter password">
                                        </div>
                                        <div class="form-group">
                                            <label for="privileges">Privileges</label>
                                            <select class="form-control" id="privileges" name="privileges">
                                                <option value="view">View</option>
                                                <option value="edit">Edit</option>
                                                <option value="delete">Delete</option>
                                                <option value="all">All</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-primary btn-user btn-block" name="addUser" id="addUser" value="Add User">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div> -->
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
