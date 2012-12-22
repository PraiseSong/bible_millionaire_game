<?php include_once("config.php5"); ?>
<?php include_once(TEMPLATES_PATH."/header.php5"); ?>
<div id="J-getScore" class="hide">
    <p class="getScrore">1000</p>
    <div class="webkit-box">
        <a href="javascript:void(0)" id="J-exit" style="display:none;">离开</a>
        <a href="javascript:void(0)" id="J-next"  style="display:none;">下一题</a>
    </div>
</div>

<div class="container center-box" id="J-container">
    <div class="loading" id="J-loading">
        <img src="static/images/bwfw.png" class="fling" id="J-bwfw-logo" />
        <p class="fling-reverse2">圣经问答游戏</p>
        <p class="title fling-reverse"><img src="static/images/logo-small.png" class="logo-small"/>杭州基督教磐石堂</p>
    </div>
    <div id="J-introducing" class="introducing hide">
        <div class="introducing-main">
            <nav class="block">
                <img src="static/images/logo.png" id="J-ps-logo"/>
                <ul>
                    <li class="current" id="J-starting">开始游戏</li>
                    <li data-role="rules">游戏规则</li>
                    <li data-role="people">制作人员</li>
                </ul>
            </nav>
            <div class="flex">
                <div data-role="rules" class="panel">
                    <h1>游戏规则</h1>
                    <div class="content">
                        <ul>
                            <li>
                                1、在规定时间内，独立回答电脑中的题目
                            </li>
                            <li>
                                2、每获一次晋级，即可获得2个快乐果
                            </li>
                            <li>
                                3、持绿卡人员，可得一次现场30秒内求助机会（除工作人员外），仅限一题
                                三种求助方式：过滤，跳过，提示
                            </li>
                            <li>
                                4、每人仅限玩两次，不得重复选题，间隔需30分钟以上
                            </li>
                            <li>
                                5、通过人员不得泄露题目
                            </li>
                        </ul>
                    </div>
                </div>
                <div data-role="people" class="hide panel">
                    <h1>制作人员</h1>
                    <div class="content">这里是制作人员的内容</div>
                </div>
            </div>
        </div>
    </div>
    <div id="J-topics-box" class="introducing hide">
        <div class="introducing-main">
            <nav class="block">
                <img src="static/images/logo.png" />
                <a href="javascript:void(0)" class="back"></a>
            </nav>
            <div class="flex">
                <div data-role="rules" class="panel">
                    <h1>选择主题</h1>
                    <div id="J-topics" class="content"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="J-subject-box" class="subject-box hide">
        <div class="webkit-box">
            <div class="score flex">
                <ul id="J-score-box">

                </ul>
            </div>
            <div class="flex bwfw-logo"><img src="static/images/bwfw.png" class="rotating3" id="J-subject-logo" /></div>
            <div class="currentTopicDes flex" id="J-currentTopicDes-box">
                当前的主题内容
                <div id="J-reference">经文参考</div>
            </div>
        </div>
        <div class="questionAndsolutionsBox" id="J-questionAndsolutionsBox">
            <p class="solution-title">
                title
            </p>
            <p class="webkit-box">
                <span class="solution flex current">A</span>
                <span class="space flex"></span>
                <span class="solution flex">B</span>
            </p>
            <p class="webkit-box">
                <span class="solution flex">C</span>
                <span class="space flex"></span>
                <span class="solution flex">D</span>
            </p>
        </div>
        <div class="webkit-box controler">
            <p class="flex" id="J-maxTime">时限</p>
            <p class="flex">
                <a href="javascript:void(0)" id="J-ok" style="display:none;">确定</a>
            </p>
            <p class="flex">
                <a href="javascript:void(0)" id="J-filter">过滤</a>
                <a href="javascript:void(0)" id="J-skip">跳过</a>
                <a href="javascript:void(0)" id="J-tip">提示</a>
            </p>
        </div>
    </div>
</div>
<script type="text/javascript" src="static/js/game.js"></script>

<?php include_once(TEMPLATES_PATH."/footer.php5"); ?>