/**
 * Created by jhyang on 2016/2/17.
 */
$(function(){
    //TVC.bindInputOnlyNumber();
    TVC.bindChangeTextareaLeftWords();
    /*$("[name=title]").on("keyup input change",function(){
        TVC.changeTextareaLeftWords("span[name=title_max]", 1000, $(this).val().length);
    });*/
    $("#popup_confirm_box [name=confirm]").click(function(){
        $("[name=error]").hide();
        if (!TVC.FormValidator.checkForm()) {
            return;
        }
        var title = $("textarea[name=title]").val();
        var id = $("input[name=id]").val();
        var pid = $("input[name=pid]").val();
        var data = {pid:pid, title: title, id: id};
        var url = "/skill/skill/tagAdd";
        TVC.showLoading();
        TVC.postJson(url, data, saveSuccess, function(result){
            TVC.hideLoading();
            if (result.message == '-1') {
                $("[data-name=title]").text("部分技能标签重复，请重新输入").show();
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
