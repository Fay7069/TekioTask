<?php $__env->startSection('title', 'Active Task'); ?>
<?php $__env->startSection('page-title', 'Active Task'); ?>

<?php $__env->startSection('content'); ?>
<div class="routine-view">

<?php if($isLocked): ?>

    
    <div style="text-align:center; padding:60px 20px;">
        <div style="font-size:48px; margin-bottom:16px;">⏸</div>
        <div style="font-size:20px; font-weight:700; color:#1e3a5f; margin-bottom:8px;">
            <?php echo e($task->title); ?>

        </div>
        <div style="font-size:15px; color:#6b7280; max-width:320px; margin:0 auto;">
            Your teacher has been notified and will help you with this one soon.
            Hang tight!
        </div>
    </div>

<?php else: ?>

    <div class="task-title"><?php echo e($task->title); ?></div>

    <div class="timer-canvas-wrap">
        <canvas id="timerCanvas" width="220" height="220"></canvas>
        <div class="timer-label">
            <?php
                $m = floor($task->estimated_duration_seconds / 60);
                $s = $task->estimated_duration_seconds % 60;
            ?>
            <div class="timer-time" id="timerDisplay">
                <?php echo e(str_pad($m,2,'0',STR_PAD_LEFT)); ?>:<?php echo e(str_pad($s,2,'0',STR_PAD_LEFT)); ?>

            </div>
            <div class="timer-sub" id="timerSub">REMAINING</div>
        </div>
    </div>

    <div class="task-action-row">
        <button onclick="markDone()" class="btn btn-success btn-lg" id="doneBtn">Done</button>
        <button onclick="markHelp()" class="btn btn-warning btn-lg" id="helpBtn">Need Help</button>
    </div>

    <div class="task-progress-indicator">
        Task <?php echo e($currentTaskNum); ?> of <?php echo e($totalTasks); ?>

    </div>

    
    <div id="retryOverlay" style="display:none; position:fixed; inset:0;
         background:rgba(0,0,0,0.6); z-index:998; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:16px; padding:36px 40px;
                    text-align:center; max-width:340px; width:90%;">
            <div style="font-size:16px; font-weight:600; color:#1e3a5f; margin-bottom:8px;">
                Let's Try Again</div>
            <div style="font-size:14px; color:#6b7280; margin-bottom:20px;">
                1 more attempt before we break it into steps. Starting in...
            </div>
            <div id="retryCountdown"
                 style="font-size:56px; font-weight:800; color:#2563eb; line-height:1;">5</div>
            <div style="font-size:13px; color:#9ca3af; margin-top:8px;">seconds</div>
        </div>
    </div>

<?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php if(!$isLocked): ?>
<?php $__env->startSection('scripts'); ?>
<script>
const TASK_ID    = <?php echo e($task->task_id); ?>;
const TOTAL_SEC  = <?php echo e($task->estimated_duration_seconds); ?>;
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

let pausedRemaining  = TOTAL_SEC * 1000;
let startTime        = null;
let rafId            = null;
let running          = false;
let actionInProgress = false;
let timerExpired     = false;
let runCount         = 1; // tracks which attempt: 1 = first run, 2 = second run

const canvas  = document.getElementById('timerCanvas');
const ctx     = canvas.getContext('2d');
const display = document.getElementById('timerDisplay');
const sub     = document.getElementById('timerSub');
const CX = 110, CY = 110, R = 90;

// ── Canvas drawing ────────────────────────────────────────────
function drawTimer(remainingMs) {
    const pct = Math.max(0, remainingMs / (TOTAL_SEC * 1000));
    ctx.clearRect(0, 0, 220, 220);

    ctx.beginPath();
    ctx.arc(CX, CY, R, 0, Math.PI * 2);
    ctx.strokeStyle = '#e5e7eb';
    ctx.lineWidth   = 14;
    ctx.stroke();

    if (pct > 0) {
        ctx.beginPath();
        ctx.arc(CX, CY, R, -Math.PI / 2, -Math.PI / 2 + Math.PI * 2 * pct);
        ctx.strokeStyle = pct > 0.5 ? '#2563eb' : pct > 0.25 ? '#f59e0b' : '#dc2626';
        ctx.lineWidth   = 14;
        ctx.lineCap     = 'round';
        ctx.stroke();
    }

    const totalSec = Math.ceil(remainingMs / 1000);
    const m = String(Math.floor(Math.max(0, totalSec) / 60)).padStart(2, '0');
    const s = String(Math.max(0, totalSec) % 60).padStart(2, '0');
    display.textContent = m + ':' + s;
}

