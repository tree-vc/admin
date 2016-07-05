/**
 * 添加工程师页面
 * Created by jhyang on 2016/3/29.
 */
$(function(){
    //工程师身份信息项
    var _elements = ['thumb','name','mobile','gender','birthday','id_number','education',
        'seniority','province','city','id_front_photo','id_back_photo'];
    var _notSaveIdentify = true;      //没有保存过身份信息
    var _engineerId = $("#engineer_id").val();  //工程师id
    var _engineerUploaders = {};
    var _experienceCount = 0;
    var _projectCount = 0;
    var _certCount = 0;
    var _locationCount = 0;

    function init() {
        initImgUploader();
        initRegionSelector();
        bindDeletePicture();
        switchTab();
        saveIdentify();
    }

    /**********初始化上传图片插件************/
    function initImgUploader() {
        var avatarUploader = new SingleImageUploader();  //头像
        var avatarOption = {
            'container': '#avatar_uploader',
            'queue': '#avatar_queueList',
            'picker': '#avatar_filePicker',
            'pidContainer': 'input[name=thumb]'
        };
        avatarUploader.init(avatarOption);
        _engineerUploaders['thumb'] = avatarUploader;

        var frontalUploader = new SingleImageUploader();  //身份证正面照
        var frontalOption = {
            'container': '#frontal_uploader',
            'queue': '#frontal_queueList',
            'picker': '#frontal_filePicker',
            'pidContainer': 'input[name=id_front_photo]'
        };
        frontalUploader.init(frontalOption);
        _engineerUploaders['id_front_photo'] = frontalUploader;

        var backUploader = new SingleImageUploader();  //身份证背面照
        var backOption = {
            'container': '#back_uploader',
            'queue': '#back_queueList',
            'picker': '#back_filePicker',
            'pidContainer': 'input[name=id_back_photo]'
        };
        backUploader.init(backOption);
        _engineerUploaders['id_back_photo'] = backUploader;

        if (_engineerId) {  //编辑工程师,身份信息照片上传按钮显示重新上传
            for (var up in _engineerUploaders) {
                _engineerUploaders[up].showFilePicker("重新上传");
            }
        }
    }

    /**********初始化上传证书插件************/
    function initCertificatePhotoUploader() {
        var certificateUploader = new SingleImageUploader();  //证书照片
        var certificateOption = {
            'container': '#cert_uploader',
            'queue': '#cert_queueList',
            'picker': '#cert_filePicker',
            'pidContainer': 'input[name=photo]'
        };
        certificateUploader.init(certificateOption);
        _engineerUploaders['photo'] = certificateUploader;
    }

    /*********初始化省市下拉框列表数据*********/

    function initRegionSelector() {
        var regionSelector = new TVC.regionSelector("select[name='province']");
        $("select[name='province']").change(function () {
            regionSelector.provinceChanged("select[name='province']", "select[name='city']", "select[name='region']");
        });
        //编辑工程师身份信息时需要根据工程师原有的省份和城市使其在select框中被选中
        if (_engineerId) {
            setProvinceCitySelected();
        }

        function setProvinceCitySelected() {
            var province = $("#province_init").text();
            var city = $("#city_init").text();
            regionSelector.setProvinceCitySelected("select[name=province]","select[name=city]","",province,city);
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
            _engineerUploaders[pidName].showFilePicker("上传图片")
        });
    }

    /**********切换tab栏操作***********/
    function switchTab() {
        $(".tab-nav li:lt(3)").on('click', function(){
            var currNav = $(".tab-nav [class*=activeli]");  //获取当前tab
            var currName = currNav.attr('name');
            var name = $(this).attr('name');
            if (currName == name) {                   //点击当前tab，不响应
                return;
            }
            //当前tab栏为身份信息时，身份信息未填写完整时，提示“请先填写身份信息”
            if (currName == "identify" && !isIdentifyComplete()) {
                TVC.showTip("请先填写身份信息");
                return;
            }
            //当前tab栏为身份信息时，【添加】【身份信息填写完整】【未保存】时，提示“请先保存身份信息”
            if (currName == "identify" && !_engineerId && _notSaveIdentify) {
                TVC.showTip("请先保存身份信息");
                return;
            }
            currNav.removeClass("activeli");
            $("#" + currName).hide();               //隐藏当前页面
            $(this).addClass("activeli");

            if (name != "identify") {           //服务设置页面、完善资料页面
                loadHtml(name);
            }
            $("#" + name).show();   //显示新页面
        });
    }

    /********ajax加载添加工程师的html片段：服务设置页面、完善资料页面*********/
    function loadHtml(name) {
        if ($("#" + name).html()) {     //已加载过
            return;
        }
        var url = (name == "setting") ? "/engineer/engineer/setting" : "/engineer/engineer/info";
        TVC.showLoading();
        TVC.getText(url, {type: 1,'id': _engineerId}, function(rqData){
            $("#" + name).html(rqData);
            bindHidePopWindow();
            (name == "setting") ? bindSettingOperate() : bindInfoOperate();
        });
    }
    /*****检查工程师身份信息是否填写完整*******/
    function isIdentifyComplete() {
        for (var i = 0; i < _elements.length; ++i) {
            var value = $.trim(TVC.Form.elementValue(_elements[i]));
            if (value == '') {
                return false;
            }
        }
        return true;
    }

    /**********隐藏弹窗************/
    function bindHidePopWindow() {
        $("[name=popup_cancel]").unbind("click").bind("click", function(){
            $("[name=pop_window]").hide();
        });
    }

    /*********绑定服务设置中的操作*********/
    function bindSettingOperate() {
        initSettingData();
        bindAddWorkTime();
        bindDeleteWorkTime();
        bindInputLocation();
        bindDeleteLocation();
        bindSaveAbility();
    }

    /******绑定完善资料信息中的操作******/
    function bindInfoOperate() {
        initInfoData();
        initCertificatePhotoUploader();
        bindAddWorkExp();
        bindEditWorkExp();
        bindDeleteWorkExp();
        bindAddProjectExp();
        bindEditProjectExp();
        bindDeleteProjectExp();
        bindAddCertificate();
        bindEditCertificate();
        bindDeleteCertificate();
        bindTextareaInputEvent();
    }

    function initSettingData() {
        _locationCount = $("[item-location-id]").length;
        if (_locationCount >= 10) {
            $("#key_word").attr("disabled","disabled");
        }
    }

    function initInfoData() {
        _experienceCount = $("[item-exp-id]").length;
        _projectCount = $("[item-project-id]").length;
        _certCount = $("[item-cert-id]").length;
    }

    /******textarea框中输入字符事件*********/
    function bindTextareaInputEvent() {
        TVC.bindChangeTextareaLeftWords();
    }

    /*******保存工程师身份信息********/
    function saveIdentify() {
        $("#save_identify").click(function(){
            $("#identify_form [name=error]").hide();
            if (!TVC.FormValidator.checkForm("#identify_form")) {
                return;
            }
            var data = {};
            for (var i = 0; i < _elements.length; ++i) {
                data[_elements[i]] = $.trim(TVC.Form.elementValue(_elements[i]));
            }
            data['huhang_id'] = $.trim($("input[name=huhang_id]").val());
            if (_engineerId) {
                var url = "/engineer/engineer/save";
                data['id'] = _engineerId;
            }else {
                var url = "/engineer/engineer/add";
            }
            TVC.showLoading();
            TVC.postJson(url, data, saveIdentifySuccess, function(result){
                var message = result.message;
                if (message == -1) {
                    $("[data-name=mobile]").text("手机号重复，请重新输入").show();
                }else if (message == -2) {
                    $("[data-name=id_number]").text("身份证号重复，请重新输入").show();
                }else {
                    TVC.alert(message);
                }
            })
        });

        function saveIdentifySuccess(rqData) {
            TVC.showTip("保存成功");
            if (!_engineerId) {
                _engineerId = rqData;
            }
        }
    }

    /************************************服务设置页面******************************************
     ****************************************************************************************/


    /************************************服务时间*********************************************/

    /********添加服务时间操作********/
    function bindAddWorkTime() {
        $("#show_work_time_box").click(function(){ //显示添加时间窗口
            $("#work_time_form")[0].reset();
            $("#work_time_form [name=error]").hide();
            $("#work_time_box").show();
        });

        $("#work_time_form input[name=confirm]").unbind("click").bind("click", function(){
            if (!$("#work_time_form input[name=weekday]").is(":checked")) {
                $("[data-name=weekday]").show();
                return;
            }
            var start_time = $("select[name=start_hour]").find("option:selected").val()+":"+$("select[name=start_minute]").find("option:selected").val();
            var end_time = $("select[name=end_hour]").find("option:selected").val()+":"+$("select[name=end_minute]").find("option:selected").val();
            if (end_time <= start_time) {
                $("[data-name=end_time]").show();
                return;
            }
            var selectors = {"form": "#work_time_form","window": "#work_time_box","item":"item-time-id","list":"#work_time_list"};
            var urls = {"addUrl": "/engineer/worktime/add","saveUrl":""};
            var elements = ['start_hour','start_minute','end_hour','end_minute','weekday'];
            submitEngineerAttributeInfo(selectors,elements,urls);
        });
    }

    /********删除服务时间操作********/
    function bindDeleteWorkTime() {
        $("a[name=delete_work_time]").unbind("click").bind("click", function(){
            var time_id = $(this).attr("time-id");  //要删除的服务时间id
            TVC.showLoading();
            TVC.postJson("/engineer/worktime/delete", {id: time_id}, function(rqData){
                $("[item-time-id="+time_id+"]").remove();
                TVC.showTip("删除成功");
            },function(result){TVC.alert(result.message);})
        });
    }

    /***********************************常用地址***********************************************/

    /*********添加常用地址操作**************/
    function bindInputLocation() {
        function onSelectStart(locationData){
            $("#key_word").val("");          //选择后清空关键字输入框
            locationData['engineer_id'] = _engineerId;
            TVC.showLoading();
            TVC.postJson("/engineer/location/add", locationData, function(rqData){
                $("#location_list").prepend(rqData);
                bindDeleteLocation();
                TVC.showTip("保存成功");
                _locationCount++;
                if (_locationCount >= 10) {    //添加10个地址后禁用地址输入框
                    $("#key_word").attr("disabled","disabled");
                }
            }, function(result){TVC.alert(result.message);});
        }
        //onSelectStart 为可扩展业务逻辑回调函数
        AB.Address("#key_word","#suggestStart",onSelectStart);
    }

    /*********删除常用地址操作**************/
    function bindDeleteLocation() {
        $("a[name=delete_location]").unbind("click").bind("click",function(){
            var id = $(this).attr("location-id");  //要删除的常用地址id
            TVC.showLoading();
            TVC.postJson("/engineer/location/delete", {id: id}, function(rqData){
                $("[item-location-id="+id+"]").remove();
                TVC.showTip("删除成功");
                _locationCount--;
                $("#key_word").attr("disabled",false);
            },function(result){TVC.alert(result.message);})
        });
    }

    /***********************************技能标签***********************************************/

    /**********保存技能标签****************/
    function bindSaveAbility() {
        $("#save_ability").click(function(){
            $("#setting_form [name=error]").hide();
            if (!isCompleteSetting()) {
                return;
            }
            var abilities = TVC.Form.elementValue("ability");
            TVC.showLoading();
            TVC.postJson("/engineer/engineer/saveAbility", {'id': _engineerId,'abilities_ids': abilities}, function(rq){
                TVC.showTip("保存成功");
            },function(result){TVC.alert(result.message);});

        });
    }

    //检查服务设置页是否必填项已填写完整
    function isCompleteSetting() {
        var isComplete = true;
        if ($("[item-time-id]").length == 0) {
            $("[data-name=work_time_list]").text("请添加服务时间").show();
            isComplete = false;
        }
        if ($("[item-location-id]").length == 0) {
            $("[data-name=location_list]").text("请输入常用地址").show();
            isComplete = false;
        }
        var abilities = TVC.Form.elementValue("ability");
        if (!abilities) {
            $("[data-name=ability]").text("请选择技能标签").show();
            isComplete = false;
        }
        return isComplete;
    }

    /*****************************完善资料信息页面****************************************
     **********************************************************************************/


    /********初始化选择年份下拉框********
     **untilNow：是否需要“至今”选项*****/
    function initYearMonthSelectBox(yearSelector, monthSelector,untilNow, oldDate) {
        $(yearSelector + " option").remove();
        $(monthSelector + " option").remove();
        var myDate = new Date();
        var thisYear = parseInt(myDate.getFullYear());
        var option = '';
        for (var year = 1950; year < thisYear; ++year) {
            option += "<option>"+year+"</option>";
        }

        untilNow ? option += "<option>"+thisYear+"</option><option value='9999' selected>至今</option>" :
                   option += "<option selected>"+thisYear+"</option>";
        $(yearSelector).append(option);

        var monthListOption = "";
        for (var month = 1; month <= 12; ++month) {
            //1-9月前面加0
            var monthValue = (month < 10) ? ("0" + month) : month;
            monthListOption += "<option value="+monthValue+">"+month+"</option>";
        }
        $(monthSelector).append(monthListOption);

        if (oldDate) { //编辑时数据原有的日期，要处于选中状态
            var tmpArr = oldDate.split("-");
            $(yearSelector + " option[value="+tmpArr[0]+ "]").attr("selected", true);
            tmpArr[0] == "9999" ? $(monthSelector + " option").remove() :  //原有日期为至今，则清空月份下拉列表
                                $(monthSelector + " option[value="+tmpArr[1]+ "]").attr("selected", true);
        }

        if (untilNow && !oldDate) {
            $(monthSelector + " option").remove();
        }

        //年份下拉列表中存在至今一项，绑定年份变化事件
        if (untilNow) {
            bindYearChange();
        }

        function bindYearChange() {
            $(yearSelector).change(function(){
                if ($(this).val() == "9999") {  //选中‘至今’时，月份下拉列表需为空
                    $(monthSelector + " option").remove();
                }else if ($(monthSelector + " option").length == 0) { //由至今切换年份时，需恢复月份下拉列表
                    $(monthSelector).append(monthListOption);
                }
            });
        }
    }

    //显示工程师相关的弹框（工作经验、项目经验、证书信息）
    function showEngineerAttrWindow(form, window, windowName) {
        $(form + " [name=error]").hide();
        $(form)[0].reset();
        $("[data-name=_max_number]").text("500"); //最多输入字符
        $(window + " [name=pop_window_name]").text(windowName);
        $(window).show();
    }

    /***提交工程师的各种属性（工作经验，项目经验，证书，服务时间、地址）****
     ****selectors:选择器对象(form,window,item,list)*
     ****urls:保存的url(addUrl,saveUrl)*********************
     ********type:1-工作经验；2-项目；3-证书******************/
    function submitEngineerAttributeInfo(selectors, elements, urls, id, type) {
        type = type ? type : 0;
        $(selectors.form + " [name=error]").hide();
        if (!TVC.FormValidator.checkForm(selectors.form)) {
            return;
        }
        var url = id ? urls.saveUrl : urls.addUrl;
        var data = {};
        //获取表单内容
        for (var i = 0 ; i < elements.length; ++i) {
            data[elements[i]] = TVC.Form.elementValue(elements[i]);
        }
        if (id) {
            data['id'] = id;
        }
        data['engineer_id'] = _engineerId;
        TVC.showLoading();
        TVC.postJson(url, data, submitSuccess, function(result){TVC.alert(result.message);});

        //保存成功后回调
        function submitSuccess(html) {
            $(selectors.window).hide();
            TVC.showTip("保存成功");
            if (id) {//编辑后，用新数据替换原有的
                var $dom = $("<div>"+html+"</div>");
                $("table["+selectors.item+"="+id+"]").html($dom.find("table["+selectors.item+"="+id+"]").html());
            } else {
                $(selectors.list).prepend(html);
            }

            switch (type) {
                case 1:                      //工作经验
                    bindEditWorkExp();
                    bindDeleteWorkExp();
                    ++_experienceCount;
                    break;
                case 2:                     //项目经验
                    bindEditProjectExp();
                    bindDeleteProjectExp();
                    ++_projectCount;
                    break;
                case 3:                     //证书信息
                    bindEditCertificate();
                    bindDeleteCertificate();
                    ++_certCount;
                    break;
                default :
                    bindDeleteWorkTime();
            }
        }
    }

    /*****删除完善资料信息（工作经验，项目经验，证书）******/
    function deleteEngineerAttributeInfo(id,removeSelector,url,infoName,type) {
        TVC.confirm("确定删除该"+infoName+"吗？该操作不可恢复", function(){
            TVC.showLoading();
            TVC.postJson(url,{'id': id}, function(rq){
                $("table["+removeSelector+"="+id+"]").remove();
                TVC.showTip("删除成功");
                switch (type) {
                    case 1:                      //工作经验
                        --_experienceCount;
                        break;
                    case 2:                     //项目经验
                        --_projectCount;
                        break;
                    case 3:                     //证书信息
                        --_certCount;
                        break;
                }
            },function(result){
                TVC.alert(result.message);
            });
        });
    }

    /************************************工作经验*********************************************/

    /******点击添加工作经验操作*******/
    function bindAddWorkExp() {
        $("#add_work_exp").click(function(){
            if (_experienceCount >= 20) {
                TVC.showTip("最多添加20个工作经验");
                return;
            }
            initYearMonthSelectBox("select[name=start_year]","select[name=start_month]");
            initYearMonthSelectBox("select[name=end_year]", "select[name=end_month]",true);
            showEngineerAttrWindow("#work_exp_form", "#work_exp","添加工作经验");
            bindSaveWorkExp();
        });
    }

    /******点击编辑工作经验操作*******/
    function bindEditWorkExp() {
        $("a[name=edit_work_exp]").unbind("click").bind("click",function(){
            showEngineerAttrWindow("#work_exp_form", "#work_exp","编辑工作经验");
            var id = $(this).attr("exp-id");
            TVC.showLoading();
            //实时获取所编辑的工作经验的数据，然后显示在工作经验弹窗中
            TVC.postJson("/engineer/experience/save", {'id': id, 'type': 1}, function(rqData){
                initYearMonthSelectBox("select[name=start_year]","select[name=start_month]", false,rqData.entry_date);
                initYearMonthSelectBox("select[name=end_year]", "select[name=end_month]",true,rqData.leave_date);
                $("[name=company_name]").val(rqData.company_name);
                $("[name=company_role]").val(rqData.company_role);
                $("[name=job_content]").val(rqData.job_content);
                TVC.changeTextareaLeftWords($("span[name=job_content_max]"), rqData.job_content.length);
            },function(result){TVC.alert(result.message);});
            bindSaveWorkExp(id);
        });
    }

    /****保存工作经验（点击弹框中的确定）****/
    function bindSaveWorkExp(id) {  //id参数：存在则表明是编辑工作经验
        $("#work_exp_form input[name=confirm]").unbind("click").bind("click", function(){
            var selectors = {"form": "#work_exp_form","window": "#work_exp","item":"item-exp-id","list":"#engineer_work_exp_list"};
            var urls = {"addUrl": "/engineer/experience/add","saveUrl":"/engineer/experience/save"};
            var elements = ['start_year','start_month','end_year','end_month','company_name','company_role','job_content'];
            submitEngineerAttributeInfo(selectors,elements,urls,id,1);
        });
    }

    /******点击删除工作经验操作*******/
    function bindDeleteWorkExp() {
        $("a[name=delete_work_exp]").unbind("click").bind("click", function(){
            var url = "/engineer/experience/delete";
            deleteEngineerAttributeInfo($(this).attr("exp-id"),"item-exp-id",url,"工作经验",1);
        });
    }

    /************************************项目经验*********************************************/

    /*******点击添加项目经验操作*************/
    function bindAddProjectExp() {
        $("#add_project_exp").click(function(){
            if (_projectCount >= 20) {
                TVC.showTip("最多添加20个项目经验");
                return;
            }
            initYearMonthSelectBox("select[name=pro_start_year]","select[name=pro_start_month]");
            initYearMonthSelectBox("select[name=pro_end_year]", "select[name=pro_end_month]",true);
            showEngineerAttrWindow("#project_exp_form", "#project_exp","添加项目经验");
            bindSaveProjectExp();
        });
    }

    /*******点击编辑项目经验操作*************/
    function bindEditProjectExp() {
        $("a[name=edit_project]").unbind("click").bind("click",function(){
            showEngineerAttrWindow("#project_exp_form", "#project_exp","编辑项目经验");
            var id = $(this).attr("project-id");
            TVC.showLoading();
            //实时获取所编辑的项目经验的数据，然后显示在项目经验弹窗中
            TVC.postJson("/engineer/project/save", {'id': id, 'type': 1}, function(rqData){
                initYearMonthSelectBox("select[name=pro_start_year]","select[name=pro_start_month]", false,rqData.start_date);
                initYearMonthSelectBox("select[name=pro_end_year]", "select[name=pro_end_month]",true,rqData.end_date);
                $("[name=project_name]").val(rqData.project_name);
                $("[name=job]").val(rqData.job);
                $("[name=project_desc]").val(rqData.project_desc);
                TVC.changeTextareaLeftWords($("span[name=project_desc_max]"), rqData.project_desc.length);
            },function(result){TVC.alert(result.message);});
            bindSaveProjectExp(id);
        });
    }

    /******保存项目经验（点击弹框中的确定）******/
    function bindSaveProjectExp(id) {
        $("#project_exp_form input[name=confirm]").unbind("click").bind("click", function(){
            var selectors = {"form": "#project_exp_form","window": "#project_exp","item":"item-project-id","list":"#engineer_project_exp_list"};
            var urls = {"addUrl": "/engineer/project/add","saveUrl":"/engineer/project/save"};
            var elements = ['pro_start_year','pro_start_month','pro_end_year','pro_end_month','project_name','job','project_desc'];
            submitEngineerAttributeInfo(selectors,elements,urls,id,2);
        });
    }

    /*********删除项目经验************/
    function bindDeleteProjectExp() {
        $("a[name=delete_project]").unbind("click").bind("click", function(){
            var url = "/engineer/project/delete";
            deleteEngineerAttributeInfo($(this).attr("project-id"),"item-project-id",url,"项目经验",2);
        });
    }

    /************************************证书信息*********************************************/

    /*******点击添加证书信息********/
    function bindAddCertificate() {
        $("#add_certificate").click(function(){
            if (_certCount >= 20) {
                TVC.showTip("最多添加20个证书信息");
                return;
            }
            removeCertificatePhoto();
            _engineerUploaders['photo'].showFilePicker("上传图片");
            showEngineerAttrWindow("#certificate_form", "#certificate_info","添加证书信息");
            initYearMonthSelectBox("select[name=cert_year]","select[name=cert_month]");
            bindSaveCertificate();
        });
    }

    //删除证书照片
    function removeCertificatePhoto() {
        $("#cert_uploader .staticImgDiv").remove();
        _engineerUploaders['photo'].removeImage();
    }

    /*******点击编辑证书信息********/
    function bindEditCertificate() {
        $("a[name=edit_certificate]").unbind("click").bind("click",function(){
            removeCertificatePhoto();
            showEngineerAttrWindow("#certificate_form", "#certificate_info","编辑证书信息");
            var id = $(this).attr("cert-id");
            TVC.showLoading();
            //实时获取所编辑的证书信息的数据，然后显示在证书信息弹窗中
            TVC.postJson("/engineer/certificate/save", {'id': id, 'type': 1}, function(rqData){
                initYearMonthSelectBox("select[name=cert_year]","select[name=cert_month]", false,rqData.issue_at);
                $("[name=title]").val(rqData.title);
                $("input[name=photo]").val(rqData.photo);
                var imgHtml = "<div class='staticImgDiv relative'><img width='110' height='110' class='clickShowBigImg' src='"+rqData.src+"'><div name='close_image' pid-name='photo' class='close_image' style='height:0px;'><img class='close' src='/images/close.png' width='20' height='20'></div></div>"
                $("#cert_queueList").append(imgHtml);
                _engineerUploaders['photo'].showFilePicker("重新上传");
                bindDeletePicture();
            },function(result){TVC.alert(result.message);});
            bindSaveCertificate(id);
        });
    }

    /****保存证书信息（点击弹框中的确定）****/
    function bindSaveCertificate(id) {
        $("#certificate_form input[name=confirm]").unbind("click").bind("click", function(){
            var selectors = {"form": "#certificate_form","window": "#certificate_info","item":"item-cert-id","list":"#engineer_certificate_list"};
            var urls = {"addUrl": "/engineer/certificate/add","saveUrl":"/engineer/certificate/save"};
            var elements = ['cert_year','cert_month','title','photo'];
            submitEngineerAttributeInfo(selectors,elements,urls,id,3);
        });
    }

    /*******点击删除证书信息********/
    function bindDeleteCertificate() {
        $("a[name=delete_certificate]").unbind("click").bind("click", function(){
            var url = "/engineer/certificate/delete";
            deleteEngineerAttributeInfo($(this).attr("cert-id"),"item-cert-id",url,"证书信息",3);
        });
    }

    init();
});