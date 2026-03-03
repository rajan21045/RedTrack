<!DOCTYPE html>
<html>
<head>
    <title>Become a Donor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-danger text-white text-center">
            <h4>Become a Blood Donor</h4>
        </div>

        <div class="card-body">
            <p class="text-center text-muted">
                One donation can save up to three lives.
            </p>

            <form method="POST" action="save_donor.php">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Age</label>
                        <input type="number" name="age" class="form-control" min="18" max="65" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Gender</label>
                        <select name="gender" class="form-control" required>
                            <option value="">Select</option>
                            <option>Male</option>
                            <option>Female</option>
                            <option>Other</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Blood Group</label>
                        <select name="blood" class="form-control" required>
                            <option>A+</option><option>A-</option>
                            <option>B+</option><option>B-</option>
                            <option>AB+</option><option>AB-</option>
                            <option>O+</option><option>O-</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Address</label>
                        <input type="text" name="address" class="form-control" required>
                    </div>
                </div>

                <button class="btn btn-danger w-100">Register as Donor</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
