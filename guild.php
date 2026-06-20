<?php
// ===================================================
// 1. DYNAMIC FILE SELECTION VIA URL PARAMETER
// ===================================================
$class_param = $_GET['class'] ?? '';
$safe_class_id = preg_replace('/[^a-zA-Z0-9_\-]/', '', $class_param);

if (empty($safe_class_id)) {
    $target_file = "data.txt";
    $display_class_name = "総合";
} else {
    $target_file = "data_" . $safe_class_id . ".txt";
    $display_class_name = $safe_class_id;
}

// ===================================================
// 2. DATA LOADING & PARSING
// ===================================================
$members = [];
$guild_atk = $guild_def = $guild_matk = $guild_spd = $guild_eva = $guild_mnd = $guild_hp = 0;

if (file_exists($target_file)) {
    $lines = file($target_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    if ($lines !== false) {
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $data = explode(",", $line);
            $data = array_map('trim', $data);
            
            if (count($data) >= 9) {
                // インデックスの完全な割り当て
                $name          = $data[1];
                $attendance_no = $data[2];
                $gender        = $data[3];
                $food          = $data[4];
                $trend_food    = $data[5];
                $location      = $data[6];
                $work_style    = $data[7];
                $motto         = $data[8];
            } else {
                continue; // 壊れたデータ行はスキップ
            }
            
            // パラメータ算出ロジック（write.phpと完全同一に調整）
            $q4_data = ['hamburg' => 5, 'curry' => 4, 'ramen' => 3, 'sushi' => 2, 'cake' => 1];
            $q4_score = $q4_data[$food] ?? 1;

            $q5_data = ['tanghulu' => 5, 'shipuwan' => 4, 'dobaichoco' => 3, 'biryani' => 2, 'phakchi' => 1];
            $q5_score = $q5_data[$trend_food] ?? 1;

            $q6_data = ['luxury' => 5, 'fukuoka' => 5, 'osaka' => 4, 'nagoya' => 3, 'downtown' => 3, 'okinawa' => 2, 'hokkaido' => 1];
            $q6_score = $q6_data[$location] ?? 1;

            $q7_data = ['blank' => 5, 'dekirudakeyaru' => 4, 'cheat' => 2, 'douga' => 1, 'girigiri' => 3];
            $q7_score = $q7_data[$work_style] ?? 1;

            $motto_len = mb_strlen($motto);
            $att_num   = (int)$attendance_no;

            $p_hp   = ($motto_len * 4500 * $q4_score) + 45000;
            $p_atk  = ($q4_score * 1200) + ($q6_score * 150) + 2000;
            $p_def  = ($q6_score * 1200) + ($q4_score * 150) + 2000;
            $p_matk = ($q5_score * 1200) + ($q7_score * 150) + 2000;
            $p_spd  = ($q7_score * 1400) + ($q5_score * 150) - ($motto_len * 40) + 2000;
            $p_eva  = floor($p_spd * 0.95) + ($q6_score * 150) + 100;
            $p_mnd  = ($q4_score * 800) + ($q5_score * 400) + ($att_num * 10) + 2000;

            // 職業の判定
            $stats_map = [
                'グラディエーター' => $p_atk, 
                'アルケミスト'   => $p_matk, 
                'ガーディアン'     => $p_def, 
                'アサシン'       => $p_spd
            ];
            arsort($stats_map);
            $display_job = key($stats_map);

            $total_score = $p_hp + $p_atk + $p_def + $p_matk + $p_spd + $p_eva + $p_mnd;
            if ($total_score >= 80000) { $job_rank = "SSR"; }
            elseif ($total_score >= 60000) { $job_rank = "SR"; }
            else { $job_rank = "R"; }

            $non_hp_total = $p_atk + $p_def + $p_matk + $p_spd + $p_eva + $p_mnd;

            $members[] = [
                'name' => $name,
                'attendance_no' => $attendance_no,
                'job' => $display_job,
                'rank' => $job_rank,
                'non_hp_total' => $non_hp_total
            ];

            $guild_hp   += $p_hp;
            $guild_atk  += $p_atk;
            $guild_def  += $p_def;
            $guild_matk += $p_matk;
            $guild_spd  += $p_spd;
            $guild_eva  += $p_eva;
            $guild_mnd  += $p_mnd;
        }
    }
}

