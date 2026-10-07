<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/api/db.php';
require_once __DIR__ . '/api/auth.php'; // Gives us checkPassword function

if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: ' . url('admin/index.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } else {
        $admin = getAdminByUsername($username);
        if ($admin && checkPassword($password, $admin['password_hash'])) {
            startAppSession();
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: ' . url('admin/index.php'));
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

$settings = getPublicSettings();
$currentPage = 'admin_login';
require __DIR__ . '/includes/header.php';
?>

<div class="min-h-[70vh] flex items-center justify-center py-12">
    <div
        class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-gray-100 p-8 space-y-6 text-center relative">
        <div class="absolute -top-10 left-1/2 transform -translate-x-1/2">
            <div class="bg-[var(--primary-color)] text-white p-4 rounded-2xl shadow-lg shrink-0">
                <?php if (!empty($settings['favicon'])): ?>
                    <img src="<?= e($settings['favicon']) ?>" alt="Favicon" class="w-8 h-8 object-cover rounded">
                <?php else: ?>
                    <i data-lucide="lock" class="w-8 h-8"></i>
                <?php endif; ?>
            </div>
        </div>
        <div class="pt-6">
            <h2 class="text-2xl font-bold text-[var(--secondary-color)] font-heading">
                <?php
                if (!empty($settings['logo'])) {
                    if (preg_match('/\.(jpg|jpeg|png|gif|svg|webp)$/i', $settings['logo'])) {
                        echo '<img src="' . e($settings['logo']) . '" alt="Logo" class="h-10 mx-auto object-contain mb-2">';
                        echo 'Admin Login';
                    } else {
                        echo e($settings['logo']) . ' Admin';
                    }
                } else {
                    echo 'Admin Login';
                }
                ?>
            </h2>
            <p class="text-xs text-gray-400 mt-1">Sign in with your administrator username and password.</p>
        </div>

        <?php if ($error): ?>
            <div class="p-3 bg-red-50 text-red-700 text-xs rounded-lg text-left border-l-4 border-red-500">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-4">
            <input type="text" name="username" autocomplete="username" placeholder="Username" required
                class="w-full p-3.5 border border-gray-200 rounded-xl text-sm bg-gray-50/50">
            <input type="password" name="password" autocomplete="current-password" placeholder="Password" required
                class="w-full p-3.5 border border-gray-200 rounded-xl text-sm bg-gray-50/50">
            <button type="submit"
                class="w-full bg-gray-900 text-white font-bold p-3.5 rounded-xl text-sm cursor-pointer hover:bg-black">
                Sign In
            </button>
        </form>
    </div>
</div>

<?php
require __DIR__ . '/includes/footer.php';
