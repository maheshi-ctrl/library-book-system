<?php
session_start();
include 'db.php';


error_reporting(E_ALL);
ini_set('display_errors', 1);


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $delete = $conn->prepare("DELETE FROM users WHERE email = ?");
        $delete->bind_param("s", $email);
        $delete->execute();
        $delete->close();

        
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')");
        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            $msg = "♻️ Previous student removed. ✅ Student re-registered!";
        } else {
            $msg = "❌ Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')");
        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            $msg = "✅ Student registered!";
        } else {
            $msg = "❌ Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $check->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Student</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #11998e, #38ef7d);
            color: #fff;
            padding: 40px;
        }
        .box {
            max-width: 400px;
            margin: auto;
            background: rgba(255,255,255,0.15);
            padding: 30px;
            border-radius: 16px;
            backdrop-filter: blur(8px);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: none;
            border-radius: 8px;
        }
        input[type="submit"] {
            background: #333;
            color: #fff;
            cursor: pointer;
            margin-top: 20px;
        }
        .msg {
            margin-top: 15px;
            text-align: center;
            font-weight: bold;
        }
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
