<?php $__env->startSection('title', '勤怠登録登録画面（一般ユーザー）'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/auth/employee/attendance-create.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-wrap">
        <div class="container">
            <div class="form-group">
                <form class="form">
                    <p class="work-status">勤務外</p>
                    <p class="date">2023年6月1日(木)</p>
                    <p class="time">08:00</p>
                    <button type="submit" class="form-group__submit-btn">出勤</button>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/auth/employee/attendance-create.blade.php ENDPATH**/ ?>