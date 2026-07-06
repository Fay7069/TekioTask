<?php $__env->startSection('title', 'Badge Manager'); ?>
<?php $__env->startSection('page-title', 'Badge Manager'); ?>

<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if($errors->any()): ?>
    <div class="alert alert-error"><?php echo e($errors->first()); ?></div>
<?php endif; ?>

<div style="max-width:700px;">

    
    <div class="card mb-3">
        <div class="card-header">
            <span class="card-title">Create New Badge</span>
        </div>
        <form method="POST" action="<?php echo e(route('teacher.badges.store')); ?>">
            <?php echo csrf_field(); ?>
            <div style="display:grid; grid-template-columns:1fr 80px 120px auto; gap:12px; align-items:end;">
                <div class="form-group" style="margin:0;">
                    <label>Badge Name</label>
                    <input type="text" name="name"
                           value="<?php echo e(old('name')); ?>"
                           placeholder="e.g. Star Performer" required>
                </div>
                <div class="form-group" style="margin:0;">
                    <label>Icon</label>
                    <input type="text" name="icon_letter"
                           value="<?php echo e(old('icon_letter')); ?>"
                           placeholder="S" maxlength="1"
                           style="text-align:center; text-transform:uppercase;" required>
                </div>
                <div class="form-group" style="margin:0;">
                    <label>After N tasks</label>
                    <input type="number" name="threshold"
                           value="<?php echo e(old('threshold')); ?>"
                           placeholder="e.g. 15" min="1" max="999" required>
                </div>
                <div style="padding-bottom:1px;">
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </div>
            <p style="font-size:12px; color:#9ca3af; margin-top:8px;">
                The badge is awarded automatically when a student completes that many tasks total.
            </p>
        </form>
    </div>

    
    <div class="card">
        <div class="card-header">
            <span class="card-title">Your Badges</span>
        </div>

        <?php $__empty_1 = true; $__currentLoopData = $badges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div style="display:flex; align-items:center; gap:16px;
                    padding:14px 0; border-bottom:1px solid #f3f4f6;">
            <div style="width:44px; height:44px; border-radius:50%;
                        background:#dbeafe; color:#2563eb;
                        display:flex; align-items:center; justify-content:center;
                        font-size:18px; font-weight:800; flex-shrink:0;">
                <?php echo e($badge->icon_letter); ?>

            </div>
            <div style="flex:1;">
                <div style="font-size:15px; font-weight:600; color:#1e3a5f;">
                    <?php echo e($badge->name); ?>

                </div>
                <div style="font-size:12px; color:#6b7280;">
                    Awarded after <?php echo e($badge->threshold); ?> completed tasks
                </div>
            </div>
            <form method="POST"
                  action="<?php echo e(route('teacher.badges.destroy', $badge->badge_id)); ?>"
                  onsubmit="return confirm('Delete badge <?php echo e(addslashes($badge->name)); ?>?')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-danger"
                        style="font-size:12px; padding:4px 10px;">Delete</button>
            </form>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p style="color:#9ca3af; font-size:13px; padding:12px 0;">
            No badges yet. Create one above.
        </p>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.teacher', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TekioTask\resources\views/teacher/badges.blade.php ENDPATH**/ ?>