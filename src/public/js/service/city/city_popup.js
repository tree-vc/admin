/**
 * Created by slwu on 2016-04.
 */
$(function(){
    var _cityId = $("input[name=id]").val();
    var provinceChange = new TVC.regionSelector("select[name='province']");
    
    $("select[name='province']").change(function () {
        provinceChange.provinceChanged("select[name='province']", "select[name='city']", "select[name='region']");
    });
    if(_cityId) {
       setProvinceCitySelected(); 
    }

    function setProvinceCitySelected() {
        var regionSelector = new TVC.regionSelector("select[name='province']");
        var province = $("#province_init").text();
        var city = $("#city_init").text();
        regionSelector.setProvinceCitySelected("select[name=province]","select[name=city]","",province, city);
    }

    $("#popup_confirm_box [name=confirm]").click(function(){
        $("[name=error]").hide();
        if (!TVC.FormValidator.checkForm()) {
            return;
        }
        var city = $("select[name=city]").val();
        var province = $("select[name=province]").val();
        var service_range = $("textarea[name=service_range]").val();
        var id = $("input[name=id]").val();
        var data = {city:city, province:province, service_range:service_range,id:id};
        var url = id ? "/service/city/save" : "/service/city/add";
        TVC.showLoading();
        TVC.postJson(url, data, saveSuccess, function(result){
            TVC.hideLoading();
            if (result.message == '-1') {
                $("[data-name=city]").text("服务城市名称重复，请重新选择").show();
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
