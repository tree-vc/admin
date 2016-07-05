/**
 * Created by jhyang on 2016/1/14.
 */
$(function(){
    function init() {
        bindSubmitEvent();
        RefreshPic();
    }

    function bindSubmitEvent()
    {
        $("#login").click(function () {
            doSubmit();
        });

        document.onkeydown=function(event){
            var e = event || window.event || arguments.callee.caller.arguments[0];
            if(e && e.keyCode==13){
                doSubmit();
            }
        };

        function doSubmit() {
            $("[data-name=error_box]").hide();
            if (!TVC.Validator.validateForm()) {
                return;
            }
            var data = getFormData();
            TVC.showLoading("");
            TVC.postJson('/admin/login', data, function(url){TVC.redirect(url);}, loginError);
        }
    }

    function loginError(result) {
        TVC.hideLoading();
        if (result.message == '-1') {
            result.message = '用户名或密码错误，请检查';
            $("input[name=password]").val("");
        }
        $("[data-name=error_box]").text(result.message).show();
        $("input[name=captcha]").val("");
        $("#img").attr('src', "/captcha/image?_t=" + Math.random());
    }

    function getFormData() {
        var name = $("input[name=name]").val();
        var password = $("input[name=password]").val();
        var captcha = $("input[name=captcha]").val()
        var redirect = $("input[name=redirect]").val();
        var data = {name: name, password: password, captcha: captcha, redirect:redirect};
        return data;
    }

    function RefreshPic()
    {
        $("#img").click(function () {
            $(this).attr('src', "/captcha/image?_t=" + Math.random());
        });
    }

    init();

});