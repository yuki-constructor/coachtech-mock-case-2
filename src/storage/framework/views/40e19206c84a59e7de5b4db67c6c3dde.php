<?php $__env->startSection('title', 'ログイン'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/auth/admin/login.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-wrap">
        <div class="container">
            <h1 class="title-center">管理者ログイン</h1>
            
            <div class="message">
                <?php if(session()->has('error')): ?>
                    <p><?php echo e(session()->get('error')); ?></p>
                <?php endif; ?>
            </div>
            <form class="form" method="POST" action="<?php echo e(route('admin.authenticate')); ?>">
                <?php echo csrf_field(); ?>
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
                    <input class="form-group__input" type="text" id="email" name="email" />
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
                <button type="submit" class="form-group__submit-btn">管理者ログインする</button>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.common', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/auth/admin/login.blade.php ENDPATH**/ ?>