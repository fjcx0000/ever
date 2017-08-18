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
                <label for="color">选择颜色</label>
                <select name="color" id="color">
                </select>
                <label for="size">选择尺码</label>
                <select name="size" id="size">
                </select>
            </fieldset>
            <button id="submit">提交</button>

        </div>
    </div>
    <div data-role="page" id="stockPage" data-theme="a">
        <div data-role="header" data-position="fixed">
            <h1>库存结果</h1>
            <a href="#mainPage" data-role="button" data-icon="back">返回</a>
        </div>
        <div data-role="content">
            <h2 id="product"></h2>
            <div id="stocks"></div>
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
            $('#searchField').val("");
            $('#color').empty();
            $('#color').selectmenu('refresh');
            $('#size').empty();
            $('#size').selectmenu('refresh');

            $("#searchField").autocomplete({
                target: $('#suggestions'),
                source: '<?php echo e(route("mobile.getproducts")); ?>',
                minLength: 3,
                callback: function(e) {
                    var $a = $(e.currentTarget);
                    $('#searchField').val($a.text());
                    $('#searchField').autocomplete('clear');
                    $goodsno = $a.text().substring(0,$a.text().indexOf('-'));
                    $.ajax({
                        url: '<?php echo e(route('mobile.getcolors')); ?>',
                        type: "get",
                        data: {'goodsno':$goodsno},
                        success: function(data) {
                            $('#color').append("<option></option>");
                            $.each(data, function(i, value){
                                $('#color').append("<option value='"+value.colorcode+"'>"+value.colordesc+"</option>");
                            });
                            $('#color').attr("selectedIndex",0);
                            $('#color').selectmenu('refresh');
                        }
                    });

                }
            });
            $('#color').change(function(){
                $goodsno = $('#searchField').val().substring(0,$('#searchField').val().indexOf('-'));
                $colorcode = $('#color').val();
                $.ajax({
                    url: '<?php echo e(route('mobile.getsizes')); ?>',
                    type: "get",
                    data: {'goodsno':$goodsno, 'colorcode':$colorcode},
                    success: function(data) {
                        $('#size').empty();
                        $('#size').append("<option></option>");
                        $.each(data, function(i, value){
                            $('#size').append("<option value='"+value.barcode+"'>"+value.sizedesc+"</option>");
                        });
                        $('#size').attr("selectedIndex",0);
                        $('#size').selectmenu('refresh');
                    }
                });

            });
            $('#submit').on('click',function(event) {
                $.ajax({
                    url: '<?php echo e(route('mobile.getstock')); ?>',
                    type: "get",
                    data: {'goodsno':$('#searchField').val().substring(0,$('#searchField').val().indexOf('-')),
                        'colorcode':$('#color').val(), 'barcode': $('#size').val()},
                    beforeSend: function () {
                        showLoader();
                    },
                    complete:function(){
                        hideLoader();
                    },
                    success: function(data) {
                        $('#product').text($('#searchField').val());
                        $('#stocks').empty();
                        $('#stocks').append("<div class='ui-grid-b'>" +
                            "<div class='ui-block-a'><span>尺寸</span></div>"+
                            "<div class='ui-block-b'><span>库存</span></div>"+
                            "<div class='ui-block-c'><span>可用</span></div>"+
                            "</div>");
                        $.each(data,function(key,value){
                            $('#stocks').append("<h3>"+key+"</h3>");
                            var $ulstr = "<ul data-role='listview' data-inset='true'>";
                            $.each(value, function(key2,value2){
                                $ulstr += "<li><div class='ui-grid-b'>" +
                                    "<div class='ui-block-a'><span>"+value2.sizedesc+"</span></div>"+
                                    "<div class='ui-block-b'><span>"+value2.qty+"</span></div>"+
                                    "<div class='ui-block-c'><span>"+value2.lockqty+"</span></div>"+
                                    "</div></li>";
                            });
                            $ulstr += "</ul>";
                            $('#stocks').append($ulstr);
                        });
                        $("#stocks ul").listview();
                        $.mobile.changePage("#stockPage");
                    }
                });
            });
        });
        //显示加载器
        function showLoader() {
            //显示加载器.for jQuery Mobile 1.2.0
            $.mobile.loading('show', {
                text: '数据查询中...', //加载器中显示的文字
                textVisible: true, //是否显示文字
                theme: 'a',        //加载器主题样式a-e
                textonly: false,   //是否只显示文字
                html: ""           //要显示的html内容，如图片等
            });
        }

        //隐藏加载器.for jQuery Mobile 1.2.0
        function hideLoader()
        {
            //隐藏加载器
            $.mobile.loading('hide');
        }

    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.mobile', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>