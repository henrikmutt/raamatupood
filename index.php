<?php

require_once('./db.php');

if (isset($_GET['filter']) && $_GET['filter'] === '2') {
    // NEW FROM 2010
    $stmt = $pdo->query("SELECT * FROM books WHERE release_date >= 2010 AND type = 'new' ORDER BY title");
    $view = 'books';

} elseif (isset($_GET['filter']) && $_GET['filter'] === '3') {
    // USED < 1970 AND < 20 €
    $stmt = $pdo->query("SELECT title, release_date, type, price FROM books WHERE release_date <= 1970 AND type = 'used' AND price < 20");
    $view = 'books';

} elseif (isset($_GET['filter']) && $_GET['filter'] === '4') {
    // PER YEAR
    $stmt = $pdo->query("SELECT YEAR(order_date) AS aasta, COUNT(id) AS tellimuste_arv FROM orders GROUP BY YEAR(order_date) ORDER BY YEAR(order_date)");
    $view = 'orders';

} elseif (isset($_GET['filter']) && $_GET['filter'] === '5') {
    // COMPLETED ORDERS + TOTAL SALES PER YEAR
    $stmt = $pdo->query("SELECT YEAR(o.order_date) AS aasta,COUNT(o.id) AS tellimuste_arv,ROUND(SUM(b.price), 2) AS muukide_summa FROM orders o LEFT JOIN books b ON o.book_id = b.id WHERE o.status = 'sent' GROUP BY YEAR(o.order_date) ORDER BY YEAR(order_date)");
    $view = 'sales';

} elseif (isset($_GET['filter']) && $_GET['filter'] === '6') {
    // LAST YEAR ORDERS
    $stmt = $pdo->query("SELECT COUNT(orders.id) AS Tellimuste_arv, ROUND(SUM(books.price), 2) AS Summa FROM orders LEFT JOIN books ON orders.book_id = books.id;
    ");
    $view = 'last_year';

} elseif (isset($_GET['filter']) && $_GET['filter'] === '7') {
    // LAST YEAR CLIENT ORDERS
    $stmt = $pdo->query("SELECT first_name, last_name, ROUND(SUM(price), 2) AS summa FROM orders o LEFT JOIN clients c ON o.client_id = c.id LEFT JOIN books b ON b.id = o.book_id WHERE YEAR(order_date) = (SELECT  MAX(YEAR(order_date)) FROM orders) GROUP BY c.id ORDER BY SUM(price) DESC;;
    ");
    $view = 'clients_last_year';

} elseif (isset($_GET['filter']) && $_GET['filter'] === '8') {
    // LAST YEAR TOP 10
    $stmt = $pdo->query("SELECT title AS 'Pealkiri', COUNT(*) AS 'Müüdud' FROM orders o LEFT JOIN books b ON o.book_id = b.id WHERE YEAR(order_date) = (SELECT MAX(YEAR(order_date)) FROM orders) GROUP BY title ORDER BY COUNT(*) DESC LIMIT 10");
    $view = 'last_year_top';
    
} else {
    // DEFAULT
    $stmt = $pdo->query("SELECT * FROM books WHERE is_deleted = 0");
    $view = 'books';
}


$results = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books & Orders</title>
</head>
<body>
<div>
    <h2>yl21</h2>

    <form method="get" style="display:inline;">
        <button type="submit" name="filter" value="2">2</button>
    </form>
    <form method="get" style="display:inline;">
        <button type="submit" name="filter" value="3">3</button>
    </form>
    <form method="get" style="display:inline;">
        <button type="submit" name="filter" value="4">4</button>
    </form>
    <form method="get" style="display:inline;">
        <button type="submit" name="filter" value="5">5</button>
    </form>
    <form method="get" style="display:inline;">
        <button type="submit" name="filter" value="6">6</button>
    </form>
    <form method="get" style="display:inline;">
        <button type="submit" name="filter" value="7">7</button>
    </form>
    <form method="get" style="display:inline;">
        <button type="submit" name="filter" value="8">8</button>
    </form>
</div>

<?php if ($view === 'books'): ?>
    <ul>
        <?php foreach ($results as $book) { ?>
            <li>
                <?php if (isset($book['id'])): ?>
                    <a href="/book.php?id=<?= $book['id'] ?>">
                        <?= $book["title"] ?>
                    </a>
                <?php else: ?>
                    <?= $book["title"] ?> | <?= $book["release_date"] ?> | <?= $book["type"] ?> | €<?= $book["price"] ?>
                <?php endif; ?>
            </li>
        <?php } ?>
    </ul>

<?php elseif ($view === 'orders'): ?>
    <h3>Tellimuste arv aastate lõikes</h3>
    <table border="1" cellpadding="5">
        <tr>
            <th>Aasta</th>
            <th>Tellimuste arv</th>
        </tr>
        <?php foreach ($results as $row) { ?>
            <tr>
                <td><?= $row["aasta"] ?></td>
                <td><?= $row["tellimuste_arv"] ?></td>
            </tr>
        <?php } ?>
    </table>

<?php elseif ($view === 'sales'): ?>
    <h3>Täidetud tellimuste arv ja müükide summa aastate lõikes</h3>
    <table border="1" cellpadding="5">
        <tr>
            <th>Aasta</th>
            <th>Tellimuste arv</th>
            <th>Müükide summa</th>
        </tr>
        <?php foreach ($results as $row) { ?>
            <tr>
                <td><?= $row["aasta"] ?></td>
                <td><?= $row["tellimuste_arv"] ?></td>
                <td><?= number_format($row["muukide_summa"], 2, ',', ' ') ?></td>
            </tr>
        <?php } ?>
    </table>

<?php elseif ($view === 'last_year'): ?>
<h3>Täidetud tellimuste arv viimase aasta jooksul ja müükide summa</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>Tellimuste arv</th>
        <th>Summa</th>
    </tr>
    <?php foreach ($results as $row) { ?>
        <tr>
            <td><?= $row["Tellimuste_arv"] ?></td>
            <td><?= number_format($row["Summa"], 2, ',', ' ') ?></td>
        </tr>
    <?php } ?>
</table>

<?php elseif ($view === 'clients_last_year'): ?>
<h3>Kliendid viimase aasta jooksul tehtud tellimuste põhjal kulutatud summa järgi</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>Eesnimi</th>
        <th>Perenimi</th>
        <th>Summa</th>
    </tr>
    <?php foreach ($results as $row) { ?>
        <tr>
            <td><?= $row["first_name"] ?></td>
            <td><?= $row["last_name"] ?></td>
            <td><?= number_format($row["summa"], 2, ',', ' ') ?></td>
        </tr>
    <?php } ?>
</table>

<?php elseif ($view === 'last_year_top'): ?>
<h3>Viimase aasta top 10 enim müüdud raamatud</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>Pealkiri</th>
        <th>Müüdud</th>
    </tr>
    <?php foreach ($results as $row) { ?>
        <tr>
            <td><?= $row["Pealkiri"] ?></td>
            <td><?= $row["Müüdud"] ?></td>
        </tr>
    <?php } ?>
</table>

<?php endif; ?>


</body>
</html>
