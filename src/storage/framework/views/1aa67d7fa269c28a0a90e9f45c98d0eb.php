<?php $__env->startSection('title', '勤怠登録登録画面（一般ユーザー）'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/attendance/employee/attendance-message.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-wrap">
        <div class="container">
            <div class="form-group">
                <p class="work-status"> 退勤済</p>
                <p class="date" id="current-date"></p>
                <p class="time" id="current-time"></p>
                
                <div class="message">
                    <p><?php echo e(session()->get('message')); ?></p>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo e(asset('js/attendance-create.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/attendance/employee/attendance-message.blade.php ENDPATH**/ ?>