// ── rAF loop ──────────────────────────────────────────────────
function tick(now) {
    if (!running) return;
    const elapsed   = now - startTime;
    const remaining = Math.max(0, pausedRemaining - elapsed);
    drawTimer(remaining);

    if (remaining <= 0) {
        running         = false;
        timerExpired    = true;
        pausedRemaining = 0;
        drawTimer(0);
        display.textContent = '00:00';
        sub.textContent     = 'TIME UP';
        if (!actionInProgress) onTimerExpired();
        return;
    }
    rafId = requestAnimationFrame(tick);
}

// ── Called when timer hits 00:00 ──────────────────────────────
function onTimerExpired() {
    if (runCount === 1) {
        // First run expired — log failure, show retry countdown, start 2nd run
        runCount = 2;
        markHelp(true); // true = called by timer, not button
    } else {
        // Second run expired — log failure. calledByTimer stays true so
        // markHelp knows this was an automatic 2nd-run expiry, not a
        // manual first-run "Need Help" click.
        markHelp(true);
    }
}

function startTimer(fromMs = null) {
    if (fromMs !== null) pausedRemaining = fromMs;
    startTime = performance.now();
    running   = true;
    rafId     = requestAnimationFrame(tick);
}

function stopTimer() {
    if (!running) return;
    running = false;
    cancelAnimationFrame(rafId);
    const elapsed   = performance.now() - startTime;
    pausedRemaining = Math.max(0, pausedRemaining - elapsed);
}

function disableButtons() {
    document.getElementById('doneBtn').disabled = true;
    document.getElementById('helpBtn').disabled = true;
}
function enableButtons() {
    document.getElementById('doneBtn').disabled = false;
    document.getElementById('helpBtn').disabled = false;
}

// ── Done ──────────────────────────────────────────────────────
async function markDone() {
    if (actionInProgress) return;
    actionInProgress = true;
    stopTimer();
    disableButtons();

    const timeTaken = Math.round((TOTAL_SEC * 1000 - pausedRemaining) / 1000);
    showToast('Saving...', 'info', 0);

    try {
        const res = await fetch('<?php echo e(route("student.task.complete")); ?>', {
            method : 'POST',
            headers: {
                'Content-Type' : 'application/json',
                'X-CSRF-TOKEN' : CSRF_TOKEN,
                'Accept'       : 'application/json',
            },
            body: JSON.stringify({ task_id: TASK_ID, time_taken_seconds: timeTaken }),
        });

        if (res.ok) {
            showToast('Task complete! Loading next task...', 'success', 0);
            setTimeout(() => { window.location.href = '<?php echo e(route("student.routine")); ?>'; }, 1200);
        } else {
            const body = await res.text();
            console.error('Done 500:', res.status, body.substring(0, 400));
            showToast('Error saving. Check console for details.', 'danger', 5000);
            actionInProgress = false;
            enableButtons();
            if (!timerExpired) startTimer();
        }
    } catch (e) {
        console.error('Done network error:', e);
        showToast('Connection error. Please try again.', 'danger', 5000);
        actionInProgress = false;
        enableButtons();
        if (!timerExpired) startTimer();
    }
}

