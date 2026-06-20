<?php
// ===================================================
// 1. DATA RECEIPT & SECURITY VALIDATION
// ===================================================
$class_id      = $_POST["class_id"] ?? '';
$name          = $_POST["name"] ?? '';
$attendance_no = $_POST["attendance_no"] ?? '';
$gender        = $_POST["gender"] ?? '';
$food          = $_POST["food"] ?? '';
$trend_food    = $_POST["trend_food"] ?? '';
$location      = $_POST["location"] ?? '';
$work_style    = $_POST["work_style"] ?? '';
$motto         = $_POST["motto"] ?? '';

// 改行や不要なカンマ、スペースの除去を徹底
$name          = str_replace([",", "\n", "\r"], "", trim($name));
$attendance_no = str_replace([",", "\n", "\r"], "", trim($attendance_no));
$motto         = str_replace([",", "\n", "\r"], "", trim($motto));

$safe_class_id = preg_replace('/[^a-zA-Z0-9_\-]/', '', $class_id);
if (empty($safe_class_id)) {
    $safe_class_id = "T_TUE";
}

if (empty($name)) { $name = "名無しの冒険者"; }
if (empty($attendance_no)) { $attendance_no = "99"; }

// カンマ区切りの新データ行を作成（末尾に改行をつけない状態で管理）
$new_data_array = [$safe_class_id, $name, $attendance_no, $gender, $food, $trend_food, $location, $work_style, $motto];
$new_line_string = implode(",", $new_data_array);

$target_file = "data_" . $safe_class_id . ".txt";

// ===================================================
// 2. FILE GENERATION & OVERWRITE LOGIC (FIXED)
// ===================================================
$updated_lines = [];
$is_overwritten = false;

if (file_exists($target_file)) {
    $lines = file($target_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines !== false) {
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $row = explode(",", $line);
            $row = array_map('trim', $row);
            
            // 要素数が3以上あり、出席番号(インデックス2)が一致する場合
            if (count($row) >= 3 && $row[2] === trim($attendance_no)) {
                $updated_lines[] = $new_line_string; // 新データで上書き
                $is_overwritten = true;
            } else {
                $updated_lines[] = $line; // そのまま保持
            }
        }
    }
}

if (!$is_overwritten) {
    $updated_lines[] = $new_line_string;
}

// 最後に一括して改行で結合し、ファイルに保存（LOCK_EXで排他ロック）
$final_data = implode("\n", $updated_lines) . "\n";
file_put_contents($target_file, $final_data, LOCK_EX);

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// ===================================================
// 3. MASTER DATA DEFINITIONS & STATUS CALCULATION
// ===================================================
$q4_data = ['hamburg' => 5, 'curry' => 4, 'ramen' => 3, 'sushi' => 2, 'cake' => 1];
$q4_score = $q4_data[$food] ?? 1;

$q5_data = ['tanghulu' => 5, 'shipuwan' => 4, 'dobaichoco' => 3, 'biryani' => 2, 'phakchi' => 1];
$q5_score = $q5_data[$trend_food] ?? 1;

$q6_data = ['luxury' => 5, 'fukuoka' => 5, 'osaka' => 4, 'nagoya' => 3, 'downtown' => 3, 'okinawa' => 2, 'hokkaido' => 1];
$q6_score = $q6_data[$location] ?? 1;

// キーを 'girigiri' に修正
$q7_data = ['blank' => 5, 'dekirudakeyaru' => 4, 'cheat' => 2, 'douga' => 1, 'girigiri' => 3];
$q7_score = $q7_data[$work_style] ?? 1;

$motto_len = mb_strlen($motto);
$att_num   = (int)$attendance_no;

$hp   = ($motto_len * 4500 * $q4_score) + 45000;
$atk  = ($q4_score * 1200) + ($q6_score * 150) + 2000;
$def  = ($q6_score * 1200) + ($q4_score * 150) + 2000;
$matk = ($q5_score * 1200) + ($q7_score * 150) + 2000;
$spd  = ($q7_score * 1400) + ($q5_score * 150) - ($motto_len * 40) + 2000;
$eva  = floor($spd * 0.95) + ($q6_score * 150) + 100;
$mnd  = ($q4_score * 800) + ($q5_score * 400) + ($att_num * 10) + 2000;

