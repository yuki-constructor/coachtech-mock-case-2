<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $__env->yieldContent('title', 'デフォルトタイトル'); ?></title>
    <!-- 共通のCSS -->
    <link rel="stylesheet" href="<?php echo e(asset('css/employee-app-1-2-1.css')); ?>">
    <!-- ページごとのCSS -->
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>
    <!-- 共通のヘッダー -->
    <header class="header">
        <div class="header-container">
            <div class="header-left">
                <img src="<?php echo e(asset('storage/photos/logo_images/logo.svg')); ?>" alt="COACHTECH ロゴ" class="logo" />
            </div>
            <div class="header-center"></div>
            <div class="header-right">
                <nav class="nav">
                    <ul class="nav__ul">
                        <li>
                            <a class="nav__attendance" href="<?php echo e(route("employee.attendance.create")); ?>">
                                勤怠
                            </a>
                        </li>
                        <li>
                            <a class="nav__attendance-list" href="<?php echo e(route('employee.attendance.list')); ?>">
                                勤怠一覧
                            </a>
                        </li>
                        <li>
                            <a class="nav__application" href="">
                                申請
                            </a>
                        </li>
                        <li>
                            <form action="<?php echo e(route('employee.logout')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="nav__logout">ログアウト</button>
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    <main>
        <!-- ページごとのコンテンツ -->
        <?php echo $__env->yieldContent('content'); ?>
    </main>
</body>

</html>
<?php /**PATH /var/www/resources/views/layouts/employee-app-1-2-1.blade.php ENDPATH**/ ?>