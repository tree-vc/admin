/**
 * 编辑临时客户页面
 * Created by jhyang on 2016/4/11.
 */
$(function(){
    var _addressChoosed = $("input[name=address_name]").val() ? true : false;
    //是否选择了地址下拉列表中的某项

    function init() {
        bindInputAddress();
        bindSaveEvent();
    }

    /***********公司地址联想词*************/
    function bindInputAddress() {
        $("#key_word").on("input", function(){
            _addressChoosed = false;
        });

        function onSelectStart(locationData){
            _addressChoosed = true;
            var lnglat = locationData.lnglat.split(',');
            $("#key_word").val(locationData.name);
            $("input[name=address]").val(locationData.address);
            $("input[name=province]").val(locationData.province);
            $("input[name=city]").val(locationData.city);
            $("input[name=longitude]").val(lnglat[0]);
            $("input[name=latitude]").val(lnglat[1]);
        }
        AB.Address("#key_word","#suggestStart",onSelectStart);
    }

    /*************保存临时客户信息*******************/
    function bindSaveEvent() {
        $("#submit").click(function(){
            $("[name=error]").hide();
            var addressLegal = true;
            var addressName = $("input[name=address_name]").val();
            var no = $("input[name=no]").val();
            if ((no && !addressName) || (addressName && !_addressChoosed)) {
                $("[data-name=company_address]").text("请选择公司地址").show();
                addressLegal = false;
            }

            if (!TVC.FormValidator.checkForm() || !addressLegal) {
                return;
            }

            var elements = ['id','mobile','email','company_contact','company_tel','company_title',
                'company_workers','company_devices','require_services'];
            var data = {};
            //获取表单内容
            for (var i = 0 ; i < elements.length; ++i) {
                data[elements[i]] = TVC.Form.elementValue(elements[i]);
            }
            if (addressName) {
                data['province'] = $("input[name=province]").val();
                data['city'] = $("input[name=city]").val();
                var location = {};
                location['name'] = addressName;
                location['address'] = $("input[name=address]").val();
                location['no'] = $("input[name=no]").val();
                location['longitude'] = $("input[name=longitude]").val();
                location['latitude'] = $("input[name=latitude]").val();
                data['company_location'] = JSON.stringify(location);
            } else {
                data['province'] = '';
                data['city'] = '';
                data['company_location'] = '';
            }

            TVC.showLoading();
            TVC.postJson("/customer/customer/save", data, function(rq){
                TVC.showTip("保存成功");
            },function(result){
                if (result.message == -1) {
                    $("[data-name=mobile]").text("该手机号已经注册").show();
                } else {
                    TVC.alert(result.message);
                }
            });
        });
    }

    init();
});