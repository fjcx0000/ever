<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Include meta tag to ensure proper rendering and touch zooming -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Include jQuery Mobile stylesheets -->
    <link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">

</head>
<body id="mobile-layout">
<?php echo $__env->yieldContent('content'); ?>

<!-- JavaScripts -->
<!-- Include the jQuery library -->
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<!-- Include the jQuery Mobile library -->
<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
