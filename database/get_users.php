<?php
require_once 'db.php';

$stmt = $conn->query("SELECT user_id, student_number, first_name, last_name, email, role FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    echo $user['student_number'] . " - " . $user['first_name'] . " " . $user['last_name'] . " (" . $user['role'] . ")" . "<br>";
}
