<?php
require_once 'db.php';

$user_id = 1;
$room_id = 2;
$reservation_date = '2025-06-15';
$start_time = '10:00:00';
$end_time = '12:00:00';
$purpose = 'Test Reservation';
$attendees_count = 10;

$stmt = $conn->prepare("
    INSERT INTO reservations (user_id, room_id, reservation_date, start_time, end_time, purpose, attendees_count)
    VALUES (:user_id, :room_id, :reservation_date, :start_time, :end_time, :purpose, :attendees_count)
");

$stmt->execute([
    ':user_id' => $user_id,
    ':room_id' => $room_id,
    ':reservation_date' => $reservation_date,
    ':start_time' => $start_time,
    ':end_time' => $end_time,
    ':purpose' => $purpose,
    ':attendees_count' => $attendees_count
]);

echo "Reservation added successfully!";
