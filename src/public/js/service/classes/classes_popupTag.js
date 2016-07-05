/**
 * Created by jhyang on 2016/2/17.
 */
$(function(){
    var _uploader = null;
    TVC.bindInputLimitEvent();
    initImgUploader();
    bindDeletePicture();
    $("#popup_confirm_box [name=confirm]").click(function(){
        $("[name=error]").hide();
        if (!TVC.FormValidator.checkForm()) {
            return;
        }
        var title = $("input[name=title]").val();
        var id = $("input[name=id]").val();
        var pid = $("input[name=pid]").val();
        var sort = $("input[name=sort]").val();
        var thumb = $("input[name=thumb]").val();
        var data = {pid:pid, title:title, id:id, sort:sort, thumb:thumb};
        var url = id ? "/service/classes/tagSave" : "/service/classes/tagAdd";
        TVC.showLoading();
        TVC.postJson(url, data, saveSuccess, function(result){
            TVC.hideLoading();
            if (result.message == '-1') {
                $("[data-name=title]").text("二级分类名称重复，请重新输入").show();
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
    /**********初始化上传图片插件************/
    function initImgUploader() {
        _uploader = new SingleImageUploader();
        var Option = {
            'pidContainer': 'input[name=thumb]',
            'fileSingleSizeLimit': 1 * 1024 * 1024
        };
        _uploader.init(Option);
        if ($("input[name=id]").val()) {
            _uploader.showFilePicker("重新上传");
        }
    }

    /**（编辑工程师信息时）删除图片(图片不是插件上传，
     *不在插件图片队列中，需手动做成与插件一致)**/
    function bindDeletePicture() {
        //鼠标移动到图片上时显示删除图标，移出时隐藏删除图标
        $(".staticImgDiv").unbind("mouseover").bind("mouseover",function(){
            $(this).children("div[name='close_image']").stop().animate({height: 20});
        }).unbind("mouseout").bind("mouseout",function(){
            $(this).children("div[name='close_image']").stop().animate({height: 0});
        });

        $("[name=close_image]").unbind("click").bind("click",function(){
            $(this).parent().remove();             //清空图片容器（图片消失）
            var pidName = $(this).attr("pid-name");
            $("input[name="+pidName+"]").val("");  //清空该图片pid值
            _uploader.showFilePicker("上传图片")
        });
    }
});
