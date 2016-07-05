/**
 * Created by jhyang on 2016/1/6.
 */
$(function(){
    var _isSubmitting = false;

    function init() {
        bindUserOperateEvent();
        bindDialogBoxEvent();
    }

    function bindDialogBoxEvent() {
        $("#confirm").click(function(){
            if (_isSubmitting) {          //防止重复提交
                return;
            }
            hideErr();
            var data = getFormData();
            if (!validate(data)) {
                return false;
            }
            _isSubmitting = true;
            var url = data.id ? "/admin/user/save" : "/admin/user/add";
            TVC.showLoading();
            TVC.postJson(url, data, saveSuccess, saveError);
        });

        $("[name=cancel]").click(function(){
            $("#add_dialog_box").hide();
            hideErr();
        });
    }

    function saveError(result) {
        var error = result.message;
        TVC.hideLoading();
        _isSubmitting = false;
        if (error === "-1") {
            showErr("#name_error", "用户名重复，请重新输入");
        }else if (error === "-2") {
            showErr("#email_error", "邮箱重复，请重新输入");
        }else {
            TVC.alert("保存失败，请重新保存");
        }
    }

    function getFormData() {
        var name = $("input[name=name]").val();
        var nickname = $("input[name=nickname]").val();
        var email = $("input[name=email]").val();
        var roles = [];
        $("input:checkbox:checked").each(function(){
            roles.push($(this).val());
        });
        var data = {name: name, nickname: nickname, email: email,roles_id: roles.join(",")};
        var id = $("input[name=id]").val();
        if (id) {
            data['id'] = id;
        }
        return data;
    }

    function validate(data) {
        var _isLegal = true;
        if (data.name === "") {
            showErr("#name_error", "请输入用户名");
            _isLegal = false;
        }else if (!(/^[a-zA-Z0-9_]{4,20}$/.test(data.name))) {
            showErr("#name_error", "请输入正确的格式");
            _isLegal = false;
        }
        if (data.nickname === "") {
            showErr("#nickname_error", "请输入姓名");
            _isLegal = false;
        }else if (data.nickname.length > 50) {
            showErr("#nickname_error", "最多输入50字符");
            _isLegal = false;
        }
        if (data.email === "") {
            showErr("#email_error", "请输入邮箱");
            _isLegal = false;
        }else if(!(TVC.Validator.isEmail(data.email))) {
            showErr("#email_error", "邮箱格式错误，请重新输入");
            _isLegal = false;
        }else if (data.email.length > 100) {
            showErr("#email_error", "最多输入100字符");
            _isLegal = false;
        }
        if (data.roles_id === "") {
            showErr("#role_error", "请选择角色");
            _isLegal = false;
        }
        return _isLegal;
    }

    function showErr(errCarrier, errMsg) {
        $(errCarrier).text(errMsg).show();
    }

    function hideErr() {
        $("[name=error]").hide();
    }

    function saveSuccess(rqData) {
        _isSubmitting = false;
        TVC.showTip("保存成功");
        $("#refresh").click();
        $("#add_dialog_box").hide();
    }

    function bindUserOperateEvent() {
        $("a[name=add]").click(function(){
            resetForm();
            $("#add_dialog_box").show();
        });

        $("a[name=edit]").click(function(){
            var id = jQuery(this).attr("dataid");
            showEditBox(id);
        });
    }

    function showEditBox(id) {
        var user = $("#index_"+id);
        var name = user.find("td[name=name]").html();
        var nickname = user.find("td[name=nickname]").html();
        var email = user.find("td[name=email]").html();
        var rolesId = user.find("td[name=role]").attr("data-id");

        resetForm(id, name, nickname, email, rolesId);
        $("#add_dialog_box").show();
    }

    function resetForm(id, name, nickname, email, rolesId) {
        id = id || "";
        name = name || "";
        nickname = nickname || "";
        email = email || "";
        rolesId = rolesId || "";
        var title = id ? "编辑用户" : "添加用户";
        $("#dialog_title").text(title);
        $("#add_dialog_box input[name=id]").val(id);
        $("#add_dialog_box input[name=name]").val(name);
        if (id) {
            $("#add_dialog_box input[name=name]").attr("disabled", "disabled");
        }else {
            $("#add_dialog_box input[name=name]").removeAttr("disabled");
        }
        $("#add_dialog_box input[name=nickname]").val(nickname);
        $("#add_dialog_box input[name=email]").val(email);

        if (id){
            $("#user_form input[name=name]").attr("disabled");
        }

        $("#add_dialog_box input:checkbox").prop("checked", false);
        if ("" != rolesId){
            var rids = rolesId.split(",");
            for(var i = 0; i < rids.length; i++){
                $("#add_dialog_box input:checkbox[value="+rids[i]+"]").prop("checked", true);
            }
        }
    }

    var page = {
        init: init,
        bindOperate: bindUserOperateEvent
    };

    window['adminUser'] = page;
    page.init();
});