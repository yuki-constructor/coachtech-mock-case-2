<?php $__env->startSection('title', '日次勤怠一覧画面（管理者）'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/attendance/admin/attendance-daily-list.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-wrap">
        <div class="container">
            <h1 class="title"><?php echo e($date->isoFormat('YYYY年M月D日')); ?>の勤怠</h1>
            <div class="day-navigation">
                <a href="<?php echo e(route('admin.attendance.daily-list', ['date' => $date->copy()->subDay()->toDateString()])); ?>">
                    &larr;前日</a>
                <div class="day-navigation-center">
                    <img class="day-navigation-calendar__image" src="<?php echo e(asset('storage/photos/logo_images/calendar.png')); ?>"
                        alt="カレンダー" />
                    <span class="day"><?php echo e($date->format('Y/m/d')); ?></span>
                </div>
                <a href="<?php echo e(route('admin.attendance.daily-list', ['date' => $date->copy()->addDay()->toDateString()])); ?>">
                    翌日&rarr;</a>
            </div>

            <div class="attendance-table">
                <div class="table-header">
                    <span>名前</span>
                    <span>出勤</span>
                    <span>退勤</span>
                    <span>休憩</span>
                    <span>合計</span>
                    <span>詳細</span>
                </div>

                
                <?php $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="table-row">
                        <span><?php echo e($attendance->employee->name); ?></span>

                        
                        <span><?php echo e(\Carbon\Carbon::parse($attendance->start_time)->format('H:i')); ?></span>

                        
                        <span><?php echo e($attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '-'); ?></span>

                        
                        <span><?php echo e($attendance->total_break_time); ?></span>

                        
                        <span><?php echo e($attendance->total_work_time); ?></span>

                        
                        <a href="<?php echo e(route('admin.attendance.show', ['attendanceId' => $attendance->id])); ?>">詳細</a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/attendance/admin/attendance-daily-list.blade.php ENDPATH**/ ?>