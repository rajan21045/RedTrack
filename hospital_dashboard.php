<?php
session_start();

// Security: Check if hospital is logged in
if (!isset($_SESSION['hospital_logged_in']) || $_SESSION['hospital_logged_in'] !== true) {
    header("Location: hospital_login.php");
    exit();
}

$hospital_id = $_SESSION['hospital_id'];
$hospital_name = $_SESSION['hospital_name'];

// Database connection
$conn = new mysqli("localhost", "root", "*rajan12345#", "blood_bank");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create hospital_support table if not exists
$createSupportTable = "CREATE TABLE IF NOT EXISTS hospital_support (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_id INT NOT NULL,
    hospital_name VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    sender_type ENUM('hospital', 'admin') DEFAULT 'hospital',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE
)";
$conn->query($createSupportTable);

// Fetch blood inventory
$inventory = $conn->query("SELECT * FROM blood_inventory ORDER BY blood_group");

// Fetch hospital's transaction history
$transactions = $conn->prepare("SELECT * FROM hospital_transactions WHERE hospital_name = ? ORDER BY transaction_date DESC LIMIT 10");
$transactions->bind_param("s", $hospital_name);
$transactions->execute();
$transactionResult = $transactions->get_result();

// Count unread messages from admin
$unreadMessages = $conn->prepare("SELECT COUNT(*) as count FROM hospital_support WHERE hospital_id = ? AND sender_type = 'admin' AND id > (SELECT COALESCE(MAX(id), 0) FROM hospital_support WHERE hospital_id = ? AND sender_type = 'hospital')");
$unreadMessages->bind_param("ii", $hospital_id, $hospital_id);
$unreadMessages->execute();
$unreadCount = $unreadMessages->get_result()->fetch_assoc()['count'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Dashboard | RedTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, .05);
            margin-bottom: 2rem;
        }

        .section-title {
            font-weight: 700;
            color: #2b2d42;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid #667eea;
            padding-left: 10px;
        }

        .blood-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
            border: 2px solid #e0e0e0;
        }

        .blood-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .badge-notification {
            position: relative;
            top: -10px;
            left: -5px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark p-3 sticky-top">
    <div class="container d-flex justify-content-between align-items-center">
        <span class="navbar-brand fw-bold">
            <i class="bi bi-hospital"></i> <?= htmlspecialchars($hospital_name) ?>
        </span>
        <div>
            <a href="hospital_support.php" class="btn btn-outline-light btn-sm me-2">
                <i class="bi bi-chat-dots"></i> Support
                <?php if ($unreadCount > 0): ?>
                    <span class="badge bg-danger badge-notification"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>
            <a href="hospital_logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    
    <!-- Welcome Section -->
    <div class="card p-4 mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <h3><i class="bi bi-grid-fill"></i> Welcome to Your Dashboard</h3>
        <p class="mb-0">Manage blood requirements and communicate with blood bank administration</p>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Blood Inventory -->
    <h5 class="section-title"><i class="bi bi-droplet-fill text-danger"></i> Available Blood Inventory</h5>
    <div class="card p-4 mb-4">
        <div class="row g-3">
            <?php while ($blood = $inventory->fetch_assoc()): ?>
                <div class="col-6 col-md-3">
                    <div class="blood-card">
                        <h4 class="text-danger mb-2"><?= $blood['blood_group'] ?></h4>
                        <h5 class="mb-0"><?= $blood['units'] ?></h5>
                        <small class="text-muted">Units Available</small>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Request Blood Section -->
    <h5 class="section-title"><i class="bi bi-cart-plus text-success"></i> Request Blood</h5>
    <div class="card p-4 mb-4">
        <form action="hospital_request_blood.php" method="POST" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Blood Group</label>
                <select name="blood_group" class="form-select" required>
                    <option value="">Select Blood Group</option>
                    <option>A+</option>
                    <option>A-</option>
                    <option>B+</option>
                    <option>B-</option>
                    <option>O+</option>
                    <option>O-</option>
                    <option>AB+</option>
                    <option>AB-</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Units Required</label>
                <input type="number" name="units" class="form-control" min="1" required>
            </div>
            <div class="col-md-5">
                <label class="form-label">Urgency Level</label>
                <select name="urgency" class="form-select" required>
                    <option value="normal">Normal</option>
                    <option value="urgent">Urgent</option>
                    <option value="critical">Critical</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Additional Notes (Optional)</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Patient details, reason, etc."></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success btn-lg w-100">
                    <i class="bi bi-send-fill"></i> Submit Blood Request
                </button>
            </div>
        </form>
    </div>

    <!-- Transaction History -->
    <h5 class="section-title"><i class="bi bi-clock-history text-info"></i> Recent Transactions</h5>
    <div class="card p-3 mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Transaction ID</th>
                        <th>Blood Group</th>
                        <th>Units</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($transactionResult->num_rows > 0): ?>
                        <?php while ($trans = $transactionResult->fetch_assoc()): ?>
                            <tr>
                                <td><strong>#<?= $trans['id'] ?></strong></td>
                                <td><span class="badge bg-danger"><?= $trans['blood_group'] ?></span></td>
                                <td><?= $trans['units'] ?> Units</td>
                                <td><strong>₹<?= number_format($trans['amount'], 2) ?></strong></td>
                                <td><span class="badge bg-primary text-uppercase"><?= $trans['payment_method'] ?></span></td>
                                <td><?= date('d M Y', strtotime($trans['transaction_date'])) ?></td>
                                <td><span class="badge bg-success"><?= ucfirst($trans['status']) ?></span></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No transactions yet
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
     <h5 class="section-title"><i class="bi bi-lightning-fill text-warning"></i> Quick Actions</h5>
        <div class="col-md-4">
            <a href="#" class="card p-4 text-decoration-none text-center" data-bs-toggle="modal" data-bs-target="#profileModal">
                <i class="bi bi-person-circle fs-1 text-success mb-2"></i>
                <h6>View Profile</h6>
                <small class="text-muted">Manage hospital details</small>
            </a>
        </div>
       
</div>

<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title"><i class="bi bi-hospital"></i> Hospital Profile</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php
                $hospitalInfo = $conn->prepare("SELECT * FROM hospitals WHERE id = ?");
                $hospitalInfo->bind_param("i", $hospital_id);
                $hospitalInfo->execute();
                $info = $hospitalInfo->get_result()->fetch_assoc();
                ?>
                <div class="mb-3">
                    <label class="text-muted small">Hospital Name</label>
                    <p class="fw-bold"><?= htmlspecialchars($info['hospital_name']) ?></p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Email</label>
                    <p class="fw-bold"><?= htmlspecialchars($info['email']) ?></p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Phone</label>
                    <p class="fw-bold"><?= htmlspecialchars($info['phone']) ?></p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Address</label>
                    <p class="fw-bold"><?= htmlspecialchars($info['address']) ?></p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">City</label>
                    <p class="fw-bold"><?= htmlspecialchars($info['city']) ?></p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Registration Number</label>
                    <p class="fw-bold"><?= htmlspecialchars($info['registration_number']) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="text-center py-4 text-muted small">Hospital Portal - RedTrack © 2026</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>