<?php $__env->startSection('title', 'Therapist Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="dashboard-grid">

    
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">Welcome, <?php echo e(Auth::user()->name); ?></span>
            <a href="<?php echo e(route('therapist.case-notes')); ?>" class="btn btn-primary">+ New Case Note</a>
        </div>
        <p class="text-muted">
            You have <strong><?php echo e($students->count()); ?></strong> student(s) in the system.
            Case notes are confidential and not visible to parents or teachers.
        </p>
    </div>

    
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">Recent Case Notes</span>
            <a href="<?php echo e(route('therapist.case-notes')); ?>" class="btn btn-outline">View All</a>
        </div>

        <?php $__empty_1 = true; $__currentLoopData = $recentNotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div style="padding:14px 0;border-bottom:1px solid #f3f4f6;">
                <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                    <strong style="font-size:14px;"><?php echo e($note->student->name ?? '-'); ?></strong>
                    <span class="text-muted" style="font-size:12px;">
                        <?php echo e($note->created_at->format('d M Y, H:i')); ?>

                    </span>
                </div>
                <p style="font-size:13px;color:#374151;line-height:1.5;">
                    <?php echo e(Str::limit($note->content, 140)); ?>

                </p>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="empty-state">No case notes yet. <a href="<?php echo e(route('therapist.case-notes')); ?>">Write your first one</a>.</div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.therapist', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TekioTask\resources\views/therapist/dashboard.blade.php ENDPATH**/ ?>