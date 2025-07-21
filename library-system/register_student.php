<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secured
    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', 'student')";
    $msg = $conn->query($sql) ? "✅ Student registered!" : "❌ Error: could not register student.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Student</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #11998e, #38ef7d); color: #fff; padding: 40px; }
        .box { max-width: 400px; margin: auto; background: rgba(255,255,255,0.15); padding: 30px; border-radius: 16px; backdrop-filter: blur(8px); }
        h2 { text-align: center; margin-bottom: 20px; }
        input { width: 100%; padding: 10px; margin-top: 10px; border: none; border-radius: 8px; }
        input[type="submit"] { background: #333; color: #fff; cursor: pointer; margin-top: 20px; }
        .msg { margin-top: 15px; text-align: center; font-weight: bold; }
    </style>
</head>
<body>

<div class="box">
    <h2>👩‍🎓 Register New Student</h2>
    <?php if (isset($msg)) echo "<div class='msg'>$msg</div>"; ?>
    <form method="post">
        <input type="text" name="name" placeholder="Student Name" required>
        <input type="email" name="email" placeholder="Student Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" value="Register Student">
    </form>
</div>

</body>
</html>