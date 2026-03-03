<?php
session_start();

// Security: Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

/* ===============================
    DATABASE CONNECTIONS
================================ */
$connDonor = new mysqli("localhost", "root", "*rajan12345#", "list_donor");
$connInventory = new mysqli("localhost", "root", "*rajan12345#", "blood_bank");
$connUsers = new mysqli("localhost", "root", "*rajan12345#", "store_data_login_signup");
$connSupport = new mysqli("localhost", "root", "*rajan12345#", "admin_support");

/* ===============================
    FETCH DATA (ORIGINAL LOGIC)
================================ */
// Stats
$totalDonors = $connDonor->query("SELECT COUNT(*) AS total FROM donors")->fetch_assoc()['total'];
$totalUsers = $connUsers->query("SELECT COUNT(*) AS total FROM sign_up")->fetch_assoc()['total'];

// 1. Support Tickets (New System)
$support_tickets = $connSupport->query("
    SELECT s1.* FROM support_messages s1
    INNER JOIN (
        SELECT user_id, MAX(id) as last_id FROM support_messages GROUP BY user_id
    ) s2 ON s1.id = s2.last_id
    ORDER BY s1.created_at DESC
");

// 2. Original Donor List with Update/Delete
$donorList = $connDonor->query("SELECT * FROM donors ORDER BY donor_id DESC");

// 3. Original Blood Inventory (Stock management)
$inventory = $connInventory->query("SELECT * FROM blood_inventory");

// 4. Original User Management
$userList = $connUsers->query("SELECT id, username, email FROM sign_up ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel | RedTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .navbar {
            background: #d90429;
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
            border-left: 4px solid #d90429;
            padding-left: 10px;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .transport-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            display: inline-block;
        }

        .inventory-box {
            transition: transform 0.2s;
        }

        .inventory-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark p-3 sticky-top">
        <div class="container d-flex justify-content-between">
            <span class="navbar-brand fw-bold">RedTrack Admin Dashboard</span>
            <a href="logout.php" class="btn btn-outline-light btn-sm fw-bold">Logout</a>
        </div>
    </nav>

    <div class="container mt-4">

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

        <!-- Stats Cards -->
        <div class="row g-4 mb-4 text-center">
            <div class="col-md-6">
                <div class="card bg-danger text-white p-4">
                    <h6>Total Donors</h6>
                    <h2><?= $totalDonors ?></h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-white p-4">
                    <h6>Total Users</h6>
                    <h2><?= $totalUsers ?></h2>
                </div>
            </div>
        </div>

        <!-- Support Tickets Section -->
        <h5 class="section-title"><i class="bi bi-headset-fill text-primary"></i> Support Tickets</h5>
        <div class="card p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Recent Message</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($support_tickets && $support_tickets->num_rows > 0): ?>
                            <?php while ($t = $support_tickets->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($t['username']) ?></strong></td>
                                    <td class="text-muted text-truncate" style="max-width: 250px;"><?= htmlspecialchars($t['message']) ?></td>
                                    <td><span class="badge <?= $t['sender_type'] == 'user' ? 'bg-warning text-dark' : 'bg-success' ?>"><?= $t['sender_type'] == 'user' ? 'New' : 'Replied' ?></span></td>
                                    <td><a href="admin_reply.php?user_id=<?= $t['user_id'] ?>" class="btn btn-primary btn-sm px-3">Reply</a></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">No support messages.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Donor Management Section -->
        <h5 class="section-title"><i class="bi bi-person-heart text-danger"></i> Donor Management</h5>
        <div class="card p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Blood</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($d = $donorList->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['fullname']) ?></td>
                                <td><span class="badge bg-danger"><?= $d['blood_group'] ?></span></td>
                                <td><?= htmlspecialchars($d['phone']) ?></td>
                                <td><?= $d['status'] ?></td>
                                <td>
                                    <a href="edit_data.php?id=<?= $d['donor_id'] ?>" class="btn btn-sm btn-outline-primary">Update</a>
                                    <a href="delete_donor.php?id=<?= $d['donor_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this donor?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Share Stock with Hospital Section -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-hospital"></i> Share Stock with Hospital</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="share_stock_original.php">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="h_name" class="form-label"><i class="bi bi-building"></i> Hospital Name</label>
                            <input type="text" class="form-control" id="h_name" name="h_name" placeholder="Enter hospital name" required>
                        </div>

                        <div class="col-md-6">
                            <label for="b_group" class="form-label"><i class="bi bi-droplet-fill text-danger"></i> Blood Group</label>
                            <select class="form-select" id="b_group" name="b_group" required>
                                <option value="">Select Blood Group</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="units" class="form-label"><i class="bi bi-box"></i> Units</label>
                            <input type="number" class="form-control" id="units" name="units" min="1" placeholder="Enter number of units" required>
                        </div>

                        <div class="col-md-6">
                            <label for="transport_partner" class="form-label"><i class="bi bi-truck"></i> Transport Partner</label>
                            <select class="form-select" id="transport_partner" name="transport_partner" required>
                                <option value="">Select Transport Partner</option>
                                <option value="Pathao">🏍️ Pathao</option>
                                <option value="Plasma Connect">🚑 Plasma Connect</option>
                                <option value="Deerwalk">🚗 Deerwalk</option>
                                <option value="Red Cross Transport">🔴 Red Cross Transport</option>
                                <option value="Hospital Ambulance">🚨 Hospital Ambulance</option>
                                <option value="Self Collection">📦 Self Collection</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-send-fill"></i> Send Stock to Hospital
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Current Inventory Section -->
        <h5 class="section-title"><i class="bi bi-droplet-fill text-danger"></i> Current Inventory</h5>
        <div class="card p-4">
            <div class="row g-3">
                <?php
                $inventory->data_seek(0); // Reset pointer to beginning
                while ($row = $inventory->fetch_assoc()):
                ?>
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded text-center inventory-box">
                            <h5 class="m-0 text-danger fw-bold"><?= $row['blood_group'] ?></h5>
                            <small class="text-muted"><?= $row['units'] ?> Units</small>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Recent Transactions Section -->
        <h5 class="section-title"><i class="bi bi-clock-history text-info"></i> Recent Transactions</h5>
        <div class="card p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Hospital</th>
                            <th>Blood Group</th>
                            <th>Units</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Transport</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recentTransactions = $connInventory->query("SELECT * FROM hospital_transactions ORDER BY transaction_date DESC LIMIT 10");
                        if ($recentTransactions && $recentTransactions->num_rows > 0):
                            while ($trans = $recentTransactions->fetch_assoc()):
                        ?>
                                <tr>
                                    <td><strong>#<?= $trans['id'] ?></strong></td>
                                    <td><?= htmlspecialchars($trans['hospital_name']) ?></td>
                                    <td><span class="badge bg-danger"><?= $trans['blood_group'] ?></span></td>
                                    <td><?= $trans['units'] ?> Units</td>
                                    <td><strong>₹<?= number_format($trans['amount'], 2) ?></strong></td>
                                    <td><span class="badge bg-primary text-uppercase"><?= $trans['payment_method'] ?></span></td>
                                    <td>
                                        <?php if (!empty($trans['transport_partner'])): ?>
                                            <span class="transport-badge">
                                                <i class="bi bi-truck"></i> <?= htmlspecialchars($trans['transport_partner']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d M Y', strtotime($trans['transaction_date'])) ?></td>
                                </tr>
                            <?php
                            endwhile;
                        else:
                            ?>
                            <tr>
                                <td colspan="8" class="text-center py-3 text-muted">No transactions yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <footer class="text-center py-4 text-muted small">
        <i class="bi bi-heart-fill text-danger"></i> RedTrack Admin Ecosystem © 2026
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>