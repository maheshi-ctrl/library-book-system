<?php
session_start();
include 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (isset($_POST['add'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO books (title, author, category, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $author, $category, $status);
    $stmt->execute();
    $stmt->close();
    $msg = "✅ Book added!";
}

if (isset($_POST['update'])) {
    $id = $_POST['bookID'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE books SET title=?, author=?, category=?, status=? WHERE bookID=?");
    $stmt->bind_param("ssssi", $title, $author, $category, $status, $id);
    $stmt->execute();
    $stmt->close();
    $msg = "✏️ Book updated!";
}


if (isset($_POST['delete'])) {
    $id = $_POST['bookID'];
    $stmt = $conn->prepare("DELETE FROM books WHERE bookID=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $msg = "🗑️ Book deleted!";
}


$books = $conn->query("SELECT * FROM books");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Books</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f1f8e9;
            padding: 40px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .msg {
            text-align: center;
            font-weight: bold;
            color: green;
            margin-bottom: 15px;
        }
        .form-box, .table-box {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        input, select {
            padding: 10px;
            margin-top: 10px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            background: #2e7d32;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }
        form.inline-form {
            display: flex;
            gap: 10px;
        }
        form.inline-form input[type="submit"] {
            background: #d32f2f;
        }
    </style>
</head>
<body>

<h2>📚 Manage Books</h2>
<?php if (isset($msg)) echo "<div class='msg'>$msg</div>"; ?>

<div class="form-box">
    <form method="post">
        <input type="text" name="title" placeholder="Book Title" required>
        <input type="text" name="author" placeholder="Author" required>
        <input type="text" name="category" placeholder="Category" required>
        <select name="status" required>
            <option value="Available">Available</option>
            <option value="Borrowed">Borrowed</option>
        </select>
        <input type="submit" name="add" value="➕ Add Book">
    </form>
</div>

<div class="table-box">
    <table>
        <tr>
            <th>ID</th><th>Title</th><th>Author</th><th>Category</th><th>Status</th><th>Actions</th>
        </tr>
        <?php while ($row = $books->fetch_assoc()): ?>
        <tr>
            <form method="post" class="inline-form">
                <td><input type="hidden" name="bookID" value="<?= $row['bookID'] ?>"><?= $row['bookID'] ?></td>
                <td><input type="text" name="title" value="<?= $row['title'] ?>"></td>
                <td><input type="text" name="author" value="<?= $row['author'] ?>"></td>
                <td><input type="text" name="category" value="<?= $row['category'] ?>"></td>
                <td>
                    <select name="status">
                        <option value="Available" <?= $row['status'] === 'Available' ? 'selected' : '' ?>>Available</option>
                        <option value="Borrowed" <?= $row['status'] === 'Borrowed' ? 'selected' : '' ?>>Borrowed</option>
                    </select>
                </td>
                <td>
                    <input type="submit" name="update" value="Update">
                    <input type="submit" name="delete" value="Delete">
                </td>
            </form>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
