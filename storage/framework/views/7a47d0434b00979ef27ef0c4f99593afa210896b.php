<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Flarepoint CRM</title>
    <link href="<?php echo e(URL::asset('css/jasny-bootstrap.css')); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo e(URL::asset('css/font-awesome.min.css')); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo e(URL::asset('css/jquery.dataTables.min.css')); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo e(URL::asset('css/dropzone.css')); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo e(URL::asset('css/bootstrapDatepickr-1.0.0.css')); ?>" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="<?php echo e(asset(elixir('css/app.css'))); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/css/bootstrap-dialog.min.css">
    <?php echo $__env->yieldPushContent('links'); ?>



    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>"/>
    <style>
        .row-align-bottom {
            position: relative;
        }
        .group-align-bottom {
            position: absolute;
            display: inline;
            bottom: 12px;
        }
    </style>
</head>
<body>


<div id="wrapper">
    <div class="navbar navbar-default navbar-top">
        <!--NOTIFICATIONS START-->
        <div class="dropdown">
            <a id="dLabel" role="button" data-toggle="dropdown"  href="/page.html">
                <i class="glyphicon glyphicon-bell"><span id="notifycount"></span></i>
            </a>
            <ul class="dropdown-menu notify-drop  notifications" role="menu" aria-labelledby="dLabel">
                <div class="notification-heading"><h4 class="menu-title">Notifications</h4><h4
                            class="menu-title pull-right"><a href="<?php echo e(url('notifications/markall')); ?>">Mark all as
                            read</a><i class="glyphicon glyphicon-circle-arrow-right"></i></h4>
                </div>
                <li class="divider"></li>
                <div class="notifications-wrapper">
                    <span id="notification-item"></span>
                    <?php $__env->startPush('scripts'); ?>
                    <script>
                        id = {};
                        function postRead(id) {
                            $.ajax({
                                type: 'post',
                                url: '<?php echo e(url('/notifications/markread')); ?>',
                                data: {
                                    id: id,
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });

                        }

                        $(function () {
                            $.get("<?php echo e(url('/notifications/getall')); ?>", function (notifications) {
                                var notifyItem = document.getElementById('notification-item');
                                var bell = document.getElementById('notifycount');
                                var msg = "";
                                var count = 0;
                                $.each(notifications, function (index, notification) {
                                    count++;
                                    var id = notification['id'];
                                    var url = notification['data']['url'];

                                    msg += `<div>
        <a class="content"  id="notify" href="<?php echo e(url('notifications')); ?>/` + id + `">
        `
                                            + notification['data']['message'] +
                                            ` </a></div>
        <hr class="notify-line"/>`;
                                    notifyItem.innerHTML = msg;

                                    /**         notifyItem.onclick = (function(id){
             return function(){
                 postRead(id);
             }})(id); **/

                                });
                                bell.innerHTML = count;
                            })

                        });

                    </script>
                <?php $__env->stopPush(); ?>
                </div>

            </ul>
        </div>
        <!--NOTIFICATIONS END-->
        <button type="button" class="navbar-toggle" data-toggle="offcanvas" data-target="#myNavmenu">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
    </div>


    <!-- /#sidebar-wrapper -->
    <!-- Sidebar menu -->

    <nav id="myNavmenu" class="navmenu navmenu-default navmenu-fixed-left offcanvas-sm" role="navigation">
        <div class="list-group panel">
            <p class=" list-group-item" title=""><img src="<?php echo e(url('images/logo1.jpg')); ?>" alt=""></p>
            <a href="<?php echo e(route('dashboard', \Auth::id())); ?>" class=" list-group-item" data-parent="#MainMenu"><i
                        class="glyphicon glyphicon-dashboard"></i> <?php echo app('translator')->get('menu.dashboard'); ?> </a>
            <!--
            <a href="<?php echo e(route('users.show', \Auth::id())); ?>" class=" list-group-item" data-parent="#MainMenu"><i
                        class="glyphicon glyphicon-user"></i> <?php echo app('translator')->get('menu.profile'); ?> </a>
            -->


            <a href="#products" class=" list-group-item" data-toggle="collapse" data-parent="#MainMenu"><i class="glyphicon glyphicon-tag"></i> 产品管理 </a>
            <div class="collapse" id="products">

                <!--
                <a href="<?php echo e(route('products.index')); ?>" class="list-group-item childlist"><?php echo app('translator')->get('menu.products.enquiry'); ?></a>
                <a href="<?php echo e(route('products.create')); ?>" class="list-group-item childlist"><?php echo app('translator')->get('menu.products.create'); ?></a>
                -->
                <a href="<?php echo e(route('products.fileselect')); ?>" class="list-group-item childlist">产品数据上传</a>
                <a href="<?php echo e(route('products.shownosku')); ?>" class="list-group-item childlist">分配SKU</a>
            </div>
            <a href="#ebay" class=" list-group-item" data-toggle="collapse" data-parent="#MainMenu"><i class="glyphicon glyphicon-shopping-cart"></i> EBAY操作 </a>
            <div class="collapse" id="ebay">
                <a href="<?php echo e(route('ebay.index')); ?>" class="list-group-item childlist">Check SKU</a>
            </div>

            <a href="#storages" class=" list-group-item" data-toggle="collapse" data-parent="#MainMenu"><i
                        class="fa fa-object-group"></i>仓库管理</a>
            <div class="collapse" id="storages">
                <a href="<?php echo e(route('storages.locindex')); ?>" class="list-group-item childlist">库存位置管理</a>
                <a href="<?php echo e(route('storages.itemindex')); ?>" class="list-group-item childlist">存储物品管理</a>
                <a href="<?php echo e(route('storages.locitemindex')); ?>" class="list-group-item childlist">库位使用管理</a>
                <a href="#" class="list-group-item childlist">库位使用查询</a>
            </div>

            <a href="#smartchannel" class=" list-group-item" data-toggle="collapse" data-parent="#MainMenu"><i
                        class="fa fa-object-group"></i>Smart Channel处理</a>
            <div class="collapse" id="smartchannel">
                <a href="<?php echo e(route('smartchannel.orderindex')); ?>" class="list-group-item childlist">订单处理</a>
                <a href="<?php echo e(route('smartchannel.paymentindex')); ?>" class="list-group-item childlist">账单处理</a>
            </div>

            <!--
            <a href="#tasks" class=" list-group-item" data-toggle="collapse" data-parent="#MainMenu"><i
                        class="glyphicon glyphicon-tasks"></i> <?php echo app('translator')->get('menu.tasks.title'); ?> </a>
            <div class="collapse" id="tasks">
                <a href="<?php echo e(route('tasks.index')); ?>" class="list-group-item childlist"><?php echo app('translator')->get('menu.tasks.all'); ?></a>
                <?php if(Entrust::can('task-create')): ?>
                    <a href="<?php echo e(route('tasks.create')); ?>" class="list-group-item childlist"><?php echo app('translator')->get('menu.tasks.new'); ?></a>
                <?php endif; ?>
            </div>

            <a href="#user" class=" list-group-item" data-toggle="collapse" data-parent="#MainMenu"><i
                        class="fa fa-users"></i> <?php echo app('translator')->get('menu.users.title'); ?> </a>
            <div class="collapse" id="user">
                <a href="<?php echo e(route('users.index')); ?>" class="list-group-item childlist"><?php echo app('translator')->get('menu.users.all'); ?></a>
                <?php if(Entrust::can('user-create')): ?>
                    <a href="<?php echo e(route('users.create')); ?>"
                       class="list-group-item childlist"><?php echo app('translator')->get('menu.users.new'); ?></a>
                <?php endif; ?>
            </div>

            <a href="#leads" class=" list-group-item" data-toggle="collapse" data-parent="#MainMenu"><i
                        class="glyphicon glyphicon-hourglass"></i> <?php echo app('translator')->get('menu.leads.title'); ?></a>
            <div class="collapse" id="leads">
                <a href="<?php echo e(route('leads.index')); ?>" class="list-group-item childlist"><?php echo app('translator')->get('menu.leads.all'); ?></a>
                <?php if(Entrust::can('lead-create')): ?>
                    <a href="<?php echo e(route('leads.create')); ?>"
                       class="list-group-item childlist"><?php echo app('translator')->get('menu.leads.new'); ?></a>
                <?php endif; ?>
            </div>
            <a href="#departments" class=" list-group-item" data-toggle="collapse" data-parent="#MainMenu"><i
                        class="fa fa-object-group"></i> <?php echo app('translator')->get('menu.departments.title'); ?></a>
            <div class="collapse" id="departments">
                <a href="<?php echo e(route('departments.index')); ?>"
                   class="list-group-item childlist"><?php echo app('translator')->get('menu.departments.all'); ?></a>
                <?php if(Entrust::hasRole('administrator')): ?>
                    <a href="<?php echo e(route('departments.create')); ?>"
                       class="list-group-item childlist"><?php echo app('translator')->get('menu.departments.new'); ?></a>
                <?php endif; ?>
            </div>
            -->

            <?php if(Entrust::hasRole('administrator')): ?>
                <a href="#settings" class=" list-group-item" data-toggle="collapse" data-parent="#MainMenu"><i
                            class="glyphicon glyphicon-cog"></i> <?php echo app('translator')->get('menu.settings.title'); ?></a>
                <div class="collapse" id="settings">
                    <a href="<?php echo e(route('settings.index')); ?>"
                       class="list-group-item childlist"><?php echo app('translator')->get('menu.settings.overall'); ?></a>

                    <a href="<?php echo e(route('roles.index')); ?>"
                       class="list-group-item childlist"><?php echo app('translator')->get('menu.settings.roles'); ?></a>
                    <a href="<?php echo e(route('integrations.index')); ?>"
                       class="list-group-item childlist"><?php echo app('translator')->get('menu.settings.integrations'); ?></a>
                </div>


            <?php endif; ?>
            <a href="<?php echo e(url('/logout')); ?>" class=" list-group-item impmenu" data-parent="#MainMenu"><i
                        class="glyphicon glyphicon-log-out"></i> <?php echo app('translator')->get('menu.signout'); ?> </a>

        </div>
    </nav>


    <!-- Page Content -->
    <div id="page-content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <h1><?php echo $__env->yieldContent('heading'); ?></h1>
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </div>
        </div>
        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                    <p><?php echo e($error); ?></p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
            </div>

        <?php endif; ?>
        <?php if(Session::has('flash_message_warning')): ?>
             <message message="<?php echo e(Session::get('flash_message_warning')); ?>" type="warning"></message>
        <?php endif; ?>
        <?php if(Session::has('flash_message')): ?>
            <message message="<?php echo e(Session::get('flash_message')); ?>" type="success"></message>
        <?php endif; ?>
    </div>
    <!-- /#page-content-wrapper -->
</div>
    <script type="text/javascript" src="<?php echo e(URL::asset('js/app.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(URL::asset('js/dropzone.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(URL::asset('js/jquery.dataTables.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(URL::asset('js/jasny-bootstrap.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(URL::asset('js/multiselect.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(URL::asset('js/bootstrapDatepickr-1.0.0.js')); ?>"></script>
    <script type=""text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/js/bootstrap-dialog.min.js">0</script>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
  