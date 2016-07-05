$(function(){
    var _elements = ['normal_ratio', 'huhang_ratio']
    $("#save_identify").on("click", function(){
        $("[name=error]").hide();
        if (!TVC.FormValidator.checkForm()) {
            return;
        }
        TVC.showLoading();
        url = "/config/save";
        var data = {}; 
        for (var i = 0; i < _elements.length; ++i) {
            value = $.trim(TVC.Form.elementValue(_elements[i]));
            data[_elements[i]] = value;
        }

        TVC.postJson(url, data, saveSuccess, function(result) {
            TVC.alert(result.message);
        })
        function saveSuccess(rqData) {
            TVC.hideLoading();
            TVC.showTip("保存成功");
        }
    })
})
