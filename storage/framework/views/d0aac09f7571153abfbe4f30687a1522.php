<?php $__env->startSection('title', 'Student Profiles'); ?>
<?php $__env->startSection('page-title', 'Student Profiles'); ?>

<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if($errors->any()): ?>
    <div class="alert alert-error"><?php echo e($errors->first()); ?></div>
<?php endif; ?>

<div class="page-actions">
    <a href="<?php echo e(route('admin.students.create')); ?>" class="btn btn-primary">+ Add Student</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Diagnosis</th>
                    <th>Accessibility</th>
                    <th>Email</th>
                    <th>Linked Parent</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $a = $student->accessibility_settings ?? []; ?>
                    <tr>
                        <td><strong><?php echo e($student->name); ?></strong></td>
                        <td><?php echo e($student->age ?? '-'); ?></td>
                        <td>
                            <?php if($student->diagnosis): ?>
                                <span class="status-pill progress"><?php echo e($student->diagnosis); ?></span>
                            <?php else: ?> - <?php endif; ?>
                        </td>
                        <td style="font-size:12px; color:#6b7280;">
                            <?php if(!empty($a['large_buttons'])): ?> Large &nbsp; <?php endif; ?>
                            <?php if(!empty($a['high_contrast'])): ?> Contrast &nbsp; <?php endif; ?>
                            <?php if(!empty($a['mute_sounds'])): ?>   Muted <?php endif; ?>
                            <?php if(empty($a)): ?> - <?php endif; ?>
                        </td>
                        <td style="font-size:13px;"><?php echo e($student->email); ?></td>
                        <td style="font-size:12px;">
                            <?php $__empty_2 = true; $__currentLoopData = $student->linked_parents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                <div style="margin-bottom:2px;">
                                    <strong><?php echo e($parent->name); ?></strong><br>
                                    <span style="color:#6b7280;"><?php echo e($parent->email); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                <span style="color:#9ca3af;">Not linked</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="flex gap-1">
                                <a href="<?php echo e(route('admin.students.edit', $student->user_id)); ?>"
                                   class="btn btn-outline" style="font-size:12px; padding:4px 8px;">
                                    Edit
                                </a>
                                <button class="btn btn-outline" style="font-size:12px; padding:4px 8px;"
                                        onclick="openLinkModal(<?php echo e($student->user_id); ?>, '<?php echo e(addslashes($student->name)); ?>')">
                                    Link Parent
                                </button>
                                <form method="POST"
                                      action="<?php echo e(route('admin.students.destroy', $student->user_id)); ?>"
                                      onsubmit="return confirm('Delete <?php echo e($student->name); ?>? This cannot be undone.')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-danger"
                                            style="font-size:12px; padding:4px 8px;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" style="text-align:center; color:#9ca3af; padding:20px;">
                            No students yet. <a href="<?php echo e(route('admin.students.create')); ?>">Add one</a>.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<div id="linkModal" style="display:none; position:fixed; inset:0;
     background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div class="card" style="max-width:420px; width:90%; margin:auto;">
        <div class="card-header">
            <span class="card-title">Link Parent to <span id="linkStudentName"></span></span>
            <button onclick="closeLinkModal()" class="btn btn-outline">Close</button>
        </div>
        <form method="POST" id="linkForm" action="">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label>Parent Email</label>
                <input type="email" name="parent_email"
                       placeholder="parent@example.com" required>
                <p class="form-hint">The parent must already have an account in the system.</p>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary">Link</button>
                <button type="button" onclick="closeLinkModal()" class="btn btn-outline">Cancel</button>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function openLinkModal(id, name) {
    document.getElementById('linkStudentName').textContent = name;
    document.getElementById('linkForm').action = '/admin/students/' + id + '/link-parent';
    document.getElementById('linkModal').style.display = 'flex';
}
function closeLinkModal() {
    document.getElementById('linkModal').style.display = 'none';
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TekioTask\resources\views/admin/students.blade.php ENDPATH**/ ?>