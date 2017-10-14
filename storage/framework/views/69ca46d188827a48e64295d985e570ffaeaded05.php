<?php $__env->startSection('content'); ?>
    <div data-role="page" id="mainPage" data-theme="a">
        <div data-role="header">
            <h1>库存数据查询</h1>
            <a href="<?php echo e(route('mstorage.index')); ?>" data-ajax="false" class="ui-btn ui-btn-right ui-icon-grid ui-btn-icon-left">库位巡检</a>
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
            <a href="#mainPage" data-role="button" data-icon="back">返回</a>
            <h1>库存结果</h1>
            <a href="#" onclick="getItemLocations();" class="ui-btn ui-btn-right ui-icon-star ui-btn-icon-left">库位</a>
        </div>
        <div data-role="content">
            <h2 id="product"></h2>
            <div id="stocks"></div>
            </ul>
        </div>
        <div data-role="popup" id="showlocs" class="ui-corner-all">
            <div role="main" class="ui-content">
                <div id="locations"></div>
                <div align="center">
                    <a data-role="button" data-rel="back">返回</a>
                </div>
            </div>
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
        });
        $("#searchField").autocomplete({
            target: $('#suggestions'),
            source: '<?php echo e(route("mobile.getproducts")); ?>',
            minLength: 3,
            callback: function(e) {
                var $a = $(e.currentTarget);
                $('#searchField').val($a.text());
                $('#searchField').autocomplete('clear');
                $goodsno = getGoodsno($a.text());
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
            $goodsno = getGoodsno($('#searchField').val());
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
                data: {'goodsno':getGoodsno($('#searchField').val()),
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

        function getItemLocations()
        {
            $.ajax({
                url: '<?php echo e(route('mstorage.itemlocs')); ?>',
                type: "get",
                data: {'goodsno':getGoodsno($('#searchField').val()),
                    'colorcode':$('#color').val()},
                beforeSend: function () {
                    showLoader();
                },
                complete:function(){
                    hideLoader();
                },
                success: function(data) {
                    $('#locations').empty();
                    if (data.length == 0) {
                        $('#locations').append("<h3>没有库位记录</h3>");
                    } else {
                        $('#locations').append("<div class='ui-grid-a'>" +
                            "<div class='ui-block-a'><span>&nbsp;&nbsp;&nbsp;&nbsp;库位</span></div>"+
                            "<div class='ui-block-b'><span>物品</span></div>"+
                            "</div>");
                        var $ulstr = "<ul data-role='listview' data-inset='true'>";
                        $.each(data,function(key,value){
                            $storageno = value.storageno;
                            $itemstr = value.goodsno+'-'+value.goodsname+'-'+value.colordesc;
                            $comments = value.comments == null ? "" : value.comments;
                            $ulstr += "<li>"+$storageno+"&nbsp;&nbsp;"+$itemstr+"&nbsp;&nbsp;"+$comments+"</li>";
                        });
                        $ulstr += "</ul>";
                        $('#locations').append($ulstr);
                        $("#locations ul").listview();
                    }
                    $('#showlocs').popup();
                    $('#showlocs').popup('open');
                }
            });

        }
        function getGoodsno(str)
        {

            if (str.charAt(str.indexOf('-') + 1) == '-')
                return str.substring(0,str.indexOf('-')+1);
            else
                return str.substring(0,str.indexOf('-'));
        }

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