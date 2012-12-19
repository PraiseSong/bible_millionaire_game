$(function (){
    //最终的所有游戏主题对象
    var newTopics = {};
    //最终拼装后的所有主题结构图
    var topicsHtml = '';
    //最终的所有游戏题目数据
    var subjects = {};
    //当前选择的主题
    var currentTopics = [];
    //随机抽出的题目
    var activitySubject = [];
    //当前主题内容
    var currentTopicHtml = '';
    //当前题目
    var currentQuestion = null;
    //当前答题结果
    var currentAnswerResult = false;

    var introduceBox = $('#J-introducing'),
        loadingBox = $('#J-loading'),
        topicBox = $('#J-topics-box'),
        subjectBox = $('#J-subject-box');

    //调整游戏界面
    function adjustLayout(){
        var winH = $(window).height(),
            containerH = $('#J-container').get(0).offsetHeight,
            scrollTop = $(document).scrollTop(),
            h = (winH-containerH)/2+scrollTop;

        if(h <= 0){
            h = 'auto';
        }
        $('#J-container').css('margin-top',h)
    };
    adjustLayout();
    $(window).resize(adjustLayout);

    //loading
    function loading(){
        var containerH = $('#J-container').get(0).clientHeight,
            loadingH = $('#J-loading').get(0).offsetHeight,
            scrollTop = $(document).scrollTop(),
            h = (containerH-loadingH)/2+scrollTop;

        $('#J-bwfw-logo').get(0).addEventListener('webkitAnimationEnd',function (){
            $('#J-bwfw-logo').attr('class','rotating');
            getData();
        },false);
    }
    loading();

    //获取所有数据
    function getData(){
        getTopics();
        getSubjects();
    }

    //获取所有游戏主题
    function getTopics(){
        var ajaxurl = 'app/ajax.php5';
        $.ajax(ajaxurl,{
            dataType: 'json',
            data:'action=query_topic',
            success:success
        });
        function success(data){
            if(data.data){
                var topics = data.data;
                var html = '';
                drawTopicsStructure(topics);
                setTimeout(function (){
                    introducing();
                },2000);
            }
        }
    }

    //获取所有游戏题目
    function getSubjects(){
        var ajaxurl = 'app/ajax.php5';
        $.ajax(ajaxurl,{
            dataType: 'json',
            data:'action=query_subjects',
            success:success
        });
        function success(data){
            if(data.data){
                subjects = data.data;
            }
        }
    }

    //绘制游戏的当前主题结构
    function drawTopicsStructure(data){
        if(!data){
            return topicsHtml = '暂无主题';
        }
        var noParent = [];
        var haveParent = [];

        $.each(data,function (k,v){
            if(v.parent){
                haveParent.push(v);
            }else{
                noParent.push(v);
            }
        });

        $.each(haveParent,function (k,v){
            var pids = v.parent.split(',');
            haveParentToNewObject(pids,v);
        });

        $.each(noParent,function (k,v){
            newTopics[v.id]=v;
        });

        //将有父级主题的topic放到一个新对象中
        function haveParentToNewObject(pids,o){
            $.each(data,function (k,v){
                if(pids.indexOf(v.id) !== -1){
                    //如果在新主题对象中找到当前这个有子主题的对象，则直接在这个对象
                    //上push这个子主题
                    if(newTopics[v.id]){
                        newTopics[v.id].subTopics.push(o);
                    }else{
                        if(!v.subTopics){
                            v.subTopics = [];
                        }
                        v.subTopics.push(o);

                        if(!v.parent){
                            newTopics[v.id] = v;
                        }
                    }
                }
            });
        }

        function renderStructure(){
            var html = '<ul class="topics-tree" data-topic-root="true">';

            for(i in newTopics){
                var v = newTopics[i];
                var id = v.id;
                var c = '<ins>'+v.content+'</ins>';

                if(v.subTopics){
                    subTopicsCallbackCount ? subTopicsCallbackCount = 0 : '';
                    c += subTopicsCallback(v.subTopics);
                }

                html += '<li data-topic-id="'+ id+'"><span>'+ c+'</span></li>';
            }

            html += '</ul>';
            return html;
        }

        //渲染子主题，并且返回拼装好的html片段
        var subTopicsCallbackCount = 0;//记录子主题回调函数的递归次数
        function subTopicsCallback(data){
            var tem = ++subTopicsCallbackCount;
            var html = '<ul class="subTopics-box" data-topic-level="'+tem+'">';

            $.each(data,function (k,v){
                var c = '<ins>'+v.content+'</ins>';
                if(v.subTopics){
                    c += subTopicsCallback(v.subTopics);
                }
                html += '<li data-topic-id="'+ v.id+'"><span>'+ c+'</span></li>';
                subTopicsCallbackCount = tem;
            });

            html += '</ul>';
            return html;
        }

        topicsHtml = renderStructure();
        $('#J-topics').html(topicsHtml).find('li').click(function (e){
            e.stopPropagation();
            var obj = $(this);
            var parents = obj.parents('li');
            currentTopics = [];
            currentTopics.push(obj.attr('data-topic-id'));
            $.each(parents,function (k,v){
                currentTopics.push($(v).attr('data-topic-id'));
            });
            currentTopicHtml = obj.find('ins').html();
            goToSubject();
        });
    }

    //介绍模块
    function introducing(){
        loadingBox.hide();
        introduceBox.show();
        introduceBox.find('nav li').click(function (){
            if($(this).hasClass('current')){return;}
            introduceBox.find('nav li').removeClass('current');
            var role = $(this).addClass('current').attr('data-role');
            introduceBox.find('.panel').hide();
            introduceBox.find('[data-role='+role+']').show();
        });
        $('#J-ps-logo').addClass('rotating2').get(0).addEventListener('webkitAnimationEnd',function (){
            $('#J-ps-logo').removeClass('rotating2');
            setTimeout(function (){
                $('#J-ps-logo').addClass('rotating2');
            },5000);
        },false);
        $('#J-starting').click(starting);
    }

    //开始游戏
    function starting(){
        introduceBox.hide();
        topicBox.show();
    }

    //去题目页面
    function goToSubject(){
        topicBox.hide();
        subjectBox.show();
        activitySubject = [];
        $.each(subjects,function (k,v){
            var topics = currentTopics;//.join(',');
            var subject_topicId = v.topics;
            $.each(topics,function (i,topic){
                if(subject_topicId.indexOf(topic) !== -1){
                    activitySubject.push(v);
                }
            })
        })

        renderQuestion();
    }

    //渲染一个问题
    function renderQuestion(){
        if(activitySubject.length <= 0){
            return alert('对不起，已经没有题目了');
        }
        $.each(activitySubject,function (k,v){
            currentQuestion = v;
            activitySubject.splice(k,1);
            return false;
        });

        var topic_des_box = $('#J-currentTopicDes-box'),
            questionAndsolutionsBox = $('#J-questionAndsolutionsBox'),
            maxTimeBox = $('#J-maxTime'),
            referenceBox = $('#J-reference');

        topic_des_box.html(currentTopicHtml);
        maxTimeBox.html("时限："+currentQuestion.time+":00");

        var solutions = currentQuestion.solutions.split(','),
            solutionsHtml = '<p class="webkit-box">';
        $.each(solutions,function (k,v){
            solutionsHtml += '<span class="solution flex" data-value="'+v+'">'+(++k)+' : '+v+'</span>';
            if(k %2 !== 0 || k === 0){
                solutionsHtml += '<span class="space flex"></span>';
            }
            if(k >0 && k %2 === 0){
                solutionsHtml += '</p><p class="webkit-box">';
            }
        });
        solutionsHtml += '</p>';
        questionAndsolutionsBox.html('<p class="solution-title">'+currentQuestion.content+'</p>'+solutionsHtml);

        bindUItoQuestionPage();
    }

    //绑定事件到题目页面
    function bindUItoQuestionPage(){
        var currentSolutionNode = null;
        $('#J-questionAndsolutionsBox .solution').unbind().click(function (){
            $('#J-questionAndsolutionsBox .solution').removeClass('current');
            $(this).addClass('current');
            currentSolutionNode = $(this);
        });

        $('#J-ok').unbind().click(function (){
            var solution = $.trim(currentSolutionNode.attr('data-value'));
            if(solution && currentQuestion.right_solution === solution){
                currentAnswerResult = true;
                right();
            }else{
                currentAnswerResult = false;
                wrong();
            }

            answerEnd();
        });
    }

    //正确答题
    function right(){
        console.log('right')
    }

    //错误答题
    function wrong(){
        console.log('wrong');
    }

    //答题结束
    function answerEnd(){
        console.log(currentAnswerResult)
    }

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
})