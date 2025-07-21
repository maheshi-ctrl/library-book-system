<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$today = date('Y-m-d');

$query = "SELECT bb.student_email, b.title, bb.due_date 
          FROM borrowed_books bb 
          JOIN books b ON bb.bookID = b.bookID 
          WHERE bb.returned = 0 AND bb.due_date < '$today'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Overdue Books</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(to right, #e96443, #904e95); color: #fff; padding: 30px; }
        h2 { text-align: center; margin-bottom: 25px; }
        table { width: 90%; margin: auto; background: rgba(255,255,255,0.2); border-collapse: collapse; }
        th, td { padding: 14px; text-align: left; border-bottom: 1px solid #ccc; }
        th { background: rgba(0,0,0,0.2); }
        tr:hover { background: rgba(255,255,255,0.3); }
        .due { color: #ffdddd; font-weight: bold; }
    </style>
</head>
<body>

<h2>⏰ Overdue Books</h2>

<?php if ($result->num_rows > 0): ?>
    <table>
        <tr><th>Student Email</th><th>Book Title</th><th>Due Date</th></tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['student_email'] ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td class="due"><?= $row['due_date'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p style="text-align:center; font-weight:bold;">✅ All borrowed books are currently within due date.</p>
<?php endif; ?>

</body>
</html>