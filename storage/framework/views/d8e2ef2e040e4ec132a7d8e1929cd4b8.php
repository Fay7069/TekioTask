<?php $__env->startSection('title', "All Done! – TekioTask"); ?>
<?php $__env->startSection('page-title', "Today's Summary"); ?>

<?php $__env->startSection('content'); ?>
<div style="max-width:500px; margin:0 auto; text-align:center;">

    <div class="card" style="padding:2rem;">

        <div style="font-size:56px; margin-bottom:12px;">✅</div>
        <h2 style="font-size:22px; font-weight:500; margin-bottom:8px;">
            All done for today!
        </h2>
        <p style="color:#6b7280; margin-bottom:24px;">
            <?php echo e(Auth::user()->name); ?>, you completed all your tasks. Great work!
        </p>

        <div style="text-align:left; margin-bottom:24px;">
            <div style="font-size:13px; font-weight:500; color:#374151; margin-bottom:10px;">
                Today's tasks:
            </div>

            <?php $__empty_1 = true; $__currentLoopData = $taskSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div style="display:flex; justify-content:space-between; align-items:center;
                        padding:8px 0; border-bottom:0.5px solid #e5e7eb; font-size:13px;">
                <span>
                    <?php if($t->status === 'completed'): ?> ✅
                    <?php elseif($t->status === 'skipped'): ?> ⏭
                    <?php elseif($t->status === 'failed'): ?> ❌
                    <?php else: ?> ⏳
                    <?php endif; ?>
                    <?php echo e($t->title); ?>

                </span>
                <span style="color:#16a34a; font-weight:500;">
                    <?php if($t->points > 0): ?> +<?php echo e($t->points); ?> pts <?php endif; ?>
                </span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p style="color:#6b7280; font-size:13px; text-align:center;">
                No tasks recorded today.
            </p>
            <?php endif; ?>
        </div>

        <div style="background:#EAF3DE; border-radius:8px; padding:12px; margin-bottom:20px;">
            <span style="font-size:14px; color:#3B6D11; font-weight:500;">
                Points earned today: +<?php echo e($totalPoints); ?> pts
            </span>
        </div>

        <a href="<?php echo e(route('student.dashboard')); ?>" class="btn btn-primary btn-full btn-lg">
            Back to Dashboard
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TekioTask\resources\views/student/summary.blade.php ENDPATH**/ ?>