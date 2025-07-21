<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$section = $_GET['section'] ?? 'dashboard';
$msg = "";

// ====== Add Book ======
if ($section === 'add_book' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $category = $conn->real_escape_string($_POST['category']);
    if ($conn->query("INSERT INTO books (title, author, category) VALUES ('$title', '$author', '$category')")) {
        $msg = "✅ Book added!";
    }
}

// ====== Issue Book ======
if ($section === 'issue_book' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_email = $conn->real_escape_string($_POST['student_email']);
    $bookID = $_POST['bookID'];
    $borrow_date = $_POST['borrow_date'];
    $due_date = $_POST['due_date'];
    $insert = "INSERT INTO borrowed_books (student_email, bookID, borrow_date, due_date) 
               VALUES ('$student_email', $bookID, '$borrow_date', '$due_date')";
    $update = "UPDATE books SET status = 'borrowed' WHERE bookID = $bookID";
    if ($conn->query($insert) && $conn->query($update)) {
        $msg = "✅ Book issued!";
    }
}

// ====== Return Book ======
if ($section === 'return_book' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $borrowID = $_POST['borrowID'];
    $bookID = $conn->query("SELECT bookID FROM borrowed_books WHERE borrowID=$borrowID")->fetch_assoc()['bookID'];
    $conn->query("UPDATE borrowed_books SET returned=1 WHERE borrowID=$borrowID");
    $conn->query("UPDATE books SET status='available' WHERE bookID=$bookID");
    $msg = "✅ Book returned!";
}

// ====== Register Student ======
if ($section === 'register_student' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', 'student')";
    if ($conn->query($sql)) {
        $msg = "✅ Student registered!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #2c3e50, #4ca1af);
            color: #fff;
        }
        .nav {
            display: flex;
            gap: 20px;
            padding: 20px;
            background-color: rgba(0,0,0,0.3);
        }
        .nav a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }
        .section {
            padding: 30px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 8px;
            border: none;
        }
        input[type="submit"] {
            background-color: #007acc;
            color: white;
            margin-top: 20px;
            cursor: pointer;
        }
        table {
            width: 100%;
            margin-top: 20px;
            background-color: rgba(255,255,255,0.15);
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
        }
        .msg {
            margin: 20px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="nav">
    <a href="?section=dashboard">🏠 Dashboard</a>
    <a href="?section=add_book">📚 Add Book</a>
    <a href="?section=issue_book">📤 Issue Book</a>
    <a href="?section=return_book">📥 Return Book</a>
    <a href="?section=overdue">⏰ Overdue</a>
    <a href="?section=register_student">👩‍🎓 Register Student</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="section">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?> (Admin)</h2>
    <?php if ($msg): ?><div class="msg"><?= $msg ?></div><?php endif; ?>

    <?php if ($section === 'add_book'): ?>
        <h3>Add New Book</h3>
        <form method="post">
            <input type="text" name="title" placeholder="Book Title" required>
            <input type="text" name="author" placeholder="Author" required>
            <input type="text" name="category" placeholder="Category" required>
            <input type="submit" value="Add Book">
        </form>

    <?php elseif ($section === 'issue_book'):
        $books = $conn->query("SELECT bookID, title FROM books WHERE status='available'");
        $students = $conn->query("SELECT email, name FROM users WHERE role='student'");
    ?>
        <h3>Issue Book</h3>
        <form method="post">
            <select name="student_email" required>
                <option value="">Select Student</option>
                <?php while ($s = $students->fetch_assoc()): ?>
                    <option value="<?= $s['email'] ?>"><?= $s['name'] ?> (<?= $s['email'] ?>)</option>
                <?php endwhile; ?>
            </select>
            <select name="bookID" required>
                <option value="">Select Book</option>
                <?php while ($b = $books->fetch_assoc()): ?>
                    <option value="<?= $b['bookID'] ?>"><?= $b['title'] ?></option>
                <?php endwhile; ?>
            </select>
            <input type="date" name="borrow_date" required>
            <input type="date" name="due_date" required>
            <input type="submit" value="Issue Book">
        </form>

    <?php elseif ($section === 'return_book'):
        $borrowed = $conn->query("SELECT bb.borrowID, bb.student_email, b.title, bb.due_date 
                                  FROM borrowed_books bb 
                                  JOIN books b ON bb.bookID = b.bookID 
                                  WHERE bb.returned = 0");
    ?>
        <h3>Return Borrowed Book</h3>
        <table>
            <tr><th>Student</th><th>Book</th><th>Due Date</th><th>Action</th></tr>
            <?php while ($row = $borrowed->fetch_assoc()): ?>
            <tr>
                <td><?= $row['student_email'] ?></td>
                <td><?= $row['title'] ?></td>
                <td><?= $row['due_date'] ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="borrowID" value="<?= $row['borrowID'] ?>">
                        <input type="submit" value="Return Book">
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

    <?php elseif ($section === 'overdue'):
        $today = date('Y-m-d');
        $late = $conn->query("SELECT bb.student_email, b.title, bb.due_date 
                              FROM borrowed_books bb 
                              JOIN books b ON bb.bookID = b.bookID 
                              WHERE bb.returned=0 AND bb.due_date < '$today'");
    ?>
        <h3>Overdue Books</h3>
        <table>
            <tr><th>Student</th><th>Book</th><th>Due Date</th></tr>
            <?php while ($row = $late->fetch_assoc()): ?>
            <tr>
                <td><?= $row['student_email'] ?></td>
                <td><?= $row['title'] ?></td>
                <td><?= $row['due_date'] ?></td>
            </tr>
            <?php endwhile; ?>
        </table>

    <?php elseif ($section === 'register_student'): ?>
        <h3>Register New Student</h3>
               <form method="post">
            <input type="text" name="name" placeholder="Student Name" required>
            <input type="email" name="email" placeholder="Student Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Register Student">
        </form>
    <?php endif; ?>
</div>

</body>
</html>