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
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 0 15px;
        }
        .main-title {
            font-size: 24px;
            color: #6c757d;
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
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
        .survey-form .section-title:first-child { margin-top: 0; }
        .form-group { margin-bottom: 25px; }
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
            transition: border-color 0.15s ease-in-out;
        }
        .form-control:focus { border-color: #86b7fe; outline: 0; }
        .radio-group {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        .radio-group input[type="radio"] { margin-right: 8px; cursor: pointer; }
        .radio-group label { cursor: pointer; font-size: 14px; }
        .radio-inline-container { display: flex; flex-wrap: wrap; gap: 15px; }
        .btn-container { text-align: center; margin-top: 40px; }
        .btn-submit {
            background-color: #0d6efd;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            padding: 12px 40px;
            margin-left: 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.15s ease-in-out;
        }
        .btn-submit:hover { background-color: #0b5ed7; }
    </style>
</head>
<body>
<div class="container">
    <h1 class="main-title">G's ACADEMY クラス統計調査</h1>
    <form action="post_confirm.php" method="POST" class="survey-form">
        <h4 class="section-title">【属性設定】基本情報をご入力ください</h4>
        <div class="form-group">
            <label for="class_id" class="question-label">Q0. クラス名を入力してください（英数半角、ハイフン、アンダーバーのみ可）</label>
            <input type="text" name="class_id" id="class_id" class="form-control" placeholder="例：T_TUE" required pattern="[a-zA-Z0-9_\-]+">
        </div>
        <div class="form-group">
            <label for="name" class="question-label">Q1. ニックネームを入力してください</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="例：G太郎" required>
        </div>
        <div class="form-group">
            <label for="attendance_no" class="question-label">Q2. 出席番号（2桁）を入力してください</label>
            <input type="number" name="attendance_no" id="attendance_no" class="form-control" min="1" max="99" placeholder="例：05" required>
        </div>
        <div class="form-group">
            <label class="question-label">Q3. 性別を選択してください</label>
            <div class="radio-inline-container">
                <div class="radio-group">
                    <input type="radio" name="gender" id="gender_m" value="male" checked>
                    <label for="gender_m">男</label>
                </div>
                <div class="radio-group">
                    <input type="radio" name="gender" id="gender_f" value="female">
                    <label for="gender_f">女</label>
                </div>
                <div class="radio-group">
                    <input type="radio" name="gender" id="gender_n" value="none">
                    <label for="gender_n">回答しない</label>
                </div>
                <div class="radio-group">
                    <input type="radio" name="gender" id="gender_g" value="goddess">
                    <label for="gender_g">その他（選択を委ねる）</label>
                </div>
            </div>
        </div>

        <h4 class="section-title">【価値観調査】あなたの嗜好について</h4>
        <div class="form-group">
            <label class="question-label">Q4. あなたが「これだけは一生飽きずに食べられる」というものは？</label>
            <div class="radio-group"><input type="radio" name="food" id="food_h" value="hamburg" checked><label for="food_h">ハンバーグ</label></div>
            <div class="radio-group"><input type="radio" name="food" id="food_c" value="curry"><label for="food_c">カレー</label></div>
            <div class="radio-group"><input type="radio" name="food" id="food_r" value="ramen"><label for="food_r">ラーメン</label></div>
            <div class="radio-group"><input type="radio" name="food" id="food_s" value="sushi"><label for="food_s">寿司</label></div>
            <div class="radio-group"><input type="radio" name="food" id="food_k" value="cake"><label for="food_k">ケーキ</label></div>
        </div>

        <div class="form-group">
            <label class="question-label">Q5. 今、名前を聞いてなんだか気になる食べ物はなんですか？</label>
            <div class="radio-group"><input type="radio" name="trend_food" id="tf_phakchi" value="phakchi" checked><label for="tf_phakchi">パクチー</label></div>
            <div class="radio-group"><input type="radio" name="trend_food" id="tf_biryani" value="biryani"><label for="tf_biryani">ビリヤニ</label></div>
            <div class="radio-group"><input type="radio" name="trend_food" id="tf_dobaichoco" value="dobaichoco"><label for="tf_dobaichoco">ドバイチョコ</label></div>
            <div class="radio-group"><input type="radio" name="trend_food" id="tf_shipuwan" value="shipuwan"><label for="tf_shipuwan">シプウォンパン</label></div>
            <div class="radio-group"><input type="radio" name="trend_food" id="tf_tanghulu" value="tanghulu"><label for="tf_tanghulu">タンフル</label></div>
        </div>

        <div class="form-group">
            <label class="question-label">Q6. 住むならどこ？</label>
            <div class="radio-group"><input type="radio" name="location" id="loc_hokkaido" value="hokkaido" checked><label for="loc_hokkaido">北海道</label></div>
            <div class="radio-group"><input type="radio" name="location" id="loc_luxury" value="luxury"><label for="loc_luxury">東京の高級住宅街</label></div>
            <div class="radio-group"><input type="radio" name="location" id="loc_downtown" value="downtown"><label for="loc_downtown">東京の下町</label></div>
            <div class="radio-group"><input type="radio" name="location" id="loc_osaka" value="osaka"><label for="loc_osaka">大阪</label></div>
            <div class="radio-group"><input type="radio" name="location" id="loc_nagoya" value="nagoya"><label for="loc_nagoya">名古屋</label></div>
            <div class="radio-group"><input type="radio" name="location" id="loc_fukuoka" value="fukuoka"><label for="loc_fukuoka">福岡</label></div>
            <div class="radio-group"><input type="radio" name="location" id="loc_okinawa" value="okinawa"><label for="loc_okinawa">沖縄</label></div>
        </div>

        <h4 class="section-title">【行動特性】あなたの行動パターンについて</h4>
        <div class="form-group">
            <label class="question-label">Q7. 仕事の締め切りが「明日の夜」だと気づいた時、あなたの行動に一番近いものは？</label>
            <div class="radio-group"><input type="radio" name="work_style" id="work_fast" value="douga" checked><label for="work_fast">「明日の自分に期待！」と、とりあえず見たかった動画を見る</label></div>
            <div class="radio-group"><input type="radio" name="work_style" id="work_ギリ" value="girigiri"><label for="work_ギリ">エナジードリンクを買い込んで徹夜の準備をする</label></div>
            <div class="radio-group"><input type="radio" name="work_style" id="work_sleep" value="dekirudakeyaru"><label for="work_sleep">今日のうちにやれるところまでやる</label></div>
            <div class="radio-group"><input type="radio" name="work_style" id="work_cheat" value="cheat"><label for="work_cheat">同僚に助けを求める（振る）もしくは、AIに頼る</label></div>
        </div>

        <h4 class="section-title">【精神性調査】あなたの内面について</h4>
        <div class="form-group">
            <label for="motto" class="question-label">Q8. あなたの「座右の銘」または「好きな言葉」を教えてください（フリー回答）</label>
            <input type="text" name="motto" id="motto" class="form-control" placeholder="例：こわそう、つくろう、ジブンを、セカイを。" required>
        </div>
        <div class="btn-container">
            <button type="submit" class="btn-submit">アンケートを終えて回答を確認する</button>
        </div>
    </form>
</div>
</body>
</html>