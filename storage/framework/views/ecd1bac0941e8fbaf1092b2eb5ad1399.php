<?php $__env->startSection('title', 'Record Home Task'); ?>
<?php $__env->startSection('page-title', 'Record Home Task'); ?>

<?php $__env->startSection('content'); ?>
<div style="max-width:520px;">

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-error">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div>- <?php echo e($e); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    
    <div class="card mb-3">
        <div class="card-header">
            <span class="card-title">Log a Home Task</span>
        </div>

        <form method="POST" action="<?php echo e(route('parent.home-task.store')); ?>">
            <?php echo csrf_field(); ?>

            <?php if($children->count() > 1): ?>
                <div class="form-group">
                    <label>Child</label>
                    <select name="student_id" required>
                        <option value="">- Select child -</option>
                        <?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->user_id); ?>"
                                <?php if(old('student_id') == $c->user_id): echo 'selected'; endif; ?>>
                                <?php echo e($c->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php else: ?>
                <input type="hidden" name="student_id"
                       value="<?php echo e($children->first()?->user_id); ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Task Name</label>
                <input type="text" name="task_name"
                       value="<?php echo e(old('task_name')); ?>"
                       placeholder="e.g. Brushed teeth before bed" required>
            </div>

            <div class="form-group">
                <label>Date Completed</label>
                <input type="date" name="completed_date"
                       value="<?php echo e(old('completed_date', today()->format('Y-m-d'))); ?>"
                       max="<?php echo e(today()->format('Y-m-d')); ?>" required>
            </div>

            <div class="form-group">
                <label>Notes <span style="font-weight:400; color:#9ca3af;">(optional)</span></label>
                <textarea name="notes" rows="3"
                          placeholder="Any observations about how the task went..."><?php echo e(old('notes')); ?></textarea>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary">Save Task</button>
                <a href="<?php echo e(route('parent.home-task.history')); ?>" class="btn btn-outline">
                    View History
                </a>
            </div>
        </form>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.parent', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TekioTask\resources\views/parent/home-task.blade.php ENDPATH**/ ?>