// ===================================================
// 4. JOB & RANK DETERMINATION
// ===================================================
$stats_map = [
    'グラディエーター（剛腕の戦士）' => $atk, 
    'アルケミスト（禁忌の魔導士）'   => $matk, 
    'ガーディアン（不落の鉄壁）'     => $def, 
    'アサシン（神速の暗殺者）'       => $spd
];
arsort($stats_map);
$job = key($stats_map);

$total_score = $hp + $atk + $def + $matk + $spd + $eva + $mnd;
if ($total_score >= 80000) { $job_rank = "SSR（神話級）"; }
elseif ($total_score >= 60000) { $job_rank = "SR（英雄級）"; }
else { $job_rank = "R（一般兵級）"; }

// ===================================================
// 5. SKILL SELECTION
// ===================================================
$skill1_power = rand(1500, 3500);
if ($job === 'グラディエーター（剛腕の戦士）') { $skill1 = "ギガント・スクラム（威力: " . $skill1_power . "）"; }
elseif ($job === 'アルケミスト（禁忌の魔導士）') { $skill1 = "メテオ・プログラミング（威力: " . $skill1_power . "）"; }
elseif ($job === 'ガーディアン（不落の鉄壁）') { $skill1 = "絶対要件定義ウォール（耐久: " . $skill1_power . "）"; }
else { $skill1 = "ソニック・バックスタブ（速度: " . $skill1_power . "）"; }

$skills_pool = [0 => "無限サーバー（容量上限解放）", 1 => "完全オートデバック", 2 => "全プログラミング言語理解", 3 => "C言語習得", 4 => "コピー&ペースト", 5 => "チートコード生成", 6 => "デュアルオクタコア"];
$skill2_index = $motto_len % 7;
$skill2 = $skills_pool[$skill2_index];

