<?php
session_start();
require_once '../database/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_number = $_POST['student_number'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $check = $conn->prepare("SELECT user_id FROM users WHERE email = :email");
    $check->execute([':email' => $email]);

    if ($check->rowCount() > 0) {
        $error = "Email already registered.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO users (student_number, first_name, last_name, email, password, role)
            VALUES (:student_number, :first_name, :last_name, :email, :password, 'student')
        ");
        $stmt->execute([
            ':student_number' => $student_number,
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':email' => $email,
            ':password' => $password,
        ]);
        $success = "Account created! You can now login.";
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-xl shadow p-8 mt-10">
    <h2 class="text-2xl font-bold text-blue-800 dark:text-blue-400 mb-6 text-center">Create an Account</h2>

    <?php if ($success): ?>
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4 text-sm"><?= $success ?>
            <a href="login.php" class="underline font-medium">Login here</a>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-sm"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student Number</label>
            <input type="text" name="student_number" required
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name</label>
            <input type="text" name="first_name" required
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name</label>
            <input type="text" name="last_name" required
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
            <input type="email" name="email" required
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
            <input type="password" name="password" required
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit"
            class="w-full bg-blue-800 dark:bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-medium">
            Register
        </button>
    </form>
    <p class="text-center text-sm text-gray-500 mt-4">Already have an account?
        <a href="login.php" class="text-blue-700 hover:underline">Login here</a>
    </p>
</div>

<?php include '../includes/footer.php'; ?>