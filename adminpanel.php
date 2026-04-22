<?php
session_start();

// Security check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

/* DB CONNECTIONS */
$connDonor     = new mysqli("localhost", "root", "*rajan12345#", "list_donor");
$connInventory = new mysqli("localhost", "root", "*rajan12345#", "blood_bank");
$connUsers     = new mysqli("localhost", "root", "*rajan12345#", "store_data_login_signup");
$connSupport   = new mysqli("localhost", "root", "*rajan12345#", "admin_support");

/* FETCH DATA */
$totalDonors = $connDonor->query("SELECT COUNT(*) AS total FROM donors")->fetch_assoc()['total'];
$totalUsers  = $connUsers->query("SELECT COUNT(*) AS total FROM sign_up")->fetch_assoc()['total'];

$pendingCount = $connInventory->query("
    SELECT COUNT(*) AS total FROM hospital_transactions WHERE status='Pending'
")->fetch_assoc()['total'];

$support_tickets = $connSupport->query("
    SELECT s1.* FROM support_messages s1
    INNER JOIN (
        SELECT user_id, MAX(id) as last_id FROM support_messages GROUP BY user_id
    ) s2 ON s1.id = s2.last_id
");

$donorList = $connDonor->query("SELECT * FROM donors ORDER BY donor_id DESC");
$inventory = $connInventory->query("SELECT * FROM blood_inventory");
$userList  = $connUsers->query("SELECT id, username, email FROM sign_up");

$recentTransactions = $connInventory->query("
    SELECT * FROM hospital_transactions ORDER BY transaction_date DESC LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>RedTrack Admin | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fb;
            font-family: 'Segoe UI', sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, #d90429, #ef233c);
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
            transition: 0.2s;
            margin-bottom: 20px;
        }

        .card:hover {
            transform: translateY(-4px);
        }

        .section-title {
            font-weight: 700;
            margin-bottom: 15px;
            border-left: 5px solid #ef233c;
            padding-left: 10px;
        }

        .table th {
            background: #edf2f4;
        }

        /* Status Colors */
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-complete {
            background: #d4edda;
            color: #155724;
        }

        .status-failed {
            background: #f8d7da;
            color: #721c24;
        }

        .inventory-box {
            border-radius: 12px;
            transition: 0.2s;
        }

        .inventory-box:hover {
            transform: scale(1.05);
        }

        .btn-primary {
            background: #ef233c;
            border: none;
        }

        .btn-primary:hover {
            background: #d90429;
        }

        /* Support Chat Styling */
        .clickable-row {
            cursor: pointer;
            transition: background 0.2s;
        }

        .clickable-row:hover {
            background-color: rgba(239, 35, 60, 0.05) !important;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark p-3 shadow-sm">
        <div class="container d-flex justify-content-between">
            <span class="navbar-brand fw-bold"><i class="bi bi-droplet-fill"></i> RedTrack Admin Dashboard</span>
            <a href="logout.php" class="btn btn-light btn-sm fw-bold text-danger">Logout</a>
        </div>
    </nav>

    <div class="container mt-4">

        <div class="row g-4 mb-4 text-center">
            <div class="col-md-4">
                <div class="card bg-danger text-white p-4">
                    <h6>Total Donors</h6>
                    <h2><?= number_format($totalDonors) ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4">
                    <h6>Total Users</h6>
                    <h2><?= number_format($totalUsers) ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning p-4">
                    <h6>Pending Payments</h6>
                    <h2><?= number_format($pendingCount) ?></h2>
                </div>
            </div>
        </div>

        <div class="card p-3">
            <h5 class="section-title"><i class="bi bi-headset"></i> Support Tickets</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Latest Message</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($s = $support_tickets->fetch_assoc()): ?>
                            <tr class="clickable-row" onclick="window.location='admin_reply.php?user_id=<?= $s['user_id'] ?>';">
                                <td class="fw-bold"><?= htmlspecialchars($s['username']) ?></td>
                                <td class="text-muted"><?= htmlspecialchars(substr($s['message'], 0, 60)) ?>...</td>
                                <td>
                                    <span class="badge <?= $s['sender_type'] == 'user' ? 'bg-warning text-dark' : 'bg-success' ?>">
                                        <?= $s['sender_type'] == 'user' ? 'New Message' : 'Replied' ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="admin_reply.php?user_id=<?= $s['user_id'] ?>" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-chat-dots"></i> Open
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card p-3">
            <h5 class="section-title"><i class="bi bi-person-heart"></i> Donor Management</h5>
            <table class="table table-hover">
                <tr>
                    <th>Name</th>
                    <th>Blood</th>
                    <th>Phone</th>
                    <th>Status</th>
                </tr>
                <?php while ($d = $donorList->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['fullname']) ?></td>
                        <td><span class="badge bg-danger"><?= $d['blood_group'] ?></span></td>
                        <td><?= htmlspecialchars($d['phone']) ?></td>
                        <td><span class="badge bg-secondary"><?= $d['status'] ?></span></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <div class="card p-4">
            <h5 class="section-title"><i class="bi bi-hospital"></i> Share Stock (Dispatch)</h5>
            <form method="POST" action="shipping.php">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="h_name" class="form-control" placeholder="Hospital Name" required>
                    </div>
                    <div class="col-md-6">
                        <select name="b_group" class="form-select" required>
                            <option value="">Select Blood Group</option>
                            <option>A+</option>
                            <option>B+</option>
                            <option>O+</option>
                            <option>AB+</option>
                            <option>A-</option>
                            <option>B-</option>
                            <option>O-</option>
                            <option>AB-</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="number" name="units" class="form-control" placeholder="Units Required" required>
                    </div>
                    <div class="col-md-6">
                        <select name="transport_partner" class="form-select">
                            <option>Pathao</option>
                            <option>Ambulance</option>
                            <option>Red Cross</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select name="payment_method" class="form-select" required>
                            <option value="">Payment Method</option>
                            <option value="COD">Cash On Delivery</option>
                            <option value="Esewa">eSewa</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-lg w-100">🚀 Dispatch Blood Stock</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card p-3">
            <h5 class="section-title"><i class="bi bi-droplet"></i> Current Inventory</h5>
            <div class="row g-3">
                <?php while ($i = $inventory->fetch_assoc()): $low = $i['units'] < 5; ?>
                    <div class="col-6 col-md-3">
                        <div class="p-3 text-center inventory-box <?= $low ? 'bg-danger bg-opacity-10 border border-danger' : 'bg-white border' ?>">
                            <h4 class="mb-0"><?= $i['blood_group'] ?></h4>
                            <p class="<?= $low ? 'text-danger fw-bold' : 'text-muted' ?> mb-0">
                                <?= $i['units'] ?> Units <?= $low ? '⚠️' : '' ?>
                            </p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="card p-3">
            <h5 class="section-title"><i class="bi bi-people"></i> Registered Users</h5>
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($u = $userList->fetch_assoc()): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><?= htmlspecialchars($u['username']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="card p-3">
            <h5 class="section-title"><i class="bi bi-clock-history"></i> Recent Transactions</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Hospital</th>
                            <th>Type</th>
                            <th>Units</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($t = $recentTransactions->fetch_assoc()):
                            $statusClass = match (strtolower($t['status'])) {
                                'completed' => 'status-complete',
                                'failed'    => 'status-failed',
                                default     => 'status-pending'
                            };
                        ?>
                            <tr>
                                <td>#<?= $t['id'] ?></td>
                                <td><?= htmlspecialchars($t['hospital_name']) ?></td>
                                <td><span class="badge bg-danger"><?= $t['blood_group'] ?></span></td>
                                <td><?= $t['units'] ?></td>
                                <td>Rs <?= number_format($t['amount']) ?></td>
                                <td><?= $t['payment_method'] ?></td>
                                <td><span class="badge <?= $statusClass ?>"><?= ucfirst($t['status']) ?></span></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <?php
    // Close all connections
    $connDonor->close();
    $connInventory->close();
    $connUsers->close();
    $connSupport->close();
    ?>
</body>

</html>