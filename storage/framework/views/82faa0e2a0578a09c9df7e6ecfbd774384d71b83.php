<?php $__env->startSection('content'); ?>
    <div data-role="page" id="mainPage" data-theme="a">
        <div data-role="header" data-position="fixed">
            <a href="<?php echo e(route('mobile.index')); ?>" data-ajax="false" class="ui-btn ui-btn-left ui-icon-info ui-btn-icon-left">库存查询</a>
            <h1>库位巡检</h1>
            <a href="<?php echo e(route('mstorage.erpoptions')); ?>" data-ajax="false" class="ui-btn ui-btn-right ui-icon-grid ui-btn-icon-left">ERP操作</a>
        </div>
        <div data-role="content">
            <h4>库存位置</h4>
            <div class="ui-grid-b">
                <div class="ui-block-a">
                    <label for="area">区</label>
                    <input type="text" id="area" readonly="yes" value="">
                </div>
                <div class="ui-block-b">
                    <label for="line">列</label>
                    <input type="text" id="line" readonly="yes" value="">
                </div>
                <div class="ui-block-c">
                    <label for="unit">间</label>
                    <input type="text" id="unit" readonly="yes" value="">
                </div>
            </div>
            <h4>存放物品</h4>
            <ul data-role="listview" data-inset="true" id="levels">
            </ul>
        </div>
        <div data-role="popup" id="itemcontent">
        </div>
        <div data-role="popup" id="popupOptResp">
            <h4 id="respMsg"></h4>
        </div>
        <div data-role="popup" id="confirm-itemdel">
            <div role="main" class="ui-content">
                <h3 id="notice-itemdel"></h3>
                <div style="display:none;" id="item-id"></div>
                <div align="center">
                    <a data-role="button" data-rel="back" onclick="deleteitem();" data-inline="true" class="ui-btn-active">确认</a>
                    <a data-role="button" data-rel="back" data-inline="true">取消</a>
                </div>
            </div>
        </div>
        <div data-role="popup" id="locInput" data-dismissible="false" class="ui-content">
            <h4>库位选择：</h4>
            <div data-role="fieldcontain">
                <table>
                    <tr>
                        <td>
                            <label for="areainput">区：</label>
                        </td>
                        <td>
                            <select name="areainput" id="areainput">
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="lineinput">列：</label>
                        </td>
                        <td>
                            <select name="lineinput" id="lineinput">
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="unitinput">间：</label>
                        </td>
                        <td>
                            <select name="unitinput" id="unitinput">
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="text-align: center;">
                <a href="#" data-role="button" data-ajax="false" data-rel="back" data-transition="flow"
                   onclick="setLocation();" data-inline="true" class="ui-btn-active">跳转</a>
                <a data-role="button" data-rel="back" data-inline="true">取消</a>
            </div>
        </div>
        <div data-role="popup" id="popupItemAdd" data-dismissible="false" class="ui-content">
            <div>
                <label for="searchField">产品编号或名称</label>
                <input type="text" id="searchField" placeholder="产品编号或名称">
            <ul id="suggestions" data-role="listview" data-inset="true"></ul>
            </div>
            <fieldset data-role="fieldcontain">
                <p id="prd-name"></p>
                <label for="color">选择颜色</label>
                <select name="color" id="color">
                </select>
                <label for="comments">备注</label>
                <input type="text" id="comments">
                <input type="hidden" id="storageLevel">
            </fieldset>
            <div align="center">
                <a data-role="button" data-rel="back" onclick="submitAddItem();" data-inline="true" class="ui-btn-active">确认</a>
                <a data-role="button" onclick="resetAddItem();" data-inline="true">重置</a>
                <a data-role="button" data-rel="back" data-inline="true">取消</a>
            </div>

        </div>
        <div data-role="footer" data-position="fixed" data-id="footernav" data-theme="a">
            <div data-role="navbar">
                <ul>
                    <li><button id="nextPosi">下一库位</button></li>
                    <li><button id="gotoPosi">跳转位置</button></li>
                    <li><button id="prevPosi">上一库位</button></li>
                </ul>
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
    $(document).on('pageinit', function(event){
        $.ajax({
            url: '<?php echo e(route('mstorage.firstloc')); ?>',
            type: "get",
            success: function(data) {
                loadLocdata(data);
            }
        });
    });
    $('#nextPosi').on('click',function(event) {
        $.ajax({
            url: '<?php echo e(route('mstorage.nextloc')); ?>',
            type: "get",
            data: {'area':$('#area').val(), 'line':$('#line').val(), 'unit':$('#unit').val()},
            beforeSend: function () {
                showLoader();
            },
            complete:function(){
                hideLoader();
            },
            success: function(data) {
                loadLocdata(data);
            }
        });
    });
    $('#prevPosi').on('click',function(event) {
        $.ajax({
            url: '<?php echo e(route('mstorage.prevloc')); ?>',
            type: "get",
            data: {'area':$('#area').val(), 'line':$('#line').val(), 'unit':$('#unit').val()},
            beforeSend: function () {
                showLoader();
            },
            complete:function(){
                hideLoader();
            },
            success: function(data) {
                loadLocdata(data);
            }
        });
    });
    $('#gotoPosi').on('click', function(event){
        $('#locInput').popup();
        $('#locInput').popup('open');
        $('#areainput').empty();
        $('#areainput').selectmenu('refresh');
        $('#lineinput').empty();
        $('#lineinput').selectmenu('refresh');
        $('#unitinput').empty();
        $('#unitinput').selectmenu('refresh');
        $.ajax({
            url: '<?php echo e(route('mstorage.arealist')); ?>',
            type: "get",
            success: function(data) {
                $('#areainput').empty();
                $('#areainput').append("<option></option>");
                $.each(data, function(i, value){
                    $('#areainput').append("<option value='"+value.area+"'>"+value.area+"</option>");
                });
                $('#areainput').attr("selectedIndex",0);
                $('#areainput').selectmenu('refresh');
            }
        });
    });
    $('#areainput').change(function(){
        $.ajax({
            url: '<?php echo e(route('mstorage.linelist')); ?>',
            type: "get",
            data: {'area': $('#areainput').val()},
            success: function(data) {
                $('#lineinput').empty();
                $('#lineinput').append("<option></option>");
                $.each(data, function(i, value){
                    $('#lineinput').append("<option value='"+value.line+"'>"+value.line+"</option>");
                });
                $('#lineinput').attr("selectedIndex",0);
                $('#lineinput').selectmenu('refresh');
            }
        });
    });
    $('#lineinput').change(function(){
        $.ajax({
            url: '<?php echo e(route('mstorage.unitlist')); ?>',
            type: "get",
            data: {'area': $('#areainput').val(), 'line': $('#lineinput').val()},
            success: function(data) {
                $('#unitinput').empty();
                $('#unitinput').append("<option></option>");
                $.each(data, function(i, value){
                    $('#unitinput').append("<option value='"+value.unit+"'>"+value.unit+"</option>");
                });
                $('#unitinput').attr("selectedIndex",0);
                $('#unitinput').selectmenu('refresh');
            }
        });
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
                    $('#color').empty();
                    $('#color').selectmenu('refresh');
                    $('#comments').val("");
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
    /*
 * Fix for footer when the keyboard is displayed
 */
    $(document).on('focus', 'input, textarea', function()
    {
        $.mobile.activePage.find("div[data-role='footer']").hide();
    });

    $(document).on('blur', 'input, textarea', function()
    {
        $.mobile.activePage.find("div[data-role='footer']").show();
    });

    function submitAddItem() {
        $.ajax({
            url: '<?php echo e(route('mstorage.additem')); ?>',
            headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
            type: "post",
            data: {'goodsno':getGoodsno($('#searchField').val()),
                'colorcode':$('#color').val(), 'barcode': $('#size').val(),
                'area':$('#area').val(),
                'line': $('#line').val(),
                'unit': $('#unit').val(),
                'level': $('#storageLevel').val(),
                'comments': $('#comments').val(),
            },
            beforeSend: function () {
                showLoader();
            },
            complete:function(){
                hideLoader();
            },
            success: function(data) {
                $('#respMsg').empty();
                $('#respMsg').text(data);
                $('#popupOptResp').popup();
                $('#popupOptResp').popup('open');
                setTimeout(function(){$('#popupOptResp').popup('close');}, 1000);
                refreshLocation();
            }
        });
    }

    function loadLocdata(locdata)
    {
        $('#area').val(locdata.area);
        $('#line').val(locdata.line);
        $('#unit').val(locdata.unit);
        $('#levels').empty();
        $.each(locdata.levels, function(i, level) {
            $('#levels').append("<li data-role='list-divider'>第"+level.level+"层</li>");
            $.each(level.items, function(i, item){
                if (item.comments == null) item.comments = " ";
                itemcontent=item.goodsno+"-"+item.goodsname+"-"+item.colordesc+". "+item.comments;
                $('#levels').append("<li><a onclick='showItemContent(\""+itemcontent+"\")'>"+itemcontent+
                "</a><a data-icon='delete' onclick='confirmItemdelete(\""+item.id+"\",\""+itemcontent+"\")'>Delete</a></li>");
            });
            $('#levels').append("<li><a>增加存放物品</a><a data-icon='plus' onclick='addStorageItem(\""+level.level+
                "\")'>Plus</a></li>");
        });
        $('#levels').listview('refresh');
    }
    function setLocation()
    {
        $('#area').val($('#areainput').val());
        $('#line').val($('#lineinput').val());
        $('#unit').val($('#unitinput').val());
        refreshLocation();
    }
    function showItemContent(itemcontent)
    {
        $('#itemcontent').empty();
        $('#itemcontent').append("<p>"+itemcontent+"</p>");
        $('#itemcontent').popup();
        $('#itemcontent').popup('open');
    }
    function confirmItemdelete(itemid,itemcontent)
    {
        $("#notice-itemdel").empty();
        $("#notice-itemdel").text("删除"+itemcontent+"？");
        $("#item-id").empty();
        $("#item-id").text(itemid);
        $('#confirm-itemdel').popup();
        $('#confirm-itemdel').popup('open');
    }
    function deleteitem()
    {
        $.ajax({
            url: '<?php echo e(route('mstorage.deleteitem')); ?>',
            headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
            type: "post",
            data: {'id': $('#item-id').text()},
            success: function() {
                $('#respMsg').empty();
                $('#respMsg').text('删除成功！');
                $('#popupOptResp').popup();
                $('#popupOptResp').popup('open');
                setTimeout(function(){$('#popupOptResp').popup('close');}, 1000);
                refreshLocation();
            }
        });

    }

    function refreshLocation()
    {
        $.ajax({
            url: '<?php echo e(route('mstorage.locdata')); ?>',
            type: "get",
            data: {'area': $('#area').val(), 'line': $('#line').val(), 'unit': $('#unit').val()},
            beforeSend: function () {
                showLoader();
            },
            complete:function(){
                hideLoader();
            },
            success: function(data) {
                loadLocdata(data);
            }
        });
    }

    function addStorageItem(level)
    {
        $('#storageLevel').val(level);
        $('#comments').val("");
        $('#popupItemAdd').popup();
        $('#popupItemAdd').popup('open');
    }
    function resetAddItem()
    {
        $('#searchField').val("");
        $('#color').empty();
        $('#color').selectmenu('refresh');
        $('#comments').val("");
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