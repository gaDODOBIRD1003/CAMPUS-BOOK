<?php
session_start();
require_once '../database/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Delete handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $reservation_id = $_POST['reservation_id'];
    $stmt = $conn->prepare("DELETE FROM reservations WHERE reservation_id = :reservation_id AND user_id = :user_id");
    $stmt->execute([
        ':reservation_id' => $reservation_id,
        ':user_id' => $_SESSION['user_id']
    ]);
    header("Location: my_reservations.php");
    exit();
}

$stmt = $conn->prepare("
    SELECT r.reservation_id, ro.room_number, ro.room_type,
           r.reservation_date, r.start_time, r.end_time,
           r.purpose, r.attendees_count, r.status, r.remarks
    FROM reservations r
    JOIN rooms ro ON r.room_id = ro.room_id
    WHERE r.user_id = :user_id
    ORDER BY r.reservation_date DESC
");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">My Reservations</h2>
    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Track your booking requests</p>
</div>

<?php if (empty($reservations)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 text-center text-gray-500 dark:text-gray-400">
        No reservations yet. <a href="dashboard.php" class="text-blue-700 hover:underline">Book a room!</a>
    </div>
<?php else: ?>
    <div class="space-y-4">
        <?php foreach ($reservations as $res): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-bold text-blue-800 dark:text-blue-400"><?= htmlspecialchars($res['room_number']) ?> — <?= htmlspecialchars($res['room_type']) ?></h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                            <?= $res['reservation_date'] ?> | <?= $res['start_time'] ?> - <?= $res['end_time'] ?>
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Purpose: <?= htmlspecialchars($res['purpose']) ?></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Attendees: <?= $res['attendees_count'] ?></p>
                        <?php if ($res['remarks']): ?>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Remarks: <?= htmlspecialchars($res['remarks']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <span class="text-xs font-semibold px-3 py-1 rounded-full 
                        <?php echo match ($res['status']) {
                            'approved' => 'bg-green-100 text-green-700',
                            'rejected' => 'bg-red-100 text-red-700',
                            'cancelled' => 'bg-gray-100 text-gray-500',
                            default => 'bg-yellow-100 text-yellow-700'
                        }; ?>">
                            <?= strtoupper($res['status']) ?>
                        </span>
                        <form method="POST" onsubmit="return confirm('Delete this reservation?')">
                            <input type="hidden" name="reservation_id" value="<?= $res['reservation_id'] ?>">
                            <button type="submit" name="action" value="delete"
                                class="text-xs text-red-500 hover:underline">
                                🗑 Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>