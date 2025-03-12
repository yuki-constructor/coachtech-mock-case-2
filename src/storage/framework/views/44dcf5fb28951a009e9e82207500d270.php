<?php $__env->startSection('title', '勤怠登録登録画面（一般ユーザー）'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/attendance/employee/attendance-create.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-wrap">
        <div class="container">
            <div class="form-group">
                <?php if(!$attendance || $attendance->status->status === '勤務外'): ?>
                    <p class="work-status">勤務外</p>
                    <p class="date" id="current-date"></p>
                    <p class="time" id="current-time"></p>
                    <form action="<?php echo e(route('attendance.clock-in')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button class="form-group__submit-btn">出勤</button>
                    </form>
                <?php elseif($attendance->status->status === '勤務中'): ?>
                    <p class="work-status">勤務中</p>
                    <p class="date" id="current-date"></p>
                    <p class="time" id="current-time"></p>
                    <div class="form-group__submit-btn--container">
                        <form action="<?php echo e(route('attendance.clock-out')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button class="form-group__submit-btn">退勤</button>
                        </form>
                        <form action="<?php echo e(route('attendance.break-start')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button class="form-group__submit-btn--white">休憩入</button>
                        </form>
                    </div>
                <?php elseif($attendance->status->status === '休憩中'): ?>
                    <p class="work-status">休憩中</p>
                    <p class="date" id="current-date"></p>
                    <p class="time" id="current-time"></p>
                    <form action="<?php echo e(route('attendance.break-end')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button class="form-group__submit-btn--white">休憩戻</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="<?php echo e(asset('js/attendance-create.js')); ?>"></script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/attendance/employee/attendance-create.blade.php ENDPATH**/ ?>