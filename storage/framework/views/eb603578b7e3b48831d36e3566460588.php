<?php $__env->startSection('title', 'Home Task History'); ?>
<?php $__env->startSection('page-title', 'Home Task History'); ?>

<?php $__env->startSection('content'); ?>
<div style="max-width:700px;">

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Recorded Home Tasks</span>
            <a href="<?php echo e(route('parent.home-task')); ?>" class="btn btn-primary">+ Record New</a>
        </div>

        <?php if($homeTasks->count()): ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Task</th>
                            <th>Student</th>
                            <th>Notes</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $homeTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ht): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td style="font-size:12px; color:#6b7280; white-space:nowrap;">
                                    <?php echo e(\Carbon\Carbon::parse($ht->completed_date)->format('d M Y')); ?>

                                </td>
                                <td><strong><?php echo e($ht->task_name); ?></strong></td>
                                <td style="font-size:13px;"><?php echo e($ht->student_name ?? '-'); ?></td>
                                <td style="font-size:12px; color:#6b7280;">
                                    <?php echo e($ht->notes ?? '-'); ?>

                                </td>
                                <td style="white-space:nowrap;">
                                    <form method="POST"
                                          action="<?php echo e(route('parent.home-task.delete', $ht->home_task_id)); ?>"
                                          onsubmit="return confirm('Delete this home task entry?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-danger"
                                                style="font-size:11px; padding:3px 8px;">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div style="margin-top:16px;">
                <?php echo e($homeTasks->links()); ?>

            </div>
        <?php else: ?>
            <div class="empty-state">
                No home tasks recorded yet.
                <a href="<?php echo e(route('parent.home-task')); ?>" style="margin-left:6px;">Record one now</a>.
            </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.parent', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TekioTask\resources\views/parent/home-task-history.blade.php ENDPATH**/ ?>