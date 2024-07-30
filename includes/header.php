       
<?php
class SiteInfo {
    private $connection;
    private $logo;
    private $title;

    public function __construct($db) {
        $this->connection = $db->getConnection();
        $this->fetchSiteInfo();
    }

    private function fetchSiteInfo() {
        $query = "SELECT siteLogo, siteTitle FROM tblsite";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->logo = $row['siteLogo'];
        $this->title = $row['siteTitle'];
    }

    public function getLogo() {
        return $this->logo;
    }

    public function getTitle() {
        return $this->title;
    }
}


require_once 'config.php';


// Create a database connection
$db = new Database();

// Fetch site information
$siteInfo = new SiteInfo($db);
$logo = $siteInfo->getLogo();
$title = $siteInfo->getTitle();
?>

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container px-5">
                <a class="navbar-brand" href="index.php">   <img src="admin/uploadeddata/<?php echo $logo;?>"  width="50"><?php echo $title;?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php.
                            ">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="reporting.php">Reporting</a></li>
                        <li class="nav-item"><a class="nav-link" href="search.php">View Status</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin/">Admin</a></li>
                    </ul>
                </div>
            </div>
        </nav>