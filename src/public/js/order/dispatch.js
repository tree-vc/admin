/**
 * 派单页
 * Created by jhyang on 2016/4/14.
 */

/*************查询列表数据后的回调函数，在listTable js中调用******************/
function search_success_callback() {
    orderDispatch.bindOperate();
}

$(function(){

    function init() {
        TVC.moreInfoTip("","a[data-info=more]");
        bindOrderTrace();
        bindChooseDistanceEvent();
    }

    /*************选择距离操作******************/
    function bindChooseDistanceEvent() {
        $("input[data-name=range]").click(function(){
            if (!$(this).hasClass("active")) {
                $("input[data-name=range]").removeClass("active");
                $(this).addClass("active");
                $("input[name=range]").val($(this).attr("data-val"));
            }
        });

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

    function bindDispatchEvent() {
        $("a[name=dispatch]").unbind("click").bind("click", function(){
            var engineerId = $(this).attr("engineer-id");
            var orderId = $("#order_id").val();
            TVC.confirm("确定给该工程师派单吗？", function(){
                TVC.showLoading();
                var data = {id: orderId, engineer_id: engineerId};
                TVC.postJson("/order/dispatch", data, function(rq){
                    TVC.showTip('派单成功', 1500, true, function(){
                        TVC.redirect("/order/list");
                    });
                },function(result){
                    if (result.message == -1) {
                        TVC.alert("派单失败，请重新派单");
                        return;
                    }
                    TVC.alert(result.message, function(){
                        TVC.redirect("/order/list");
                    });
                })
            });

        });
    }


    var page = {
        bindOperate: bindDispatchEvent
    };

    window['orderDispatch'] = page;
    init();
});