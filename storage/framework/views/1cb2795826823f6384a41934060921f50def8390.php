<div class="modal fade" id="fileupload-popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">File Uploading</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div>
                        <form action="#" method="post">
                            <?php echo e(csrf_field()); ?>

                            <input type="hidden" name="filetype" id="filetype" value="<?php echo e($filetype); ?>" />
                            <input type="file" id="uploadfile" name="uploadfile" data-url="<?php echo e($uploadurl); ?>" />
                            <br />
                            <p id="loading"></p>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->startPush('scripts'); ?>
<script type="text/javascript" src="<?php echo e(URL::asset('js/jquery.ui.widget.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(URL::asset('js/jquery.iframe-transport.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(URL::asset('js/jquery.fileupload.js')); ?>"></script>
<script>
    $(function() {
        $('#uploadfile').fileupload({
            dataType: 'json',
            add: function (e, data) {
                $("#loading").text('Uploading to '+'<?php echo e($uploadurl); ?>'+" ...");
                data.submit();
            },
            done: function (e, data) {
                if (data.result.result) {
                    $("#loading").addClass("text-success");
                    $("#loading").text(data.result.message);
                } else {
                    $("#loading").addClass("text-warning");
                    $("#loading").text(data.result.message);
                }
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>
