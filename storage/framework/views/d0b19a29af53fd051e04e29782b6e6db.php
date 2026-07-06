<?php $__env->startSection('title', 'My Rewards'); ?>
<?php $__env->startSection('page-title', 'My Rewards'); ?>

<?php $__env->startSection('content'); ?>
<div class="dashboard-grid">

    
    <div class="card" style="text-align:center;">
        <div class="card-header">
            <span class="card-title">Total Points</span>
        </div>
        <div class="points-big"><?php echo e($totalPoints); ?></div>
        <div class="points-label">pts earned</div>

        <?php
            $nextBadgeAt = 50;
            $pct = min(round(($totalPoints / $nextBadgeAt) * 100), 100);
        ?>
        <p style="font-size:12px; color:#6b7280; margin:12px 0 6px;">
            Progress to next reward
        </p>
        <div class="progress-bar-wrap">
            <div class="progress-bar-fill" style="width:<?php echo e($pct); ?>%"></div>
        </div>
        <p class="text-muted"><?php echo e($totalPoints); ?> / <?php echo e($nextBadgeAt); ?> pts</p>
    </div>

    
    <div class="card">
        <div class="card-header">
            <span class="card-title">My Badges</span>
        </div>

        <?php if(count($badges) > 0): ?>
            <div class="badges-row" style="flex-wrap:wrap; gap:16px;">
                <?php $__currentLoopData = $badges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badgeName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="badge-item">
                        <div class="badge-icon" style="width:56px; height:56px; font-size:20px;">
                            <?php echo e(strtoupper(substr($badgeName, 0, 1))); ?>

                        </div>
                        <div class="badge-name" style="font-size:12px;"><?php echo e($badgeName); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>No badges yet!</p>
                <p class="text-muted" style="margin-top:8px;">
                    Complete tasks to earn your first badge.
                </p>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">How to Earn</span>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; text-align:center;">
            <div style="padding:16px; background:#eff6ff; border-radius:10px;">
                <div style="font-size:14px; font-weight:600; color:#1e3a5f;">Complete a task</div>
                <div style="font-size:20px; font-weight:800; color:#2563eb; margin-top:6px;">+10 pts</div>
            </div>
            <div style="padding:16px; background:#f0fdf4; border-radius:10px;">
                <div style="font-size:14px; font-weight:600; color:#1e3a5f;">First task ever</div>
                <div style="font-size:13px; color:#16a34a; font-weight:600; margin-top:6px;">First Task badge</div>
            </div>
            <div style="padding:16px; background:#fefce8; border-radius:10px;">
                <div style="font-size:14px; font-weight:600; color:#1e3a5f;">Complete 10 tasks</div>
                <div style="font-size:13px; color:#d97706; font-weight:600; margin-top:6px;">Ten Done badge</div>
            </div>
        </div>
    </div>

    
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">Completed Task History</span>
        </div>

        <?php if($taskHistory->count()): ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Task</th>
                            <th>Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $taskHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td style="font-size:12px; color:#6b7280;">
                                    <?php echo e($log->attempt_timestamp->format('d M Y, H:i')); ?>

                                </td>
                                <td><?php echo e($log->task->title ?? '-'); ?></td>
                                <td style="color:#16a34a; font-weight:600;">+10 pts</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">No completed tasks yet. Start your routine!</div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TekioTask\resources\views/student/rewards.blade.php ENDPATH**/ ?>