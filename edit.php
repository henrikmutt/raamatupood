<?php

require_once('./db.php');

$id = $_GET['id'];

if ( isset($_POST['submit_book']) && $_POST['submit_book'] == 'Save' ) {
    
    $stmt = $pdo->prepare('UPDATE books SET title = :title WHERE id = :id');
    $stmt->execute([
        'id' => $id,
        'title' => $_POST['title']
    ]);

    header('Location: ./book.php?id=' . $id);

}

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

    <form action="./edit.php?id=<?= $book['id']; ?>" method="post">
        <input type="hidden" name="id" value="<?= $book['id']; ?>">
        <input type="text" name="title" value="<?= $book['title']; ?>" style="width: 240px;">
        <br><br>
        <input type="submit" name="submit_book" value="Save">
    </form>


    <br><br>
    <a href="./book.php?id=<?= $book['id']; ?>">
        cancel
    </a>
</body>
</html>