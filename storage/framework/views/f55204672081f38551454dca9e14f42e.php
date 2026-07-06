<?php $__env->startSection('title', 'Reports'); ?>
<?php $__env->startSection('page-title', 'Reports & Export'); ?>

<?php $__env->startSection('content'); ?>
<div class="card" style="max-width:640px;">
    <div class="card-header">
        <span class="card-title">Generate Progress Report</span>
    </div>

    <div class="form-group">
        <label>Student</label>
        <select id="studentSelect">
            <option value="">All Students (Class Report)</option>
            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($s->user_id); ?>"><?php echo e($s->name); ?> (<?php echo e($s->diagnosis ?? 'No diagnosis'); ?>)</option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        <div class="form-group">
            <label>From</label>
            <input type="date" id="fromDate" value="<?php echo e(now()->startOfMonth()->format('Y-m-d')); ?>">
        </div>
        <div class="form-group">
            <label>To</label>
            <input type="date" id="toDate" value="<?php echo e(now()->format('Y-m-d')); ?>">
        </div>
    </div>

    <button class="btn btn-primary mt-3" onclick="generatePreview()">Generate Preview</button>

    
    <div id="previewSection" style="display:none; margin-top:24px; padding-top:24px; border-top:1px solid #e5e7eb;">
        <div class="card-title mb-3">Preview</div>
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-bottom:20px;">
            <div style="text-align:center;">
                <div id="prevTotal" style="font-size:28px; font-weight:500; color:#1e3a5f;">-</div>
                <div class="text-muted" style="font-size:13px;">Total Tasks</div>
            </div>
            <div style="text-align:center;">
                <div id="prevRate" style="font-size:28px; font-weight:500; color:#16a34a;">-</div>
                <div class="text-muted" style="font-size:13px;">Completion Rate</div>
            </div>
            <div style="text-align:center;">
                <div id="prevAdapt" style="font-size:28px; font-weight:500; color:#d97706;">-</div>
                <div class="text-muted" style="font-size:13px;">Adaptations</div>
            </div>
        </div>

        <div class="flex gap-2">
            <a id="csvLink" href="#" class="btn btn-primary">Download CSV</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
async function generatePreview() {
    const student = document.getElementById('studentSelect').value;
    const from    = document.getElementById('fromDate').value;
    const to      = document.getElementById('toDate').value;

    if (!from || !to) { alert('Please select a date range.'); return; }

    const params = new URLSearchParams({ from, to });
    if (student) params.append('student_id', student);

    const res  = await fetch(`<?php echo e(route('admin.reports.preview')); ?>?${params}`, {
        headers: { 'Accept': 'application/json' }
    });
    const data = await res.json();

    document.getElementById('prevTotal').textContent = data.total;
    document.getElementById('prevRate').textContent  = data.rate + '%';
    document.getElementById('prevAdapt').textContent = data.adaptations;

    const csvUrl = `<?php echo e(route('admin.reports.export')); ?>?${params}`;
    document.getElementById('csvLink').href = csvUrl;

    document.getElementById('previewSection').style.display = '';
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TekioTask\resources\views/admin/reports.blade.php ENDPATH**/ ?>