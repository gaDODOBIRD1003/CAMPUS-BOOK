<?php
require_once 'db.php';

$reservation_id = 1;
$status = 'approved';
$approved_by = 3; // admin user_id

$stmt = $conn->prepare("
    UPDATE reservations 
    SET status = :status, approved_by = :approved_by
    WHERE reservation_id = :reservation_id
");

$stmt->execute([
    ':status' => $status,
    ':approved_by' => $approved_by,
    ':reservation_id' => $reservation_id
]);

echo "Reservation status updated to: " . strtoupper($status);
