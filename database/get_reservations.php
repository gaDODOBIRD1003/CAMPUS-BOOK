<?php
require_once 'db.php';

$stmt = $conn->query("
    SELECT r.reservation_id, 
           u.first_name, u.last_name,
           ro.room_number, ro.room_type,
           r.reservation_date, r.start_time, r.end_time,
           r.purpose, r.status
    FROM reservations r
    JOIN users u ON r.user_id = u.user_id
    JOIN rooms ro ON r.room_id = ro.room_id
");
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($reservations as $res) {
    echo $res['first_name'] . " " . $res['last_name'] . " reserved " . $res['room_number'] . " (" . $res['room_type'] . ") on " . $res['reservation_date'] . " from " . $res['start_time'] . " to " . $res['end_time'] . " - " . strtoupper($res['status']) . "<br>";
}