// ── Need Help ─────────────────────────────────────────────────
// calledByTimer: true means the timer expired and triggered this automatically
async function markHelp(calledByTimer = false) {
    if (actionInProgress && !calledByTimer) return;
    actionInProgress = true;
    stopTimer();
    disableButtons();
    showToast('Saving...', 'info', 0);

    try {
        const res = await fetch('<?php echo e(route("student.task.fail")); ?>', {
            method : 'POST',
            headers: {
                'Content-Type' : 'application/json',
                'X-CSRF-TOKEN' : CSRF_TOKEN,
                'Accept'       : 'application/json',
            },
            body: JSON.stringify({ task_id: TASK_ID }),
        });

        if (!res.ok) {
            const body = await res.text();
            console.error('Need Help 500:', res.status, body.substring(0, 400));
            showToast('Error. Check console for details.', 'danger', 5000);
            actionInProgress = false;
            enableButtons();
            if (!timerExpired) startTimer();
            return;
        }

        const data = await res.json();
        removeToast();

        if (data.adapt) {
            // Threshold reached — go to microsteps regardless of how we got here
            showToast("Breaking it down into steps...", 'info', 0);
            setTimeout(() => { window.location.href = '<?php echo e(route("student.microstep")); ?>'; }, 1200);
        } else if (data.stop) {
            // Threshold reached but this task has no micro-steps to fall
            // back on — stop the retry loop instead of restarting the
            // timer again. Teacher has been notified server-side.
            showToast(data.message || "Let's come back to this one later.", 'info', 4000);
            setTimeout(() => { window.location.href = '<?php echo e(route("student.routine")); ?>'; }, 1500);
        } else if (runCount === 2 && calledByTimer) {
            // First run expired via timer — show countdown then start 2nd full run
            showRetryCountdown(() => {
                timerExpired     = false;
                actionInProgress = false;
                enableButtons();
                sub.textContent  = 'REMAINING';
                startTimer(TOTAL_SEC * 1000);
            });
        } else if (!calledByTimer) {
            // Student pressed Need Help manually on first run
            showRetryCountdown(() => {
                timerExpired     = false;
                actionInProgress = false;
                runCount         = 2; // next expiry = 2nd run
                enableButtons();
                sub.textContent  = 'REMAINING';
                startTimer(TOTAL_SEC * 1000);
            });
        } else {
            // Fallback — if somehow here, just go to microsteps
            showToast("Breaking it down into steps...", 'info', 0);
            setTimeout(() => { window.location.href = '<?php echo e(route("student.microstep")); ?>'; }, 1200);
        }

    } catch (e) {
        console.error('Need Help network error:', e);
        showToast('Connection error. Are you online?', 'danger', 5000);
        actionInProgress = false;
        enableButtons();
        if (!timerExpired) startTimer();
    }
}

// ── Retry countdown overlay ───────────────────────────────────
function showRetryCountdown(onComplete) {
    const overlay = document.getElementById('retryOverlay');
    const num     = document.getElementById('retryCountdown');
    overlay.style.display = 'flex';
    let count = 5;
    num.textContent = count;
    const iv = setInterval(() => {
        count--;
        if (count <= 0) {
            clearInterval(iv);
            overlay.style.display = 'none';
            onComplete();
        } else {
            num.textContent = count;
        }
    }, 1000);
}

// ── Toast ─────────────────────────────────────────────────────
function showToast(msg, type, duration) {
    removeToast();
    const colours = { success:'#16a34a', warning:'#d97706', info:'#2563eb', danger:'#dc2626' };
    const el = document.createElement('div');
    el.id = 'feedback-toast';
    el.textContent = msg;
    el.style.cssText =
        'position:fixed; top:20px; left:50%; transform:translateX(-50%);' +
        'background:' + (colours[type] || '#333') + '; color:#fff;' +
        'padding:12px 28px; border-radius:8px; font-size:15px; font-weight:500;' +
        'z-index:9999; box-shadow:0 4px 12px rgba(0,0,0,0.15); white-space:nowrap;';
    document.body.appendChild(el);
    if (duration > 0) setTimeout(removeToast, duration);
}
function removeToast() { document.getElementById('feedback-toast')?.remove(); }

drawTimer(TOTAL_SEC * 1000);
startTimer();
</script>
<?php $__env->stopSection(); ?>
<?php endif; ?>

<?php echo $__env->make('layouts.student', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TekioTask\resources\views/student/routine.blade.php ENDPATH**/ ?>