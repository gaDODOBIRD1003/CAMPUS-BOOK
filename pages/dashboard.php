<?php
session_start();
require_once '../database/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $conn->query("SELECT * FROM rooms WHERE status = 'available'");
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Available Rooms</h2>
    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Select a room to book</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php foreach ($rooms as $room): ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 hover:shadow-lg transition">
            <h3 class="text-lg font-bold text-blue-800 dark:text-blue-400"><?= htmlspecialchars($room['room_number']) ?></h3>
            <p class="text-gray-600 dark:text-gray-300 text-sm mt-1"><?= htmlspecialchars($room['room_type']) ?></p>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Capacity: <?= $room['capacity'] ?> people</p>
            <a href="book.php?room_id=<?= $room['room_id'] ?>"
                class="mt-4 block text-center bg-blue-800 dark:bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                Book This Room
            </a>
        </div>
    <?php endforeach; ?>
</div>

<?php include '../includes/footer.php'; ?>