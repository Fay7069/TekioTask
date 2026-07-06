<?php
$layout = match(Auth::user()->role) {
    'Student'       => 'layouts.student',
    'Teacher'       => 'layouts.teacher',
    'Parent'        => 'layouts.parent',
    'Administrator' => 'layouts.admin',
    'Therapist'     => 'layouts.therapist',
    default         => 'layouts.admin',
};
?>


<?php $__env->startSection('title', 'Profile & Settings'); ?>
<?php $__env->startSection('page-title', 'Profile & Settings'); ?>

<?php $__env->startSection('content'); ?>
<div style="max-width:580px;">

    
    <div class="card mb-3">
        <div class="card-header">
            <span class="card-title">Profile & Accessibility</span>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success mb-3"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('profile.update')); ?>">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

            <div class="form-group">
                <label>Full name</label>
                <input type="text" name="name"
                       value="<?php echo e(old('name', $user->name)); ?>" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" value="<?php echo e($user->email); ?>" disabled
                       style="background:#f9fafb; color:#9ca3af; cursor:not-allowed;">
                <p class="form-hint">Email cannot be changed. Contact your administrator.</p>
            </div>

            <div class="form-group">
                <label>Role</label>
                <input type="text" value="<?php echo e($user->role); ?>" disabled
                       style="background:#f9fafb; color:#9ca3af; cursor:not-allowed;">
            </div>

            <div style="border-top:1px solid #f3f4f6; margin:20px 0; padding-top:16px;">
                <div class="card-title mb-3">Accessibility</div>

                <div class="pref-row">
                    <div>
                        <div style="font-size:14px; font-weight:500;">High contrast mode</div>
                        <div class="form-hint">Increases colour contrast for better visibility.</div>
                    </div>
                    <input type="hidden" name="high_contrast" value="0">
                    <input type="checkbox" name="high_contrast" value="1"
                           <?php if(old('high_contrast', $user->setting('high_contrast'))): echo 'checked'; endif; ?>
                           style="width:20px; height:20px; accent-color:#2563eb;">
                </div>

                <div class="pref-row">
                    <div>
                        <div style="font-size:14px; font-weight:500;">Large buttons</div>
                        <div class="form-hint">Makes all buttons at least 60x60px.</div>
                    </div>
                    <input type="hidden" name="large_buttons" value="0">
                    <input type="checkbox" name="large_buttons" value="1"
                           <?php if(old('large_buttons', $user->setting('large_buttons'))): echo 'checked'; endif; ?>
                           style="width:20px; height:20px; accent-color:#2563eb;">
                </div>

                <div class="pref-row">
                    <div>
                        <div style="font-size:14px; font-weight:500;">Mute all sounds</div>
                        <div class="form-hint">Disables all audio cues in the app.</div>
                    </div>
                    <input type="hidden" name="mute_sounds" value="0">
                    <input type="checkbox" name="mute_sounds" value="1"
                           <?php if(old('mute_sounds', $user->setting('mute_sounds'))): echo 'checked'; endif; ?>
                           style="width:20px; height:20px; accent-color:#2563eb;">
                </div>

                <div class="form-group" style="margin-top:16px;">
                    <label>Text size</label>
                    <select name="text_size">
                        <?php $__currentLoopData = [14=>'14pt (Small)',16=>'16pt (Default)',18=>'18pt (Medium)',20=>'20pt (Large)',24=>'24pt (Extra Large)']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($size); ?>"
                                <?php if((int)old('text_size', $user->setting('text_size', 16)) === $size): echo 'selected'; endif; ?>>
                                <?php echo e($label); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>

    
    <div class="card">
        <div class="card-header">
            <span class="card-title">Change Password</span>
        </div>

        <?php if($errors->has('current_password')): ?>
            <div class="alert alert-error mb-3"><?php echo e($errors->first('current_password')); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('profile.password')); ?>">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

            <div class="form-group">
                <label>Current password</label>
                <div style="position:relative;">
                    <input type="password" name="current_password" id="pw_current" required>
                    <button type="button" onclick="togglePw('pw_current', this)"
                            style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
                                   background:none; border:none; cursor:pointer;
                                   font-size:13px; color:#6b7280;">Show</button>
                </div>
            </div>

            <div class="form-group">
                <label>New password</label>
                <div style="position:relative;">
                    <input type="password" name="password" id="pw_new" required minlength="6">
                    <button type="button" onclick="togglePw('pw_new', this)"
                            style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
                                   background:none; border:none; cursor:pointer;
                                   font-size:13px; color:#6b7280;">Show</button>
                </div>
            </div>

            <div class="form-group">
                <label>Confirm new password</label>
                <div style="position:relative;">
                    <input type="password" name="password_confirmation" id="pw_confirm" required>
                    <button type="button" onclick="togglePw('pw_confirm', this)"
                            style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
                                   background:none; border:none; cursor:pointer;
                                   font-size:13px; color:#6b7280;">Show</button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Change Password</button>
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

<?php echo $__env->make($layout, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TekioTask\resources\views/shared/profile.blade.php ENDPATH**/ ?>