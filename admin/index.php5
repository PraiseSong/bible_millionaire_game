<?php include_once("config.php5"); ?>
<?php include_once(APP_PATH."/db.php5"); ?>
<?php include_once(TEMPLATES_PATH."/header.php5"); ?>
<?php include_once("load_app.php5"); ?>

<div class="container">
    <h2>游戏题目配置</h2>
    <form action="#" id="J-subjects">
        <table>
            <tbody>
              <tr>
                  <td>题目内容：</td>
                  <td><textarea type="text" class="input_text" id="J-content"></textarea></td>
              </tr>
              <tr>
                  <td>参考经文：</td>
                  <td><input type="text" class="input_text" id="J-reference" /><a href="javascript:void(0)" class="find-bible" id="J-find-bible">查找经文</a></td>
              </tr>
              <tr>
                  <td>完成游戏所需时间：</td>
                  <td><input type="text" class="input_text" id="J-time" /></td>
              </tr>
              <tr>
                  <td>所属的主题：</td>
                  <td><input type="text" class="input_text" id="J-topic" /><a href="javascript:void(0)" class="find-bible">查找主题</a></td>
              </tr>
              <tr>
                  <td></td>
                  <td><input type="submit" value="确定" class="btn-ok" /></td>
              </tr>
            </tbody>
        </table>
    </form>

    <table class="hide" id="J-form-table">
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
            <td><input type="button" class="btn-blue" value="使用" id="J-query" /></td>
            <td><a href="javascript:void(0)" class="J-close">关闭</a></td>
        </tr>
        </tbody>
    </table>
    <p id="J-bible-box"></p>

    <script type="text/javascript">
        function bindQueryBible(){
            var trigger = $('#J-find-bible');
            var pop = new Pop({
                element: '#J-form-table',
                close:'.J-close',
                beforeShow: function (){

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

            function error(){
                loading.html('服务器发生异常，请重试。');
            }

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

                if(data <= 0){
                    return loading.html('数据有误，请重试。');
                }

                var html = '';
                for(var i=1;i<=data;i++){
                    html += '<option value="'+i+'">'+i+'</option>';
                }

                article_num.html(html);

                query_verse();
            }

            function query_article_num(){
                currentTitle = booktitle.val();

                if(!currentTitle){return;}

                start();
                $.ajax(bibleAjaxurl,{
                    data: 'action=query_article_num&id='+currentTitle+'',
                    type: 'get',
                    success: query_article_num_success,
                    error:error
                });
            }

            function query_verse(){
                if(!article_num.val() || !booktitle.val()){return;}

                start();
                $.ajax(bibleAjaxurl,{
                    data: 'action=query_verse_num&article='+article_num.val()+'&id='+booktitle.val()+'',
                    type: 'get',
                    success: query_verse_num_success,
                    error:error
                });
            }

            function query_verse_num_success(data){
                quest_end();

                loading.empty();

                if(data <= 0){
                    return loading.html('数据有误，请重试。');
                }

                var html = '';
                for(var i=1;i<=data;i++){
                    html += '<option value="'+i+'">'+i+'</option>';
                }

                verse_start.html(html);
                verse_stop.html(html);

                query_bible();
            }

            function query_bible(){
                if(!article_num.val() || !verse_start.val() || !booktitle.val()){return;}

                start();
                var _verse_stop = verse_stop.val() || 0;

                $.ajax(bibleAjaxurl,{
                    data: 'action=query_bible&article='+article_num.val()+'&id='+booktitle.val()+'&verse_start='+verse_start.val()+'&verse_stop='+_verse_stop+'',
                    type: 'get',
                    success: query_bible_success,
                    error:error
                });
            }

            function query_bible_success(data){
                quest_end();

                loading.empty();

                if(!data){
                    html = '没有'+booktitle+article_num+":"+verse_start+'的经文。';
                    if(verse_stop.val()>verse_start.val()){
                        html = '没有'+booktitle+article_num+":"+verse_start+"-"+verse_stop+'的经文。';
                    }
                    return bibleBox.html(html);
                }else{
                    bibleBox.html(data);
                }
                $('#J-quickIndex').select().focus();
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

            $('#J-quickIndex').keyup(function (e){
                var index = $.trim($(this).val());
                var booktitles = $('#booktitle option');
                if(!index || !booktitles){return;}
                booktitles.each(function (k,v){
                    var booktitle = $(v);
                    if(booktitle.attr('data-alias').toLowerCase() === index.toLowerCase()){
                        $('#J-quickIndex-tip').html(booktitle.html());
                        $('#booktitle').get(0).selectedIndex = k;
                        $('#J-query').focus();
                    }
                });
            });

            $('#J-query').click(function (){
                if(!$.trim($('#J-quickIndex').val())){return;}
                query_article_num();
            });
        }
    </script>
</div>

<?php include_once(TEMPLATES_PATH."/footer.php5"); ?>