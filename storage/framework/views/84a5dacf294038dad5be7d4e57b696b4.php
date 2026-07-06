<?php $__env->startSection('title', 'Case Notes'); ?>
<?php $__env->startSection('page-title', 'Case Notes'); ?>

<?php $__env->startSection('content'); ?>
<div style="max-width:700px;">

    <?php if(session('success')): ?>
        <div class="alert alert-success mb-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <div class="card mb-3">
        <div class="card-header">
            <span class="card-title">New Note Entry</span>
        </div>

        <form method="POST" action="<?php echo e(route('therapist.case-notes.store')); ?>">
            <?php echo csrf_field(); ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-error mb-3">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div>- <?php echo e($e); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="student_id">Student <span style="color:#dc2626;">*</span></label>
                <select id="student_id" name="student_id" required>
                    <option value="">- Select student -</option>
                    <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s->user_id); ?>"
                            <?php if(old('student_id') == $s->user_id): echo 'selected'; endif; ?>>
                            <?php echo e($s->name); ?>

                            <?php if($s->diagnosis): ?> (<?php echo e($s->diagnosis); ?>) <?php endif; ?>
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group">
                <label for="content">Confidential Note <span style="color:#dc2626;">*</span></label>
                <textarea id="content" name="content" rows="5"
                          placeholder="Describe the session, observations, and any recommendations..."
                          required><?php echo e(old('content')); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Save Note</button>
        </form>
    </div>

    
    <div class="card mb-3" style="padding:16px;">
        <div style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">

            
            <div class="form-group" style="flex:1; margin-bottom:0; min-width:180px;">
                <label style="font-size:12px;">Search notes</label>
                <input type="text" id="noteSearch"
                       placeholder="Search by student name or note content..."
                       oninput="filterNotes()"
                       style="width:100%;">
            </div>

            
            <form method="GET" action="<?php echo e(route('therapist.case-notes')); ?>"
                  style="display:flex; gap:8px; align-items:flex-end;">
                <div class="form-group" style="margin-bottom:0;">
                    <label style="font-size:12px;">Filter by student</label>
                    <select name="student_id">
                        <option value="">All students</option>
                        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->user_id); ?>"
                                <?php if(request('student_id') == $s->user_id): echo 'selected'; endif; ?>>
                                <?php echo e($s->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-outline">Filter</button>
                <a href="<?php echo e(route('therapist.case-notes')); ?>" class="btn btn-outline">Clear</a>
            </form>
        </div>
    </div>

    
    <div class="card">
        <div class="card-header">
            <span class="card-title">Past Notes</span>
            <span class="text-muted" style="font-size:12px;" id="noteCount">
                <?php echo e($notes->count()); ?> note(s)
            </span>
        </div>

        <div id="notesList">
            <?php $__empty_1 = true; $__currentLoopData = $notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="note-row"
                     data-name="<?php echo e(strtolower($note->student->name ?? '')); ?>"
                     data-content="<?php echo e(strtolower($note->content)); ?>"
                     style="padding:16px 0; border-bottom:1px solid #f3f4f6;">
                    <div style="display:flex; justify-content:space-between;
                                align-items:flex-start; margin-bottom:8px;">
                        <div>
                            <strong style="font-size:14px;">
                                <?php echo e($note->student->name ?? '-'); ?>

                            </strong>
                            <span class="text-muted" style="font-size:12px; margin-left:10px;">
                                <?php echo e($note->created_at->format('d M Y, H:i')); ?>

                            </span>
                        </div>
                        <form method="POST"
                              action="<?php echo e(route('therapist.case-notes.delete', $note->note_id)); ?>"
                              onsubmit="return confirm('Delete this case note?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger"
                                    style="font-size:11px; padding:3px 8px;">Delete</button>
                        </form>
                    </div>
                    <p style="font-size:13px; color:#374151; line-height:1.6;
                              white-space:pre-wrap;"><?php echo e($note->content); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="empty-state" id="emptyState">No case notes found.</div>
            <?php endif; ?>

            <div id="noMatchState" style="display:none; padding:20px;
                 text-align:center; color:#9ca3af; font-size:13px;">
                No notes match your search.
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function filterNotes() {
    const query = document.getElementById('noteSearch').value.toLowerCase().trim();
    const rows  = document.querySelectorAll('.note-row');
    let visible = 0;

    rows.forEach(row => {
        const name    = row.dataset.name    || '';
        const content = row.dataset.content || '';
        const matches = !query || name.includes(query) || content.includes(query);
        row.style.display = matches ? '' : 'none';
        if (matches) visible++;
    });

    document.getElementById('noteCount').textContent = visible + ' note(s)';
    document.getElementById('noMatchState').style.display =
        (rows.length > 0 && visible === 0) ? '' : 'none';
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.therapist', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TekioTask\resources\views/therapist/case-notes.blade.php ENDPATH**/ ?>