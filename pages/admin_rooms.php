<?php
session_start();
require_once '../database/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$success = "";
$error = "";

// Add room
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $room_number = $_POST['room_number'];
        $capacity = $_POST['capacity'];
        $room_type = $_POST['room_type'];
        $status = $_POST['status'];

        $check = $conn->prepare("SELECT room_id FROM rooms WHERE room_number = :room_number");
        $check->execute([':room_number' => $room_number]);

        if ($check->rowCount() > 0) {
            $error = "Room number already exists.";
        } else {
            $stmt = $conn->prepare("
                INSERT INTO rooms (room_number, capacity, room_type, status)
                VALUES (:room_number, :capacity, :room_type, :status)
            ");
            $stmt->execute([
                ':room_number' => $room_number,
                ':capacity' => $capacity,
                ':room_type' => $room_type,
                ':status' => $status,
            ]);
            $success = "Room added successfully!";
        }
    }

    if ($_POST['action'] === 'delete') {
        $room_id = $_POST['room_id'];
        $stmt = $conn->prepare("DELETE FROM rooms WHERE room_id = :room_id");
        $stmt->execute([':room_id' => $room_id]);
        $success = "Room deleted.";
    }

    if ($_POST['action'] === 'update_status') {
        $room_id = $_POST['room_id'];
        $status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE rooms SET status = :status WHERE room_id = :room_id");
        $stmt->execute([':status' => $status, ':room_id' => $room_id]);
        $success = "Room status updated.";
    }
}

$rooms = $conn->query("SELECT * FROM rooms ORDER BY room_number")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Manage Rooms</h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Add, update, or remove rooms</p>
    </div>
    <a href="admin.php" class="text-sm text-blue-700 dark:text-blue-400 hover:underline">← Back to Reservations</a>
</div>

<?php if ($success): ?>
    <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4 text-sm"><?= $success ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-sm"><?= $error ?></div>
<?php endif; ?>

<!-- Add Room Form -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-6">
    <h3 class="font-bold text-gray-800 dark:text-white mb-4">Add New Room</h3>
    <form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input type="hidden" name="action" value="add">
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Room Number</label>
            <input type="text" name="room_number" required
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Capacity</label>
            <input type="number" name="capacity" min="1" required
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Room Type</label>
            <select name="room_type" required
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="Classroom">Classroom</option>
                <option value="Computer Laboratory">Computer Laboratory</option>
                <option value="Auditorium">Auditorium</option>
                <option value="Conference Room">Conference Room</option>
                <option value="Study Room">Study Room</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Status</label>
            <select name="status"
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </div>
        <div class="md:col-span-4">
            <button type="submit"
                class="bg-blue-800 dark:bg-blue-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                + Add Room
            </button>
        </div>
    </form>
</div>

<!-- Rooms List -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
            <tr>
                <th class="px-6 py-3 text-left">Room Number</th>
                <th class="px-6 py-3 text-left">Type</th>
                <th class="px-6 py-3 text-left">Capacity</th>
                <th class="px-6 py-3 text-left">Status</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            <?php foreach ($rooms as $room): ?>
                <tr class="text-gray-700 dark:text-gray-300">
                    <td class="px-6 py-4 font-medium"><?= htmlspecialchars($room['room_number']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($room['room_type']) ?></td>
                    <td class="px-6 py-4"><?= $room['capacity'] ?></td>
                    <td class="px-6 py-4">
                        <form method="POST" class="flex items-center gap-2">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="room_id" value="<?= $room['room_id'] ?>">
                            <select name="status" onchange="this.form.submit()"
                                class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded px-2 py-1 text-xs">
                                <option value="available" <?= $room['status'] === 'available' ? 'selected' : '' ?>>Available</option>
                                <option value="unavailable" <?= $room['status'] === 'unavailable' ? 'selected' : '' ?>>Unavailable</option>
                                <option value="maintenance" <?= $room['status'] === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                            </select>
                        </form>
                    </td>
                    <td class="px-6 py-4">
                        <form method="POST" onsubmit="return confirm('Delete this room?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="room_id" value="<?= $room['room_id'] ?>">
                            <button type="submit" class="text-red-600 hover:underline text-xs">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>