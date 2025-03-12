<?php $__env->startSection('title', 'メール認証誘導'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/auth/employee/email-authentication-invitation.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-wrap">
        <div class="container">
            
            <?php if(session('error')): ?>
                <p class="error-message"><?php echo e(session('error')); ?></p>
            <?php endif; ?>
            <p class="message">登録していただいたメールアドレスに認証メールを送付しました。</p>
            <p class="message">メール認証を完了してください。</p>
            <div class="mail-check-link">
                <a class="mail-check-link__btn" href="http://localhost:8025">認証はこちらから</a>
            </div>
            <div class="send-mail-link">
                <form action="<?php echo e(route('verification.resend', ['employeeId' => $employee->id])); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="send-mail-link__btn">
                        認証メールを送信する
                    </button>
                </form>
            </div>
            </p>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.common', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/auth/employee/email-authentication-invitation.blade.php ENDPATH**/ ?>