<?php
session_start();
require_once '../database/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$room_id = $_GET['room_id'] ?? null;
if (!$room_id) {
    header("Location: dashboard.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM rooms WHERE room_id = :room_id");
$stmt->execute([':room_id' => $room_id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    header("Location: dashboard.php");
    exit();
}

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_date = $_POST['reservation_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $purpose = $_POST['purpose'];
    $attendees_count = $_POST['attendees_count'];

    // Check for conflicts
    $conflict = $conn->prepare("
        SELECT * FROM reservations 
        WHERE room_id = :room_id 
        AND reservation_date = :reservation_date
        AND status != 'cancelled'
        AND (
            (start_time < :end_time AND end_time > :start_time)
        )
    ");
    $conflict->execute([
        ':room_id' => $room_id,
        ':reservation_date' => $reservation_date,
        ':start_time' => $start_time,
        ':end_time' => $end_time,
    ]);

    if ($conflict->rowCount() > 0) {
        $error = "This room is already booked during that time. Please choose another slot.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO reservations (user_id, room_id, reservation_date, start_time, end_time, purpose, attendees_count)
            VALUES (:user_id, :room_id, :reservation_date, :start_time, :end_time, :purpose, :attendees_count)
        ");
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':room_id' => $room_id,
            ':reservation_date' => $reservation_date,
            ':start_time' => $start_time,
            ':end_time' => $end_time,
            ':purpose' => $purpose,
            ':attendees_count' => $attendees_count,
        ]);
        $success = "Reservation submitted! Waiting for admin approval.";
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="max-w-lg mx-auto bg-white dark:bg-gray-800 rounded-xl shadow p-8">
    <h2 class="text-2xl font-bold text-blue-800 dark:text-blue-400 mb-1">Book a Room</h2>
    <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">
        <?= htmlspecialchars($room['room_number']) ?> — <?= htmlspecialchars($room['room_type']) ?> (Capacity: <?= $room['capacity'] ?>)
    </p>

    <?php if ($success): ?>
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4 text-sm"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-sm"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
            <input type="date" name="reservation_date" required
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Time</label>
            <input type="time" name="start_time" required
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Time</label>
            <input type="time" name="end_time" required
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Purpose</label>
            <textarea name="purpose" required rows="3"
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Number of Attendees</label>
            <input type="number" name="attendees_count" min="1" max="<?= $room['capacity'] ?>" required
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit"
            class="w-full bg-blue-800 dark:bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-medium">
            Submit Reservation
        </button>
        <a href="dashboard.php" class="block text-center text-sm text-gray-500 dark:text-gray-400 mt-3 hover:underline">← Back to Dashboard</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>