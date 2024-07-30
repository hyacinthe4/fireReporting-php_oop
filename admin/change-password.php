<?php
session_start();
require_once('includes/config.php');

class ChangePassword
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function handleChangePassword()
    {
        // Validate session
        if (empty($_SESSION['aid'])) {
            header('location:logout.php');
            exit();
        }

        if (isset($_POST['submit'])) {
            $adminid = $_SESSION['aid'];
            $cpassword = md5($_POST['currentpassword']);
            $newpassword = md5($_POST['newpassword']);

            // Check if current password is correct
            $stmt = $this->pdo->prepare("SELECT ID FROM tbladmin WHERE ID=:adminid AND Password=:currentpassword");
            $stmt->bindParam(':adminid', $adminid, PDO::PARAM_INT);
            $stmt->bindParam(':currentpassword', $cpassword, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $uname = $_SESSION['uname'];
            $uip = $_SERVER['REMOTE_ADDR'];
            $link = $_SERVER['REQUEST_URI'];
            $action = 'Password Updation';
            $status = 0;

            if ($row) {
                // Update password
                $updateStmt = $this->pdo->prepare("UPDATE tbladmin SET Password=:newpassword WHERE ID=:adminid");
                $updateStmt->bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
                $updateStmt->bindParam(':adminid', $adminid, PDO::PARAM_INT);
                $updateStmt->execute();

                // Log action
                $status = 1;
            }

            // Insert log entry
            $logStmt = $this->pdo->prepare("INSERT INTO tbllogs(userName, userIp, userAction, actionUrl, actionStatus) VALUES (:uname, :uip, :action, :link, :status)");
            $logStmt->bindParam(':uname', $uname, PDO::PARAM_STR);
            $logStmt->bindParam(':uip', $uip, PDO::PARAM_STR);
            $logStmt->bindParam(':action', $action, PDO::PARAM_STR);
            $logStmt->bindParam(':link', $link, PDO::PARAM_STR);
            $logStmt->bindParam(':status', $status, PDO::PARAM_INT);
            $logStmt->execute();

            if ($row) {
                echo '<script>alert("Your password has been successfully changed.")</script>';
            } else {
                echo '<script>alert("Your current password is wrong.")</script>';
            }
        }
    }

    public function renderPage()
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <meta name="description" content="">
            <meta name="author" content="">
            <title>Change Password</title>

            <!-- Custom fonts for this template -->
            <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
            <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

            <!-- Custom styles for this template -->
            <link href="css/sb-admin-2.min.css" rel="stylesheet">
            <style type="text/css">
                label {
                    font-size: 16px;
                    font-weight: bold;
                    color: #000;
                }
            </style>
            <script type="text/javascript">
                function checkpass() {
                    if (document.changepassword.newpassword.value != document.changepassword.confirmpassword.value) {
                        alert('New Password and Confirm Password field does not match');
                        document.changepassword.confirmpassword.focus();
                        return false;
                    }
                    return true;
                }
            </script>
        </head>

        <body id="page-top">
            <!-- Page Wrapper -->
            <div id="wrapper">
                <?php include_once('includes/sidebar.php'); ?>
                <div id="content-wrapper" class="d-flex flex-column">
                    <div id="content">
                        <?php include_once('includes/topbar.php'); ?>
                        <div class="container-fluid">
                            <h1 class="h3 mb-4 text-gray-800">Change Password</h1>
                            <form method="post" name="changepassword" onsubmit="return checkpass();">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="card shadow mb-4">
                                            <div class="card-header py-3">
                                                <h6 class="m-0 font-weight-bold text-primary">Change Password</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label>Current Password</label>
                                                    <input type="password" id="currentpassword" name="currentpassword" value="" class="form-control" required="true">
                                                </div>
                                                <div class="form-group">
                                                    <label>New Password</label>
                                                    <input type="password" id="newpassword" name="newpassword" value="" class="form-control" required="true">
                                                </div>
                                                <div class="form-group">
                                                    <label>Confirm Password</label>
                                                    <input type="password" id="confirmpassword" name="confirmpassword" class="form-control" value="" required="true">
                                                </div>
                                                <div class="form-group">
                                                    <input type="submit" class="btn btn-primary btn-user btn-block" name="submit" id="submit">
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
        <?php
    }
}

// Instantiate ChangePassword object with PDO instance
$changePassword = new ChangePassword($pdo); // Assuming $pdo is your PDO connection object from config.php
$changePassword->handleChangePassword();
$changePassword->renderPage();
?>
