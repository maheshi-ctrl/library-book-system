<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: index.php");
    exit();
}

$section = $_GET['section'] ?? 'dashboard';
$email = $_SESSION['user']['email'];
$name = $_SESSION['user']['name'];
$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Portal</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: linear-gradient(to right, #ff758c, #ff7eb3);
            color: #fff;
        }
        .nav {
            display: flex;
            gap: 20px;
            padding: 20px;
            background: rgba(0,0,0,0.2);
        }
        .nav a {
            color: #fff;
            font-weight: bold;
            text-decoration: none;
        }
        .section {
            padding: 30px;
        }
        input[type="text"] {
            width: 60%;
            padding: 10px;
            margin-right: 10px;
            border-radius: 8px;
            border: none;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background: #007acc;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        table {
            width: 100%;
            margin-top: 20px;
            background: rgba(255,255,255,0.2);
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
        }
        th {
            background: rgba(0,0,0,0.2);
        }
        tr:hover {
            background: rgba(255,255,255,0.3);
        }
        .status-returned { color: #baffba; }
        .status-pending  { color: #ffd2d2; }
        .status-overdue  { color: #ff9999; }
    </style>
</head>
<body>

<div class="nav">
    <a href="?section=dashboard">🏠 Home</a>
    <a href="?section=search_books">🔍 Search Books</a>
    <a href="?section=my_books">📖 My Borrowed Books</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="section">
    <h2>🎓 Welcome, <?= htmlspecialchars($name) ?></h2>

    <?php if ($section === 'search_books'): ?>
        <h3>Search Available Books</h3>
        <form method="post">
            <input type="text" name="query" placeholder="Search by title, author, or category" required>
            <input type="submit" value="Search">
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])) {
            $search = $conn->real_escape_string($_POST['query']);
            $sql = "SELECT * FROM books 
                    WHERE status='available' AND 
                    (title LIKE '%$search%' OR author LIKE '%$search%' OR category LIKE '%$search%')";
            $results = $conn->query($sql);
            if ($results->num_rows > 0):
        ?>
        <table>
            <tr><th>Title</th><th>Author</th><th>Category</th></tr>
            <?php while ($book = $results->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($book['title']) ?></td>
                <td><?= htmlspecialchars($book['author']) ?></td>
                <td><?= htmlspecialchars($book['category']) ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
            <p>No books found for "<?= htmlspecialchars($_POST['query']) ?>".</p>
        <?php endif; } ?>

    <?php elseif ($section === 'my_books'):
        $query = "SELECT b.title, b.author, bb.borrow_date, bb.due_date, bb.returned
                  FROM borrowed_books bb
                  JOIN books b ON bb.bookID = b.bookID
                  WHERE bb.student_email = '$email'";
        $result = $conn->query($query);
    ?>
        <h3>My Borrowed Books</h3>
        <?php if ($result->num_rows > 0): ?>
        <table>
            <tr><th>Title</th><th>Author</th><th>Borrowed On</th><th>Due Date</th><th>Status</th></tr>
            <?php while ($row = $result->fetch_assoc()):
                $isOverdue = (!$row['returned'] && $row['due_date'] < $today);
            ?>
            <tr>
                <td><?= $row['title'] ?></td>
                <td><?= $row['author'] ?></td>
                <td><?= $row['borrow_date'] ?></td>
                <td><?= $row['due_date'] ?></td>
                <td class="<?= $row['returned'] ? 'status-returned' : ($isOverdue ? 'status-overdue' : 'status-pending') ?>">
                    <?= $row['returned'] ? '✅ Returned' : ($isOverdue ? '⚠️ Overdue' : '⏳ Pending') ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
            <p>You haven’t borrowed any books yet.</p>
        <?php endif; ?>

    <?php else: ?>
        <p>Use the menu above to search books or view what you’ve borrowed.</p>
    <?php endif; ?>
</div>

</body>
</html>