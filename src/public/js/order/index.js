/**
 * 订单列表页
 * Created by jhyang on 2016/4/13.
 */

/*************查询列表数据后的回调函数，在listTable js中调用******************/
function search_success_callback() {
    orderIndex.bindOperate();
}

$(function(){
    var _customerInfo = {};       //客户信息

    function bindOperateEvent() {
        bindOperateCustomerInfo();
        bindClickServiceMore();
        bindOrderTrace();
    }

    /***************订单追踪***********************/
    function bindOrderTrace() {
        $("a[name=order_trace]").unbind("click").bind("click", function(){
            var id = $(this).attr("order-id");
            TVC.showPopup("订单追踪");                           //显示弹窗框架（只有框架，框内为空）
            TVC.getText("/order/trace", {id: id}, function(html){
                $("#popup_content").html(html);                 //将获取的框内html片段赋到框内
            });
        })
    }

    /**************点击服务项更多按钮*********************/
    function bindClickServiceMore() {
        $("a[name=service_detail]").unbind("click").bind("click",function(){
            var that = this;
            var id = $(this).attr("order-id");
            if (!$("#service_detail_" + id).is(":hidden")) {  //更多已处于显示状态，隐藏它
                $("#service_detail_" + id).hide();
                $(this).find("span.gengduo").html("&or;");
                $.Win.setPageWidth();
            } else if ($("#service_detail_" + id).html()) {   //更多处于隐藏状态，且有更多内容，显示它
                $("#service_detail_" + id).show();
                $(this).find("span.gengduo").html("&and;");
                $.Win.setPageWidth();

            } else {  //更多处于隐藏状态，且没有更多内容，需去服务器端加载数据
                TVC.showLoading();
                TVC.getText("/order/services", {id: id}, function(html){
                    $("#service_detail_" + id).html(html).show();
                    $(that).find("span.gengduo").html("&and;");
                    $.Win.setPageWidth();
                });
            }

        });
    }

    /***************客户信息的展示与隐藏***********************/
    function bindOperateCustomerInfo() {
        $("a[name=customer_info]").unbind("mouseover mousemove").bind("mouseover mousemove",function(event){
            var e = event || window.event || arguments.callee.caller.arguments[0];
            var x=e.pageX;
            var y=e.pageY;
            var orderId = $(this).attr("order-id");
            var id = $(this).attr("customer-id");
            if (!_customerInfo[id]) {
                TVC.showLoading();
                TVC.postJson("/order/customer",{id: id},function(rqData){
                    _customerInfo[id] = rqData;
                },function(result){TVC.alert(result.message);});
            }
            showCustomerInfo(_customerInfo[id], orderId,x,y);
        });

        $("a[name=customer_info]").unbind("mouseout").bind("mouseout",function(){
            var orderId = $(this).attr("order-id");
            $("#customer_info_" + orderId).hide();
        });
    }
    /***************浮层显示客户信息*********************/
    function showCustomerInfo(customer, orderId,x,y,defaultTop) {
        if (!customer) {
            return;
        }
        var hasNoMsg = true;
        for (var name in customer) {
            if (customer[name] == "" || customer[name] == 0) {
                $("#customer_info_" + orderId + " span[name=" + name + "]").parent().hide();
            } else {
                hasNoMsg = false;
                $("#customer_info_" + orderId + " span[name=" + name + "]").text(customer[name]);
            }
        }

        if (hasNoMsg) {
            $("#customer_info_" + orderId + " p[name=no_msg]").show();
        }
        //$("#customer_info_" + orderId).show();
        var defaultTop=defaultTop || 25;
        var top=y;
        var left=x;
        var winWidth=$(window).width();
        var winHeight=$(window).height();
        var divWidth=parseInt($("#customer_info_" + orderId).outerWidth());
        var divHeight=parseInt($("#customer_info_" + orderId).outerHeight());
        if((winHeight - top) >=  divHeight){
            $("#customer_info_" + orderId).find("div").removeClass("arrows-bottom").addClass("arrows-top");
            $("#customer_info_" + orderId).css({
                "top": (y+defaultTop)+"px",
                "left":(x-divWidth/2)+"px"
            }).show();
        }else{
            $("#customer_info_" + orderId).find("div").removeClass("arrows-top").addClass("arrows-bottom");
            $("#customer_info_" + orderId).css({
                "top": (y-defaultTop-divHeight)+"px",
                "left":(x-divWidth/2)+"px"
            }).show();
        }


    }

    var page = {
        bindOperate: bindOperateEvent
    };


    window['orderIndex'] = page;
});