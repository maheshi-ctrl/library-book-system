<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$name = $_SESSION['user']['name'];
$role = $_SESSION['user']['role'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Unified Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(120deg, #6a11cb, #2575fc);
            color: #fff;
        }

        .header {
            text-align: center;
            padding: 30px;
            font-size: 26px;
            background: rgba(0,0,0,0.15);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .role-tag {
            font-size: 16px;
            opacity: 0.7;
        }

        .menu {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 40px 20px;
        }

        .card {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            border-radius: 14px;
            width: 200px;
            margin: 15px;
            padding: 25px;
            text-align: center;
            transition: transform 0.3s ease, background 0.3s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .card:hover {
            transform: translateY(-5px);
            background: rgba(255,255,255,0.3);
        }

        .card a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            display: block;
        }

        .logout {
            display: block;
            margin: 30px auto;
            text-align: center;
            color: #ffdddd;
        }

        @media screen and (max-width: 500px) {
            .card {
                width: 80%;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        👋 Welcome, <?= htmlspecialchars($name) ?> <br>
        <span class="role-tag">Role: <?= ucfirst($role) ?></span>
    </div>

    <div class="menu">
        <?php if ($role === 'admin'): ?>
            <div class="card"><a href="add_book.php">📚 Add Book</a></div>
            <div class="card"><a href="issue_book.php">📤 Issue Book</a></div>
            <div class="card"><a href="return_book.php">📥 Return Book</a></div>
            <div class="card"><a href="overdue.php">⏰ Overdue Books</a></div>
            <div class="card"><a href="register_student.php">👩‍🎓 Register Student</a></div>
        <?php elseif ($role === 'student'): ?>
            <div class="card"><a href="search_books.php">🔍 Search Books</a></div>
            <div class="card"><a href="my_borrowed_books.php">📖 My Borrowed Books</a></div>
        <?php endif; ?>
    </div>

    <div class="logout">
        <a href="logout.php">🚪 Logout</a>
    </div>

</body>
</html>