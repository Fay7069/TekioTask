<?php $__env->startSection('title', isset($user) ? 'Edit Staff' : 'Add Staff'); ?>
<?php $__env->startSection('page-title', isset($user) ? 'Edit Staff Account' : 'Add Staff Account'); ?>

<?php $__env->startSection('content'); ?>
<div style="max-width:520px;">
    <div class="card">
        <form method="POST"
              action="<?php echo e(isset($user) ? route('admin.users.update', $user->user_id) : route('admin.users.store')); ?>">
            <?php echo csrf_field(); ?>
            <?php if(isset($user)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-error mb-3">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div>- <?php echo e($e); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name"
                       value="<?php echo e(old('name', $user->name ?? '')); ?>" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                       value="<?php echo e(old('email', $user->email ?? '')); ?>" required>
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role" required>
                    <option value="">- Select role -</option>
                    <?php $__currentLoopData = ['Administrator', 'Teacher', 'Therapist', 'Parent']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($role); ?>"
                            <?php if(old('role', $user->role ?? '') === $role): echo 'selected'; endif; ?>>
                            <?php echo e($role); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group">
                <label>Password
                    <?php if(isset($user)): ?>
                        <span style="font-weight:400; color:#9ca3af;">(leave blank to keep current)</span>
                    <?php else: ?>
                        <span style="color:#dc2626;">*</span>
                    <?php endif; ?>
                </label>
                <div style="position:relative;">
                    <input type="password" name="password" id="pw1"
                           <?php echo e(isset($user) ? '' : 'required'); ?> minlength="6">
                    <button type="button" onclick="togglePw('pw1', this)"
                            style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
                                   background:none; border:none; cursor:pointer;
                                   font-size:13px; color:#6b7280;">Show</button>
                </div>
            </div>

            <?php if(!isset($user)): ?>
            <div class="form-group">
                <label>Confirm Password <span style="color:#dc2626;">*</span></label>
                <div style="position:relative;">
                    <input type="password" name="password_confirmation" id="pw2" required>
                    <button type="button" onclick="togglePw('pw2', this)"
                            style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
                                   background:none; border:none; cursor:pointer;
                                   font-size:13px; color:#6b7280;">Show</button>
                </div>
            </div>
            <?php endif; ?>

            <div class="flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    <?php echo e(isset($user) ? 'Save Changes' : 'Create Account'); ?>

                </button>
                <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function togglePw(fieldId, btn) {
    const field = document.getElementById(fieldId);
    if (field.type === 'password') {
        field.type = 'text';
        btn.textContent = 'Hide';
    } else {
        field.type = 'password';
        btn.textContent = 'Show';
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TekioTask\resources\views/admin/user-form.blade.php ENDPATH**/ ?>