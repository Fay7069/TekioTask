<?php $__env->startSection('title', 'Staff Management'); ?>
<?php $__env->startSection('page-title', 'Staff Management'); ?>

<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="page-actions">
    <a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-primary">+ Add Staff</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><strong><?php echo e($user->name); ?></strong></td>
                        <td><?php echo e($user->email); ?></td>
                        <td>
                            <span class="status-pill progress"><?php echo e($user->role); ?></span>
                        </td>
                        <td>
                            <div class="flex gap-1">
                                <a href="<?php echo e(route('admin.users.edit', $user->user_id)); ?>"
                                   class="btn btn-outline" style="font-size:12px; padding:4px 8px;">
                                    Edit
                                </a>
                                <form method="POST"
                                      action="<?php echo e(route('admin.users.destroy', $user->user_id)); ?>"
                                      onsubmit="return confirm('Delete <?php echo e($user->name); ?>?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-danger"
                                            style="font-size:12px; padding:4px 8px;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" style="text-align:center; color:#9ca3af; padding:20px;">
                            No staff accounts yet.
                            <a href="<?php echo e(route('admin.users.create')); ?>">Add the first one</a>.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TekioTask\resources\views/admin/users.blade.php ENDPATH**/ ?>