/**
 * 订单详情页
 * Created by jhyang on 2016/4/14.
 */
$(function(){

    function init() {
        TVC.moreInfoTip("","a[data-info=more]");
        getOrderTraceList();
        bindOrderOperateEvent();
    }

    /**************进入订单详情页后再通过ajax去取订单追踪列表**************/
    function getOrderTraceList() {
        var id = $("#order_id").val();
        TVC.getText("/order/trace", {id: id}, function(html){
            var $list = $("<div>"+html+"</div>");
            $("#order_trace_list").html($list.find("#ajax_trace_list").html());
        });
    }

    function bindOrderOperateEvent() {
        $("[data-type=popup]").unbind("click").bind("click",function(){
            var title = $(this).attr("data-title"),         //弹窗的标题，如：添加备注
                url = $(this).attr("data-url"),             //获取弹窗内html片段的url
                id = $(this).attr("data-id"),               //操作的记录id
                category = $(this).attr("data-category");   //忘了，想起来再补
            var data = {id: id, popup: true, category: category};
            TVC.showPopup(title);                           //显示弹窗框架（只有框架，框内为空）
            TVC.postJson(url, data, function(rqData){
                $("#popup_content").html(rqData);            //将获取的框内html片段赋到框内
            },function(result){
                TVC.alert(result.message);
            });
        });
    }


    init();
});