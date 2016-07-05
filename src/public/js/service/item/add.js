/**
* 添加服务项页面
* @author slwu
*/
$(function(){
    var _uploader = null;
    initImgUploader();
    initClasses();
    bindDeletePicture();
    setServiceUploadDom();
    
    //初始化服务分类选择框
    function initClasses() {
        t1 = $("#hide_classes_t1").val();
        t2 = $("#hide_classes_t2").val();
        if(t1 && t2) {
            $("select[name=classes_t1]").val(t1);
            setClasses_t2_html(t1);
            $("select[name=classes_t2]").val(t2);
        }
    }

    //服务分类
    $("select[name=classes_t1]").on("change", function(){
        pid = $(this).val();
        setClasses_t2_html(pid);
    })
    
    //设置2级分类的html
    function setClasses_t2_html(pid) {
        if(pid) {
            _option = $("#classes_t1_child_"+pid).html();
        } else {
            _option = "<option value=''>请选择二级分类</option>"
        }
        $("select[name=classes_t2]").html(_option);
    }
    
    //初始化上传图片插件
    function initImgUploader() {
        _uploader = new SingleImageUploader();
        var Option = {
            'pidContainer': 'input[name=thumb]',
            'fileSingleSizeLimit': 1 * 1024 * 1024
        };
        _uploader.init(Option);
        if ($("input[name=id]").val() && $("input[name=top_sort]").val()) {
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

    //首页排序号与常用服务项图标捆绑
    $("input[name=top_sort]").on("blur", function() {
        setServiceUploadDom();
    })

    function setServiceUploadDom() {
        _serviceUploadDom = $("#service_upload")
        if($("input[name=top_sort]").val() == "") {
            $("#queueList").html("<ul class='filelist'></ul>");
            initImgUploader();
            _serviceUploadDom.val("");
            _serviceUploadDom.hide();
        } else {
            _serviceUploadDom.show();
        }
    }

    //提交表单
    var _elements = ['id','classes_t1','classes_t2','name','description','sort','top_sort','thumb','duration','price','failure_types','service_types','device_types','os_types','device_brands','device_components','abilities_ids']
    $("#submit").on("click", function(){
        $("[name=error]").hide();
        
        _thumbcheck = false;
        if($("input[name=top_sort]").val() != "") {
            if($("input[name=thumb]").val() == "") {
                $("#thumb_error").text("请上传常用服务项图标");
                $("#thumb_error").show();
                _thumbcheck = true;
            } 
        }
        if (!TVC.FormValidator.checkForm() || _thumbcheck) {
            return;
        }


        TVC.showLoading();
        var data = {};
        id = $("input[name=id]").val();
        for (var i = 0; i < _elements.length; ++i) {
            value = $.trim(TVC.Form.elementValue(_elements[i]));
            data[_elements[i]] = value;
        }
        
        url = id ? "/service/item/save" : "/service/item/add"
        TVC.postJson(url, data, saveSuccess, function(result){
            TVC.hideLoading();
            if (result.message == '-1') {
                $("[data-name=name]").text("服务项重复，请重新输入").show();
                $("input[name=name]").focus();
            }else {
                TVC.alert(result.message);
            }
        });

        function saveSuccess(rqData) {
            TVC.hideLoading();
            TVC.showTip("保存成功");
            if(!id) {
                $("#biz_form")[0].reset();
                $("#queueList").html("<ul class='filelist'></ul>");
                initImgUploader();
                _serviceUploadDom = $("#service_upload");
                _serviceUploadDom.val("");
                _serviceUploadDom.hide();
            }
        }
    })

});
