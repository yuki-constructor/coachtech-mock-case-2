<?php $__env->startSection('title', '勤怠詳細画面（管理者）'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/attendance/admin/attendance-show.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-wrap">
        <div class="container">
            <h1 class="title">勤怠詳細</h1>

            
            <span class="success-message">
                <?php if(session('success')): ?>
                    <p class="success-message"><?php echo e(session('success')); ?></p>
                <?php endif; ?>
            </span>

            <form action="<?php echo e(route('admin.attendance.correct', ['attendanceId' => $attendance->id])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="date" value="<?php echo e($attendance->date); ?>">
                <div class="attendance-table">

                    
                    <div class="table-row">
                        <span class="label">名前</span>
                        <span class="name"><?php echo e($attendance->employee->name); ?></span>
                        <span class="error-message"></span>
                    </div>

                    
                    <div class="table-row">
                        <span class="label">日付</span>
                        <div class="date">
                            <span class="date-year"><?php echo e(\Carbon\Carbon::parse($attendance->date)->format('Y年')); ?></span>
                            <span class="date-day"><?php echo e(\Carbon\Carbon::parse($attendance->date)->format('n月j日')); ?></span>
                        </div>
                        <span class="error-message"></span>
                    </div>

                    
                    <div class="table-row">
                        <span class="label">出勤・退勤</span>
                        <div class="time">
                            <input class="time-box" type="time" name="start_time"
                                value="<?php echo e(\Carbon\Carbon::parse($attendance->start_time)->format('H:i')); ?>" />〜
                            <input class="time-box" type="time" name="end_time"
                                value="<?php echo e($attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : ''); ?>" />
                        </div>
                        
                        <span class="error-message">
                            <?php if($errors->has('start_time') || $errors->has('end_time')): ?>
                                <p>
                                    <?php echo e($errors->first('start_time') ?? $errors->first('end_time')); ?>

                                </p>
                            <?php endif; ?>
                        </span>
                    </div>

                    
                    <?php $__currentLoopData = $attendance->breaks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $break): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="table-row">
                            <span class="label">休憩</span>
                            <div class="time">
                                <input class="time-box" type="time" name="breaks[<?php echo e($break->id); ?>][start]"
                                    value="<?php echo e(\Carbon\Carbon::parse($break->break_start_time)->format('H:i')); ?>" />〜
                                <input class="time-box" type="time" name="breaks[<?php echo e($break->id); ?>][end]"
                                    value="<?php echo e($break->break_end_time ? \Carbon\Carbon::parse($break->break_end_time)->format('H:i') : ''); ?>" />
                            </div>
                            
                            <span class="error-message">
                                
                                <?php if($errors->has("breaks.$break->id.invalid_time")): ?>
                                    <p><?php echo e($errors->first("breaks.$break->id.invalid_time")); ?></p>
                                <?php endif; ?>
                                
                                <?php if($errors->has("breaks.$break->id.outside_working_hours")): ?>
                                    <p><?php echo e($errors->first("breaks.$break->id.outside_working_hours")); ?></p>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    
                    <div class="table-row">
                        <span class="label">備考</span>
                        <div class="reason">
                            <textarea class="reason__input" name="reason" placeholder="修正理由"></textarea>
                        </div>
                        
                        <span class="error-message">
                            <?php if($errors->has('reason')): ?>
                                <p><?php echo e($errors->first('reason')); ?></p>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <button class="edit-button">修正</button>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/attendance/admin/attendance-show.blade.php ENDPATH**/ ?>