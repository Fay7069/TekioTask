
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?php echo $__env->yieldContent('title', 'TekioTask'); ?></title>
    <link rel="stylesheet" href="<?php echo e(asset('css/tekiotask.css')); ?>">
    <link rel="manifest" href="/manifest.json">
    <?php echo $__env->yieldContent('extra-styles'); ?>
</head>
<body class="<?php echo e(implode(' ', array_filter([
    Auth::user()->setting('high_contrast') ? 'a11y-high-contrast' : '',
    Auth::user()->setting('large_buttons') ? 'a11y-large-buttons' : '',
    'text-size-' . Auth::user()->setting('text_size', 16),
]))); ?>">

<div class="app-wrapper">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">T</div>
            <span class="brand-name">TekioTask</span>
        </div>
        <nav class="sidebar-nav">
            <a href="<?php echo e(route('admin.dashboard')); ?>"
               class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                Dashboard
            </a>
            <a href="<?php echo e(route('admin.students.index')); ?>"
               class="nav-link <?php echo e(request()->routeIs('admin.students*') ? 'active' : ''); ?>">
                Students
            </a>
            <a href="<?php echo e(route('admin.users.index')); ?>"
               class="nav-link <?php echo e(request()->routeIs('admin.users*') ? 'active' : ''); ?>">
                Manage Staff
            </a>
            <a href="<?php echo e(route('admin.reports')); ?>"
               class="nav-link <?php echo e(request()->routeIs('admin.reports*') ? 'active' : ''); ?>">
                Reports
            </a>
        </nav>
        <div class="sidebar-footer">
            <a href="<?php echo e(route('profile.index')); ?>"
               class="nav-link <?php echo e(request()->routeIs('profile.*') ? 'active' : ''); ?>">
                Profile
            </a>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="nav-link">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
            <span class="topbar-title"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></span>
            <div class="topbar-user">
                <div class="avatar"><?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?></div>
                <span class="user-name"><?php echo e(Auth::user()->name); ?></span>
                <span class="role-badge admin"><?php echo e(Auth::user()->role); ?></span>
            </div>
        </div>
        <div class="content">
            <?php if(session('success')): ?>
                <div class="alert alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert alert-error"><?php echo e(session('error')); ?></div>
            <?php endif; ?>
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
</div>

<script src="<?php echo e(asset('js/pwa-register.js')); ?>"></script>
<script src="<?php echo e(asset('js/session-heartbeat.js')); ?>"></script>
<?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\TekioTask\resources\views/layouts/admin.blade.php ENDPATH**/ ?>