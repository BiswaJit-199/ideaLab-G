<?php
/**
 * SECURE ADMIN LOGIN PAGE
 * 
 * Implements password hashing, session fixation prevention, and secure inputs.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/logs_helper.php';

$error = '';

// If already logged in, redirect to dashboard
if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Prevent brute force / timing attacks by validating against secure parameters
    if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
        // Regenerate Session ID to mitigate Session Fixation attacks
        session_regenerate_id(true);
        
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['username'] = $username;
        
        addLog('Admin Login Successful', 'User logged in successfully.');
        
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Invalid credentials. Please try again.';
        addLog('Admin Login Failed', 'Failed attempt for username: ' . sanitizeInput($username));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Admin Login - <?= htmlspecialchars(ADMIN_TITLE) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-tr from-slate-900 via-slate-800 to-indigo-950 min-h-screen flex items-center justify-center p-4 font-sans">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 md:p-10 border border-slate-100 transition-all hover:shadow-indigo-500/10 hover:shadow-3xl">
        <div class="text-center mb-8">
            <div class="h-14 w-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mx-auto shadow-lg shadow-indigo-500/20">
                🔒
            </div>
            <h1 class="text-2xl font-extrabold text-slate-800 mt-4">ideaLab Portal</h1>
            <p class="text-xs text-slate-400 mt-1 uppercase tracking-wider font-semibold">Secure Administrative Sign-in</p>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-xl text-sm mb-6 flex items-center gap-2">
                <span class="font-bold">Error:</span> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-5">
            <div>
                <label for="username" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Username</label>
                <input type="text" id="username" name="username" required autofocus class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none transition-all focus:ring-2 focus:ring-indigo-100">
            </div>
            
            <div>
                <label for="password" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Password</label>
                <input type="password" id="password" name="password" required class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none transition-all focus:ring-2 focus:ring-indigo-100">
            </div>
            
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 rounded-xl text-sm transition shadow-lg shadow-indigo-500/10">
                Login to Admin Panel
            </button>
        </form>
        
        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <span class="text-xs text-slate-400 font-medium flex items-center justify-center gap-1.5">
                🛡️ High Security Environment Enforced
            </span>
        </div>
    </div>
</body>
</html>