/*pop-box*/
function Pop(o){
    var _default = {};
    this.options = $.extend(_default,o);
    this.init();
}
Pop.prototype = {
    init: function (){
        this.popBox = $('<div class="pop-box">');
        this.mask = $('<div class="mask">');
        $('body').append(this.mask.hide()).append(this.popBox.hide());
        $(this.options.element).hide().appendTo(this.popBox);
    },
    show: function (){
        show.call(this);
        this.sync();
        this.popBox.css('opacity',1).show();
        this.mask.show();
        $(this.options.element).css('opacity',1).show();
        this.addEvent();
    },
    hide: function (){
        this.popBox.hide();
        this.mask.hide();
        $(this.options.element).hide();
        this.removeEvent();
    },
    sync: function (){
        var w = this.popBox.outerWidth(),
            h = this.popBox.outerHeight();
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
        });console.log(00)
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
    },
    removeEvent: function (){
        $(window).unbind('resize.pop');
        this.mask.unbind('click.pop');
    }
};

function show(){
    this.popBox.css('opacity',0).show();
    $(this.options.element).css('opacity',0).show();
}