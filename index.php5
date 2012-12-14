<?php include_once("config.php5"); ?>
<?php include_once(TEMPLATES_PATH."/header.php5"); ?>

<div class="container center-box" id="J-container">
    <!--<div class="loading" id="J-loading">
        <img src="static/images/bwfw.png" class="fling" id="J-bwfw-logo" />
        <p class="fling-reverse2">圣经问答游戏</p>
        <p class="title fling-reverse"><img src="static/images/logo-small.png" class="logo-small"/>杭州基督教磐石堂</p>
    </div>-->
    <div id="J-introducing" class="introducing">
        <div class="introducing-main">
            <nav class="block">
                <img src="static/images/logo.png" id="J-ps-logo"/>
                <ul>
                    <li>开始游戏</li>
                    <li class="current" data-role="rules">游戏规则</li>
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
</div>
<script type="text/javascript" src="static/js/game.js"></script>

<?php include_once(TEMPLATES_PATH."/footer.php5"); ?>