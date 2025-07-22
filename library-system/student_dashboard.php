<?php
session_start();
include("db.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: index.php");
    exit();
}

$name = $_SESSION['user']['name'];
$email = $_SESSION['user']['email'];

$borrowedQuery = "SELECT COUNT(*) AS count FROM borrowed_books WHERE student_email = '$email' AND returned IS NULL";
$borrowedCount = mysqli_fetch_assoc(mysqli_query($conn, $borrowedQuery))['count'];

$overdueQuery = "SELECT COUNT(*) AS overdue FROM borrowed_books WHERE student_email = '$email' AND returned IS NULL AND due_date < CURDATE()";
$overdueCount = mysqli_fetch_assoc(mysqli_query($conn, $overdueQuery))['overdue'];

$dueQuery = "SELECT DATEDIFF(due_date, CURDATE()) AS daysLeft FROM borrowed_books WHERE student_email = '$email' AND returned IS NULL ORDER BY due_date ASC LIMIT 1";
$dueResult = mysqli_fetch_assoc(mysqli_query($conn, $dueQuery));
$daysLeft = $dueResult ? $dueResult['daysLeft'] : null;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #ff758c, #ff7eb3);
            color: #fff;
        }

        .header {
            text-align: center;
            padding: 40px 20px;
            font-size: 28px;
            background: rgba(0,0,0,0.2);
        }

        .role {
            font-size: 16px;
            opacity: 0.8;
        }

        .widgets {
            text-align: center;
            margin-top: 10px;
            font-size: 16px;
            color: #fff;
        }

        .warning {
            background-color: rgba(255, 0, 0, 0.2);
            padding: 8px;
            border-radius: 6px;
            display: inline-block;
            margin-top: 10px;
            color: #ffdddd;
        }

        .menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            padding: 40px;
            max-width: 900px;
            margin: auto;
        }

        .card {
            background: linear-gradient(135deg, #ffffff30, #ffffff10);
            backdrop-filter: blur(10px);
            border-radius: 18px;
            padding: 30px 20px;
            text-align: center;
            transition: transform 0.3s ease, background 0.3s ease;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            cursor: pointer;
        }

        .card:hover {
            transform: scale(1.05);
            background: linear-gradient(135deg, #ffffff40, #ffffff20);
        }

        .card a {
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            font-size: 18px;
            display: block;
        }

        .icon {
            font-size: 32px;
            display: block;
            margin-bottom: 12px;
        }

        @media screen and (max-width: 500px) {
            .menu {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="header">
    🎓 Welcome, <?= htmlspecialchars($name) ?>
    <div class="role">Role: Student</div>

    <div class="widgets">
        You have <strong><?= $borrowedCount ?></strong> book<?= $borrowedCount != 1 ? 's' : '' ?> borrowed.
        <?php if ($daysLeft !== null): ?>
            <br>Next return due in: <strong><?= $daysLeft ?> day<?= $daysLeft != 1 ? 's' : '' ?></strong>
        <?php endif; ?>

        <?php if ($overdueCount > 0): ?>
            <div class="warning">⚠️ You have <?= $overdueCount ?> overdue book<?= $overdueCount != 1 ? 's' : '' ?>!</div>
        <?php endif; ?>
    </div>
</div>

<div class="menu">
    <div class="card">
        <span class="icon">🔍</span>
        <a href="search_books.php">Search Books</a>
    </div>
    <div class="card">
        <span class="icon">📖</span>
        <a href="my_borrowed_books.php">My Borrowed Books</a>
    </div>
    <div class="card">
        <span class="icon">🚪</span>
        <a href="logout.php">Logout</a>
    </div>
</div>

</body>
</html>
