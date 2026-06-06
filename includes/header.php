<!DOCTYPE html>
<html lang="en" id="html-root">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Book</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
        if (localStorage.getItem('theme') === 'dark') {
            document.getElementById('html-root').classList.add('dark');
        }
    </script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 min-h-screen transition-colors duration-300">
    <nav class="bg-blue-800 dark:bg-gray-800 text-white px-6 py-4 flex justify-between items-center shadow-md">
        <h1 class="text-xl font-bold tracking-wide">📚 Campus Book</h1>
        <div class="flex items-center gap-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="text-sm">Hello, <?= htmlspecialchars($_SESSION['first_name']) ?>!</span>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="<?= strpos($_SERVER['PHP_SELF'], 'pages') !== false ? '' : 'pages/' ?>admin.php"
                        class="text-sm bg-white text-blue-800 dark:bg-gray-600 dark:text-white px-3 py-1 rounded hover:opacity-80">
                        Reservations
                    </a>
                    <a href="<?= strpos($_SERVER['PHP_SELF'], 'pages') !== false ? '' : 'pages/' ?>admin_rooms.php"
                        class="text-sm bg-white text-blue-800 dark:bg-gray-600 dark:text-white px-3 py-1 rounded hover:opacity-80">
                        Manage Rooms
                    </a>
                <?php else: ?>
                    <a href="<?= strpos($_SERVER['PHP_SELF'], 'pages') !== false ? '' : 'pages/' ?>my_reservations.php"
                        class="text-sm bg-white text-blue-800 dark:bg-gray-600 dark:text-white px-3 py-1 rounded hover:opacity-80">
                        My Bookings
                    </a>
                <?php endif; ?>
                <a href="<?= strpos($_SERVER['PHP_SELF'], 'pages') !== false ? '../' : '' ?>logout.php"
                    class="bg-white text-blue-800 text-sm px-3 py-1 rounded hover:bg-gray-200">Logout</a>
            <?php endif; ?>
            <button onclick="toggleDark()" class="text-sm bg-white text-blue-800 dark:bg-gray-600 dark:text-white px-3 py-1 rounded hover:opacity-80">
                🌙 Dark
            </button>
        </div>
    </nav>
    <main class="max-w-5xl mx-auto px-4 py-8">

        <script>
            function toggleDark() {
                const html = document.getElementById('html-root');
                html.classList.toggle('dark');
                localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
                document.querySelector('button').textContent = html.classList.contains('dark') ? '☀️ Light' : '🌙 Dark';
            }
        </script>