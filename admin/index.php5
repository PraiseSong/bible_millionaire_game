<?php include_once("config.php5"); ?>
<?php include_once(APP_PATH."/db.php5"); ?>
<?php include_once(TEMPLATES_PATH."/header.php5"); ?>
<?php include_once("load_app.php5"); ?>

<div class="container">
    <h2>游戏题目配置</h2>
    <form action="javascript:void(0)" id="J-subjects">
        <table>
            <tbody>
              <tr>
                  <td>题目内容：</td>
                  <td><textarea type="text" class="input_text" id="J-content" required></textarea></td>
              </tr>
              <tr>
                  <td>参考经文：</td>
                  <td><input disabled="disabled" type="text" class="input_text" id="J-reference" required /><a href="javascript:void(0)" class="find-bible" id="J-find-bible">查找经文</a> |
                      <a href="javascript:void(0)" id="J-bible-clean">清空</a></td>
              </tr>
              <tr>
                  <td>完成游戏所需时间：</td>
                  <td><input type="number" class="input_text" value="5" id="J-time" required placeholder="请填整数，以分钟为单位" />以分钟为单位</td>
              </tr>
              <tr>
                  <td>所属的主题：</td>
                  <td class="t-right"><div id="J-topics-labels">暂无主题</div><!--<input type="text" class="input_text" id="J-topic" required /><a href="javascript:void(0)" class="find-bible" id="J-find-topics">查找主题</a>--></td>
              </tr>
              <tr>
                  <td></td>
                  <td><input type="submit" value="确定" class="btn-ok left" id="J-submit-subject" /></td>
              </tr>
            </tbody>
        </table>
    </form>

    <h2>创建主题</h2>
    <form action="javascript:void(0)">
    <table>
        <tbody>
        <tr>
            <td>
                名称
            </td>
            <td><input type="text" class="input_text" id="J-topic-name" required /></td>
        </tr>
        <tr>
            <td>
                它的父级
            </td>
            <td><select name="" id="J-topic-name-parent" multiple="50" class="multiple">
                <option value="0" disabled="disabled">正在加载主题</option>
            </select></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" value="确定" class="btn-ok left" id="J-submit-topic" /></td>
        </tr>
        </tbody>
    </table>
    </form>

    <div id="J-form-table" class="hide">
    <table>
        <tbody>
        <tr>
            <td>圣经书卷：</td>
            <td>
                <select id="J-booktitle"></select>
            </td>
        </tr>
        <tr>
            <td>章数：</td>
            <td>
                <select id="J-article_num">
                    <option value="null">请选择书卷</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>节数：</td>
            <td class="no-float">
                从
                <select id="J-verse_start">
                    <option value="null">请选择书卷</option>
                </select>
                节
                到
                <select id="J-verse_stop">
                    <option value="null">请选择书卷</option>
                </select>
                节
            </td>
        </tr>
        <tr class="form-field">
            <td><input type="button" class="btn-blue" value="使用" id="J-use" /></td>
            <td><a href="javascript:void(0)" class="J-close">关闭</a><span id="J-loading" class="left"></span></td>
        </tr>
        </tbody>
    </table>
    <p id="J-bible-box"></p>
    </div>

    <!--<div id="J-topics-box">
        <div id="J-topics-labels">

        </div>
        <a href="javascript:void(0)" class="J-close J-topic-close">关闭</a>
    </div>-->

    <script type="text/javascript">
        var topicPop = null;
        //绑定查询主题的事件
        function bindQueryTopic(){return;
            var trigger = $("#J-find-topics");
            topicPop = new Pop({
                element: '#J-topics-box',
                close:'.J-topic-close',
                afterShow: function (){
                    //$('#J-use').unbind().bind('click',useBible);
                }
            });
            trigger.click(function (){
                topicPop.show();
            });
        }
        bindQueryTopic();
        //渲染浮层中的所有主题
        var selected = [];
        function renderTopicsLabels(data){
            var box = $('#J-topics-labels');
            if(!data){
                return box.html('暂无主题');
            }
            var html = '';
            $.each(data,function (k,v){
                var br = (k+1) %3 === 0 ? '<br />' : '';
                var checked = selected.indexOf(v.id) !== -1 ? 'checked="checked"' : '';
                html += '<label style="margin:0px 10px 5px 0;"><input type="checkbox" id="'+ v.id+'" data-parent="'+ v.parent+'" '+checked+'>'+ v.content+'</label>'+br;
            });
            box.html(html);

            bindTopicClick(box);
        }

        //向所有的topic复选框绑定单击事件
        function bindTopicClick(dom){
            var handler = function (e){
                if($(e.target).attr('type') === 'checkbox'){
                    var currentTopic = $(e.target);
                    var id = currentTopic.attr('id');

                    if(currentTopic.attr('checked')){
                        if(selected.indexOf(id) === -1){
                            selected.push(id);
                        }
                    }

                    if(!currentTopic.attr('checked') && selected.indexOf(id) !== -1){
                        $.each(selected,function (k,v){
                            if(v === id){
                                selected.splice(k,1)
                            }
                        });
                    }
                }
            }
            dom.unbind('change').change(handler);
        }

        var pop = null;
        //使用经文
        function useBible(){
            var bt = $('#J-booktitle').find('option:selected').html(),
                article = $('#J-article_num').val(),
                verse_start = $('#J-verse_start').val(),
                verse_stop = $('#J-verse_stop').val(),
                verse = verse_start+"-"+verse_stop,
                reference = $('#J-reference'),
                currentFerence = reference.val();

            if(verse_start === verse_stop){
                verse = verse_start;
            }

            var data = bt+" "+article+":"+verse+"; ";

            reference.val(currentFerence+data);
            pop.hide();
        }
        //绑定查询圣经的事件
        function bindQueryBible(){
            $('#J-bible-clean').click(function (){
                $('#J-reference').val('');
            });

            var trigger = $('#J-find-bible');
            pop = new Pop({
                element: '#J-form-table',
                close:'.J-close',
                afterShow: function (){
                    $('#J-use').unbind().bind('click',useBible);
                }
            });
            trigger.click(function (){
                pop.show();
            });
        }
        bindQueryBible();

        var bibleAjaxurl = '../app/bible.php5';
        function queryBooktitle(){
            $.ajax(bibleAjaxurl,{
                data: 'action=queryBooktitle',
                dataType: 'json',
                success: success,
                error:AjaxGlobalError
            });

            function success(data){
                if(data && data.data.length >= 1){
                    renderBooktitle(data.data);
                }
            }

            function renderBooktitle(data){
                var box = $('#J-booktitle'),
                    html = '';
                $.each(data,function (k,v){
                    html += '<option value="'+v["Book"]+'" data-alias="'+v["Alias"]+'">'+v["BookTitle"]+'</option>';
                });

                if(html){
                    box.html(html);
                    bindSelect();
                }
            }
        }
        queryBooktitle();

        function bindSelect(){
            var booktitle = $('#J-booktitle'),
                currentTitle = null,
                currentStart = null,
                currentStop = null,
                loading = $('#J-loading'),
                article_num = $('#J-article_num'),
                verse_start = $('#J-verse_start'),
                verse_stop = $('#J-verse_stop'),
                bibleBox = $('#J-bible-box');

            function quest_end(){
                booktitle.attr('disabled',false);
                article_num.attr('disabled',false);
                verse_start.attr('disabled',false);
                verse_stop.attr('disabled',false);
            }

            function start(){
                loading.html('查询中...');
                booktitle.attr('disabled',true);
                article_num.attr('disabled',true);
                verse_start.attr('disabled',true);
                verse_stop.attr('disabled',true);
            }

            function query_article_num_success(data){
                quest_end();

                loading.empty();

                if(data.resultStatus !== 100){
                    return AjaxGlobalError(data);
                }
                if(data.data <= 0){
                    return loading.html('数据有误，请重试。');
                }

                var html = '';
                for(var i=1;i<=data.data;i++){
                    html += '<option value="'+i+'">'+i+'</option>';
                }

                article_num.html(html);

                query_verse();
            }

            //根据书卷查询章数
            function query_article_num(){
                currentTitle = booktitle.val();

                if(!currentTitle){return;}

                start();
                $.ajax(bibleAjaxurl,{
                    dataType: 'json',
                    data: 'action=query_article_num&id='+currentTitle+'',
                    success: query_article_num_success,
                    error:AjaxGlobalError
                });
            }

            //根据书卷、章数查询节数
            function query_verse(){
                if(!article_num.val() || !booktitle.val()){return;}

                start();
                $.ajax(bibleAjaxurl,{
                    data: 'action=query_verse_num&article='+article_num.val()+'&id='+booktitle.val()+'',
                    dataType: 'json',
                    success: query_verse_num_success,
                    error:AjaxGlobalError
                });
            }

            function query_verse_num_success(data){
                quest_end();

                loading.empty();

                if(data <= 0){
                    return loading.html('数据有误，请重试。');
                }

                var html = '';
                for(var i=1;i<=data.data;i++){
                    html += '<option value="'+i+'">'+i+'</option>';
                }

                verse_start.html(html);
                verse_stop.html(html);

                query_bible();
            }

            //根据书卷、章数、节数查询具体的经文
            function query_bible(){
                if(!article_num.val() || !verse_start.val() || !booktitle.val()){return;}

                start();
                var _verse_stop = verse_stop.val() || 0;

                $.ajax(bibleAjaxurl,{
                    data: 'action=query_bible&article='+article_num.val()+'&id='+booktitle.val()+'&verse_start='+verse_start.val()+'&verse_stop='+_verse_stop+'',
                    dataType: 'json',
                    success: query_bible_success,
                    error:AjaxGlobalError
                });
            }

            function query_bible_success(data){
                quest_end();

                loading.empty();

                if(!data.data){
                    html = '没有'+booktitle+article_num+":"+verse_start+'的经文。';
                    if(verse_stop.val()>verse_start.val()){
                        html = '没有'+booktitle+article_num+":"+verse_start+"-"+verse_stop+'的经文。';
                    }
                    return bibleBox.html(html);
                }else{
                    bibleBox.html(data.data);
                    pop.sync();
                }
            }

            query_article_num();
            booktitle.change(query_article_num);
            article_num.change(query_verse);
            verse_start.change(function (){
                verse_stop.val(verse_start.val());
                query_bible();
            });
            verse_stop.change(function (){
                if(parseInt(verse_stop.val()) < parseInt(verse_start.val())){return;}
                query_bible();
            });
        }


    </script>
    <script type="text/javascript">
        var ajaxurl = '../app/ajax.php5';
        function submitTopic(){
            var topic = encodeURI($.trim($('#J-topic-name').val()));
            if(!topic){
                return;
            }
            var topic_parent = $('#J-topic-name-parent').val() ? $('#J-topic-name-parent').val().toString() : '';

            $.ajax(ajaxurl,{
                dataType: 'json',
                data:'action=submit_topic&topic='+topic+'&topic_parent='+topic_parent+'',
                success:success,
                error:AjaxGlobalError
            });

            function success(data){
                if(data.resultStatus === 100){
                    queryTopic();
                }else{
                    return alert(data.memo);
                }
            }
        }
        $('#J-submit-topic').click(submitTopic);

        //查询所有的主题，并渲染到多项的select
        function queryTopic(){
            $.ajax(ajaxurl,{
                dataType: 'json',
                data:'action=query_topic',
                success:success,
                error:AjaxGlobalError
            });
            function success(data){
                if(data.data){
                    var topics = data.data;
                    var html = '';
                    renderTopicsLabels(topics);
                    $.each(topics,function (k,v){
                       html += '<option value="'+v.id+'" data-parent="'+v.parent+'">'+v.content+'</option>';
                    });
                    $('#J-topic-name-parent').html(html);
                }else{
                    $('#J-topic-name-parent').html('<option disabled="disabled">暂无主题</option>');
                }
            }
        }
        queryTopic();
    </script>
    <script type="text/javascript">
        //提交游戏题目配置
        function submitSubject(){
            var content = $.trim($('#J-content').val()),
                reference = $.trim($('#J-reference').val()),
                time = $.trim($('#J-time').val()),
                topics = $.trim(selected.toString()),
                api = '../app/ajax.php5';

            if(!content || !reference || !time || !topics){
                return;
            }
            function success(data){
                if(data.resultStatus === 100){
                    alert('题目添加成功');
                }else{
                    alert('题目添加失败');
                }
            }

            $.ajax(api,{
                dataType: 'json',
                data:'action=submit_subject&content='+encodeURI(content)+'&reference='+encodeURI(reference)+'&time='+time+'&topic='+encodeURI(topics),
                success:success,
                error:AjaxGlobalError
            });
        }
        $('#J-submit-subject').click(submitSubject);
    </script>
</div>

<?php include_once(TEMPLATES_PATH."/footer.php5"); ?>