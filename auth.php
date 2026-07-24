<?php
session_start();

// Database Connections Settings
$host = "sql108.infinityfree.com";
$db_user = "if0_42253514";       
$db_pass = "12345678DCsac";           
$db_name = "if0_42253514_irctc";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Database Connection Error: " . $conn->connect_error);
}

// =========================================================
// REGISTER SCRIPT ENGINE
// =========================================================
if (isset($_POST['register_action'])) {
    $fullname = trim($conn->real_escape_string($_POST['fullname']));
    $mobile = trim($conn->real_escape_string($_POST['mobile']));
    $email = trim($conn->real_escape_string($_POST['email']));

    // Server-side double protection for exact 10 digits
    if(strlen($mobile) !== 10 || !preg_match('/^[0-9]{10}$/', $mobile)) {
        echo "<script>alert('Error: Mobile number must be exactly 10 digits!'); window.location.href='index.php';</script>";
        exit();
    }
    // Server-side double protection for proper email address (@ and domain)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Error: Please enter a valid email address with @ and domain!'); window.location.href='index.php';</script>";
        exit();
    }

    // Duplicate records checking row search
    $check_sql = "SELECT id FROM users WHERE mobile='$mobile' OR email='$email' LIMIT 1";
    $duplicate_check = $conn->query($check_sql);

    if ($duplicate_check->num_rows > 0) {
        echo "<script>alert('Error: Mobile Number or Email already registered!'); window.location.href='index.php';</script>";
    } else {
        $insert_sql = "INSERT INTO users (fullname, mobile, email) VALUES ('$fullname', '$mobile', '$email')";
        if ($conn->query($insert_sql) === TRUE) {
            echo "<script>alert('Registration Successful! Please log in now.'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Internal error occurred. Try again.'); window.location.href='index.php';</script>";
        }
    }
}

// =========================================================
// LOGIN SCRIPT ENGINE
// =========================================================
if (isset($_POST['login_action'])) {
    $mobile = trim($conn->real_escape_string($_POST['mobile']));
    $email = trim($conn->real_escape_string($_POST['email']));

    // Authentication engine parameters verification
    $auth_sql = "SELECT * FROM users WHERE mobile='$mobile' AND email='$email' LIMIT 1";
    $auth_result = $conn->query($auth_sql);

    if ($auth_result->num_rows > 0) {
        $authenticated_user = $auth_result->fetch_assoc();
        $_SESSION['user_id'] = $authenticated_user['id'];
        $_SESSION['user_name'] = $authenticated_user['fullname']; // Navbar me automatic show hoga

        echo "<script>alert('Welcome back, " . $authenticated_user['fullname'] . "! Authentication complete.'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Invalid account details! Verify mobile or email.'); window.location.href='index.php';</script>";
    }
}

$conn->close();
?>