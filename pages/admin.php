<?php
session_start();
require_once '../database/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_id = $_POST['reservation_id'];
    $action = $_POST['action'];
    $remarks = $_POST['remarks'] ?? '';

    $status = $action === 'approve' ? 'approved' : 'rejected';

    $stmt = $conn->prepare("
        UPDATE reservations 
        SET status = :status, approved_by = :approved_by, remarks = :remarks
        WHERE reservation_id = :reservation_id
    ");
    $stmt->execute([
        ':status' => $status,
        ':approved_by' => $_SESSION['user_id'],
        ':remarks' => $remarks,
        ':reservation_id' => $reservation_id
    ]);
}

$stmt = $conn->query("
    SELECT r.reservation_id, u.first_name, u.last_name, u.student_number,
           ro.room_number, ro.room_type,
           r.reservation_date, r.start_time, r.end_time,
           r.purpose, r.attendees_count, r.status, r.remarks
    FROM reservations r
    JOIN users u ON r.user_id = u.user_id
    JOIN rooms ro ON r.room_id = ro.room_id
    ORDER BY r.reservation_date DESC
");
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Admin — All Reservations</h2>
    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Review and manage booking requests</p>
</div>

<div class="space-y-4">
    <?php foreach ($reservations as $res): ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex justify-between items-start flex-wrap gap-4">
                <div>
                    <h3 class="font-bold text-blue-800 dark:text-blue-400">
                        <?= htmlspecialchars($res['room_number']) ?> — <?= htmlspecialchars($res['room_type']) ?>
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                        <?= $res['reservation_date'] ?> | <?= $res['start_time'] ?> - <?= $res['end_time'] ?>
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        By: <?= htmlspecialchars($res['first_name'] . ' ' . $res['last_name']) ?> (<?= $res['student_number'] ?>)
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Purpose: <?= htmlspecialchars($res['purpose']) ?></p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Attendees: <?= $res['attendees_count'] ?></p>
                    <?php if ($res['remarks']): ?>
                        <p class="text-sm text-gray-400 mt-1">Remarks: <?= htmlspecialchars($res['remarks']) ?></p>
                    <?php endif; ?>
                </div>
                <span class="text-xs font-semibold px-3 py-1 rounded-full h-fit
                <?php echo match ($res['status']) {
                    'approved' => 'bg-green-100 text-green-700',
                    'rejected' => 'bg-red-100 text-red-700',
                    'cancelled' => 'bg-gray-100 text-gray-500',
                    default => 'bg-yellow-100 text-yellow-700'
                }; ?>">
                    <?= strtoupper($res['status']) ?>
                </span>
            </div>

            <?php if ($res['status'] === 'pending'): ?>
                <form method="POST" class="mt-4 flex flex-wrap gap-3 items-end">
                    <input type="hidden" name="reservation_id" value="<?= $res['reservation_id'] ?>">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Remarks (optional)</label>
                        <input type="text" name="remarks" placeholder="Add a note..."
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit" name="action" value="approve"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition">
                        ✅ Approve
                    </button>
                    <button type="submit" name="action" value="reject"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition">
                        ❌ Reject
                    </button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<?php include '../includes/footer.php'; ?>