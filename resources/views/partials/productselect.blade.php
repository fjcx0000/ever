<div class="modal fade" id="preview-popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Product Selection</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label for="product_enquiry" class="col-sm-2 control-label">Products</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="product_enquiry" placeholder="货号、英文或中文名">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-5">
                        <select name="from" id="multiselect_from_0" class="multiselect form-control" size="8" multiple="multiple" data-right="#multiselect_to_1" data-right-all="#right_All_1" data-right-selected="#right_Selected_1" data-left-all="#left_All_1" data-right-selected="#left_Selected_1">
                        </select>
                    </div>

                    <div class="col-xs-2">
                        <button type="button" id="right_All_1" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
                        <button type="button" id="right_Selected_1" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
                        <button type="button" id="left_Selected_1" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
                        <button type="button" id="left_All_1" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
                    </div>

                    <div class="col-xs-5">
                        <select name="to" id="multiselect_to_1" class="form-control" size="8" multiple="multiple"></select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">确认</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(function() {
        $('.multiselect').multiselect();
        $('#product_id').keydown(function(event) {
            if (event.keyCode == "13") {
                $('#preview-popup').modal();
            }
        });
        $('#preview-popup').on('shown.bs.modal', function(){
            $('#product_enquiry').focus();
        });
        $('#preview-popup').on('hidden.bs.modal', function () {
            $('#multiselect_to_1 option').prop('selected', true);
            $('#product_id').val($('#multiselect_to_1').val().toString());
        });
        $('#product_enquiry').keydown(function (event){
            if (event.keyCode == "13") {
                $.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                $.post('/products/selectproducts', {'enqstr': $('#product_enquiry').val()}, function(data, status) {
                    $('#multiselect_from_0').html(data);
                });
            }
        });
    });
</script>
@endpush