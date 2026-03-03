<?php
session_start();
// Prevent minor warnings
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// DATABASE CONNECTION
$servername = "localhost";
$username = "root";
$password = "*rajan12345#";
$dbname = "list_donor";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search_results = [];

if (isset($_GET['search'])) {
    $blood_group = trim($_GET['blood_group'] ?? '');
    $district = trim($_GET['district'] ?? '');

    /** * SMART FILTER LOGIC:
     * 1. Matches Blood Group and District.
     * 2. Only shows 'Verified' donors.
     * 3. Only shows donors who are NOT 'Unavailable'.
     * 4. Hides donors who donated in the last 90 days (3 months).
     */
    $sql = "SELECT * FROM donors 
            WHERE blood_group = ? 
            AND district LIKE ? 
            AND status = 'Verified' 
            AND availability != 'Unavailable'
            AND (last_donation_date IS NULL OR last_donation_date <= DATE_SUB(NOW(), INTERVAL 90 DAY))";

    $stmt = $conn->prepare($sql);
    $district_param = "%" . $district . "%";
    $stmt->bind_param("ss", $blood_group, $district_param);

    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $search_results[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Donors | RedTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        body {
            background-color: #f0f2f5;
            font-family: 'Inter', sans-serif;
        }

        .glass-card {
            background: #fff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .modern-table {
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .modern-table tbody tr {
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
            transition: 0.2s;
        }

        .modern-table tbody tr:hover {
            transform: scale(1.005);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .modern-table td {
            border: none;
            padding: 15px 20px;
        }

        .modern-table td:first-child {
            border-radius: 10px 0 0 10px;
        }

        .modern-table td:last-child {
            border-radius: 0 10px 10px 0;
        }

        .badge-blood {
            background-color: #fff1f1;
            color: #dc3545;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 6px;
            border: 1px solid #ffcccc;
        }

        .btn-msg {
            background: #0d6efd;
            border: none;
            border-radius: 8px;
            padding: 6px 15px;
            font-weight: 600;
            color: white;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg bg-dark navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">RedTrack</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link mx-3" href="index.php">Home</a>
                    <a class="nav-link mx-3" href="aboutus.php">About Us</a>
                    <a class="nav-link mx-3" href="contactus.php">Contact Us</a>
                    <!-- <a class="nav-link mx-3" href="#">Donar List</a> -->
                    <a class="nav-link mx-3 active text-danger fw-bold" href="searchdonor.php">Search Donar</a>
                    <a class="nav-link mx-3" href="myaccount.php">My Account</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="glass-card p-4 mb-5">
                    <h3 class="fw-bold text-center mb-4">Find <span class="text-danger">Donors</span></h3>
                    <form action="searchdonor.php" method="GET" class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Blood Group</label>
                            <select name="blood_group" class="form-select bg-light border-0 py-2" required>
                                <option value="">Select Group</option>
                                <?php
                                $groups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
                                foreach ($groups as $g) {
                                    $selected = ($_GET['blood_group'] == $g) ? 'selected' : '';
                                    echo "<option value='$g' $selected>$g</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold">District</label>
                            <input type="text" name="district" class="form-control bg-light border-0 py-2" placeholder="e.g. Kathmandu" value="<?php echo htmlspecialchars($_GET['district'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2 d-grid">
                            <label class="form-label d-none d-md-block">&nbsp;</label>
                            <button type="submit" name="search" class="btn btn-danger py-2"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </div>
                    </form>
                </div>

                <?php if (isset($_GET['search'])): ?>
                    <div class="px-2">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="fw-bold">Available Donors (<?php echo count($search_results); ?>)</h5>
                            <small class="text-muted italic">Only showing donors eligible to donate now.</small>
                        </div>

                        <?php if (count($search_results) > 0): ?>
                            <div class="table-responsive">
                                <table class="table modern-table">
                                    <thead>
                                        <tr class="text-muted small">
                                            <th>NAME</th>
                                            <th>BLOOD GROUP</th>
                                            <th>LOCATION</th>
                                            <th>STATUS</th>
                                            <th class="text-end">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($search_results as $donor): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold"><?php echo htmlspecialchars($donor['fullname']); ?></div>
                                                    <div class="small text-muted"><?php echo $donor['age']; ?> Yrs • <?php echo $donor['gender']; ?></div>
                                                </td>
                                                <td><span class="badge-blood"><?php echo $donor['blood_group']; ?></span></td>
                                                <td><i class="fa-solid fa-location-dot text-danger me-1"></i> <?php echo htmlspecialchars($donor['district']); ?></td>
                                                <td>
                                                    <span class="text-success fw-bold"><i class="fa-solid fa-check-circle me-1"></i> Eligible</span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="chat.php?receiver_id=<?php echo $donor['donor_id']; ?>" class="btn-msg">
                                                        <i class="fa-solid fa-paper-plane me-1"></i> Message
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5 glass-card">
                                <i class="fa-solid fa-heart-crack fa-3x text-light mb-3"></i>
                                <h5 class="text-secondary">No eligible donors found.</h5>
                                <p class="text-muted">Some donors may be temporarily ineligible due to the 90-day recovery rule.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>