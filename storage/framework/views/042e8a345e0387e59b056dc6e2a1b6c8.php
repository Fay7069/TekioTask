<?php $__env->startSection('title', 'Students'); ?>
<?php $__env->startSection('page-title', 'Students & Groups'); ?>

<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="dashboard-grid">

    
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">All Students</span>
            <button class="btn btn-primary" onclick="openCreateGroupModal()">+ Create Group</button>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Diagnosis</th>
                        <th>Groups</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($student->name); ?></strong></td>
                            <td><?php echo e($student->diagnosis ?? '-'); ?></td>
                            <td style="font-size:12px; color:#6b7280;">
                                <?php echo e($student->groups->pluck('group_name')->join(', ') ?: '-'); ?>

                            </td>
                            <td>
                                <button class="btn btn-outline"
                                        style="font-size:12px; padding:4px 10px;"
                                        onclick="openAddToGroupModal(<?php echo e($student->user_id); ?>, '<?php echo e(addslashes($student->name)); ?>')">
                                    Add to Group
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" style="text-align:center; color:#9ca3af; padding:20px;">
                                No students in the system yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">My Groups</span>
        </div>

        <?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div style="padding:14px 0; border-bottom:1px solid #f3f4f6;">
                <div class="card-header" style="margin-bottom:10px;">
                    <strong><?php echo e($group->group_name); ?></strong>
                    <form method="POST"
                          action="<?php echo e(route('teacher.groups.destroy', $group->group_id)); ?>"
                          onsubmit="return confirm('Delete this group and remove all members?')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-danger"
                                style="font-size:12px; padding:3px 8px;">Delete Group</button>
                    </form>
                </div>

                <?php if($group->members->count()): ?>
                    <div style="display:flex; flex-wrap:wrap; gap:8px;">
                        <?php $__currentLoopData = $group->members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div style="display:flex; align-items:center; gap:6px;
                                        background:#f3f4f6; border-radius:20px;
                                        padding:4px 12px; font-size:13px;">
                                <span><?php echo e($member->name); ?></span>
                                <form method="POST"
                                      action="<?php echo e(route('teacher.groups.remove-member', [$group->group_id, $member->user_id])); ?>"
                                      onsubmit="return confirm('Remove <?php echo e($member->name); ?> from <?php echo e($group->group_name); ?>?')"
                                      style="margin:0;">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit"
                                            style="background:none; border:none; color:#dc2626;
                                                   cursor:pointer; font-size:14px; font-weight:700;
                                                   padding:0 2px; line-height:1;">
                                        &times;
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted" style="font-size:13px;">No members yet.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-muted">No groups yet. Create one above.</p>
        <?php endif; ?>
    </div>

</div>


<div id="createGroupModal" style="display:none; position:fixed; inset:0;
     background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div class="card" style="max-width:400px; width:90%; margin:auto;">
        <div class="card-header">
            <span class="card-title">Create Group</span>
            <button onclick="closeCreateGroupModal()" class="btn btn-outline">Close</button>
        </div>
        <form method="POST" action="<?php echo e(route('teacher.groups.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label>Group Name</label>
                <input type="text" name="group_name" placeholder="e.g. EIP Class A" required>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Create Group</button>
        </form>
    </div>
</div>


<div id="addToGroupModal" style="display:none; position:fixed; inset:0;
     background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div class="card" style="max-width:400px; width:90%; margin:auto;">
        <div class="card-header">
            <span class="card-title">Add <span id="addStudentName"></span> to Group</span>
            <button onclick="closeAddToGroupModal()" class="btn btn-outline">Close</button>
        </div>
        <form method="POST" id="addToGroupForm" action="">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label>Select Group</label>
                <select name="group_id" required>
                    <option value="">- Select group -</option>
                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($group->group_id); ?>"><?php echo e($group->group_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Add to Group</button>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function openCreateGroupModal()  { document.getElementById('createGroupModal').style.display = 'flex'; }
function closeCreateGroupModal() { document.getElementById('createGroupModal').style.display = 'none'; }

function openAddToGroupModal(id, name) {
    document.getElementById('addStudentName').textContent = name;
    document.getElementById('addToGroupForm').action = '/teacher/groups/' + id + '/add-member';
    document.getElementById('addToGroupModal').style.display = 'flex';
}
function closeAddToGroupModal() { document.getElementById('addToGroupModal').style.display = 'none'; }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.teacher', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TekioTask\resources\views/teacher/students.blade.php ENDPATH**/ ?>