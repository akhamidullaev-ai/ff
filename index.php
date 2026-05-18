<?php
// Подключение к базе данных SQLite
$pdo = new PDO('sqlite:routers.db');
$pdo->exec("CREATE TABLE IF NOT EXISTS routers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    brand TEXT,
    model TEXT,
    price INTEGER
)");

// CREATE (Добавление роутера)
if (isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO routers (brand, model, price) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['brand'], $_POST['model'], $_POST['price']]);
    header("Location: index.php");
    exit;
}

// DELETE (Удаление роутера)
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM routers WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: index.php");
    exit;
}

// UPDATE (Обновление данных роутера)
if (isset($_POST['update'])) {
    $stmt = $pdo->prepare("UPDATE routers SET brand = ?, model = ?, price = ? WHERE id = ?");
    $stmt->execute([$_POST['brand'], $_POST['model'], $_POST['price'], $_POST['id']]);
    header("Location: index.php");
    exit;
}

// READ (Получение всех роутеров для отображения)
$routers = $pdo->query("SELECT * FROM routers")->fetchAll(PDO::FETCH_ASSOC);

// Переменная для режима редактирования
$editRouter = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM routers WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editRouter = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Router Management System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f8f9fc; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; background: white; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #0d6efd; color: white; }
        .form-container { background: white; padding: 20px; border-radius: 5px; width: 300px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input[type="text"], input[type="number"] { width: 100%; padding: 8px; margin: 5px 0 15px; box-sizing: border-box; }
        .btn { padding: 8px 15px; color: white; border: none; cursor: pointer; border-radius: 3px; text-decoration: none; font-size: 14px;}
        .btn-add { background-color: #198754; }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-delete { background-color: #dc3545; }
    </style>
</head>
<body>

<h2>Router Management System (CRUD)</h2>

<div class="form-container">
    <h3><?= $editRouter ? 'Edit Router' : 'Add Router' ?></h3>
    <form method="POST">
        <?php if ($editRouter): ?>
            <input type="hidden" name="id" value="<?= $editRouter['id'] ?>">
        <?php endif; ?>

        <label>Brand:</label>
        <input type="text" name="brand" value="<?= $editRouter['brand'] ?? '' ?>" required>

        <label>Model:</label>
        <input type="text" name="model" value="<?= $editRouter['model'] ?? '' ?>" required>

        <label>Price ($):</label>
        <input type="number" name="price" value="<?= $editRouter['price'] ?? '' ?>" required>

        <?php if ($editRouter): ?>
            <button type="submit" name="update" class="btn btn-edit">Save Changes</button>
            <a href="index.php" style="margin-left: 10px; color: #666; text-decoration: none;">Cancel</a>
        <?php else: ?>
            <button type="submit" name="add" class="btn btn-add">Add Router</button>
        <?php endif; ?>
    </form>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Brand</th>
        <th>Model</th>
        <th>Price ($)</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($routers as $r): ?>
    <tr>
        <td><?= $r['id'] ?></td>
        <td><?= htmlspecialchars($r['brand']) ?></td>
        <td><?= htmlspecialchars($r['model']) ?></td>
        <td><?= $r['price'] ?></td>
        <td>
            <a href="?edit=<?= $r['id'] ?>" class="btn btn-edit">Edit</a>
            <a href="?delete=<?= $r['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this router?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
