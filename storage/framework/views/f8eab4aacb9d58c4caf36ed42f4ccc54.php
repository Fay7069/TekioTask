<?php $__env->startSection('title', 'Routine Management'); ?>
<?php $__env->startSection('page-title', 'Routine Management'); ?>

<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="page-actions">
    <a href="<?php echo e(route('teacher.routines.create')); ?>" class="btn btn-primary">+ Create New Routine</a>
</div>

<?php $__empty_1 = true; $__currentLoopData = $routines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $routine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="card mb-3">
        <div class="card-header">
            <div>
                <span class="card-title"><?php echo e($routine->name); ?></span>
                <span class="text-muted" style="font-size:12px; margin-left:8px;">
                    <?php echo e($routine->tasks_count); ?> task<?php echo e($routine->tasks_count !== 1 ? 's' : ''); ?>

                </span>
            </div>
            <div class="flex gap-2">
                <button class="btn btn-outline"
                        onclick="openAssignModal(<?php echo e($routine->routine_id); ?>, '<?php echo e(addslashes($routine->name)); ?>')">
                    Assign
                </button>
                <a href="<?php echo e(route('teacher.routines.edit', $routine)); ?>" class="btn btn-outline">Edit</a>
                <form method="POST" action="<?php echo e(route('teacher.routines.destroy', $routine)); ?>"
                      onsubmit="return confirm('Delete routine &quot;<?php echo e($routine->name); ?>&quot;? This will also remove all assignments.')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>

        
        <?php if($routine->assignments->count()): ?>
            <div style="display:flex; flex-wrap:wrap; gap:6px; margin-top:4px;">
                <?php $__currentLoopData = $routine->assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display:inline-flex; align-items:center; gap:6px;
                                font-size:12px; background:#eff6ff; color:#1e40af;
                                border:1px solid #bfdbfe; border-radius:20px;
                                padding:3px 6px 3px 12px;">
                        <span>
                            <?php if($assignment->student): ?>
                                <?php echo e($assignment->student->name); ?>

                            <?php elseif($assignment->group): ?>
                                <?php echo e($assignment->group->group_name); ?> (group)
                            <?php endif; ?>
                            <span style="color:#93c5fd; margin-left:4px;">
                                <?php echo e(\Carbon\Carbon::parse($assignment->assigned_date)->format('d M')); ?>

                            </span>
                        </span>
                        <form method="POST"
                              action="<?php echo e(route('teacher.routines.assignments.destroy', [$routine->routine_id, $assignment->assignment_id])); ?>"
                              onsubmit="return confirm('Remove this assignment?')"
                              style="margin:0;">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit"
                                    style="background:none; border:none; cursor:pointer;
                                           color:#93c5fd; font-size:15px; font-weight:700;
                                           padding:0 2px; line-height:1;"
                                    title="Remove assignment">&times;</button>
                        </form>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div style="font-size:12px; color:#9ca3af; margin-top:4px;">Not yet assigned</div>
        <?php endif; ?>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="empty-state">
        No routines yet. <a href="<?php echo e(route('teacher.routines.create')); ?>">Create your first one</a>.
    </div>
<?php endif; ?>


<div id="assignModal" style="display:none; position:fixed; inset:0;
     background:rgba(0,0,0,0.4); z-index:999; align-items:center; justify-content:center;">
    <div class="card" style="max-width:440px; width:90%; margin:auto;">
        <div class="card-header">
            <span class="card-title">Assign: <span id="assignRoutineName"></span></span>
            <button onclick="closeAssignModal()" class="btn btn-outline">Close</button>
        </div>

        <form method="POST" id="assignForm">
            <?php echo csrf_field(); ?>

            <div class="form-group mb-3">
                <label>Assign to</label>
                
                <div style="display:flex; gap:32px; margin-top:10px;">
                    <label style="display:flex; align-items:center; gap:8px;
                                  cursor:pointer; font-size:14px; font-weight:500;">
                        <input type="radio" name="assign_to" value="student" checked
                               onchange="toggleAssignType('student')"
                               style="width:16px; height:16px; accent-color:#2563eb;">
                        Individual student
                    </label>
                    <label style="display:flex; align-items:center; gap:8px;
                                  cursor:pointer; font-size:14px; font-weight:500;">
                        <input type="radio" name="assign_to" value="group"
                               onchange="toggleAssignType('group')"
                               style="width:16px; height:16px; accent-color:#2563eb;">
                        Group
                    </label>
                </div>
            </div>

            <div class="form-group mb-3" id="studentSelect">
                <label>Student</label>
                <select name="student_id">
                    <option value="">- Select student -</option>
                    <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <option value="<?php echo e($student->user_id); ?>">
                            <?php echo e($student->name); ?><?php echo e($student->diagnosis ? ' (' . $student->diagnosis . ')' : ''); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <option disabled>No students registered yet</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group mb-3" id="groupSelect" style="display:none;">
                <label>Group</label>
                <select name="group_id">
                    <option value="">- Select group -</option>
                    <?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <option value="<?php echo e($group->group_id); ?>"><?php echo e($group->group_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <option disabled>No groups created yet</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary">Assign</button>
                <button type="button" onclick="closeAssignModal()" class="btn btn-outline">Cancel</button>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function openAssignModal(id, name) {
    document.getElementById('assignRoutineName').textContent = name;
    document.getElementById('assignForm').action = '/teacher/routines/' + id + '/assign';
    // Reset to default (individual student) each time modal opens
    document.querySelector('input[name="assign_to"][value="student"]').checked = true;
    toggleAssignType('student');
    document.getElementById('assignModal').style.display = 'flex';
}
function closeAssignModal() {
    document.getElementById('assignModal').style.display = 'none';
}
function toggleAssignType(type) {
    document.getElementById('studentSelect').style.display = type === 'student' ? '' : 'none';
    document.getElementById('groupSelect').style.display   = type === 'group'   ? '' : 'none';
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.teacher', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TekioTask\resources\views/teacher/routines.blade.php ENDPATH**/ ?>