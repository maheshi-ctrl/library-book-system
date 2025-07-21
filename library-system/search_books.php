<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: index.php");
    exit();
}

$results = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search = $conn->real_escape_string($_POST['query']);
    $sql = "SELECT * FROM books 
            WHERE status = 'available' AND 
            (title LIKE '%$search%' OR author LIKE '%$search%' OR category LIKE '%$search%')";
    $results = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Books</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #ff6a00, #ee0979);
            color: #fff;
            padding: 40px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .search-box {
            text-align: center;
            margin-bottom: 30px;
        }
        input[type="text"] {
            width: 60%;
            max-width: 400px;
            padding: 12px;
            border-radius: 10px;
            border: none;
            font-size: 16px;
        }
        input[type="submit"] {
            padding: 12px 20px;
            margin-left: 10px;
            background-color: #fff;
            color: #ee0979;
            font-weight: bold;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #ffe6ed;
        }
        table {
            width: 90%;
            margin: auto;
            background: rgba(255,255,255,0.15);
            border-collapse: collapse;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #fff;
        }
        th {
            background-color: rgba(0,0,0,0.2);
        }
        tr:hover {
            background-color: rgba(255,255,255,0.25);
        }
        .no-results {
            text-align: center;
            font-weight: bold;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<h2>🔍 Search Available Books</h2>

<div class="search-box">
    <form method="post">
        <input type="text" name="query" placeholder="Type title, author, or category" required>
        <input type="submit" value="Search">
    </form>
</div>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <?php if ($results && $results->num_rows > 0): ?>
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
        <div class="no-results">😢 No available books match “<?= htmlspecialchars($_POST['query']) ?>”</div>
    <?php endif; ?>
<?php endif; ?>

</body>
</html>