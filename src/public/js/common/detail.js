/**
 * Created by jhyang on 2016/4/1.
 */
$(function(){
    var _engineerId = $("#engineer_id").val();

    function init() {
        bindSwitchTab();
        bindSwitchStatus();
        bindAudit();
    }

    function bindSwitchTab() {
        $(".tab-nav li:lt(3)").on('click', function(){
            var currNav = $(".tab-nav [class*=activeli]");  //获取当前tab
            var currName = currNav.attr('name');
            var name = $(this).attr('name');
            if (currName == name) {                   //点击当前tab，不响应
                return;
            }

            currNav.removeClass("activeli");
            $("#" + currName).hide();               //隐藏当前页面
            $(this).addClass("activeli");

            if (name != "identify") {           //服务设置页面、完善资料页面
                loadHtml(name);
            }
            $("#" + name).show();   //显示新页面
        });
    }

    function loadHtml(name) {
        if ($("#" + name).html()) {     //已加载过
            return;
        }
        var url = (name == "setting") ? "/engineer/engineer/setting" : "/engineer/engineer/info";
        TVC.showLoading();
        TVC.getText(url, {type: 2,'id': _engineerId}, function(rqData){
            $("#" + name).html(rqData);
        });
    }

    function bindSwitchStatus() {
        $("input[name=status]").on("click",function(){
            var status = $(this).val();
            if(status == 1){  //通过
                $("#audit-reason").hide();
                $("[name=error]").hide();
            }else {
                $("#audit-reason").show();
            }
        });
    }

    function bindAudit() {
        $("#audit").click(function () {
            $("[name=error]").hide();
            var status = $("input[name=status]:checked").val();
            var data = {"id": _engineerId, "status": status};
            if (status === '2') {
                data['audit_remark'] = $("[name=audit_remark]").val();
                if (!data['audit_remark']) {
                    $("[data-name=audit_remark]").text("请输入拒绝原因").show();
                    return;
                }else if (data['audit_remark'].length > 100) {
                    $("[data-name=audit_remark]").text("最多输入100字符").show();
                    return;
                }
            }
            $('#audit_project').submit();
            /*TVC.showLoading();
            TVC.postJson("/engineer/engineer/audit", data, auditSuccess, function(result){
                TVC.alert(result.message);
            });*/
        });
    }

    function auditSuccess(rqData) {
        $("#audit_box").hide();
        $("#audit_detail").html(rqData.html).show();
        if (rqData.audited) {
            TVC.alert('该工程师已被审核');
        }else {
            TVC.showTip("审核成功");
        }
    }

    init();
});
