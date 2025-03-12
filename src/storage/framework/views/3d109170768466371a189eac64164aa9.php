<?php $__env->startSection('title', '従業員一覧画面（管理者）'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/attendance/admin/employee-list.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-wrap">
        <div class="container">
            <h1 class="title">スタッフ一覧</h1>
            <div class="attendance-table">
                <div class="table-header">
                    <span class="table-header-name">名前</span>
                    <span class="table-header-mail">メールアドレス</span>
                    <span class="table-header-detail">月次退勤</span>
                </div>

                
                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="table-row">

                        
                        <span class="name"><?php echo e($employee->name); ?></span>

                        
                        <span class="mail"><?php echo e($employee->email); ?></span>

                        
                        <a href="<?php echo e(route('admin.attendance.monthly-list', ['employeeId' => $employee->id])); ?>"
                            class="detail">詳細</a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/attendance/admin/employee-list.blade.php ENDPATH**/ ?>