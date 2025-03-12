<?php $__env->startSection('title', '修正申請詳細画面（従業員）'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/attendance/employee/attendance-request-show.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-wrap">
        <div class="container">
            <h1 class="title">勤怠詳細</h1>
            <div class="attendance-table">

                
                <div class="table-row">
                    <span class="label">名前</span>
                    <span class="name"><?php echo e($attendanceRequest->attendance->employee->name); ?></span>
                    <span class="error-message"></span>
                </div>

                
                <div class="table-row">
                    <span class="label">日付</span>
                    <div class="date">
                        <span
                            class="date-year"><?php echo e(\Carbon\Carbon::parse($attendanceRequest->attendance->date)->format('Y年')); ?></span>
                        <span
                            class="date-day"><?php echo e(\Carbon\Carbon::parse($attendanceRequest->attendance->date)->format('n月j日')); ?></span>
                    </div>
                    <span class="error-message"></span>
                </div>

                
                <div class="table-row">
                    <span class="label">出勤・退勤</span>
                    <div class="time">
                        <span
                            class="time-box"><?php echo e(\Carbon\Carbon::parse($attendanceRequest->start_time)->format('H:i')); ?></span>
                        〜
                        <span
                            class="time-box"><?php echo e(\Carbon\Carbon::parse($attendanceRequest->end_time)->format('H:i')); ?></span>
                    </div>
                </div>
                <span class="error-message">
                </span>


                
                <?php $__currentLoopData = $attendanceRequest->attendanceRequestBreaks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendanceRequestBreak): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="table-row">
                        <span class="label">休憩</span>
                        <div class="time">
                            <span
                                class="time-box"><?php echo e(\Carbon\Carbon::parse($attendanceRequestBreak->break_start_time)->format('H:i')); ?></span>
                            〜
                            <span
                                class="time-box"><?php echo e(\Carbon\Carbon::parse($attendanceRequestBreak->break_end_time)->format('H:i')); ?></span>
                        </div>
                        <span class="error-message">
                        </span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <div class="table-row">
                    <span class="label">備考</span>
                    <div class="reason">
                        <span><?php echo e($attendanceRequest->reason); ?></span>
                    </div>
                    <span class="error-message">
                    </span>
                </div>
            </div>
            <?php if($attendanceRequest->attendance_request_status_id === ($pendingStatusId ?? null)): ?>
                <span class="message">＊承認待ちのため修正はできません。</span>
            <?php else: ?>
                <span></span>
            <?php endif; ?>
            </form>
        </div>
    </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/attendance/employee/attendance-request-show.blade.php ENDPATH**/ ?>