// ===================================================
// 3. SORT MEMBERS (戦闘力順)
// ===================================================
usort($members, function($a, $b) {
    return $b['non_hp_total'] <=> $a['non_hp_total'];
});

// ===================================================
// 4. GUILD NAME & LOGO GENERATION
// ===================================================
$member_count = count($members);
$prefix_pool = ["爆炎の", "蒼空の", "悠久の", "漆黒の", "黄金の", "深淵の"];
$logo_pool   = ["剣" => "sword.jpg", "杖" => "wand.jpg", "盾" => "shield.jpg", "風" => "assassin.jpg"];

if ($member_count > 0) {
    $seed = crc32($display_class_name);
    $prefix = $prefix_pool[abs($seed) % count($prefix_pool)];
    
    $guild_stats = [
        '剣' => $guild_atk,
        '杖' => $guild_matk,
        '盾' => $guild_def,
        '風' => $guild_spd
    ];
    arsort($guild_stats);
    $suffix = key($guild_stats);
} else {
    $prefix = "始まりの";
    $suffix = "盾";
}

$guild_name = $prefix . $suffix;
$logo_image = $logo_pool[$suffix] ?? "shield.jpg";

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>🏰 ギルドステータス 🏰</title>
    <style>
        body {
            background-color: #030712; color: #ffffff; font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 0; padding: 40px 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; box-sizing: border-box;
        }
        .guild-card {
            width: 90%; max-width: 720px; background: rgba(10, 20, 50, 0.75); border: 2px solid #fffb00;
            border-radius: 12px; padding: 30px; box-shadow: 0 0 25px rgba(255, 251, 0, 0.25); backdrop-filter: blur(10px); text-align: center;
        }
        .guild-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid rgba(255, 251, 0, 0.4); padding-bottom: 15px; margin-bottom: 20px; }
        .guild-name-area { text-align: left; }
        .guild-label { font-size: 14px; color: #fffb00; text-transform: uppercase; letter-spacing: 1px; }
        .guild-name { font-size: 28px; font-weight: bold; color: #ffffff; text-shadow: 0 0 8px rgba(255, 255, 255, 0.5); }
        .guild-logo { width: 140px; height: 140px; border-radius: 50%; border: 2px solid #fffb00; object-fit: cover; box-shadow: 0 0 15px rgba(255, 251, 0, 0.5); }
        .meta-info { text-align: left; font-size: 16px; color: #8cd9ff; margin-bottom: 10px; font-weight: bold; }
        .member-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; text-align: left; }
        .member-table th { background: rgba(0, 191, 255, 0.2); color: #00bfff; padding: 10px; font-size: 14px; border-bottom: 1px solid rgba(0, 191, 255, 0.4); }
        .member-table td { padding: 10px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); font-size: 15px; }
        .rank-ssr { color: #ff007f; font-weight: bold; text-shadow: 0 0 4px #ff007f; }
        .rank-sr  { color: #fffb00; font-weight: bold; }
        .rank-r   { color: #ffffff; }
        .btn-exile { color: #ff007f; text-decoration: none; font-weight: bold; font-size: 13px; border: 1px solid rgba(255, 0, 127, 0.4); padding: 2px 8px; border-radius: 4px; background: rgba(255, 0, 127, 0.05); transition: all 0.2s; }
        .btn-exile:hover { background: rgba(255, 0, 127, 0.25); box-shadow: 0 0 8px rgba(255, 0, 127, 0.5); }
        .status-section { background: rgba(0, 0, 0, 0.4); border: 1px solid rgba(0, 191, 255, 0.3); border-radius: 8px; padding: 20px; text-align: left; margin-bottom: 25px; }
        .status-title { color: #00bfff; font-size: 16px; font-weight: bold; margin-bottom: 15px; border-left: 4px solid #00bfff; padding-left: 8px; }
        .status-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 20px; }
        .status-item { display: flex; justify-content: space-between; font-size: 15px; border-bottom: 1px dashed rgba(255, 255, 255, 0.1); padding-bottom: 4px; }
        .status-label { color: #a3e2ff; }
        .status-value { font-family: 'Courier New', monospace; font-weight: bold; }
        .guild-rank-area { font-size: 30px; font-weight: bold; color: #ff007f; text-shadow: 0 0 10px #ff007f; margin: 25px 0; }
        .btn-home { display: inline-block; color: #00bfff; border: 1px solid #00bfff; padding: 10px 30px; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: bold; background: transparent; transition: all 0.3s; box-shadow: 0 0 15px rgba(0, 191, 255, 0.4); }
        .btn-home:hover { background: rgba(0, 191, 255, 0.15); }
    </style>
</head>
<body>
<div class="guild-card">
    <div class="guild-header">
        <div class="guild-name-area">
            <div class="guild-label">Guild Name [<?= h($display_class_name) ?>]</div>
            <div class="guild-name">ギルド：<?= h($guild_name) ?></div>
        </div>
        <img src="img/<?= h($logo_image) ?>" alt="Guild Logo" class="guild-logo">
    </div>

    <div class="meta-info">メンバー：<?= $member_count ?>名</div>
    <table class="member-table">
        <thead>
            <tr><th>ニックネーム</th><th>レベル</th><th>職業</th><th>ランク</th><th>操作</th></tr>
        </thead>
        <tbody>
            <?php if ($member_count > 0): ?>
                <?php foreach ($members as $m): ?>
                    <tr>
                        <td><b><?= h($m['name']) ?></b></td>
                        <td>Lv.99</td>
                        <td><?= h($m['job']) ?></td>
                        <td class="rank-<?= strtolower($m['rank']) ?>"><?= h($m['rank']) ?></td>
                        <td>
                            <a href="delete.php?class=<?= h($safe_class_id) ?>&num=<?= h($m['attendance_no']) ?>" 
                               class="btn-exile"
                               onclick="return confirm('本当にこのメンバーをギルドから追放しますか？')">追放</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align: center; color: #666;">該当クラスのギルドメンバーはまだ登録されていません。</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="status-section">
        <div class="status-title">ギルド合計パラメーター値</div>
        <div class="status-grid">
            <div class="status-item"><span class="status-label">攻撃</span><span class="status-value"><?= $guild_atk ?></span></div>
            <div class="status-item"><span class="status-label">防御</span><span class="status-value"><?= $guild_def ?></span></div>
            <div class="status-item"><span class="status-label">魔法</span><span class="status-value"><?= $guild_matk ?></span></div>
            <div class="status-item"><span class="status-label">素早さ</span><span class="status-value"><?= $guild_spd ?></span></div>
            <div class="status-item"><span class="status-label">回避</span><span class="status-value"><?= $guild_eva ?></span></div>
            <div class="status-item"><span class="status-label">回復力</span><span class="status-value"><?= $guild_mnd ?></span></div>
            <div class="status-item" style="grid-column: span 2; border-top: 1px solid rgba(0,191,255,0.4); padding-top: 8px;">
                <span class="status-label" style="color: #fffb00;">総合計HP</span>
                <span class="status-value" style="color: #fffb00; font-size: 18px;"><?= $guild_hp ?></span>
            </div>
        </div>
    </div>
    <div class="guild-rank-area">ギルドランク：SS</div>
    <a href="index.php" class="btn-home">始まりの町に戻る</a>
</div>
</body>
</html>