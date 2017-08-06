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
                            {{ csrf_field() }}
                            <input type="hidden" name="filetype" id="filetype" value="{{ $filetype }}" />
                            <input type="file" id="uploadfile" name="uploadfile" data-url="{{ $uploadurl }}" />
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
@push('scripts')
<script type="text/javascript" src="{{ URL::asset('js/jquery.ui.widget.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/jquery.iframe-transport.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/jquery.fileupload.js') }}"></script>
<script>
    $(function() {
        $('#fileupload-popup').on('show.bs.modal', function (e) {
            $("#loading").text("");
        });
        $('#uploadfile').fileupload({
            dataType: 'json',
            add: function (e, data) {
                $("#loading").text('Uploading to '+'{{ $uploadurl }}'+" ...");
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
@endpush
