


<?php
    $ti          = $ti ?? 0;
    $task        = $task ?? null;
    $isTemplate  = $isTemplate ?? false;
?>

<div class="task-row card mb-2" style="border-left: 3px solid #4f46e5;">
    <div class="card-header">
        <span class="card-title" style="font-size:13px;">Task <?php echo e($isTemplate ? '' : $ti + 1); ?></span>
        <button type="button" class="btn btn-danger" onclick="removeTask(this)">✕ Remove</button>
    </div>

    <div style="display:grid; grid-template-columns:1fr auto; gap:12px; margin-bottom:8px;">
        <div class="form-group">
            <label>Task name <span style="color:#dc2626">*</span></label>
            <input type="text"
                   name="tasks[<?php echo e($ti); ?>][title]"
                   value="<?php echo e(old("tasks.{$ti}.title", $task->title ?? '')); ?>"
                   placeholder="e.g. Brush Teeth" required>
        </div>
        <div class="form-group" style="width:140px;">
            <label>Duration (seconds)</label>
            <input type="number"
                   name="tasks[<?php echo e($ti); ?>][estimated_duration_seconds]"
                   value="<?php echo e(old("tasks.{$ti}.estimated_duration_seconds", $task->estimated_duration_seconds ?? 120)); ?>"
                   min="10" max="3600" required>
        </div>
    </div>

    
    <div>
        <button type="button" class="btn btn-outline" style="font-size:12px;"
                onclick="toggleMicroSteps(this, <?php echo e($ti); ?>)">
            <?php echo e(($task && $task->microSteps->count()) ? '− Hide Micro Steps' : '+ Add Micro Steps'); ?>

        </button>

        <div id="micro-area-<?php echo e($ti); ?>"
             style="<?php echo e(($task && $task->microSteps->count()) ? '' : 'display:none;'); ?> margin-top:10px;">

            <div id="micro-list-<?php echo e($ti); ?>">
                <?php if($task && $task->microSteps->count()): ?>
                    <?php $__currentLoopData = $task->microSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $si => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="micro-step-row flex gap-2 mb-2">
                            <input type="text"
                                   name="tasks[<?php echo e($ti); ?>][micro_steps][<?php echo e($si); ?>][description]"
                                   value="<?php echo e($step->description); ?>"
                                   placeholder="Step <?php echo e($si + 1); ?> description"
                                   style="flex:1;" required>
                            <button type="button" class="btn btn-danger"
                                    onclick="this.closest('.micro-step-row').remove()">✕</button>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>

            <button type="button" class="btn btn-outline" style="font-size:12px; margin-top:4px;"
                    onclick="addMicroStep(<?php echo e($ti); ?>)">+ Add Step</button>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\TekioTask\resources\views/teacher/partials/task-row.blade.php ENDPATH**/ ?>