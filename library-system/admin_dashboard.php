<?php
session_start();
include("db.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch counts
$totalBooks = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM books"))['count'];
$borrowedToday = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM borrowed_books WHERE borrow_date = CURDATE()"))['count'];
$overdueCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM borrowed_books WHERE returned IS NULL AND due_date < CURDATE()"))['count'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #1a1a1a;
            color: #f2f2f2;
        }

        .header {
            text-align: center;
            padding: 30px;
            background: #2c3e50;
            font-size: 24px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            padding: 40px;
            max-width: 900px;
            margin: auto;
        }

        .card {
            background: #34495e;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
            transition: 0.3s ease;
        }

        .card:hover {
            background: #3c5f7c;
        }

        .card a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 18px;
            display: block;
            margin-top: 8px;
        }

        .stat {
            font-size: 16px;
            margin-top: 6px;
            color: #bdc3c7;
        }

        @media screen and (max-width: 600px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="header">
    👋 Welcome, Admin <?= htmlspecialchars($_SESSION['user']['name']) ?>
</div>

<div class="grid">
    <div class="card">
        <div>📚</div>
        <a href="add_book.php">Manage Books</a>
        <div class="stat">Total: <?= $totalBooks ?></div>
    </div>

    <div class="card">
        <div>📤</div>
        <a href="issue_book.php">Issue Books</a>
        <div class="stat">Today: <?= $borrowedToday ?></div>
    </div>

    <div class="card">
        <div>📥</div>
        <a href="return_book.php">Return Books</a>
    </div>

    <div class="card">
        <div>⏰</div>
        <a href="overdue.php">View Overdue</a>
        <div class="stat">Overdue: <?= $overdueCount ?></div>
    </div>

    <div class="card">
        <div>👩‍🎓</div>
        <a href="register_student.php">Register Student</a>
    </div>

    <div class="card">
        <div>🚪</div>
        <a href="logout.php">Logout</a>
    </div>
</div>

</body>
</html>