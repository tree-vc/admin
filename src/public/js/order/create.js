/**
 * 新建、编辑订单页面
 * Created by jhyang on 2016/4/15.
 */
$(function(){
    var _id = $("#order_id").val();
    var _uploader = null;
    var _serviceItemList = {};          //根据二级分类获取的服务项列表
    var _orderItems = _id ? $.parseJSON($("#service_item_json").text()) : [];  //已选择的订单的服务项列表
    var _currentItem = {};              //当前选中的服务项
    var _firstItem = null;
    var _orderStatic = null;
    var _itemTypes = ['failure_types', 'service_types', 'device_types', 'os_types', 'device_brands', 'device_components'];

    var _addressChoosed = $("input[name=address_name]").val() ? true : false;
    //是否选择了地址下拉列表中的某项

    function init() {
        bindOrderServiceItemEvent();
        changeServiceClass1Event();
        changeServiceClass2Event();
        changeServiceItemEvent();
        initImageUploader();
        saveOrderEvent();
        clickItemDetailEvent();
        bindInputAddress();
        bindCheckCustomerIsExist();
    }

    /************初始化上传图片插件*************/

    function initImageUploader() {
        _uploader = new ImageUploader();
        _uploader.init({
            'fileCountLimit': 3,
            'fileSingleSizeLimit': 5 * 1024 * 1024,
            'leftImgNumSelector': "#left_img_num"
        });
    }

    /******选择一级分类事件*************/
    function changeServiceClass1Event() {
        $("select[name=class1]").on("change", function(){
            var pid = $(this).val();
            var option = pid ? $("#classes_t1_child_"+pid).html() : "<option value=''>请选择二级分类</option>";
            $("select[name=class2]").html(option);   //显示该一级分类对应的二级分类列表
            removeServiceItem();
            hideItemDetail();
        });
    }

    /******选择二级分类事件*************/
    function changeServiceClass2Event() {
        $("select[name=class2]").on("change", function(){
            var id = $(this).val();
            removeServiceItem();
            hideItemDetail();
            if (id) {
                initServiceItemSelectByClass2Id(id);
            }
        });
    }

    /******根据二级分类id初始化服务项select框***********/

    function initServiceItemSelectByClass2Id(id) {
        TVC.showLoading();
        TVC.postJson("/order/serviceItem", {id: id}, function(rqData){
            var option = "";
            _serviceItemList = {};
            for (var i = 0; i < rqData.length; ++i) {
                _serviceItemList[rqData[i]['id']] = rqData[i];
                option += "<option value='"+rqData[i]['id']+"'>" + rqData[i]['name'] + "</option>";
            }
            $("select[name=item]").append(option);
        });
    }

    /******选择服务项事件***********/

    function changeServiceItemEvent() {
        $("select[name=item]").on("change", function(){
            var id = $(this).val();
            hideItemDetail();
            if (id) {
                showServiceItemDetailByItemId(id);
            } else {
                _currentItem = {};
            }
        });
    }

    /********根据选择的服务项显示该服务项的详细信息（故障类型...）*********/

    function showServiceItemDetailByItemId(id) {
        _currentItem = _serviceItemList[id];
        for (var i = 0; i < _itemTypes.length; ++i) {
            var type = _itemTypes[i];
            if (_serviceItemList[id][type]) {
                var typeValArr = _serviceItemList[id][type].split(";");
                var li = "";
                for (var j = 0; j < typeValArr.length; ++j) {
                    li += "<li>" + typeValArr[j] + "</li>"
                }
                $("ul[type-name="+type+"]").html(li);
                $("#" + type).show();
            }
        }
    }


    /******绑定订单服务项相关操作*************/

    function bindOrderServiceItemEvent() {
        $("#add_service_item").click(showServiceItemBox);                //添加事件
        $("#service_item_box [name=cancel]").click(hideServiceItemBox);  //取消添加事件
        $("#service_item_box [name=confirm]").click(addOrderServiceItem);//确定添加事件
        bindDeleteServiceItemEvent();                                    //删除事件
    }

    /***************删除服务项********************/

    function bindDeleteServiceItemEvent() {
        $("a[name=delete_service_item]").unbind("click").bind("click", function(){
            var $item = $(this);
            TVC.confirm("是否删除该服务项", function(){
                var itemId = $item.attr("data-id");
                for (var i = 0; i < _orderItems.length; ++i) {
                    if (_orderItems[i]['service_id'] == itemId) {
                        _orderItems.splice(i, 1);
                        break;
                    }
                }
                $item.parent().parent().remove();
                calOrderStatic();
            });
        })
    }

    /******添加订单服务项************/

    function addOrderServiceItem() {
        $("#service_item_form [name=error]").hide();
        if (!TVC.FormValidator.checkForm("#service_item_form")) {
            return;
        }
        var item = {
            "service_class1": $("select[name=class1] option:selected").text(),
            "service_class2": $("select[name=class2] option:selected").text(),
            "service_name": $("select[name=item] option:selected").text(),
            "service_id": $("select[name=item] option:selected").val(),
            "device_num": $("input[name=device_num]").val(),
            "duration": _currentItem["duration"],
            "price": _currentItem["price"]
        };
        for (var i = 0; i < _itemTypes.length; ++i) {
            var type = _itemTypes[i];
            var values = [];
            $("ul[type-name="+type+"] li[class=on]").each(function(){
                values.push($(this).text());
            });
            item[type] = values.join(";");
        }
        if (!_firstItem) {
            _firstItem = item;
        }
        _orderItems.push(item);               //添加订单服务项
        insertNewItemIntoServiceItemList(item);
        hideServiceItemBox();
    }

    function removeServiceItem() {
        $("select[name=item] option[value!='']").remove();   //移除服务项列表
    }

    function hideItemDetail() {
        $("tr[data-name=item_type]").hide();                //隐藏服务项的具体各种类型：如故障类型等
        $("ul[type-name]").html("");
    }

    /****************在服务项列表中插入新添加的服务项***********************/

    function insertNewItemIntoServiceItemList(item) {
        for (var i = 0; i < _itemTypes.length; ++i) {
            var type = _itemTypes[i];
            setItemTypeValue(type, item[type]);
        }
        setItemTypeValue("service_name", item["service_name"]);
        setItemTypeValue("device_num", item["device_num"]);
        setItemTypeValue("price", (item["price"]/100).toFixed(2) + "元");
        var duration = getDuration(item["duration"]);
        setItemTypeValue("duration", duration);
        $("#service_item_demo [name=delete_service_item]").attr("data-id", item["service_id"]);

        var html = "<tr order-service-item>" + $("#service_item_demo").html() + "</tr>";
        $("#order_service_item_list").append(html);
        bindDeleteServiceItemEvent();
        calOrderStatic();

        function setItemTypeValue(type, val) {
            $("#service_item_demo [td-name="+type+"]").text(val);
        }
    }

    /************统计订单信息（总价、总时长、设备总数）*******************/

    function calOrderStatic() {
        var price = 0;
        var duration = 0;
        var deviceNum = 0;
        for (var i = 0; i < _orderItems.length; ++i) {
            price += parseInt(_orderItems[i]['price']) * parseInt(_orderItems[i]['device_num']);
            duration += parseInt((_orderItems[i]['duration']))  * parseInt(_orderItems[i]['device_num']);
            deviceNum += parseInt(_orderItems[i]['device_num']);
        }
        _orderStatic = {price: price, duration: duration, device_num: deviceNum};
        price = (price/100).toFixed(2);
        duration = getDuration(duration);
        $("#order_price").text(price);
        $("#order_duration").text(duration);
    }

    function getDuration(duration) {
        if (duration < 60) {
            return duration + "分钟";
        }
        var hour = Math.floor(duration / 60);
        var minute = duration % 60;
        if (minute == 0) {
            return hour + '小时';
        }
        return hour + '小时' + minute + '分钟';
    }

    /********显示添加服务项弹窗**********/
    function showServiceItemBox() {
        $("#service_item_box").show();
    }

    /**********隐藏添加服务项弹窗***********/
    function hideServiceItemBox() {
        $("#service_item_box").hide();
        $("#service_item_form")[0].reset();
        $("select[name=class2] option[value!='']").remove();
        removeServiceItem();
        hideItemDetail();
    }

    /***************点击服务项详情类型事件********************/

    function clickItemDetailEvent() {
        $(".items.radio").on("click","li",function(){
            $(this).toggleClass("on").siblings().removeClass("on");
        });

        $(".items.checkbox").on("click","li",function(){
            $(this).toggleClass("on");
        });
    }

    /***********服务地址联想词*************/

    function bindInputAddress() {
        $("#key_word").on("input", function(){
            _addressChoosed = false;
        });

        function onSelectStart(locationData){
            _addressChoosed = true;
            var lnglat = locationData.lnglat.split(',');
            $("#key_word").val(locationData.name);
            $("input[name=address]").val(locationData.address);
            $("input[name=province]").val(locationData.province);
            $("input[name=city]").val(locationData.city);
            $("input[name=longitude]").val(lnglat[0]);
            $("input[name=latitude]").val(lnglat[1]);
        }
        AB.Address("#key_word","#suggestStart",onSelectStart);
    }

    function bindCheckCustomerIsExist() {
        $("input[name=mobile]").blur(function(){
            var mobile = $(this).val();
            if (!mobile) {
                $("[data-name=mobile]").text("请输入客户账户").show();
                return;
            }
            if (!(/^1[3-9][0-9]{9}$/.test(mobile))) {
                $("[data-name=mobile]").text("请输入正确的手机号").show();
                return;
            }
            TVC.postJson("/order/checkCustomer", {mobile: mobile}, function(r){},
            function(result){
                $("[data-name=mobile]").text(result.message).show();
            });
        });
    }

    /***************保存订单********************/

    function saveOrderEvent() {
        $("#submit").click(function(){
            $("#order_form [name=error]").hide();
            if (_id) {                  //修改订单
                modifyOrder();
                return;
            }
            var isHaveItem = isHaveServiceItem();
            var isChooseServiceAddress = isChooseAddress();
            if (!TVC.FormValidator.checkForm("#order_form") || !isHaveItem || !isChooseServiceAddress) {
                return;
            }

            var data= getFormData();
            TVC.showLoading();
            TVC.postJson("/order/add", data, function(rq){
                TVC.showTip("保存成功");
                TVC.reload();
            },function(result){
                TVC.alert(result.message);
            });
        });
    }

    /*******************修改订单**********************/

    function modifyOrder() {
        var isHaveItem = isHaveServiceItem();
        if (!TVC.FormValidator.checkForm("#order_form") || !isHaveItem) {
            return;
        }
        var data = {id: _id};
        var isItemChanged = isServiceItemChanged();
        if (isItemChanged) {
            data['service'] = JSON.stringify(_orderItems);
        }

        var serviceTime = $("input[name=service_at]").val() + " " + $("select[name=service_hour] option:selected").val() + ":" +
            $("select[name=service_minute] option:selected").val();

        var ss = $("#service_time").val();

        var isServiceTimeChanged = serviceTime != $("#service_time").val();
        if (isServiceTimeChanged) {
            data['service_at'] = serviceTime + ":00";
        }

        if (!isItemChanged && !isServiceTimeChanged) {
            TVC.showTip("订单信息无变化");
            return;
        }

        TVC.showLoading();
        TVC.postJson("/order/modify", data, function(rq){
            TVC.showTip("订单修改成功");
            $("#service_item_json").text(JSON.stringify(_orderItems));
            $("#service_time").val(serviceTime);
        },function(result){
            TVC.alert(result.message);
        });
    }

    /********************服务项是否发生改变**************************/

    function isServiceItemChanged() {
        var tmpItem = $.parseJSON($("#service_item_json").text());
        var srcOrderItem = {};
        for (var i = 0; i < tmpItem.length; ++i) {
            srcOrderItem[tmpItem[i]['service_id']] = tmpItem[i];
        }
        for (var j = 0; j < _orderItems.length; ++j) {
            var serviceId = _orderItems[j]['service_id'];
            if (!srcOrderItem[serviceId]) {  //服务项已变更
                return true;
            }
            //服务台数变更
            if (srcOrderItem[serviceId]['device_num'] != _orderItems[j]['device_num']) {
                return true;
            }
        }
        return false;
    }


    /*****************是否添加了服务项********************/
    function isHaveServiceItem() {
        if ($("[order-service-item]").length == 0) {
            $("[data-name=service_item_error]").text("请添加服务项").show();
            return false;
        }
        return true;
    }

    /*****************是否选择了服务地址********************/
    function isChooseAddress() {
        var addressName = $("input[name=address_name]").val();
        //只有门牌号没有地址是不可以的
        if (!addressName || !_addressChoosed) {
            $("[data-name=location]").text("请选择服务地址").show();
            return false;
        }
        return true;
    }

    function getFormData() {

        function getServiceAddress() {
            var address = {};
            address['province'] = $("input[name=province]").val();
            address['city'] = $("input[name=city]").val();
            address['name'] = $("input[name=address_name]").val();
            address['address'] = $("input[name=address]").val();
            address['no'] = $("input[name=no]").val();
            address['longitude'] = $("input[name=longitude]").val();
            address['latitude'] = $("input[name=latitude]").val();
            return address;
        }

        var data = {};
        var elements = ["mobile", "contact_name", "contact_tel", "failure_desc"];
        for (var i = 0 ; i < elements.length; ++i) {
            data[elements[i]] = TVC.Form.elementValue(elements[i]);
        }
        var pids = [];
        $("[name=pid_container]").each(function(){
            var pid = $(this).attr("pid");
            if (pid) {
                pids.push($(this).attr("pid"));
            }
        });
        for (var staticName in _orderStatic) {
            data[staticName] = _orderStatic[staticName];
        }
        data['service_at'] = $("input[name=service_at]").val() + " " + $("select[name=service_hour] option:selected").val() + ":" +
            $("select[name=service_minute] option:selected").val() + ":00";
        data['failure_photos'] = pids.join(",");
        var address = getServiceAddress();
        data['service_address'] = JSON.stringify(address);
        data['service_item_list'] = JSON.stringify(_orderItems);
        return data;
    }

    init();
});
