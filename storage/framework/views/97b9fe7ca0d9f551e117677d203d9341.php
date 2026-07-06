<?php $__env->startSection('title', 'My Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="dashboard-grid">

    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">Today's Routine</span>
        </div>

        <?php if($hasRoutine && $totalTasks > 0): ?>

            <?php $pct = $totalTasks > 0 ? round(($completedToday / $totalTasks) * 100) : 0; ?>
            <div style="font-size:13px; color:#6b7280; margin-bottom:8px;">
                <?php echo e($completedToday); ?> of <?php echo e($totalTasks); ?> tasks completed
            </div>
            <div class="progress-bar-wrap" style="margin-bottom:20px;">
                <div class="progress-bar-fill" style="width:<?php echo e($pct); ?>%"></div>
            </div>

            <div style="margin-bottom:20px;">
                <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isDone    = $completedTaskIds->contains($task->task_id);
                        $isSkipped = $skippedTaskIds->contains($task->task_id);
                        $finished  = $isDone || $isSkipped;
                        $current   = !$finished && $index === $completedToday;
                    ?>
                    <div style="display:flex; align-items:center; gap:12px;
                                padding:10px 14px; margin-bottom:6px; border-radius:8px;
                                background: <?php echo e($isDone ? '#f0fdf4' : ($isSkipped ? '#f9fafb' : ($current ? '#eff6ff' : '#f9fafb'))); ?>;
                                border: 1px solid <?php echo e($isDone ? '#bbf7d0' : ($isSkipped ? '#e5e7eb' : ($current ? '#bfdbfe' : '#e5e7eb'))); ?>;">
                        <div style="width:24px; height:24px; border-radius:50%; flex-shrink:0;
                                    display:flex; align-items:center; justify-content:center;
                                    background: <?php echo e($isDone ? '#16a34a' : ($isSkipped ? '#9ca3af' : ($current ? '#2563eb' : '#e5e7eb'))); ?>;
                                    color: <?php echo e(($isDone || $isSkipped || $current) ? '#fff' : '#9ca3af'); ?>;
                                    font-size:12px; font-weight:700;">
                            <?php if($isDone): ?> &#10003;
                            <?php elseif($isSkipped): ?> &#8594;
                            <?php else: ?> <?php echo e($index + 1); ?>

                            <?php endif; ?>
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:14px;
                                        font-weight:<?php echo e($current ? '600' : '400'); ?>;
                                        color:<?php echo e($finished ? '#6b7280' : '#1e3a5f'); ?>;
                                        text-decoration:<?php echo e($isDone ? 'line-through' : 'none'); ?>;">
                                <?php echo e($task->title); ?>

                                <?php if($isSkipped): ?>
                                    <span style="font-size:11px; color:#9ca3af; font-weight:400;">(skipped)</span>
                                <?php endif; ?>
                            </div>
                            <div style="font-size:11px; color:#9ca3af;">
                                <?php echo e(floor($task->estimated_duration_seconds / 60)); ?>m
                                <?php if($task->estimated_duration_seconds % 60 > 0): ?>
                                    <?php echo e($task->estimated_duration_seconds % 60); ?>s
                                <?php endif; ?>
                                <?php if($task->has_micro_steps): ?>
                                    &nbsp;&middot;&nbsp;Has visual steps
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if($current): ?>
                            <span style="font-size:11px; font-weight:600; color:#2563eb;
                                         background:#dbeafe; padding:2px 8px; border-radius:12px;">
                                Current
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <?php if($completedToday >= $totalTasks): ?>
                <div class="alert alert-success mb-3">All tasks completed for today. Great job!</div>
                <a href="<?php echo e(route('student.summary')); ?>" class="btn btn-primary btn-lg">View Summary</a>
            <?php else: ?>
                <a href="<?php echo e(route('student.routine')); ?>" class="btn btn-primary btn-lg">
                    <?php echo e($completedToday > 0 ? 'Continue Routine' : 'Start Routine'); ?>

                </a>
            <?php endif; ?>

        <?php else: ?>
            <div class="empty-state">
                <p>No routine assigned for today.</p>
                <p class="text-muted" style="margin-top:8px;">
                    Your teacher hasn't assigned a routine yet. Check back later.
                </p>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">My Rewards</span>
        </div>
        <div class="points-big"><?php echo e($totalPoints); ?></div>
        <div class="points-label">pts</div>
        <?php if(count($badges) > 0): ?>
            <p style="font-size:13px; font-weight:600; color:#374151; margin:12px 0 10px;">My Badges</p>
            <div class="badges-row">
                <?php $__currentLoopData = $badges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badgeName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="badge-item">
                        <div class="badge-icon"><?php echo e(strtoupper(substr($badgeName, 0, 1))); ?></div>
                        <div class="badge-name"><?php echo e($badgeName); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <p class="text-muted" style="margin-top:12px;">Complete tasks to earn badges!</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Weekly Progress</span>
        </div>
        <?php $days = ['M','T','W','T','F','S','S']; ?>
        <div class="chart-wrap">
            <?php $__currentLoopData = $weeklyProgress; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $pct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bar-col">
                    <div class="bar <?php echo e($pct == 0 ? 'empty' : ''); ?>"
                         style="height:<?php echo e(max($pct, 4)); ?>px" title="<?php echo e($pct); ?>%"></div>
                    <span class="bar-label"><?php echo e($days[$i]); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php $goodDays = count(array_filter($weeklyProgress, fn($v) => $v >= 80)); ?>
        <div class="chart-summary">
            <?php echo e($goodDays); ?> great day<?php echo e($goodDays !== 1 ? 's' : ''); ?> this week
        </div>
    </div>

    <div class="motivation-banner col-full">
        <span class="motivation-text"><?php echo e($motivational); ?></span>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TekioTask\resources\views/student/dashboard.blade.php ENDPATH**/ ?>