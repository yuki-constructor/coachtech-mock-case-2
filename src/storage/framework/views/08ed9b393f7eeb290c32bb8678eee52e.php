<?php $__env->startSection('title', '勤怠修正申請一覧（承認済み）画面（従業員）'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/attendance/employee/attendance-request-list-approved.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-wrap">
        <div class="container">
            <h1 class="title">申請一覧</h1>
            
            <div class="menu">
                <div class="menu__link">
                    <a href="<?php echo e(route('employee.attendance.request.list.pending')); ?>" class="menu__link-left">承認待ち</a>
                    <a class="menu__link-right">承認済み</a>
                </div>
            </div>
            
            <div class="attendance-table">
                <div class="table-header">
                    <span>状態</span>
                    <span>名前</span>
                    <span>対象日時</span>
                    <span>申請理由</span>
                    <span>申請日時</span>
                    <span>詳細</span>
                </div>

                <?php $__currentLoopData = $attendanceRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="table-row">
                        
                        <span><?php echo e($request->attendanceRequestStatus->request_status); ?></span>
                        
                        <span><?php echo e($employee->name); ?></span>
                        
                        <span><?php echo e(\Carbon\Carbon::parse($request->attendance->date)->format('Y/m/d')); ?></span>
                        
                        <span><?php echo e($request->reason); ?></span>
                        
                        <span><?php echo e(\Carbon\Carbon::parse($request->created_at)->format('Y/m/d')); ?></span>
                        
                        <a
                            href="<?php echo e(route('employee.attendance.request.show', ['attendanceRequestId' => $request->id])); ?>">詳細</a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/attendance/employee/attendance-request-list-approved.blade.php ENDPATH**/ ?>