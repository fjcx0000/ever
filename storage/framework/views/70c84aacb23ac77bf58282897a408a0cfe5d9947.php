<?php $__env->startSection('content'); ?>
    <div data-role="page" id="mainPage" data-theme="a">
        <div data-role="header">
            <h1>库存数据查询</h1>
        </div>
        <div data-role="content">
            <p>
                <label for="searchField">产品编号或名称</label>
                <input type="text" id="searchField" placeholder="产品编号或名称">
            <ul id="suggestions" data-role="listview" data-inset="true"></ul>
            </p>
            <fieldset data-role="fieldcontain">
                <p id="prd-name"></p>
                <label for="day">选择颜色</label>
                <select name="color" id="color">
                </select>
                <button id="submit">提交</button>
            </fieldset>

        </div>
        <div data-role="footer">
            <h1>Footer</h1>
        </div>
    </div>
    <div data-role="page" id="stockPage" data-theme="a">
        <div data-role="header" data-position="fixed">
            <h1>库存结果</h1>
            <a href="#mainPage" data-role="button" data-icon="back">返回</a>
        </div>
        <div data-role="content">
            <p id="product"></p>
            <ul data-role="listview" id="stocks">
            </ul>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('links'); ?>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('scripts'); ?>
    <script type="text/javascript" src="<?php echo e(URL::asset('js/autoComplete.js/jqm.autoComplete-1.5.2-min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(URL::asset('js/autoComplete.js/code.js')); ?>"></script>

    <script>
        $("#mainPage").bind("pageshow", function(e) {

            $("#searchField").autocomplete({
                target: $('#suggestions'),
                source: '<?php echo e(route("mobile.getproducts")); ?>',
                minLength: 1,
                callback: function(e) {
                    var $a = $(e.currentTarget);
                    $('#searchField').val($a.text());
                    $('#searchField').autocomplete('clear');
                    $('#color').empty();
                    $('#color').append("<option value='black'>Black</option>");
                    $('#color').append("<option value='white'>White</option>");
                    $('#color').append("<option value='Chestnut'>Chestnut</option>");

                }
            });
            $('#submit').on('click',function() {
                $.ajax({
                    url: '<?php echo e(route('mobile.getstock')); ?>',
                    type: "get",
                    data: {'products':$('#searchField').val(), 'color':$('#color').val()},
                    success: function(data) {
                        $('#product').text(data.prd_no+"-"+data.prd_name);
                        $('#stocks').empty();
                        $.each(data.stocks,function(n,value){
                            $('#stocks').append("<li>"+value.color+"-"+value.size+",库存="+value.stock+",可用="+value.avail+"</li>");
                        });
                        $.mobile.changePage("#stockPage");
                    }
                });
            });

        });

    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.mobile', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>