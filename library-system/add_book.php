<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $category = $conn->real_escape_string($_POST['category']);

    $sql = "INSERT INTO books (title, author, category) VALUES ('$title', '$author', '$category')";
    if ($conn->query($sql)) {
        $msg = "✅ Book added successfully!";
    } else {
        $msg = "❌ Error adding book.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Book</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1d976c, #93f9b9);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .box {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(12px);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            color: #fff;
            width: 350px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: none;
            border-radius: 8px;
            background-color: rgba(255,255,255,0.9);
            color: #333;
        }

        input[type="submit"] {
            margin-top: 20px;
            background-color: #007acc;
            color: white;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #005fa3;
        }

        .msg {
            margin-top: 15px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="box">
        <h2>📘 Add a Book</h2>
        <?php if (isset($msg)) echo "<div class='msg'>$msg</div>"; ?>
        <form method="post">
            <label>Title</label>
            <input type="text" name="title" required>

            <label>Author</label>
            <input type="text" name="author" required>

            <label>Category</label>
            <input type="text" name="category" required>

            <input type="submit" value="Add Book">
        </form>
    </div>

</body>
</html>