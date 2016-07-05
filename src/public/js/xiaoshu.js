+function($){
    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

}(jQuery);

+function($){
    $.xiaoshu = {
        formatParams : function(str , defaults) {
            var data;
            var strings, secs;
            var key,value;
            data = $.extend({}, defaults);
            if (typeof str == 'string') {
                strings = str.split('|');
                for (var i in strings) {
                    if (strings.hasOwnProperty(i)) {
                        secs = strings[i].split(':');
                        if (secs.length > 1) {
                            key = secs[0];
                            secs.shift();
                            value = secs.join(':');
                            data[key] = value;
                        }
                    }
                }
            }
            return data;
        },

        ajaxPromise : function(promise , then , always ,redirect){
            if(typeof then == 'object') {
                for(var i in then){
                    if(then.hasOwnProperty(i)){
                        var done = then[i].done ? then[i].done : null ;
                        var fail = then[i].fail ? then[i].fail : null ;
                        promise = promise.then(done,fail);
                    }
                }
            } else if(typeof then == 'function'){
                promise = promise.done(then);
            } else {
                promise = promise.then(function(res){
                    if(res.status){
                        alert('提交成功:'+res.msg);
                        if(redirect){
                            location.href=redirect;
                        } else {
                            location.reload();
                        }
                    } else{
                        alert('提交失败:'+res.msg);
                    }
                },function(req){
                    alert('请求失败');
                });
            }

            if(typeof always == 'function'){
                promise.always(always);
            }
        }
    };

}(jQuery);

/*
 * resource
 */
+function ($){

    var Resource = function (element , options){
        this.element = $(element);
        this.option = options;
    };

    Resource.DEFAULTS = {
        action : '',
        method : '',
        params : null,
        func : '',
        confirmation : '确认执行?',
        ajax : {
            cache : false,
            async : false,
            dataType : 'json',
            then : '',
            always : '',
            redirect : '',
        }
    };

    Resource.prototype.run = function (){
        this[this.option.method]();
    };

    Resource.prototype.destroy = function (){
        var data = {
            _method : 'DELETE'
        };

        this.ajax(data);
    };

    Resource.prototype.ajax = function(data){

        if(confirm(this.option.confirmation)) {
            var promise = $.ajax({
                url : this.option.action,
                cache : this.option.ajax.cache,
                type  : 'post',
                dataType : this.option.ajax.dataType,
                data  : data,
                async : this.option.ajax.async
            });

            $.xiaoshu.ajaxPromise(promise,this.option.ajax.then,this.option.ajax.always,this.option.ajax.redirect);

        }
    };

    Resource.prototype.patch = function(){
        var data ={
            _method : 'PATCH'
        };

        if(this.option.func){
            data['_func'] = this.option.func;
        }

        data = $.xiaoshu.formatParams(this.option.params ,data);

        this.ajax(data);
    };


    Resource.prototype.put = function(){
        var data ={
            _method : 'PUT',
            _func : this.option.func
        };
        data = $.xiaoshu.formatParams(this.option.params ,data);

        this.ajax(data);
    };

    function Plugin(option){
        return this.each(function () {
            var $this   = $(this);
            var data    = $this.data('xs.resource');
            var options = $.extend({} ,Resource.DEFAULTS , $this.data() ,typeof option == 'object' && option);

            if(!data) $this.data('xs.resource' , (data = new Resource(this,options)));

            data.run();
        })
    }

    $.fn.xsresource            = Plugin;
    $.fn.xsresource.Contructor = Resource;

    $(document).on('click.xs.resource.data-api','[data-xiaoshu="resource"]',function(e){
        var $this   = $(this);

        Plugin.call($this);
        e.preventDefault();
    });
}(jQuery);

//admin role 节点更新 高耦合
//@todo 需要松耦合
+function($){

    $(document).on('click.xs.node.data-api','[data-xiaoshu=node]',function(e){
        var $this= $(this);
        var target = $this.data('target');
        var checked;
        checked = $this.attr('checked');
        if(checked != 'checked'){
            $(target).find('[name="nodes[]"]').removeAttr('checked');
        } else {
            $(target).find('[name="nodes[]"]').attr('checked','checked');
        }
    });

}(jQuery);


