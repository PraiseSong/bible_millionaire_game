<?php include_once("config.php5"); ?>
<?php include_once(TEMPLATES_PATH."/header.php5"); ?>
<div id="J-getScore" class="hide">
    <p class="getScrore">1000</p>
    <div class="webkit-box">
        <a href="javascript:void(0)" id="J-exit" style="display:none;">离开</a>
        <span class="space flex first-space" style="display:none;"></span>
        <a href="javascript:void(0)" id="J-recycle"  style="display:none;">再玩</a>
        <span class="space flex second-space" style="display:none;"></span>
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
                    <li data-role="push">诚意推介</li>
                    <li data-role="people">制作人员</li>
                </ul>
            </nav>
            <div class="flex">
                <div data-role="rules" class="panel">
                    <h1>游戏规则</h1>
                    <div class="content">这里是游戏规则的内容</div>
                </div>
                <div data-role="push" class="hide panel">
                    <h1>诚意推介</h1>
                    <div class="content">这里是诚意推介的内容</div>
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
                <a href="javascript:void(0)" class="back"><返回</a>
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
                    <li class="1million phases" data-value="100000000">15<>1 MILLION</li>
                    <li data-value="500000">14<>500000</li>
                    <li data-value="25000">13<>250000</li>
                    <li data-value="15000">12<>150000</li>
                    <li data-value="80000">11<>80000</li>
                    <li class="60000score phases" data-value="60000">10<>60000</li>
                    <li data-value="40000">9<>40000</li>
                    <li data-value="30000">8<>30000</li>
                    <li data-value="20000">7<>20000</li>
                    <li data-value="10000">6<>10000</li>
                    <li class="8000score phases" data-value="8000">5<>8000</li>
                    <li data-value="4000">4<>4000</li>
                    <li data-value="3000">3<>3000</li>
                    <li data-value="2000">2<>2000</li>
                    <li class="next-score" data-value="1000">1<>1000</li>
                </ul>
            </div>
            <div class="flex bwfw-logo"><img src="static/images/bwfw.png" /></div>
            <div class="currentTopicDes flex" id="J-currentTopicDes-box">
                当前的主题内容
                <p id="J-reference">经文参考</p>
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