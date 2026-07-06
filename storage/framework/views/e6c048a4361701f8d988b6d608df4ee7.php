<?php $__env->startSection('title', 'Parent Dashboard'); ?>
<?php $__env->startSection('page-title', "My Child's Progress"); ?>

<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="dashboard-grid">

    
    <?php if($children->count() > 1): ?>
        <div class="card col-full">
            <div class="card-header">
                <span class="card-title">Viewing progress for</span>
            </div>
            <div class="flex gap-2" style="flex-wrap:wrap;">
                <?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('parent.switch-child', $c->user_id)); ?>"
                       class="btn <?php echo e($child?->user_id === $c->user_id ? 'btn-primary' : 'btn-outline'); ?>">
                        <?php echo e($c->name); ?>

                        <?php if($c->diagnosis): ?> (<?php echo e($c->diagnosis); ?>) <?php endif; ?>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if(!$child): ?>
        <div class="card col-full">
            <div class="empty-state">
                No children linked to your account yet. Please contact the administrator.
            </div>
        </div>
    <?php else: ?>

    
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">Today's Routine — <?php echo e($child->name); ?></span>
        </div>

        <?php if($todayTotal > 0): ?>
            <?php $pct = round(($todayCompleted / $todayTotal) * 100); ?>
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                <div style="flex:1;">
                    <div style="font-size:15px; font-weight:600; color:#1e3a5f; margin-bottom:4px;">
                        <?php echo e($todayCompleted); ?> of <?php echo e($todayTotal); ?> tasks completed today
                    </div>
                    <div class="progress-bar-wrap">
                        <div class="progress-bar-fill" style="width:<?php echo e($pct); ?>%"></div>
                    </div>
                </div>
                <span class="status-pill <?php echo e($todayCompleted >= $todayTotal ? 'complete' : 'progress'); ?>">
                    <?php echo e($pct); ?>%
                </span>
            </div>
        <?php else: ?>
            <p class="text-muted">No routine data for today yet.</p>
        <?php endif; ?>
    </div>

    
    <div class="card">
        <div class="card-header">
            <span class="card-title">This Week</span>
        </div>
        <div class="chart-wrap">
            <?php $__currentLoopData = $weeklyProgress; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $pct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bar-col">
                    <div class="bar <?php echo e($pct == 0 ? 'empty' : ''); ?>"
                         style="height:<?php echo e(max($pct, 4)); ?>px"
                         title="<?php echo e($weeklyLabels[$i]); ?>: <?php echo e($pct); ?>%"></div>
                    <span class="bar-label"><?php echo e(substr($weeklyLabels[$i], 0, 1)); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php $goodDays = count(array_filter($weeklyProgress, fn($v) => $v >= 80)); ?>
        <div class="chart-summary"><?php echo e($goodDays); ?> great day<?php echo e($goodDays !== 1 ? 's' : ''); ?> this week</div>
        <div class="text-muted" style="font-size:11px; margin-top:2px;">A great day is 80% or more of that day's tasks completed</div>
    </div>

    
    <div class="card" style="display:flex; flex-direction:column; gap:12px;">
        <div class="card-header">
            <span class="card-title">Actions</span>
        </div>
        <button class="btn btn-primary btn-full" onclick="openCommentModal()">
            Add Comment
        </button>
        <a href="<?php echo e(route('parent.home-task')); ?>" class="btn btn-outline btn-full">
            Record Home Task
        </a>
        <a href="<?php echo e(route('parent.home-task.history')); ?>" class="btn btn-outline btn-full">
            View Home Task History
        </a>
    </div>

    
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">Task History</span>
        </div>
        <?php if($taskHistory->count()): ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Task</th>
                            <th>Status</th>
                            <th>Micro-steps used</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $taskHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td style="font-size:12px;">
                                    
                                    <?php echo e($log->attempt_timestamp->format('d M Y, H:i')); ?>

                                </td>
                                <td><?php echo e($log->task->title ?? '-'); ?></td>
                                <td>
                                    <?php if($log->status === 'completed'): ?>
                                        <span class="status-pill complete">Completed</span>
                                    <?php elseif($log->status === 'failed'): ?>
                                        <span class="status-pill failed">Failed</span>
                                    <?php else: ?>
                                        <span class="status-pill skipped">Skipped</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($log->was_adapted ? 'Yes' : '-'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">No task history yet.</div>
        <?php endif; ?>
    </div>

    
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">My Comments</span>
        </div>
        <?php $__empty_1 = true; $__currentLoopData = $recentComments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div style="display:flex; justify-content:space-between; align-items:flex-start;
                        padding:12px 0; border-bottom:1px solid #f3f4f6;">
                <div style="flex:1;">
                    <p style="font-size:13px; color:#374151; line-height:1.5;">
                        <?php echo e($comment->comment_text); ?>

                    </p>
                    <span class="text-muted" style="font-size:11px;">
                        <?php echo e(\Carbon\Carbon::parse($comment->created_at)->format('d M Y, H:i')); ?>

                    </span>
                </div>
                <form method="POST"
                      action="<?php echo e(route('parent.comment.delete', $comment->comment_id)); ?>"
                      onsubmit="return confirm('Delete this comment?')"
                      style="margin-left:12px;">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger"
                            style="font-size:11px; padding:3px 8px;">Delete</button>
                </form>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-muted">No comments yet.</p>
        <?php endif; ?>
    </div>

    
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">Recent Home Tasks</span>
            <a href="<?php echo e(route('parent.home-task.history')); ?>" class="btn btn-outline"
               style="font-size:12px; padding:4px 10px;">View All</a>
        </div>
        <?php $__empty_1 = true; $__currentLoopData = $recentHomeTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ht): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div style="padding:10px 0; border-bottom:1px solid #f3f4f6; font-size:13px;">
                <strong><?php echo e($ht->task_name); ?></strong>
                <span class="text-muted" style="margin-left:10px;">
                    <?php echo e(\Carbon\Carbon::parse($ht->completed_date)->format('d M Y')); ?>

                </span>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-muted">No home tasks recorded yet.</p>
        <?php endif; ?>
    </div>

    <?php endif; ?>

</div>


<div id="commentModal" style="display:none; position:fixed; inset:0;
     background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div class="card" style="max-width:440px; width:90%; margin:auto;">
        <div class="card-header">
            <span class="card-title">Add Comment</span>
            <button onclick="closeCommentModal()" class="btn btn-outline">Close</button>
        </div>
        <form method="POST" action="<?php echo e(route('parent.comment.store')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="student_id" value="<?php echo e($child?->user_id); ?>">
            <div class="form-group">
                <label>Note about <?php echo e($child?->name); ?>'s behaviour at home</label>
                <textarea name="comment_text" rows="4" required
                          placeholder="e.g. Completed breakfast without prompting this morning."></textarea>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary">Save Comment</button>
                <button type="button" onclick="closeCommentModal()" class="btn btn-outline">Cancel</button>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function openCommentModal()  { document.getElementById('commentModal').style.display = 'flex'; }
function closeCommentModal() { document.getElementById('commentModal').style.display = 'none'; }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.parent', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TekioTask\resources\views/parent/dashboard.blade.php ENDPATH**/ ?>