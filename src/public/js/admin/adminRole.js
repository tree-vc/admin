$(function(){
    var _isSubmitting = false;
    var _isFormChanged = {isShow: false};

    function init() {
        //TVC.setCloseWindowTip(_isFormChanged);
        //bindRoleFormEvent();
        //bindSaveEvent();
        //bindInputEvent();
        //initPage();
        bindTabEvent();
    }

    function bindInputEvent() {
        $("input").on("input change", function(){
            _isFormChanged.isShow = true;
        });
    }

    function bindTabEvent() {
        $("span[name=menu_tab]").click(function(){
            var key = $(this).text();
            $("span[name=menu_tab]").removeClass("bg-grey1");
            $("[name=secondary_menu]").hide();
            $(this).addClass("bg-grey1");
            $("#menu_index_" + key).show();
        });
    }

    function initPage() {
        setDefaultCheckBox();
        if (!$("input[name=id]").val()) {
            return;
        }
        var actionStr = $("input[name=role_actions]").val();
        var actions = actionStr.split(",");
        for (var i = 0; i < actions.length; ++i) {
            var act = actions[i].replace(/\//g, "-");
            var checkbox = $("input:checkbox[action=" + act + "]");
            checkbox.attr("checked", "checked");
            var pid = checkbox.attr("pid");
            var topSelector = "#" + checkbox.attr("data-name");           //顶级菜单的id值，用来做父级的选择器id
            if (pid) {
                $(topSelector + " input:checkbox[value=" + pid + "]").attr("checked", "checked");
            }
        }
    }

    function bindSaveEvent() {
        $("#submit").click(function(){
            hideErr();
            if (_isSubmitting) {
                return;
            }
            var id = $("input[name=id]").val();
            var name = $("input[name=name]").val();
            var actionsArr = [];
            $("input[name=actions]:checked").each(function(){
                actionsArr.push($(this).val());
            });
            var actions = actionsArr.join(",");
            var data = {name: name, actions: actions};
            if (id) {
                data['id'] = id;
            }
            if (!validateForm(data)) {
                return;
            }
            TVC.showLoading();
            _isSubmitting = true;
            var url = id ? "/admin/role/save" : "/admin/role/add";
            TVC.postJson(url, data, saveInfoSuccess, saveError);
        });
    }

    function bindRoleFormEvent() {
        $("input:checkbox").click(function () {
            var curr = $(this).attr("data-val");
            var pid = $(this).attr("pid");
            var mid = $(this).attr("mid");
            var topSelector = "#" + $(this).attr("data-name");
            if ($(this).is(":checked")) {
                if (typeof(pid) !== "undefined" && mid) { //第三级
                    $(topSelector + " input:checkbox[value=" + pid + "]").prop("checked", true);
                    $(topSelector + " input:checkbox[data-val=" + mid + "]").prop("checked", true);
                }else if (typeof(pid) !== "undefined" && !mid) {  //第二级
                    $(topSelector + " input:checkbox[value=" + pid + "]").prop("checked", true);
                    $(topSelector + " input:checkbox[mid=" + curr + "]").prop("checked", true);
                }else {  //第一级
                    $(topSelector + " input:checkbox[pid=" + curr + "]").prop("checked", true);
                }
            } else {
                if (typeof(pid) !== "undefined" && !mid) { //第二级
                    $(topSelector + " input:checkbox[mid=" + curr + "]").prop("checked", false);
                }else if (typeof(pid) === "undefined"){  //第一级
                    $(topSelector + " input:checkbox[pid=" + curr + "]").prop("checked", false);
                }
            }
        });
    }

    function validateForm(data) {
        if (data.name === "") {
            showErr("#name_error", "请输入角色名");
            return false;
        }else if (data.name.length > 20) {
            showErr("#name_error", "最多输入20字符");
            return false;
        }

        return true;
    }

    function saveInfoSuccess(rqData) {
        TVC.hideLoading();
        _isFormChanged.isShow = false;
        _isSubmitting = false;
        TVC.showTip("保存成功");
        if (!$("input[name=id]").val()) {
            $("#role_form")[0].reset();
            setDefaultCheckBox();
        }
    }

    function saveError(result) {
        TVC.hideLoading();
        _isSubmitting = false;
        if (result.message === -1) {
            showErr("#name_error", "角色名重复，请重新输入");
        }else {
            TVC.alert("保存失败，请重新保存");
        }
    }

    function showErr(errCarrier, errMsg) {
        $(errCarrier).text(errMsg).show();
    }

    function hideErr() {
        $("[name=error]").hide();
    }

    /**********个人设置下权限默认选中且不可更改***********************/
    function setDefaultCheckBox() {
        $("input[check-sign='个人设置']").prop("checked", true).prop("disabled", true);
        $("input[action='-admin-info-change_password']").prop("checked", true);
        $("input[action='-admin-info-change_password']").prop("disabled", true);
    }

    var Page = {
        init: init
    };

    Page.init();
    return Page;
});
