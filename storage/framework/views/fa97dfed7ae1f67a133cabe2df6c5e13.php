<?php $__env->startSection('title', 'No Routine – TekioTask'); ?>
<?php $__env->startSection('page-title', 'Active Task'); ?>

<?php $__env->startSection('content'); ?>
<div style="max-width:480px; margin:0 auto; text-align:center; padding-top:40px;">
    <div class="card">
        <div style="font-size:64px; margin-bottom:16px;">📭</div>
        <h2 style="font-size:20px; font-weight:700; color:#1e3a5f; margin-bottom:8px;">
            No active task right now
        </h2>
        <p style="color:#6b7280; font-size:14px; line-height:1.6; margin-bottom:24px;">
            Your teacher hasn't started a routine yet today.
            Sit tight — you'll be notified when it's time to begin!
        </p>
        <a href="<?php echo e(route('student.dashboard')); ?>" class="btn btn-primary btn-full">
            ← Back to Dashboard
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TekioTask\resources\views/student/no-routine.blade.php ENDPATH**/ ?>