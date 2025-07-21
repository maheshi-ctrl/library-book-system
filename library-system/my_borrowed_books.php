<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['user']['email'];
$today = date('Y-m-d');

$sql = "SELECT b.title, b.author, bb.borrow_date, bb.due_date, bb.returned
        FROM borrowed_books bb
        JOIN books b ON bb.bookID = b.bookID
        WHERE bb.student_email = '$email'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Borrowed Books</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #56ccf2, #2f80ed);
            color: #fff;
            padding: 30px;
            margin: 0;
        }

        h2 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 30px;
        }

        table {
            width: 95%;
            margin: auto;
            background-color: rgba(255,255,255,0.15);
            border-collapse: collapse;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: rgba(0,0,0,0.3);
        }

        tr:hover {
            background-color: rgba(255,255,255,0.2);
        }

        .status-returned {
            color: #baffba;
            font-weight: bold;
        }

        .status-pending {
            color: #ffd2d2;
            font-weight: bold;
        }

        .status-overdue {
            color: #ff9999;
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            font-size: 18px;
            margin-top: 40px;
        }
    </style>
</head>
<body>

<h2>📚 My Borrowed Books</h2>

<?php if ($result->num_rows > 0): ?>
<table>
    <tr>
        <th>Title</th>
        <th>Author</th>
        <th>Borrow Date</th>
        <th>Due Date</th>
        <th>Status</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()):
        $isOverdue = !$row['returned'] && $row['due_date'] < $today;
        $statusClass = $row['returned'] ? 'status-returned' : ($isOverdue ? 'status-overdue' : 'status-pending');
        $statusText = $row['returned'] ? '✅ Returned' : ($isOverdue ? '⚠️ Overdue' : '⏳ Pending');
    ?>
    <tr>
        <td><?= htmlspecialchars($row['title']) ?></td>
        <td><?= htmlspecialchars($row['author']) ?></td>
        <td><?= $row['borrow_date'] ?></td>
        <td><?= $row['due_date'] ?></td>
        <td class="<?= $statusClass ?>"><?= $statusText ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
    <div class="no-data">😎 You haven’t borrowed any books yet. Time to explore the library!</div>
<?php endif; ?>

</body>
</html>