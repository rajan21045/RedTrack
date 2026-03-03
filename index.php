<?php
session_start();

$servername = "localhost:3306";
$db_username = "root";
$db_password = "*rajan12345#";
$dbname = "store_data_login_signUP";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* ------------------ SIGN-UP ------------------ */
    if (isset($_POST['signup'])) {

        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt_check = $conn->prepare(
            "SELECT id FROM sign_up WHERE email = ? OR username = ?"
        );
        $stmt_check->bind_param("ss", $email, $username);
        $stmt_check->execute();

        if ($stmt_check->get_result()->num_rows > 0) {
            echo "<script>alert('Account already exists!');</script>";
        } else {
            $stmt_insert = $conn->prepare(
                "INSERT INTO sign_up (username, email, password_hash)
                 VALUES (?, ?, ?)"
            );
            $stmt_insert->bind_param("sss", $username, $email, $password_hash);

            if ($stmt_insert->execute()) {
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['username'] = $username;
                header("Location: homepage.php");
                exit();
            }
        }
    }

    /* ------------------ SIGN-IN ------------------ */ elseif (isset($_POST['signin'])) {

        $email_or_username = trim($_POST['email']);
        $submitted_password = $_POST['password'];

        $stmt_login = $conn->prepare(
            "SELECT id, username, password_hash
             FROM sign_up
             WHERE email = ? OR username = ?"
        );
        $stmt_login->bind_param("ss", $email_or_username, $email_or_username);
        $stmt_login->execute();
        $result_login = $stmt_login->get_result();

        if ($result_login->num_rows === 1) {
            $user = $result_login->fetch_assoc();

            if (password_verify($submitted_password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: homepage.php");
                exit();
            } else {
                echo "<script>alert('Incorrect Password');</script>";
            }
        } else {
            echo "<script>alert('User not found');</script>";
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign Up / Sign In</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/af02a689af.js" crossorigin="anonymous"></script>
    <style>
        /* Essential for the sliding effect */
        #nameField {
            overflow: hidden;
            transition: max-height 0.5s;
        }

        .disable {
            background: #eaeaea !important;
            color: #555 !important;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-box">
            <h1 id="title">Sign-Up</h1>
            <form id="authForm" method="POST" action="">
                <div class="input-group">
                    <div class="input-field" id="nameField">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" id="usernameInput" name="username" placeholder="Username">
                    </div>

                    <div class="input-field">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="text" name="email" placeholder="Email or Username" required>
                    </div>

                    <div class="input-field">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                </div>

                <div class="btn-field">
                    <button type="submit" id="signupBtn" name="signup">Sign Up</button>
                    <button type="button" id="signinBtn" class="disable">Sign In</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let signinBtn = document.getElementById("signinBtn");
        let signupBtn = document.getElementById("signupBtn");
        let title = document.getElementById("title");
        let nameField = document.getElementById("nameField");
        let usernameInput = document.getElementById("usernameInput");

        // When clicking Sign In button
        signinBtn.onclick = function() {
            if (title.innerHTML === "Sign-Up") {
                // Switch UI to Sign-In Mode
                nameField.style.maxHeight = "0";
                title.innerHTML = "Sign-In";
                signupBtn.classList.add("disable");
                signinBtn.classList.remove("disable");

                // Change button types so only the correct one submits
                signupBtn.type = "button";
                signinBtn.type = "submit";
                signinBtn.name = "signin"; // Ensure name is signin
                usernameInput.required = false;
            }
        }

        // When clicking Sign Up button
        signupBtn.onclick = function() {
            if (title.innerHTML === "Sign-In") {
                // Switch UI to Sign-Up Mode
                nameField.style.maxHeight = "60px";
                title.innerHTML = "Sign-Up";
                signupBtn.classList.remove("disable");
                signinBtn.classList.add("disable");

                // Change button types back
                signupBtn.type = "submit";
                signupBtn.name = "signup";
                signinBtn.type = "button";
                usernameInput.required = true;
            }
        }
    </script>
</body>

</html>