//form
+function($){

    var Form = function(element , options){
        this.element    = $(element);
        this.option     = $.extend({},Form.DEFAULTS , this.element.data(), typeof options == 'object' && options);
        this.target     = $(this.option.target);
    };

    Form.DEFAULTS = {
        target : '',
        name   : '',
        value  : '',
        method : '',
        params : '',
        attrs  : '',
        ajax : {
            cache : false,
            async : false,
            dataType : 'json',
            then : '',
            always : '',//通用
            redirect : ''
        }
    };


    Form.prototype.reset    = function(){
        if(this.option.name){
            var $target = this.target.find('[name='+this.option.name+']');
            $target.val(this.option.value);
        }

        if(this.option.params){
            var data = $.xiaoshu.formatParams(this.option.params,{});
            this.fillForm(data);
        }

        if(this.option.attrs){
            var attrs = $.xiaoshu.formatParams(this.option.attrs,{});
            this.changeForm(attrs);
        }

    };

    Form.prototype.submit   = function(){
        this.target.submit();
    };

    Form.prototype.resetSubmit = function(){
        this.reset();
        this.submit();
    };

    Form.prototype.fillForm  = function(data){
        for( var node in data){
            if(data.hasOwnProperty(node)){
                this.target.find('[name="'+node+'"]').val(data[node]);
            }
        }
    };

    Form.prototype.changeForm = function(attrs){
        for(var attr in attrs){
            if(attrs.hasOwnProperty(attr)){
                this.target.attr(attr,attrs[attr]);
            }
        }
    };


    Form.prototype.ajaxSubmit = function(){
        var type = this.target.attr('method');
        var url  = this.target.attr('action');
        var redirect =  this.target.data('redirect');
        var data ;
        if(type && type.toLowerCase() == 'get'){
            data = this.target.serialize();
        }else {
            data = this.target.serializeArray();
        }

        var promise = $.ajax({
            url   : url,
            cache : this.option.ajax.cache,
            type  : type,
            dataType : this.option.ajax.dataType,
            data  : data,
            async : this.option.ajax.async
        });

        $.xiaoshu.ajaxPromise(promise,this.option.ajax.then, this.option.ajax.always , redirect);
    };

    Form.prototype.run      = function(){

        if(/reset|submit|resetSubmit|ajaxSubmit/.test(this.option.method)){
            this[this.option.method]();
        }

    };

    function Plugin(option){
        return this.each(function(){
            var $this = $(this);
            var data  = $this.data('xs.form');
            var options = typeof option == 'object' && option;

            if(!data) $this.data('xs.form',data = new Form($this,options));
            if(typeof option == 'string') data[option](); else data.run();
        });
    }

    $.fn.xsform             = Plugin;
    $.fn.xsform.Contructor  = Form;

    $(document).on('click.xs.form.data-api','[data-xiaoshu=form]',function(e){
        var $this   = $(this);

        Plugin.call($this);
        e.preventDefault();
    });

}(jQuery);

//toggle;
+function($){

    var Toggle = function(element , option){
        this.element = $(element);
        this.option  = option;

        var href = this.element.attr('href');
        var target = this.option.target;

        this.target = target ? $(target) : $(href);
    };

    Toggle.DEFAULTS = {
        duration : 0 ,
        method   : '',
        target   : '',
        callback : function(){ return true }
    };

    Toggle.prototype.show = function(){
        this.target.show(this.option.duration,this.option.callback);
    };

    Toggle.prototype.close = function(){
        this.target.hide(this.option.duration,this.option.callback);
    };

    Toggle.prototype.toggle = function(){
        this.target.toggle(this.option.duration,this.option.callback);
    };

    Toggle.prototype.run = function(){
        if(this[this.option.method]) this[this.option.method]();
    };

    function Plugin(option) {
        var $this = $(this);
        var options = $.extend({},Toggle.DEFAULTS , typeof option == 'object' && option);
        var data  = $this.data('xs.toggle');

        if(!data) $this.data('xs.toggle',data = new Toggle(this,options));
        data.run();
    }

    $.fn.xstoggle             = Plugin;
    $.fn.xstoggle.Contructor  = Toggle;

    $(document).on('click.xs.toggle.data-api','[data-xiaoshu=toggle]',function(e){
        var $this = $(this);
        var method = $this.data('method');
        var target = $this.data('target');
        var option = {};

        if(method) option.method = method;
        if(target) option.target = target;


        Plugin.call($this,option);

    });

}(jQuery);

//dropdown
+function($){

    var Dropdown = function(element,option){
        this.element = $(element);
        this.option  = $.extend({} , Dropdown.DEFAULTS , typeof option =='object' && option);

        var target   = this.element.data('target');
        this.target  = $(target);
        this.status  = false;
    };

    Dropdown.DEFAULTS = {
        showSpeed : 0,
        hideSpeed : 0
    };

    Dropdown.prototype.toggle = function(){
        if(this.status){
            this.hide();
        } else {
            this.show();
        }
    };

    Dropdown.prototype.hide = function(){
        this.target.hide(this.option.hideSpeed);
        this.status = false;
    };

    Dropdown.prototype.show = function(){
        this.target.show(this.option.showSpeed,function(){
            var $this = $(this);
            $this.find('[data-dropdown=item]').one('click',function(){
                $this.hide(0);
            });
        });
        this.status = true;
    };

    function Plugin(option){
        return this.each(function(){
            var $this = $(this);
            var data  = $this.data('xs.dropdown');

            if(!data) $this.data('xs.dropdown',data = new Dropdown(this,option));

            data.toggle();
        });
    }

    $.fn.xsdropdown             = Plugin;
    $.fn.xsdropdown.Constructor = Dropdown;

    $(document).on('click.xs.dropdown.data-api','[data-xiaoshu=dropdown]',function(e){
        var $this = $(this);
        Plugin.call($this,'toggle');
    });


}(jQuery);

+function($){

    var Page = function (element , option){

    };


    Page.prototype.ajaxNext = function() {

    };

    function Plugin(option){
        return $(this).each(function(){
            var $this = $(this);
            var data  = $this.data('xs.page');

            if(typeof option == 'object')

            if(!data) $this.data('xs.page',data = new Page(this,option));

        });
    }

    $.fn.xsPage             =   Plugin;
    $.fn.xsPage.Constructor =   Page;

    $(document).on('click.xs.page.data-api','[data-xiaoshu=page]',function(e){
        var $this= $(this);
        Plugin.call($this);
    });

}(jQuery);
