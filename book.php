<?php

require_once('./db.php');

$id = $_GET['id'];

$stmt = $pdo->prepare('SELECT * FROM books WHERE id = :id');
$stmt->execute(['id' => $id]);
$book = $stmt->fetch();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
        <h1>
            <?= $book['title']; ?>
        </h1>

        <a href="./edit.php?id=<?= $book['id']; ?>">
            edit
        </a>
        <br><br>
        <form action="./delete.php" method="post">
            <input type="hidden" name="id" value="<?= $book['id']; ?>">
            <input type="submit" name="delete_book" value="Kustuta">
        </form>
</body>
</html>