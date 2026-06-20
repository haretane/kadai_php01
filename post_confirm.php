<?php
// post.php から送信されてきたデータを各変数にキャッチ
$class_id      = $_POST["class_id"] ?? '';
$name          = $_POST["name"] ?? '';
$attendance_no = $_POST["attendance_no"] ?? '';
$gender        = $_POST["gender"] ?? '';
$food          = $_POST["food"] ?? '';
$trend_food    = $_POST["trend_food"] ?? '';
$location      = $_POST["location"] ?? '';
$work_style    = $_POST["work_style"] ?? '';
$motto         = $_POST["motto"] ?? '';

// 安全対策（XSS対策）
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>G's ACADEMY クラス統計調査アンケート</title>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: "Helvetica Neue", Arial, "Hiragino Kaku Gothic ProN", "Hiragino Sans", Meiryo, sans-serif;
            color: #333333;
            padding: 50px 0;
            margin: 0;
            overflow-x: hidden;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 0 15px;
        }
        .survey-form {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .section-title {
            font-size: 18px;
            color: #0d6efd;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 8px;
            margin-top: 30px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .survey-form .section-title:first-child {
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .question-label {
            display: block;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 15px;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .btn-container {
            text-align: center;
            margin-top: 40px;
        }
        .btn-submit {
            background-color: #6c757d;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            padding: 12px 40px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-isekai {
            position: relative;
            background: linear-gradient(45deg, #ff007f, #7f00ff, #00bfff, #ff007f);
            background-size: 300% 300%;
            color: #ffffff;
            font-size: 18px;
            font-weight: bold;
            padding: 12px 45px;
            margin-left: 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            box-shadow: 0 0 15px rgba(127, 0, 255, 0.5);
            animation: magic-glow 3s ease infinite;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-isekai:hover {
            transform: scale(1.05);
            box-shadow: 0 0 25px rgba(0, 191, 255, 0.8), 0 0 40px rgba(255, 0, 127, 0.5);
        }
        @keyframes magic-glow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .bg-light-box {
            background-color: #f8fafc;
            border: 1px solid #ced4da;
        }

        #isekai-theater {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 9999;
            background-image: url('img/megami.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            justify-content: center;
            align-items: flex-end;
            opacity: 0;
            transition: opacity 2s ease, background 0.3s ease; /* 女神の浮き上がりを少し重厚に */
        }

        #white-curtain {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: #ffffff;
            z-index: 9998; /* シアターのすぐ真裏に配置 */
            display: none;
        }

        .msg-window {
            display: none;
            background: rgba(0, 40, 120, 0.85);
            color: #ffffff;
            border: 3px solid #00bfff;
            border-radius: 12px;
            padding: 35px 40px;
            font-size: 22px;
            line-height: 2.0;
            width: 90%;
            max-width: 600px;
            text-align: left;
            cursor: pointer;
            box-shadow: 0 0 25px rgba(0, 191, 255, 0.5);
            backdrop-filter: blur(4px);
            box-sizing: border-box;
            margin-bottom: 130px;
        }

        .text-line {
            min-height: 2.0em;
            margin: 0;
            white-space: pre-wrap;
        }

        #isekai-logo {
            position: absolute;
            top: 50%;
            transform: translateY(-50%) scale(0);
            max-width: 80%;
            height: auto;
            transition: transform 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275), top 0.8s ease, opacity 0.8s ease;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="survey-form">
        <h4 class="section-title" style="color: #198754; border-color: #198754;">📋 入力内容の確認</h4>
        <p style="color: #6c757d; font-size: 14px; margin-bottom: 20px;">以下の内容で回答を送信します。よろしいですか?</p>

        <div class="form-group">
            <label class="question-label">Q0. クラス名</label>
            <div class="form-control bg-light-box"><?= h($class_id) ?></div>
        </div>

        <div class="form-group">
            <label class="question-label">Q1. ニックネーム</label>
            <div class="form-control bg-light-box"><?= h($name) ?></div>
        </div>

        <div class="form-group">
            <label class="question-label">Q2. 出席番号</label>
            <div class="form-control bg-light-box"><?= h($attendance_no) ?> 番</div>
        </div>

        <div class="form-group">
            <label class="question-label">Q3. 性別</label>
            <div class="form-control bg-light-box">
                <?php
                if ($gender === 'male') echo '男';
                elseif ($gender === 'female') echo '女';
                elseif ($gender === 'none') echo '回答しない';
                elseif ($gender === 'goddess') echo 'その他（選択を委ねる）';
                ?>
            </div>
        </div>

        <div class="form-group">
            <label class="question-label">Q4. 一生飽きずに食べられるもの</label>
            <div class="form-control bg-light-box">
                <?php
                if ($food === 'hamburg') echo 'ハンバーグ';
                elseif ($food === 'curry') echo 'カレー';
                elseif ($food === 'ramen') echo 'ラーメン';
                elseif ($food === 'sushi') echo '寿司';
                elseif ($food === 'cake') echo 'ケーキ';
                ?>
            </div>
        </div>

        <div class="form-group">
            <label class="question-label">Q5. 気になるトレンド食べ物</label>
            <div class="form-control bg-light-box">
                <?php
                if ($trend_food === 'phakchi') echo 'パクチー';
                elseif ($trend_food === 'biryani') echo 'ビリヤニ';
                elseif ($trend_food === 'dobaichoco') echo 'ドバイチョコ';
                elseif ($trend_food === 'shipuwan') echo 'シプウォンパン';
                elseif ($trend_food === 'tanghulu') echo 'タンフル';
                ?>
            </div>
        </div>

        <div class="form-group">
            <label class="question-label">Q6. 住むならどこ？</label>
            <div class="form-control bg-light-box">
                <?php
                if ($location === 'hokkaido') echo '北海道';
                elseif ($location === 'luxury') echo '東京の高級住宅街';
                elseif ($location === 'downtown') echo '東京の下町';
                elseif ($location === 'osaka') echo '大阪';
                elseif ($location === 'nagoya') echo '名古屋';
                elseif ($location === 'fukuoka') echo '福岡';
                elseif ($location === 'okinawa') echo '沖縄';
                ?>
            </div>
        </div>

        <div class="form-group">
            <label class="question-label">Q7. 仕事の締め切りが「明日の夜」だと気づいた時</label>
            <div class="form-control bg-light-box">
                <?php
                if ($work_style === 'douga') echo '「明日の自分に期待！」と、とりあえず見たかった動画を見る';
                elseif ($work_style === 'ギリギリ') echo 'エナジードリンクを買い込んで徹夜の準備をする';
                elseif ($work_style === 'dekirudakeyaru') echo '今日のうちにやれるところまでやる';
                elseif ($work_style === 'cheat') echo '同僚に助けを求める（振る）もしくは、AIに頼る';
                ?>
            </div>
        </div>

        <div class="form-group">
            <label class="question-label">Q8. 座右の銘・好きな言葉</label>
            <div class="form-control bg-light-box"><?= h($motto) ?></div>
        </div>

        <div class="btn-container" style="display: flex; gap: 20px; justify-content: center;">
            <button type="button" onclick="history.back()" class="btn-submit">修正する</button>
            <button type="button" onclick="startIsekai()" class="btn-isekai">この回答で行く！</button>
        </div>
    </div>
</div>

<div id="white-curtain"></div>
<div id="isekai-theater" style="display: none;">
    <div id="goddess-msg" class="msg-window" onclick="nextStage()">
        <p id="line1" class="text-line"></p>
        <p id="line2" class="text-line"></p>
        <span id="click-prompt" style="font-size: 13px; color: #8cd9ff; display:block; margin-top:20px; opacity:0; text-align:right;">▼ クリックして覚醒する</span>
    </div>
    <img id="isekai-logo" src="img/rogo.png" alt="ロゴ">
</div>

<form id="final-form" action="write.php" method="POST">
    <input type="hidden" name="class_id" value="<?= h($class_id) ?>">
    <input type="hidden" name="name" value="<?= h($name) ?>">
    <input type="hidden" name="attendance_no" value="<?= h($attendance_no) ?>">
    <input type="hidden" name="gender" value="<?= h($gender) ?>">
    <input type="hidden" name="food" value="<?= h($food) ?>">
    <input type="hidden" name="trend_food" value="<?= h($trend_food) ?>">
    <input type="hidden" name="location" value="<?= h($location) ?>">
    <input type="hidden" name="work_style" value="<?= h($work_style) ?>">
    <input type="hidden" name="motto" value="<?= h($motto) ?>">
</form>

<script>

//＝＝＝＝＝＝＝＝＝
// 女神登場
//＝＝＝＝＝＝＝＝＝

    const text1 = "まっていましたよ、<?= h($name) ?>。";
    const text2 = "さあ、これがあなたの能力（ステータス）です。";

    function startIsekai() {
        const curtain = document.getElementById('white-curtain');
        const theater = document.getElementById('isekai-theater');
        
        curtain.style.display = 'block';
        
        setTimeout(() => {
            theater.style.display = 'flex';
            
            setTimeout(() => { 
                theater.style.opacity = '1'; 
                
                setTimeout(() => {
                    const msgWin = document.getElementById('goddess-msg');
                    msgWin.style.opacity = '0';
                    msgWin.style.display = 'block';
                    msgWin.style.transition = 'opacity 0.8s ease';
                    
                    setTimeout(() => {
                        msgWin.style.opacity = '1';
                        setTimeout(typeLine1, 200);
                    }, 10);
                    
                }, 1550);
                
            }, 50);
        }, 300); 
    }

    function typeLine1() {
        const el = document.getElementById('line1');
        let index = 0;
        const timer = setInterval(() => {
            el.textContent += text1[index];
            index++;
            if (index >= text1.length) {
                clearInterval(timer);
                setTimeout(typeLine2, 600);
            }
        }, 30);
    }

    function typeLine2() {
        const el = document.getElementById('line2');
        let index = 0;
        const timer = setInterval(() => {
            el.textContent += text2[index];
            index++;
            if (index >= text2.length) {
                clearInterval(timer);
                document.getElementById('click-prompt').style.opacity = '1';
                document.getElementById('click-prompt').style.transition = 'opacity 0.5s';
            }
        }, 30);
    }

    function nextStage() {
        const theater = document.getElementById('isekai-theater');
        const msg = document.getElementById('goddess-msg');
        const logo = document.getElementById('isekai-logo');
        
        msg.style.display = 'none';
        theater.style.backgroundImage = 'none';
        theater.style.backgroundColor = '#ffffff';
        logo.style.display = 'block';
        
        setTimeout(() => { 
            logo.style.transform = 'translateY(-50%) scale(1)'; 
        }, 50);

        setTimeout(() => {
            logo.style.top = '-150vh'; 
            logo.style.opacity = '0';
            setTimeout(() => { document.getElementById('final-form').submit(); }, 600);
        }, 2800);
    }
</script>

</body>
</html>