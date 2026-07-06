<?php $__env->startSection('title', isset($routine) ? 'Edit Routine' : 'Create Routine'); ?>
<?php $__env->startSection('page-title', isset($routine) ? 'Edit Routine' : 'Create New Routine'); ?>

<?php $__env->startSection('content'); ?>
<div style="max-width:700px;">
    <form method="POST"
          action="<?php echo e(isset($routine) ? route('teacher.routines.update', $routine) : route('teacher.routines.store')); ?>">
        <?php echo csrf_field(); ?>
        <?php if(isset($routine)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-error mb-3">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div>• <?php echo e($e); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <div class="card mb-3">
            <div class="card-header"><span class="card-title">Routine Details</span></div>
            <div class="form-group">
                <label for="name">Routine name <span style="color:#dc2626">*</span></label>
                <input type="text" id="name" name="name"
                       value="<?php echo e(old('name', $routine->name ?? '')); ?>"
                       placeholder="e.g. Morning Routine" required>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <span class="card-title">Tasks</span>
                <button type="button" class="btn btn-outline" onclick="addTask()">+ Add Task</button>
            </div>

            <div id="taskList">
                <?php if(isset($routine) && $routine->tasks->count()): ?>
                    <?php $__currentLoopData = $routine->tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ti => $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo $__env->make('teacher.partials.task-row', ['ti' => $ti, 'task' => $task], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <?php echo $__env->make('teacher.partials.task-row', ['ti' => 0, 'task' => null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary">
                <?php echo e(isset($routine) ? ' Save Changes' : ' Create Routine'); ?>

            </button>
            <a href="<?php echo e(route('teacher.routines.index')); ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<template id="taskTemplate">
    <?php echo $__env->make('teacher.partials.task-row', ['ti' => '__INDEX__', 'task' => null, 'isTemplate' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</template>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
let taskIndex = <?php echo e(isset($routine) ? $routine->tasks->count() : 1); ?>;

function addTask() {
    const template = document.getElementById('taskTemplate').innerHTML
                     .replace(/__INDEX__/g, taskIndex);
    const wrapper  = document.createElement('div');
    wrapper.innerHTML = template;
    document.getElementById('taskList').appendChild(wrapper.firstElementChild);
    taskIndex++;
}

function removeTask(btn) {
    const rows = document.querySelectorAll('.task-row');
    if (rows.length <= 1) { alert('A routine must have at least one task.'); return; }
    btn.closest('.task-row').remove();
}

function toggleMicroSteps(btn, index) {
    const area = document.getElementById(`micro-area-${index}`);
    area.style.display = area.style.display === 'none' ? '' : 'none';
    btn.textContent    = area.style.display === 'none' ? '+ Add Micro Steps' : '− Hide Micro Steps';
}

function addMicroStep(index) {
    const list    = document.getElementById(`micro-list-${index}`);
    const stepNum = list.children.length;
    const row     = document.createElement('div');
    row.className = 'micro-step-row';
    row.innerHTML = `
        <input type="text" name="tasks[${index}][micro_steps][${stepNum}][description]"
               placeholder="Step ${stepNum + 1} description" required>
        <button type="button" class="micro-step-remove" onclick="this.closest('.micro-step-row').remove()">✕</button>
    `;
    list.appendChild(row);
}
</script>

<style>
.micro-step-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.micro-step-row input[type="text"] {
    flex: 1;
    padding: 12px 16px;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    color: #1e3a5f;
    background: #fafafa;
    transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
}

.micro-step-row input[type="text"]:focus {
    outline: none;
    border-color: #2563eb;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
}

.micro-step-remove {
    width: 38px;
    height: 38px;
    flex-shrink: 0;
    border-radius: 10px;
    border: none;
    background: #fee2e2;
    color: #dc2626;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    transition: background 0.15s;
}

.micro-step-remove:hover {
    background: #fecaca;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.teacher', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TekioTask\resources\views/teacher/routine-form.blade.php ENDPATH**/ ?>