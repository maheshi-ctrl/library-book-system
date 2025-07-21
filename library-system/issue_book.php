<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Get available books
$bookQuery = "SELECT bookID, title FROM books WHERE status = 'available'";
$bookResult = $conn->query($bookQuery);

// Get student users
$studentQuery = "SELECT email, name FROM users WHERE role = 'student'";
$studentResult = $conn->query($studentQuery);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_email = $conn->real_escape_string($_POST['student_email']);
    $bookID = $_POST['bookID'];
    $borrow_date = $_POST['borrow_date'];
    $due_date = $_POST['due_date'];

    $insert = "INSERT INTO borrowed_books (student_email, bookID, borrow_date, due_date)
               VALUES ('$student_email', $bookID, '$borrow_date', '$due_date')";
    $update = "UPDATE books SET status = 'borrowed' WHERE bookID = $bookID";

    if ($conn->query($insert) && $conn->query($update)) {
        $msg = "✅ Book issued successfully!";
    } else {
        $msg = "❌ Failed to issue book.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Issue Book</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f7971e, #ffd200);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .box {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(12px);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            color: #333;
            width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #fff;
        }

        select, input {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 8px;
            border: none;
            box-sizing: border-box;
        }

        input[type="submit"] {
            margin-top: 20px;
            background-color: #333;
            color: #fff;
            cursor: pointer;
        }

        .msg {
            margin-top: 15px;
            text-align: center;
            font-weight: bold;
            color: #fff;
        }
    </style>
</head>
<body>

    <div class="box">
        <h2>📤 Issue Book</h2>
        <?php if (isset($msg)) echo "<div class='msg'>$msg</div>"; ?>
        <form method="post">
            <label>Select Student</label>
            <select name="student_email" required>
                <option value="">Choose...</option>
                <?php while ($s = $studentResult->fetch_assoc()): ?>
                    <option value="<?= $s['email'] ?>"><?= $s['name'] ?> (<?= $s['email'] ?>)</option>
                <?php endwhile; ?>
            </select>

            <label>Select Book</label>
            <select name="bookID" required>
                <option value="">Choose...</option>
                <?php while ($b = $bookResult->fetch_assoc()): ?>
                    <option value="<?= $b['bookID'] ?>"><?= $b['title'] ?></option>
                <?php endwhile; ?>
            </select>

            <label>Borrow Date</label>
            <input type="date" name="borrow_date" required>

            <label>Due Date</label>
            <input type="date" name="due_date" required>

            <input type="submit" value="Issue Book">
        </form>
    </div>

</body>
</html>