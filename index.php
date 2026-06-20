<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>クラス統計調査システム</title>
    <style>
        body {
            background-color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #334155;
            line-height: 1.7;
            margin: 0;
            padding: 80px 0;
        }
        .container {
            width: 100%;
            max-width: 580px;
            margin: 0 auto;
            padding: 0 20px;
            box-sizing: border-box;
        }
        .portal-card {
            background-color: #ffffff;
            border-top: 5px solid #2563eb;
            border-radius: 8px;
            padding: 40px 35px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
        }
        .title-area {
            text-align: center;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 25px;
            margin-bottom: 35px;
        }
        .sub-title {
            font-size: 13px;
            color: #64748b;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .main-title {
            font-size: 26px;
            color: #1e293b;
            margin: 0;
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        .description-text {
            font-size: 15px;
            color: #475569;
            margin: 0 0 35px 0;
            text-align: center;
        }
        .notice-box {
            background-color: #f0f5ff;
            border-left: 4px solid #2563eb;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 0 6px 6px 0;
        }
        .notice-text {
            font-size: 13.5px;
            color: #1e293b;
            margin: 0;
            white-space: pre-line;
        }
        .alert-text {
            color: #dc2626;
            font-size: 15.5px;
            font-weight: bold;
            display: inline-block;
            margin: 2px 0;
        }
        .code-highlight {
            background-color: #e2e8f0;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
            font-weight: bold;
            color: #0f172a;
        }
        .button-group {
            margin-bottom: 45px;
            text-align: center;
        }
        .btn-official {
            display: block;
            width: 100%;
            max-width: 340px;
            margin: 0 auto;
            background-color: #2563eb; 
            color: #ffffff;
            text-decoration: none;
            text-align: center;
            padding: 14px 0;
            font-size: 15px;
            font-weight: bold;
            border-radius: 6px;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
            transition: all 0.2s ease;
        }
        .btn-official:hover {
            background-color: #1d4ed8;
            box-shadow: 0 4px 12px -1px rgba(37, 99, 235, 0.3);
            transform: translateY(-1px);
        }
        .btn-secondary {
            display: block;
            width: 100%;
            max-width: 340px;
            margin: 0 auto;
            background-color: #ffffff;
            color: #2563eb;
            border: 1px solid #2563eb;
            text-decoration: none;
            text-align: center;
            padding: 13px 0;
            font-size: 14.5px;
            font-weight: bold;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .btn-secondary:hover {
            background-color: #f0f5ff;
            color: #1d4ed8;
            border-color: #1d4ed8;
        }
        .section-divider {
            border: 0;
            border-top: 1px solid #e2e8f0;
            margin: 45px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="portal-card">
        <div class="title-area">
            <div class="sub-title">ザ・ステータス</div>
            <h1 class="main-title">クラス統計調査</h1>
        </div>
        <p class="description-text">
            この調査は、クラスの統計を測定するためのものです。<br>
            下記のアンケートへの回答にご協力をお願いいたします。
        </p>
        <div class="notice-box">
            <p class="notice-text"><b>【火曜クラスのみなさまへ】</b>
<span class="alert-text">回答は、課題発表までお控えください。</span>発表時に入力をお願いいたします。
その際、クラスの入力欄は <span class="code-highlight">T_TUE</span> でお願いいたします。</p>
        </div>
        <div class="button-group">
            <a href="post.php" class="btn-official">アンケートに回答する</a>
        </div>
        <hr class="section-divider">
        <div class="notice-box">
            <p class="notice-text"><b>【火曜クラスのみなさまへ】</b>
こちらの集計結果ページは、全員の回答が完了するまでアクセスをお控えください。正確な統計データ算出のため、ご協力をお願いいたします。</p>
        </div>
        <div class="button-group" style="margin-bottom: 5px;">
            <a href="guild_list.php" class="btn-secondary">集計を確認する</a>
        </div>
    </div>

    <div style="margin-top: 80px; padding-top: 20px; border-top: 1px dashed #cbd5e1; text-align: center;">
        <p style="font-size: 11px; color: #94a3b8; margin-bottom: 8px;">【管理者・開発者専用領域】</p>
        <a href="dashboard.php" style="font-size: 12px; color: #64748b; text-decoration: none; border: 1px solid #cbd5e1; padding: 5px 15px; border-radius: 4px; background: #f8fafc; transition: all 0.2s;" onmouseover="this.style.color='#ef4444'; this.style.borderColor='#ef4444';" onmouseout="this.style.color='#64748b'; this.style.borderColor='#cbd5e1';">
            ⚙️ 運営専用ダッシュボードを開く（本番非表示）
        </a>
    </div>


</div>
</body>
</html>