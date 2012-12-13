/*pop-box*/
function Pop(o){
    var _default = {
        beforeShow: function (){},
        afterShow:function (){},
        afterHide:function (){},
        beforeHide: function (){},
        width:400
    };
    this.options = $.extend(_default,o);
    this.initializer();
}
Pop.prototype = {
    initializer: function (){
        this.popBox = $('<div class="pop-box" style="width:'+this.options.width+'px;overflow:hidden;">');
        this.mask = $('<div class="mask">');
        $('body').append(this.mask.hide()).append(this.popBox.hide());

        var node = $(this.options.element).hide().get(0);
        this.popBox.get(0).insertBefore(node);
    },
    show: function (){
        show.call(this);
        this.sync();
        this.options.beforeShow();
        this.popBox.css('opacity',1).show();
        this.mask.show();
        $(this.options.element).css('opacity',1).show();
        this.addEvent();
        this.options.afterShow();
    },
    hide: function (){
        this.options.beforeHide();
        this.popBox.hide();
        this.mask.hide();
        $(this.options.element).hide();
        this.removeEvent();
        this.options.afterHide();
    },
    sync: function (){
        var w = this.popBox.get(0).offsetWidth,
            h = this.popBox.get(0).offsetHeight;
            docW = $(window).width(),
            docH = $(document).height(),
            scrollTop = $(window).scrollTop(),
            left = (docW - w) / 2,
            _top =  ($(window).height() - h) / 2 + scrollTop;

        this.mask.css({
            height:docH,
            width:docW
        });
        this.popBox.css({
            left:left,
            top:_top
        });
    },
    destroy: function (){
        this.removeEvent();
        this.popBox.remove();
        this.mask.remove();
        $(this.options.element).remove();
        this.popBox = null;
        this.mask = null;
    },
    addEvent: function (){
        $(window).bind('resize.pop', $.proxy(this.sync,this));
        this.mask.bind('click.pop',$.proxy(this.hide,this));

        if(this.options.close){
            $(this.options.close).bind('click.pop',$.proxy(this.hide,this));
        }
    },
    removeEvent: function (){
        $(window).unbind('resize.pop');
        this.mask.unbind('click.pop');

        if(this.options.close){
            $(this.options.close).unbind('click.pop');
        }
    }
};

function show(){
    this.popBox.css('opacity',0).show();
    $(this.options.element).css('opacity',0).show();
}

function AjaxGlobalError(data){
    alert(data.memo || '与服务器通信发生故障');
    AjaxGlobalTips('与服务器通信发生故障','error');
}

function AjaxGlobalStart(){
    $('#J-global-loading').show();
}

function AjaxGlobalEnd(){
    $('#J-global-loading').hide();
}

function AjaxGlobalTips(html,type){
    var loadingBox = $('#J-global-loading');
    loadingBox.show().html(html);
    switch(type){
        case 'error':
            loadingBox.css('background','red');
            break;
        default:
            loadingBox.css('background','green');
            break;
    }
}