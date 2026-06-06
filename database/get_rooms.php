<?php
require_once 'db.php';

$stmt = $conn->query("SELECT * FROM rooms");
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rooms as $room) {
    echo $room['room_number'] . " - " . $room['room_type'] . " (Capacity: " . $room['capacity'] . ")" . "<br>";
}
