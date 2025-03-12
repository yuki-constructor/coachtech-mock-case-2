<?php $__env->startSection('title', '会員登録'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/auth/employee/register.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-wrap">
        <div class="container">
            <h1 class="title-center">会員登録</h1>
            <form class="form" method="POST" action="<?php echo e(route('employee.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label class="form-group__label" for="name">名前</label>
                    <div>
                        
                        <?php if($errors->has('name')): ?>
                            <div class="error-message">
                                <ul>
                                    <?php $__currentLoopData = $errors->get('name'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                    <input class="form-group__input" type="text" id="name" name="name"
                        value="<?php echo e(old('name')); ?>" />
                </div>
                <div class="form-group">
                    <label class="form-group__label" for="email">メールアドレス</label>
                    <div>
                        
                        <?php if($errors->has('email')): ?>
                            <div class="error-message">
                                <ul>
                                    <?php $__currentLoopData = $errors->get('email'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                    <input class="form-group__input" type="email" id="email" name="email"
                        value="<?php echo e(old('email')); ?>" />
                </div>
                <div class="form-group">
                    <label class="form-group__label" for="password">パスワード</label>
                    <div>
                        
                        <?php if($errors->has('password')): ?>
                            <div class="error-message">
                                <ul>
                                    <?php $__currentLoopData = $errors->get('password'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                    <input class="form-group__input" type="password" id="password" name="password" />
                </div>
                <div class="form-group">
                    <label class="form-group__label" for="password_confirmation">パスワード確認</label>
                    <div>
                        
                        <?php if($errors->has('password_confirmation')): ?>
                            <div class="error-message">
                                <ul>
                                    <?php $__currentLoopData = $errors->get('password_confirmation'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                    <input class="form-group__input" type="password" id="password_confirmation"
                        name="password_confirmation" />
                </div>
                <button type="submit" class="form-group__submit-btn">登録する</button>
            </form>
            <p class="login-link">
                <a class="login-link__link-btn" href="<?php echo e(route('employee.login')); ?>">ログインはこちら</a>
            </p>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.common', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/auth/employee/register.blade.php ENDPATH**/ ?>