<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$query = "SELECT bb.borrowID, bb.student_email, b.title, bb.borrow_date, bb.due_date
          FROM borrowed_books bb
          JOIN books b ON bb.bookID = b.bookID
          WHERE bb.returned = 0";
$result = $conn->query($query);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['borrowID'])) {
    $borrowID = $_POST['borrowID'];

    // Get bookID from borrowID
    $bookQuery = "SELECT bookID FROM borrowed_books WHERE borrowID = $borrowID";
    $bookRes = $conn->query($bookQuery);
    $book = $bookRes->fetch_assoc();

    $returnUpdate = "UPDATE borrowed_books SET returned = 1 WHERE borrowID = $borrowID";
    $bookUpdate = "UPDATE books SET status = 'available' WHERE bookID = {$book['bookID']}";

    if ($conn->query($returnUpdate) && $conn->query($bookUpdate)) {
        $msg = "✅ Book marked as returned!";
    } else {
        $msg = "❌ Error updating return.";
    }

    // Refresh the list
    header("Location: return_book.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Return Book</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #e53935, #e35d5b);
            color: #fff;
            padding: 30px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 90%;
            margin: auto;
            background-color: rgba(255,255,255,0.15);
            border-collapse: collapse;
            box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        }
        th, td {
            padding: 14px;
            border-bottom: 1px solid #ccc;
        }
        th {
            background-color: rgba(0,0,0,0.2);
        }
        form {
            margin: 0;
        }
        input[type="submit"] {
            padding: 8px 14px;
            background: #fff;
            border: none;
            color: #e53935;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #ffcccc;
        }
    </style>
</head>
<body>

    <h2>📥 Return Borrowed Books</h2>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Student</th>
                <th>Book Title</th>
                <th>Borrow Date</th>
                <th>Due Date</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['student_email']) ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['borrow_date']) ?></td>
                <td><?= htmlspecialchars($row['due_date']) ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="borrowID" value="<?= $row['borrowID'] ?>">
                        <input type="submit" value="Mark as Returned">
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p style="text-align:center;">✅ All borrowed books have been returned.</p>
    <?php endif; ?>

</body>
</html>