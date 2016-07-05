/**
 * Created by jhyang on 2015/11/24.
 */
$(function(){
    //TVC.setCloseWindowTip("未保存信息，确定要离开吗");
    var _validateItem = ["productid","versionid","platform","status","url"];
    var _isFormChanged = {isShow: false};
    function init() {
        TVC.setCloseWindowTip(_isFormChanged);
        bindInputEvent();
        bindSaveEvent();
    }

    function bindInputEvent() {
        $("input[name=versionid]").on("keyup", function () {
            var val = $(this).val();
            if (!(/^[0-9]*$/.test(val))) {
                var len = $(this).val().length;
                $(this).val($(this).val().substr(0, len - 1));
            }
        });
        /*$("[name=description]").on("keyup", function(){
            var val = $(this).val();
            if (val.length >= 100) {
                $(this).val(val.substr(0, 100));
            }
        });*/
        $("input,select,textarea").on("input change", function(){
            _isFormChanged.isShow = true;
        });
    }
    function bindSaveEvent() {
        $("input[name=submit]").click(function(){
            hideError();
            var data = getFormData();
            if (!validate(data)) {
                return;
            }
            TVC.showLoading("");
            var url = data['id'] ? "/app/version/save" : "/app/version/add";
            TVC.postJson(url, data, saveSuccess, saveError);
        });
    }

    function getFormData() {
        var data = {};
        data['productid'] = $("select[name=productid]").find("option:selected").val();
        data['platform'] = $("select[name=platform]").find("option:selected").val();
        data['status'] = $("input[name=status]:checked").val();
        $(".input_text").each(function () {
            data[$(this).attr("name")] = $.trim($(this).val());
        });
        var id = $("input[name=id]").val();
        if (id) {
            data['id'] = id;
        }
        return data;
    }

    function validate(data) {
        var isValidate = true;
        for (var i = 0; i < _validateItem.length; ++i) {
            if (data[_validateItem[i]] === "" || typeof(data[_validateItem[i]]) === "undefined") {
                isValidate = false;
                $("#"+_validateItem[i]+"_error").show();
            }
        }
        return isValidate;
    }

    function saveSuccess(result) {
        _isFormChanged.isShow = false;
        TVC.hideLoading();
        TVC.showTip("保存成功");
        if (!$("input[name=id]").val()) {
            $("#version_form")[0].reset();
        }
    }

    function saveError(error) {
        TVC.hideLoading();
        TVC.alert(error);
    }

    function hideError() {
        $(".errorMsg").hide();
    }

    init();
});
