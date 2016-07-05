/**
 * Created by jhyang on 2016/2/17.
 */
$(function(){
    TVC.bindInputLimitEvent();
    $("#popup_confirm_box [name=confirm]").click(function(){
        $("[name=error]").hide();
        if (!TVC.FormValidator.checkForm()) {
            return;
        }
        var sort = $("input[name=sort]").val();
        var title = $("input[name=title]").val();
        var id = $("input[name=id]").val();
        var data = {sort: sort, title: title, id: id};
        var url = id ? "/service/classes/save" : "/service/classes/add";
        TVC.showLoading();
        TVC.postJson(url, data, saveSuccess, function(result){
            TVC.hideLoading();
            if (result.message == '-1') {
                $("[data-name=title]").text("一级分类名称重复，请重新输入").show();
            }else {
                TVC.alert(result.message);
            }
        });

        function saveSuccess(rqData) {
            $("#_popup_dialog_box").remove();
            TVC.showTip("保存成功");
            $("#refresh").click();
        }
    });
    $("#popup_confirm_box [name=cancel]").click(function(){
        $("#_popup_dialog_box").remove();
    });
});
