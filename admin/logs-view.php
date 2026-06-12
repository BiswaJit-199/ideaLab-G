<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/logs_helper.php';
requireAdminLogin();

$pageTitle = "System Action Logs - " . ADMIN_TITLE;
$activePage = 'logs';

$logsFile = dirname(__DIR__) . '/data/logs.json';
$logs = [];
if (file_exists($logsFile)) {
    $logs = json_decode(file_get_contents($logsFile), true) ?? [];
}

// Simple search filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search !== '') {
    $filteredLogs = [];
    foreach ($logs as $log) {
        if (
            stripos($log['user'], $search) !== false ||
            stripos($log['action'], $search) !== false ||
            stripos($log['details'], $search) !== false ||
            stripos($log['timestamp'], $search) !== false
        ) {
            $filteredLogs[] = $log;
        }
    }
    $logs = $filteredLogs;
}

// Pagination setup
$limit = 20;
$totalItems = count($logs);
$totalPages = ceil($totalItems / $limit);
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
if ($page > $totalPages && $totalPages > 0) $page = $totalPages;
$offset = ($page - 1) * $limit;
$paginatedLogs = array_slice($logs, $offset, $limit);

include __DIR__ . '/header.php';
?>

<div class="space-y-6">
    <!-- Header Page -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">System Logs</h2>
            <p class="text-sm text-slate-500 mt-1">Audit and track administrative actions, edits, file changes, and security events in real-time.</p>
        </div>
        
        <!-- Search bar -->
        <form method="GET" class="w-full sm:w-auto flex gap-2">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search logs..." class="w-full sm:w-64 rounded-xl border border-slate-200 p-2.5 text-sm focus:border-indigo-500 focus:outline-none">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-xl text-sm transition-all">
                Search
            </button>
            <?php if ($search !== ''): ?>
                <a href="logs-view.php" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-2 px-4 rounded-xl text-sm transition-all flex items-center">
                    Clear
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs text-slate-500 uppercase font-semibold text-left">
                        <th class="p-4">Timestamp</th>
                        <th class="p-4">User</th>
                        <th class="p-4">Action</th>
                        <th class="p-4">Details</th>
                        <th class="p-4">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php if (empty($paginatedLogs)): ?>
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400">No logs found matching criteria.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($paginatedLogs as $log): ?>
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-4 whitespace-nowrap text-xs text-slate-500"><?= htmlspecialchars($log['timestamp']) ?></td>
                                <td class="p-4 whitespace-nowrap">
                                    <span class="inline-block px-2 py-0.5 rounded bg-slate-100 text-xs font-bold text-slate-600 uppercase">
                                        <?= htmlspecialchars($log['user']) ?>
                                    </span>
                                </td>
                                <td class="p-4 whitespace-nowrap font-bold text-slate-800 text-xs"><?= htmlspecialchars($log['action']) ?></td>
                                <td class="p-4 text-slate-600 max-w-md break-words"><?= htmlspecialchars($log['details']) ?></td>
                                <td class="p-4 whitespace-nowrap text-xs font-mono text-slate-400"><?= htmlspecialchars($log['ip']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <?php if ($totalPages > 1): ?>
            <div class="p-4 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-400">Showing <?= $offset + 1 ?> to <?= min($offset + $limit, $totalItems) ?> of <?= $totalItems ?> logs</span>
                <div class="flex gap-1.5">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="px-3 py-1.5 rounded border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">&larr; Prev</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="px-3 py-1.5 rounded text-xs font-semibold <?= $page == $i ? 'bg-indigo-600 text-white border border-indigo-600' : 'border border-slate-200 text-slate-600 hover:bg-slate-50' ?> transition"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="px-3 py-1.5 rounded border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">Next &rarr;</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>