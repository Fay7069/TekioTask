<?php $__env->startSection('title', 'Admin Dashboard'); ?>
<?php $__env->startSection('page-title', 'Admin Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="dashboard-grid">

    
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">Today's Attendance — <?php echo e(now()->format('d M Y')); ?></span>
            <div class="flex gap-2">
                <a href="<?php echo e(route('admin.students.create')); ?>" class="btn btn-outline">+ Student</a>
            </div>
        </div>

        <div style="font-size:32px; font-weight:800; color:#1e3a5f; margin-bottom:4px;">
            <span id="presentCount"><?php echo e($present); ?></span> / <span id="totalCount"><?php echo e($total); ?></span>
        </div>
        <div class="text-muted mb-4">students present today</div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Diagnosis</th>
                        <th>Status</th>
                        <th>Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $checkedIn = isset($checkins[$student->user_id]); ?>
                        <tr id="row-<?php echo e($student->user_id); ?>">
                            <td><strong><?php echo e($student->name); ?></strong></td>
                            <td><?php echo e($student->diagnosis ?? '-'); ?></td>
                            <td>
                                <span class="status-pill <?php echo e($checkedIn ? 'complete' : 'failed'); ?>"
                                      id="status-<?php echo e($student->user_id); ?>">
                                    <?php echo e($checkedIn ? 'Present' : 'Absent'); ?>

                                </span>
                            </td>
                            <td style="font-size:12px; color:#6b7280;" id="time-<?php echo e($student->user_id); ?>">
                                <?php echo e($checkedIn ? $checkins[$student->user_id] : '-'); ?>

                            </td>
                            <td>
                                <?php if(!$checkedIn): ?>
                                    <button class="btn btn-outline"
                                            id="btn-<?php echo e($student->user_id); ?>"
                                            onclick="checkIn(<?php echo e($student->user_id); ?>)"
                                            style="font-size:12px; padding:4px 12px;">
                                        Check In
                                    </button>
                                <?php else: ?>
                                    <span id="btn-<?php echo e($student->user_id); ?>"
                                          style="font-size:12px; color:#16a34a;">Checked in</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; color:#9ca3af; padding:20px;">
                                No students yet. <a href="<?php echo e(route('admin.students.create')); ?>">Add one</a>.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">Quick Actions</span>
        </div>
        <div class="flex gap-2" style="flex-wrap:wrap;">
            <a href="<?php echo e(route('admin.students.index')); ?>" class="btn btn-primary">Manage Students</a>
            <a href="<?php echo e(route('admin.users.index')); ?>"    class="btn btn-outline">Manage Staff</a>
            <a href="<?php echo e(route('admin.reports')); ?>"        class="btn btn-outline">Reports</a>
            <a href="<?php echo e(route('profile.index')); ?>"        class="btn btn-outline">My Profile</a>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

async function checkIn(studentId) {
    const btn = document.getElementById('btn-' + studentId);
    btn.disabled = true;
    btn.textContent = 'Saving...';

    const res  = await fetch('/admin/checkin/' + studentId, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();

    if (data.status === 'ok') {
        document.getElementById('status-' + studentId).className = 'status-pill complete';
        document.getElementById('status-' + studentId).textContent = 'Present';
        document.getElementById('time-' + studentId).textContent = data.time;
        btn.outerHTML = '<span id="btn-' + studentId + '" style="font-size:12px;color:#16a34a;">Checked in</span>';
        document.getElementById('presentCount').textContent = data.present;
        document.getElementById('totalCount').textContent   = data.total;
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TekioTask\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>