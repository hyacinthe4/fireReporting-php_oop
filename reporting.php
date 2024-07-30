<?php
include_once('includes/config.php');

class FireReport {
    private $connection;

    public function __construct($db) {
        $this->connection = $db->getConnection();
    }

    public function submitReport($fullname, $mobileNumber, $message, $province, $district, $sector) {
        try {
            $query = "INSERT INTO tblfirereport (fullName, mobileNumber, messgae, province, district, sector) VALUES (:fullname, :mobileNumber, :message, :province, :district, :sector)";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':fullname', $fullname);
            $stmt->bindParam(':mobileNumber', $mobileNumber);
            $stmt->bindParam(':message', $message);
            $stmt->bindParam(':province', $province);
            $stmt->bindParam(':district', $district);
            $stmt->bindParam(':sector', $sector);
            $stmt->execute();
            
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}

// Defining classes used
$db = new Database();
$fireReport = new FireReport($db);

if (isset($_POST['submit'])) {
    $fname = $_POST['fullname'];
    $mno = $_POST['mobileNumber'];
    $message = $_POST['message'];
    $province = $_POST['province'];
    $district = $_POST['district'];
    $sector = $_POST['sector'];

    if ($fireReport->submitReport($fname, $mno, $message, $province, $district, $sector)) {
        echo "<script>alert('Reporting successful');</script>";
        echo "<script>window.location.href ='reporting.php'</script>";
    } else {
        echo "<script>alert('Something went wrong. Please try again.');</script>";
        echo "<script>window.location.href ='reporting.php'</script>";
    }
}
?>

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
    <script>
        function updateDistricts() {
            var province = document.getElementById("province").value;
            var districts = document.getElementById("district");
            var sectors = document.getElementById("sector");

            districts.innerHTML = "<option value=''>Select District</option>";
            sectors.innerHTML = "<option value=''>Select Sector</option>";

            var provinceOptions = {
                "Western Province": {
                    "Karongi District": ["Mubuga", "Bwishura"],
                    "Rusizi District": ["Kamembe", "Kagamba"]
                },
                "Eastern Province": {
                    "Rwamagana District": ["Murambi", "Mukura"],
                    "Nyagatare District": ["Mimuri", "Rwemasha"]
                },
                "Southern Province": {
                    "Muhanga District": ["Mukarange", "Muhura"],
                    "Huye District": ["Tyazo", "Pfunda"]
                },
                "Northern Province": {
                    "Gicumbi District": ["Byumba", "Gaseke"],
                    "Musanze District": ["Muhoza", "Nyakinama"]
                }
            };

            if (province in provinceOptions) {
                for (var district in provinceOptions[province]) {
                    districts.options[districts.options.length] = new Option(district, district);
                }
            }

            districts.onchange = function () {
                sectors.innerHTML = "<option value=''>Select Sector</option>";
                var selectedDistrict = this.value;
                if (selectedDistrict in provinceOptions[province]) {
                    var sectorOptions = provinceOptions[province][selectedDistrict];
                    for (var i = 0; i < sectorOptions.length; i++) {
                        sectors.options[sectors.options.length] = new Option(sectorOptions[i], sectorOptions[i]);
                    }
                }
            }
        }

        function validateMobileNumber(input) {
            const pattern = /^(078|072|073)\d{7}$/;
            if (!pattern.test(input.value)) {
                input.setCustomValidity("Please enter a valid mobile number starting with 078, 072, or 073, and with a total of 10 digits.");
            } else {
                input.setCustomValidity("");
            }
        }
    </script>
</head>
<body>
    <!-- Responsive navbar-->
    <?php include_once('includes/header.php') ?>
    <!-- Page Content-->
    <div class="container px-4 px-lg-5">
        <!-- Heading Row-->
        <div class="row gx-4 gx-lg-5 align-items-center my-5">
            <div class="col-lg-12"><img class="img-fluid rounded mb-4 mb-lg-0" src="assets/6094899.jpg" alt="..." /></div>
        </div>

        <!-- Content Row-->
        <div class="row gx-4 gx-lg-5">
            <div class="col-md-12 mb-5">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="card-title">Online Fire Report</h2>

                        <form method="post">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" name="fullname" class="form-control" required></td>
                                    </tr>
                                    <tr>
                                        <th>Mobile Number</th>
                                        <td><input type="text" name="mobileNumber" class="form-control" required oninput="validateMobileNumber(this)" maxlength="10" pattern="^(078|072|073)\d{7}$" title="Mobile number must start with 078, 072, or 073 and be 10 digits long."></td>
                                    </tr>
                                    <tr>
                                        <th>Province</th>
                                        <td>
                                            <select id="province" name="province" class="form-control" required onchange="updateDistricts()">
                                                <option value="">Select Province</option>
                                                <option value="Western Province">Western Province</option>
                                                <option value="Eastern Province">Eastern Province</option>
                                                <option value="Southern Province">Southern Province</option>
                                                <option value="Northern Province">Northern Province</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>District</th>
                                        <td>
                                            <select id="district" name="district" class="form-control" required>
                                                <option value="">Select District</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Sector</th>
                                        <td>
                                            <select id="sector" name="sector" class="form-control" required>
                                                <option value="">Select Sector</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Message (if Any)</th>
                                        <td><textarea class="form-control" name="message"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td><input type="submit" name="submit" class="btn btn-primary"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
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
