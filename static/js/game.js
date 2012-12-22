$(function (){
    //最终的所有游戏主题对象
    var newTopics = {};
    //最终拼装后的所有主题结构图
    var topicsHtml = '';
    //最终的所有游戏题目数据
    var subjects = {};
    //恢复所有游戏题目数据
    var originSubjects = null;
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
    //浮层对象
    var pop = null;
    //当前的得分
    var currentScore = 0;
    //当前第几关
    var phases = 0;
    //已经过滤了哪些题目
    var filtered = localStorage.getItem('filtered') ? JSON.parse(localStorage.getItem('filtered')): [];
    //游戏已耗时
    var currentTime = "0:00";
    var currentTimeStop = false;//暂定当前时间
    //提示暂停时间
    var tipTime = 30;//秒
    //倒计时对象
    var timer = null;
    //提示倒计时对象
    var tipTimer = null;
    //是否已经显示闯关提示
    var shownPhases = false;
    //当前答对了几道题目
    var rightAnswer = 0;
    //logo动画对象
    var rotatingLogo = null;

    var introduceBox = $('#J-introducing'),
        loadingBox = $('#J-loading'),
        topicBox = $('#J-topics-box'),
        subjectBox = $('#J-subject-box'),
        scoreBox = $('#J-score-box');

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

    //雇用音效
    function openAudio(){
        $('.back').live('click',function (){
            $('#J-audio-click').get(0).play();
        });
        $('.solution').live('click',function (){
            if($(this).hasClass('filtered')){
                return false;
            }
            $('#J-audio-click').get(0).play();
        });
    }
    openAudio();

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
                },1000);
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
                var s = data.data;
                subjects = s;
                originSubjects = s;
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

                html += '<li data-topic-id="'+ id+'"><span class="nohover">'+ c+'</span></li>';
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
                var classname = '';
                if(v.content === "红卡" || v.content === '绿卡'){
                    classname = 'class="nohover"'
                }

                var c = '<ins>'+v.content+'</ins>';

                if(v.subTopics){
                    c += subTopicsCallback(v.subTopics);
                }

                html += '<li data-topic-id="'+ v.id+'"><span '+classname+'>'+ c+'</span></li>';
                subTopicsCallbackCount = tem;
            });

            html += '</ul>';
            return html;
        }

        topicsHtml = renderStructure();
        $('#J-topics').html(topicsHtml).find('li').click(function (e){
            e.stopPropagation();
            var obj = $(this);
            if($(obj.find('span')[0]).hasClass('nohover')){
                return false;
            }
            var parents = obj.parents('li');
            currentTopics = [];
            currentTopics.push(obj.attr('data-topic-id'));
            $.each(parents,function (k,v){
                currentTopics.push($(v).attr('data-topic-id'));
            });
            currentTopicHtml = obj.find('ins').html();
            goToSubject();
        });
        topicBox.find('.back').click(function (){
            topicBox.hide();
            introduceBox.show();
        });
    }

    //介绍模块
    function introducing(){
        //一个新用户开始玩游戏时，清除本地缓存的数据
        localStorage.clear();

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

        //一个新用户开始玩游戏时，清除本地缓存的数据
        localStorage.clear();
        resetSubjectsData();

        $('#J-filter').show();
        $('#J-skip').show();
    }

    //恢复所有题目的数据
    function resetSubjectsData(){
        //subjects的数据在处理的过程中，会有变动
        //因此每次开始游戏时，加载原始的题目数据
        subjects = $.extend(true,[],originSubjects);
    }

    //去题目页面
    function goToSubject(){
        topicBox.hide();
        subjectBox.show();

        rotatingLogo = setInterval(function (){
            var logo = $('#J-subject-logo');
            if(logo.hasClass('rotating2')){
                logo.attr('class','rotating3');
            }else if(logo.hasClass('rotating3')){
                logo.attr('class','rotating2');
            }
        },10000);

        resetSubjectsData();

        var filtered = localStorage.getItem('filtered') ? JSON.parse(localStorage.getItem('filtered')): [];
        //再一次玩游戏时，把已经答正确的题目过滤掉
        $.each(subjects,function (k,v){
            if(v && filtered.indexOf(v.id) !== -1){
                subjects.splice(k,1,null);
            }
        })


        activitySubject = [];
        var topics = currentTopics;

        /**
         * 按照游戏主题从前到后的顺序把所关联的题目拿出来
         * 比如：用户选择的主题是：科技->手机->iphone，
         * 那么查询题目时将按照：iphone->手机->科技，这样的顺序把题目列出来
         * 这样就把最接近用户想要的主题数据提取到最前面了
         */
        function getActivitySubject(){
            if(topics[0]){
                $.each(subjects,function (k,v){
                    if(v){
                        var subject_topicId = v.topics;
                        if(subject_topicId.indexOf(topics[0]) !== -1){
                            activitySubject.push(v);
                            //过滤已经查询出的题目
                            subjects.splice(k,1,null);
                        }
                    }
                })
                topics.splice(0,1);

                if(topics[0]){
                    getActivitySubject();
                }
            }
        }
        getActivitySubject();

        var score_html =    '<li class="1million phases" data-value="100000000">15<>1 MILLION</li>'+
                            '<li data-value="500000">14<>500000</li>'+
                            '<li data-value="25000">13<>250000</li>'+
                            '<li data-value="15000">12<>150000</li>'+
                            '<li data-value="80000">11<>80000</li>'+
                            '<li class="60000score phases" data-value="60000">10<>60000</li>'+
                            '<li data-value="40000">9<>40000</li>'+
                            '<li data-value="30000">8<>30000</li>'+
                            '<li data-value="20000">7<>20000</li>'+
                            '<li data-value="10000">6<>10000</li>'+
                            '<li class="8000score phases" data-value="8000">5<>8000</li>'+
                            '<li data-value="4000">4<>4000</li>'+
                            '<li data-value="3000">3<>3000</li>'+
                            '<li data-value="2000">2<>2000</li>'+
                            '<li class="next-score" data-value="1000">1<>1000</li>';
        scoreBox.html(score_html);

        $('#J-ok').hide();

        renderQuestion();
    }


    //没有题目
    function noSubjects(){
        $('#J-getScore p').html("<span style=\"font-size:35px;line-height:145px;display:block;\">对不起，没有最新的题目</span>");

        pop = new Pop({
            element: '#J-getScore',
            width: 500,
            afterShow: function (){
                pop.mask.unbind('click.pop');
            }
        });
        pop.show();

        $('#J-next').hide();
        $('#J-exit').show();
        $('.first-space').show();
    }

    //渲染一个问题
    function renderQuestion(){
        activitySubject = randomOrder(activitySubject);

        if(activitySubject.length <= 0){
            return noSubjects();
        }

        $.each(activitySubject,function (k,v){
            if(v){
              currentQuestion = v;
              activitySubject.splice(k,1,null);
              return false;
            }else{
                currentQuestion = false;
            }
        });

        if(!currentQuestion){
            return noSubjects();
        }

        var topic_des_box = $('#J-currentTopicDes-box'),
            questionAndsolutionsBox = $('#J-questionAndsolutionsBox'),
            maxTimeBox = $('#J-maxTime');

        topic_des_box.html(currentTopicHtml+'<div id="J-reference"></div>');
        var time_html = currentQuestion.time < 1 ? "时限：00:"+currentQuestion.time+"" : "时限："+currentQuestion.time+":00"
        maxTimeBox.html('时限: 00:30');

        var solutions = currentQuestion.solutions.split(','),
            solutionsHtml = '<p class="webkit-box">';
        solutions = randomOrder(solutions);
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

        countDown();

        bindUItoQuestionPage();
    }

    //倒计时
    function countDown(){
        var maxTime = currentQuestion.time+":60",//分钟
            countDownBox = $('#J-maxTime');

        //if(currentQuestion.time < 1){
            maxTime = "00:30";
        //}

        var time = maxTime.split(':'),
            minute = time[0] > 0 ? --time[0] : time[0],
            second = time[1],
            _currentTime = currentTime.split(':'),
            currentTimeMinute = parseInt(minute,10),
            currentTimeSecond = parseInt(second,10);

        timer && clearInterval(timer);

        timer = setInterval(looper,1000);

        function looper(){
            if(currentTimeStop){
                clearInterval(timer);

                setTimeout(function (){
                    timer = setInterval(looper,1000);
                },30000);
            }else{
                callback();
            }

            function callback(){
                if(currentTimeSecond <= second){
                    currentTimeSecond = --second;

                    if(currentTimeSecond === 0 && currentTimeMinute > 0){
                        second = 60;
                        currentTimeSecond = --second;

                        currentTimeMinute = --minute;
                    }
                }
                if(currentTimeMinute === 0 && currentTimeSecond === 0){
                    wrong();
                    clearInterval(timer);
                }

                countDownBox.html('时限: '+(currentTimeMinute < 10 ? "0"+currentTimeMinute+"" : currentTimeMinute)+":"+(currentTimeSecond < 10 ? "0"+currentTimeSecond+"" : currentTimeSecond));
            }
        }
    }

    //绑定事件到题目页面
    function bindUItoQuestionPage(){
        var currentSolutionNode = null;
        $('#J-questionAndsolutionsBox .solution').unbind().click(function (){
            $('#J-questionAndsolutionsBox .solution').removeClass('current');
            $(this).addClass('current');
            currentSolutionNode = $(this);
            if(currentSolutionNode){
                $('#J-ok').show();
            }
        });

        $('#J-ok').unbind().click(function (){
            if(!currentSolutionNode){return;}

            if(pop){
              pop = null;
            }
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

        $('#J-next').unbind().click(nextSubject);

        $('#J-exit').unbind().click(function (){
            /*window.open('', '_self', '');
            window.close();*/
            exit();
        });

        $('#J-tip').unbind().click(function (){
            currentQuestion && currentQuestion.reference && $('#J-reference').html(currentQuestion.reference);
            $('#J-tip').unbind();
            showTipTime();
        });

        $('#J-skip').unbind().click(function (){
            $('#J-skip').unbind().hide();
            renderQuestion();
            $('#J-ok').hide();
        });

        $('#J-filter').unbind().click(function (){
            $('#J-filter').unbind().hide();
            filterSolution();
        });
    }

    //离开游戏
    function exit(){
        localStorage.clear();
        topicBox.hide();
        subjectBox.hide();
        introduceBox.show();
        pop && pop.hide();
        clearInterval(rotatingLogo);
    }

    //显示提示时间
    function showTipTime(){
        $('#J-audio-tip').get(0).play();

        currentTimeStop = true;

        var box = $('#J-reference'),
            time = '00:30';

        var time = time.split(':'),
            minute = time[0],
            second = time[1];

        tipTimer = setInterval(callback,1000);

        if(!$('#J-tipTime-box').get(0)){
            box.append('<p id="J-tipTime-box">'+time.join(":")+'</p>');
        }
        function callback(){
            if(second === 0){
                currentTimeStop = false;
                clearInterval(tipTimer);
            }else{
                if(second>0 && second <= 30){
                    second--;

                    if(second < 10){

                    }
                    $('#J-tipTime-box').html(''+minute+':'+(second < 10 ? "0"+second+"" : second)+'');
                }
            }
        }
    }

    //过滤可选答案
    function filterSolution(){
        var questionAndsolutionsBox = $('#J-questionAndsolutionsBox'),
            solutions = questionAndsolutionsBox.find('.solution');

        var i = 0;
        function filting(){
            $.each(solutions,function (k,v){
                var obj = $(v);
                if($.trim(obj.attr('data-value')) !== currentQuestion.right_solution && !obj.hasClass('filtered') && i<2){
                    if(k === ((parseInt(Math.random()*4+1))-1)){
                        obj.addClass('filtered').unbind().css('cursor','default');
                        i++;
                        if(i < 2){
                            filting();
                        }
                    }else{
                        filting();
                    }
                }
            })
        }
        filting();
    }

    //获取转换后的分数
    function getScoreHtml(data){
        var s = data;
        var k = 0;
        //处理分数为1,000,000
        function getScore(data){
            if(data < 1000){
                return data;
            }
            var result = '';
            var b = data / 1000;
            if(b >= 1000){
                k++;
                return getScore(b);
            }else{
                result = b;
                for(var i=-1;i<k;i++){
                    result += ',000';
                }
            }
            return result;
        }

        return getScore(s);
    }

    //正确答题
    function right(){
        var score = parseInt(scoreBox.find('.next-score').attr('data-value'),10);

        $('#J-getScore p').html(getScoreHtml(score));
        pop = new Pop({
            element: '#J-getScore',
            width: 500,
            afterShow: function (){
                pop.mask.unbind('click.pop');
            }
        });
        pop.show();
        $('#J-next').show();
        $('#J-exit').hide();
        $('.first-space').hide();

        //在本地存储中保存已经答过题目的id
        if(filtered.indexOf(currentQuestion.id) === -1){
            filtered.push(currentQuestion.id);
            localStorage.setItem('filtered',JSON.stringify(filtered));
        }

        countScore();

        rightAnswer++;

        if(rightAnswer === 15){
            passPhaes();
        }
        $('#J-audio-right').get(0).play();
    }

    //通关
    function passPhaes(){
        $('#J-getScore .getScrore').html("<span style=\"font-size:40px;line-height:145px;display:block;\" class=\"passPhases\">恭喜你，通关！</span>")
        pop && pop.show();
        $('#J-next').hide();
        $('#J-exit').show();
        $('.first-space').hide();
        $('#J-audio-passPhases').get(0).play();
    }

    //计算分数
    function countScore(){
        var score = scoreBox.find('.next-score').attr('data-value');
        currentScore = parseInt(score,10);

        if(currentScore === 8000){
            shownPhases = false;
            phases = 1;
        }

        if(currentScore === 60000){
            shownPhases = false;
            phases = 2;
        }

        if(currentScore === 100000000){
            shownPhases = false;
            phases = 3;
        }
        if(phases !== 0 && !shownPhases){
            shownPhases = true;
            $('#J-getScore .getScrore').html("<span style=\"font-size:40px;line-height:145px;display:block;\" class=\"passPhases\">恭喜闯过第 "+phases+" 关</span>")
            pop && pop.show();
        }
    }

    //错误答题
    function wrong(){
        $('#J-getScore p').html(0);

        pop = new Pop({
            element: '#J-getScore',
            width: 500,
            afterShow: function (){
                pop.mask.unbind('click.pop');
            }
        });
        pop.show();

        $('#J-next').hide();
        $('#J-exit').show();
        $('.first-space').show();

        if(phases === 0){
            currentScore = 0;
        }

        if(phases > 0){
            rightAnswer = 0;
            $('#J-getScore p').html("<span style=\"font-size:30px;line-height:145px;display:block;\">当前得分："+getScoreHtml(currentScore)+"</span>");
        }
        $('#J-audio-wrong').get(0).play();
    }

    //答题结束
    function answerEnd(){
        timer && clearInterval(timer);
        tipTimer && clearInterval(tipTimer);
    }

    //进入下一题
    function nextSubject(){
        pop && pop.hide();
        $('#J-next').hide();
        $('#J-ok').hide();
        var next_score_node = scoreBox.find('.next-score').prev();
        scoreBox.find('li').removeClass('next-score');
        if(next_score_node.get(0)){
            next_score_node.addClass('next-score');
        }
        renderQuestion();
    }

    /*pop-box*/
    function Pop(o){
        var _default = {
            beforeShow: function (){},
            afterShow:function (){},
            afterHide:function (){},
            beforeHide: function (){},
            width:530
        };
        this.options = $.extend(_default,o);
        this.initializer();
    }
    Pop.prototype = {
        initializer: function (){
            this.popBox = $('<div class="pop-box" style="width:'+this.options.width+'px;overflow:hidden;position:absolute;z-index:9999999">');
            this.mask = $('<div class="mask">');
            $('body').append(this.mask.hide()).append(this.popBox.hide());

            var node = $(this.options.element).css('position','static').hide().get(0);
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
            $(window).unbind('resize.pop');
            this.mask.unbind('click.pop');
            this.popBox = null;
            this.mask = null;
        },
        addEvent: function (){
            $(window).bind('resize.pop', $.proxy(this.sync,this)).bind('scroll.pop',$.proxy(this.sync,this));
            this.mask.bind('click.pop',$.proxy(this.hide,this));

            if(this.options.close){
                $(this.options.close).bind('click.pop',$.proxy(this.hide,this));
            }
        },
        removeEvent: function (){
            $(window).unbind('resize.pop').unbind('scroll.pop');
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

    function randomOrder (targetArray)
    {
        var arrayLength = targetArray.length
        //
        //先创建一个正常顺序的数组
        var tempArray1 = new Array();

        for (var i = 0; i < arrayLength; i ++)
        {
            tempArray1 [i] = i
        }
        //
        //再根据上一个数组创建一个随机乱序的数组
        var tempArray2 = new Array();

        for (var i = 0; i < arrayLength; i ++)
        {
            //从正常顺序数组中随机抽出元素
            tempArray2 [i] = tempArray1.splice (Math.floor (Math.random () * tempArray1.length) , 1)
        }
        //
        //最后创建一个临时数组存储 根据上一个乱序的数组从targetArray中取得数据
        var tempArray3 = new Array();

        for (var i = 0; i < arrayLength; i ++)
        {
            tempArray3 [i] = targetArray [tempArray2 [i]]
        }
        //
        //返回最后得出的数组
        return tempArray3
    }
})