$g_hp   = round(($hp / 100000) * 100);
$g_atk  = round(($atk / 10000) * 100);
$g_def  = round(($def / 10000) * 100);
$g_matk = round(($matk / 10000) * 100);
$g_spd  = round(($spd / 10000) * 100);
$g_eva  = round(($eva / 10000) * 100);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>⚡ 転生完了 ⚡</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #050811;
            background-image: linear-gradient(rgba(5, 8, 17, 0.85), rgba(5, 8, 17, 0.85)), url('img/megami.png');
            background-size: cover; background-position: center; background-attachment: fixed;
            color: #ffffff; font-family: 'Helvetica Neue', Arial, sans-serif;
            padding: 50px 0; margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; box-sizing: border-box;
        }
        .status-window {
            width: 90%; max-width: 520px; background: rgba(0, 40, 120, 0.65); border: 3px solid #00bfff;
            border-radius: 16px; padding: 30px; box-shadow: 0 0 30px rgba(0, 191, 255, 0.5); backdrop-filter: blur(8px);
            box-sizing: border-box; animation: picon 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }
        @keyframes picon { 0% { transform: scale(0.5); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
        .window-title { color: #00bfff; font-size: 24px; font-weight: bold; letter-spacing: 2px; margin-bottom: 20px; text-shadow: 0 0 10px rgba(0, 191, 255, 0.8); border-bottom: 2px solid #00bfff; padding-bottom: 10px; }
        .player-info { font-size: 18px; margin-bottom: 20px; text-align: left; color: #e0f7ff; display: flex; justify-content: space-between; }
        .status-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; text-align: left; margin-bottom: 25px; }
        .status-item { background: rgba(255, 255, 255, 0.08); padding: 8px 12px; border-radius: 6px; border: 1px solid rgba(0, 191, 255, 0.2); display: flex; justify-content: space-between; }
        .status-label { color: #8cd9ff; font-size: 14px; }
        .status-value { font-weight: bold; font-family: 'Courier New', monospace; }
        .status-full { grid-column: span 2; background: rgba(0, 191, 255, 0.15); border: 1px solid #00bfff; }
        .chart-container { position: relative; width: 100%; height: 280px; margin: 15px auto 25px auto; background: rgba(0, 0, 0, 0.2); border-radius: 8px; padding: 20px; box-sizing: border-box; }
        .skill-section { text-align: left; background: rgba(0, 0, 0, 0.3); padding: 15px; border-radius: 8px; border-left: 4px solid #ff007f; }
        .skill-title { font-size: 12px; color: #ff007f; font-weight: bold; margin-bottom: 5px; }
        .skill-name { font-size: 15px; font-weight: bold; color: #ffffff; margin-bottom: 10px; }
        .btn-next {
            display: block; 
            width: fit-content;
            margin: 25px auto 0;
            margin-top: 25px; 
            color: #fffb00; border: 2px 
            solid #fffb00; padding: 12px 35px;
            border-radius: 6px; 
            cursor: pointer; 
            text-decoration: none; 
            font-size: 16px; 
            font-weight: bold; 
            background: rgba(255, 251, 0, 0.05); 
            transition: all 0.3s; 
            text-shadow: 0 0 8px rgba(255, 251, 0, 0.6); 
            box-shadow: 0 0 10px rgba(255, 251, 0, 0.2);
        }
        .btn-next:hover { background: rgba(255, 251, 0, 0.25); box-shadow: 0 0 25px rgba(255, 251, 0, 0.7); transform: translateY(-2px); }
    </style>
</head>
<body>
<div class="status-window">
    <div class="window-title">STATUS WINDOW</div>
    <div class="player-info">
        <div style="font-size:30px; ">👤 <b style="font-size:30px; "><?= h($name) ?></b> <span style="font-size:20px; color:#8cd9ff;">(No.<?= h($attendance_no) ?>)</span></div>
        <div style="color: #fffb00; font-size: 30px; font-weight: bold; font-family: 'Courier New', monospace;">LEVEL 99</div>
    </div>
    <div class="status-grid">
        <div class="status-item status-full"><span class="status-label">職業</span><span class="status-value" style="color:#fffb00;"><?= h($job) ?></span></div>
        <div class="status-item status-full"><span class="status-label">職業ランク</span><span class="status-value" style="color:#ff007f; text-shadow: 0 0 5px #ff007f;"><?= h($job_rank) ?></span></div>
        <div class="status-item"><span class="status-label">HP</span><span class="status-value"><?= $hp ?></span></div>
        <div class="status-item"><span class="status-label">攻撃力</span><span class="status-value"><?= $atk ?></span></div>
        <div class="status-item"><span class="status-label">防御力</span><span class="status-value"><?= $def ?></span></div>
        <div class="status-item"><span class="status-label">魔法</span><span class="status-value"><?= $matk ?></span></div>
        <div class="status-item"><span class="status-label">素早さ</span><span class="status-value"><?= $spd ?></span></div>
        <div class="status-item"><span class="status-label">回避</span><span class="status-value"><?= $eva ?></span></div>
        <div class="status-item status-full"><span class="status-label">回復力</span><span class="status-value"><?= $mnd ?></span></div>
    </div>
    <div class="chart-container"><canvas id="statusChart"></canvas></div>
    <div class="skill-section">
        <div class="skill-title">⚔️ スキル（クラス・固有）</div>
        <div class="skill-name" style="color:#00bfff;">① <?= h($skill1) ?></div>
        <div class="skill-title">💥 チートスキル</div>
        <div class="skill-name" style="color:#00bfff;">② <?= h($skill2) ?></div>
    </div>
    <a href="guild.php?class=<?= h($safe_class_id) ?>" class="btn-next">ギルドを訪ねる</a>
</div>
<script>
    const ctx = document.getElementById('statusChart').getContext('2d');
    const playerData = [<?= $g_hp ?>, <?= $g_atk ?>, <?= $g_def ?>, <?= $g_matk ?>, <?= $g_spd ?>, <?= $g_eva ?>];
    const citizenData = [15, 15, 15, 15, 15, 15];
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['HP', '攻撃力', '防御力', '魔法', '素早さ', '回避'],
            datasets: [
                { label: '転生パラメータ特性', data: playerData, backgroundColor: 'rgba(0, 191, 255, 0.4)', borderColor: '#00bfff', borderWidth: 2, pointBackgroundColor: '#ff007f', pointBorderColor: '#ffffff', pointRadius: 3, z: 10 },
                { label: '一般市民の平均', data: citizenData, backgroundColor: 'rgba(255, 255, 255, 0.02)', borderColor: 'rgba(255, 255, 255, 0.4)', borderWidth: 1, pointRadius: 0, borderDash: [3, 3], z: 1 }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                r: {
                    min: 0, max: 45,
                    ticks: { display: false, stepSize: 15 },
                    grid: { color: 'rgba(0, 191, 255, 0.12)' },
                    angleLines: { color: 'rgba(0, 191, 255, 0.25)' },
                    pointLabels: { color: '#8cd9ff', font: { size: 13, fontWeight: 'bold' } }
                }
            }
        }
    });
</script>
</body>
</html>