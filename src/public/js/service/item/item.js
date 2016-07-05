/**
* 服务项列表页面
* @author slwu
*/
$(function(){  
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
            _option = "<option value=''>请选择二级服务分类</option>"
        }
        $("select[name=classes_t2]").html(_option);
    }    
})
