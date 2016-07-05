/**
 * 修改密码页面
 */
$(function () {

    function init() {
        bindChangePassEvent();
    }

    function bindChangePassEvent() {
        $("#login_password_form input[name=submit]").click(function () {
            hideErr();
            var data = getChangeData();
            if (!validate(data)) {
                return;
            }
            TVC.showLoading();
            TVC.postJson('/admin/info/change_password', data, saveInfoSuccess, saveError);
        });
    }

    function validate(data) {
        if (!TVC.Validator.validateForm()) {
            isConfirmEqual2pass();
            return;
        }

        function isConfirmEqual2pass() {
            var confirmPass = $("[name=pass_confirm]").val();
            if (data.new_pass && confirmPass && data.new_pass !== confirmPass) {
                showErr("[data-name=pass_confirm]", "新密码和确认密码不一致，请重新输入");
                return false;
            }
            return true;
        }

        if (!isConfirmEqual2pass()) {
            return false;
        }

        return true;
    }

    function getChangeData() {
        var old_pass = $("#login_password_form input[name=old_pass]").val();
        var new_pass = $("#login_password_form input[name=new_pass]").val();
        var pass_confirm = $("#login_password_form input[name=pass_confirm]").val();
        var data = {password: old_pass, new_pass: new_pass};
        return data;
    }

    function saveError(result) {
        TVC.hideLoading();
        if (result.message === "-1") {
            showErr("[data-name=old_pass]", "原密码错误，请重新输入");
        }else if (result.message === "-2"){
            showErr("[data-name=new_pass]", "只能输入6-20位字符");
        }else {
            TVC.alert(error);
        }
    }

    function showErr(errCarrier, errMsg) {
        $(errCarrier).text(errMsg).show();
    }

    function hideErr() {
        $("[name=error]").hide();
    }

    function saveInfoSuccess(result) {
        TVC.hideLoading();
        TVC.showTip('密码修改成功');
        $("#login_password_form")[0].reset();
    }

    init();
});
