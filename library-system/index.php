<?php
include 'db.php';
session_start();

$admin_email = "admin@library.com";
$admin_password = "admin123";
$student_email = "studentuser@library.com";
$student_password = "student123";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    $email = $conn->real_escape_string($email);
    $password = $conn->real_escape_string($password);

    $query = "SELECT * FROM users WHERE email='$email' AND password='$password' AND role='$role'";
    $result = $conn->query($query);

    if ($result && $result->num_rows == 1) {
        $_SESSION['user'] = $result->fetch_assoc();
        if ($role === 'admin') {
            header("Location: admin_dashboard.php");
        } elseif ($role === 'student') {
            header("Location: student_dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid email, password, or role.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Login</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            height: 100vh;
            background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 0 30px rgba(0,0,0,0.2);
            padding: 40px;
            width: 360px;
        }

        h2 {
            text-align: center;
            color: #fff;
            margin-bottom: 25px;
        }

        label {
            color: #fff;
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border: none;
            border-radius: 8px;
            background: rgba(255,255,255,0.9);
        }

        input[type="submit"] {
            background-color: #007acc;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #005fa3;
        }

        .error {
            color: #fff;
            background-color: rgba(255,0,0,0.3);
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }

        @media screen and (max-width: 400px) {
            .login-box {
                width: 90%;
                padding: 30px;
            }
        }
    </style>
    <script>
        function autofillFields() {
            const role = document.querySelector('select[name="role"]').value;
            if (role === 'admin') {
                document.querySelector('input[name="email"]').value = "<?= $admin_email ?>";
                document.querySelector('input[name="password"]').value = "<?= $admin_password ?>";
            } else if (role === 'student') {
                document.querySelector('input[name="email"]').value = "<?= $student_email ?>";
                document.querySelector('input[name="password"]').value = "<?= $student_password ?>";
            }
        }
    </script>
</head>
<body>

<div class="login-box">
    <h2>📚 Library Login</h2>
    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="post">
        <label>Email</label>
        <input type="email" name="email" required
               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">

        <label>Password</label>
        <input type="password" name="password" required
               value="<?= isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '' ?>">

        <label>Role</label>
        <select name="role" onchange="autofillFields()" required>
            <option value="">Choose Role</option>
            <option value="admin" <?= (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
            <option value="student" <?= (isset($_POST['role']) && $_POST['role'] === 'student') ? 'selected' : '' ?>>Student</option>
        </select>

        <input type="submit" value="Login">
    </form>
</div>

</body>
</html>