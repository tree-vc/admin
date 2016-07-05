/**
 * 忘记密码页面
 */
$(function () {

    function init() {
        bindFindPassEvent();
        RefreshPic();
    }

    function bindFindPassEvent() {
        $("#submit").click(function () {
            doSubmit();
        });

        document.onkeydown=function(event){
            var e = event || window.event || arguments.callee.caller.arguments[0];
            if(e && e.keyCode==13){
                doSubmit();
            }
        };

        function doSubmit() {
            $("#errormsg").hide();
            var data = getFindData();
            if (!data) {
                return;
            }
            TVC.showLoading();
            $('#submit_real').click();
            /*TVC.postJson('/admin/find_password', data, findPassSuccess, function(result){
                showError(result.message);
            });*/
        }
    }

    function getFindData(){
        var name = $("input[name=name]").val();
        var email = $("input[name=email]").val();
        var captcha = $("input[name=captcha]").val();
        if ('' === name) {
            showError("请输入用户名");
            return false;
        }
        if ('' === email) {
            showError("请输入邮箱");
            return false;
        }else if (!TVC.Validator.isEmail(email)) {
            showError("邮箱格式错误，请检查");
            return false;
        }else if (email.length > 100) {
            showError("最多输入100字符");
            return false;
        }
        if ('' === captcha) {
            showError("请输入验证码");
            return false;
        }
        var data = {name: name, email: email, captcha: captcha};
        return data;
    }

    function findPassSuccess(result) {
        TVC.hideLoading();
        TVC.showTip("提交成功");
        TVC.redirect("/admin/login");
    }

    function RefreshPic(){
        $("#img").click(function () {
            var img = $(this);
            $.ajax({
                url: $(this).attr('data-refresh-url'),
                type: 'get',
                success: function(data){
                    img.attr('src', data);
                }
            });
            //$(this).attr('src', "/captcha/image?_t=" + Math.random());
        });
    }

    function showError(msg) {
        TVC.hideLoading();
        $("#errormsg").html(msg).show();
        $("input[name=captcha]").val("");
        //$("#img").attr('src', "/captcha/image?_t=" + Math.random());
    }

    init();
});

