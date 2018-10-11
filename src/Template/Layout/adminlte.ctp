<?php use Cake\Core\Configure ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo Configure::read('Theme.title.' . $this->name); ?></title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <?php echo $this->Html->css('AdminLTE./bootstrap/css/bootstrap.min'); ?>
        <?php echo $this->Html->css('/plugins/font-awesome/css/font-awesome.min'); ?>
        <?php echo $this->Html->css('/plugins/ionicons/css/ionicons.min'); ?>
        <!-- Theme style -->
        <?php echo $this->Html->css('AdminLTE.AdminLTE.min'); ?>
        <?php echo $this->Html->css('AdminLTE.skins/skin-'. Configure::read('Theme.skin') .'.min'); ?>

        <?php echo $this->fetch('css'); ?>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="hold-transition skin-<?php echo Configure::read('Theme.skin'); ?> sidebar-mini">
        <!-- Site wrapper -->
        <div class="wrapper">
            <header class="main-header">
                <!-- Logo -->
                <a href="<?php echo $this->Url->build('/'); ?>" class="logo">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span class="logo-mini"><?php echo Configure::read('Theme.logo.mini'); ?></span>
                    <!-- logo for regular state and mobile devices -->
                    <span class="logo-lg"><?php echo Configure::read('Theme.logo.large'); ?></span>
                </a>
                <!-- Header Navbar: style can be found in header.less -->
                <?php echo $this->element('nav-top') ?>
            </header>

            <!-- Left side column. contains the sidebar -->
            <?php echo $this->element('aside-main-sidebar'); ?>

            <!-- =============================================== -->

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">

                <?php echo $this->Flash->render(); ?>
                <?php echo $this->Flash->render('auth'); ?>
                <?php echo $this->fetch('content'); ?>

            </div>
            <!-- /.content-wrapper -->

            <?php echo $this->element('footer'); ?>

            <!-- Control Sidebar -->
            <?php echo $this->element('aside-control-sidebar'); ?>

            <!-- /.control-sidebar -->
            <!-- Add the sidebar's background. This div must be placed
            immediately after the control sidebar -->
            <div class="control-sidebar-bg"></div>
        </div>
        <!-- ./wrapper -->

        <?php echo $this->Html->script('AdminLTE./plugins/jQuery/jquery-2.2.3.min'); ?>
        <?php echo $this->Html->script('AdminLTE./bootstrap/js/bootstrap.min'); ?>
        <?php echo $this->Html->script('AdminLTE./plugins/slimScroll/jquery.slimscroll.min'); ?>
        <?php echo $this->Html->script('AdminLTE./plugins/fastclick/fastclick.min'); ?>
        <!-- AdminLTE App -->
        <?php echo $this->Html->script('AdminLTE./js/app.min'); ?>

        <?php echo $this->fetch('script'); ?>
        <?php echo $this->fetch('scriptBottom'); ?>

        <script type="text/javascript">
            $(document).ready(function(){
                $(".navbar .menu").slimscroll({
                    height: "200px",
                    alwaysVisible: false,
                    size: "3px"
                }).css("width", "100%");

                var a = $('a[href="<?php echo $this->request->webroot . $this->request->url ?>"]');
                if (!a.parent().hasClass('treeview') && !a.parent().parent().hasClass('pagination')) {
                    a.parent().addClass('active').parents('.treeview').addClass('active');
                }
            });
        </script>
    </body>
</html>
