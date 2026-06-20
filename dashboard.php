<?php
// ===================================================
// 📊 DEVELOPER & ADMINISTRATOR DASHBOARD (BACKOFFICE)
// ===================================================

// 安全対策（XSS対策）
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// 1. アクティブなギルド（クラスファイル）の自動スキャン
$files = glob("data_*.txt");
$guild_list = [];

$total_all_players = 0; // 全クラス合算のプレイヤー数

if ($files) {
    foreach ($files as $file) {
        $class_id = str_replace(["data_", ".txt"], "", basename($file));
        if (empty($class_id) || $class_id === 'default') continue;

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) $lines = [];
        
        $member_count = count($lines);
        $total_all_players += $member_count;

        // 各ギルドの集計用初期化
        $g_atk = $g_def = $g_matk = $g_spd = $g_eva = $g_mnd = $g_hp = 0;
        $members_detail = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            $row = explode(",", $line);
            if (count($row) < 9) continue;

            // データのマッピング
            $p_name    = trim($row[1]);
            $p_num     = trim($row[2]);
            $p_gender  = trim($row[3]);
            $p_food    = trim($row[4]);
            $p_trend   = trim($row[5]);
            $p_location = trim($row[6]);
            $p_style   = trim($row[7]);
            $p_motto   = trim($row[8]);

            // 正確な個人ステータス計算（guild.phpから完全移植）
            $q4_data = ['hamburg' => 5, 'curry' => 4, 'ramen' => 3, 'sushi' => 2, 'cake' => 1];
            $q4_score = $q4_data[$p_food] ?? 1;

            $q5_data = ['tanghulu' => 5, 'shipuwan' => 4, 'dobaichoco' => 3, 'biryani' => 2, 'phakchi' => 1];
            $q5_score = $q5_data[$p_trend] ?? 1;

            $q6_data = ['luxury' => 5, 'fukuoka' => 5, 'osaka' => 4, 'nagoya' => 3, 'downtown' => 3, 'okinawa' => 2, 'hokkaido' => 1];
            $q6_score = $q6_data[$p_location] ?? 1;

            $q7_data = ['blank' => 5, 'dekirudakeyaru' => 4, 'cheat' => 2, 'douga' => 1, 'girigiri' => 3];
            $q7_score = $q7_data[$p_style] ?? 1;

            $motto_len = mb_strlen($p_motto);
            $att_num   = (int)$p_num;

            $atk  = ($q4_score * 1200) + ($q6_score * 150) + 2000;
            $def  = ($q6_score * 1200) + ($q4_score * 150) + 2000;
            $matk = ($q5_score * 1200) + ($q7_score * 150) + 2000;
            $spd  = ($q7_score * 1400) + ($q5_score * 150) - ($motto_len * 40) + 2000;
            $hp   = ($motto_len * 4500 * $q4_score) + 45000;
            $eva  = floor($spd * 0.95) + ($q6_score * 150) + 100;
            $mnd  = ($q4_score * 800) + ($q5_score * 400) + ($att_num * 10) + 2000;

            // ギルド合計に加算
            $g_atk += $atk; $g_def += $def; $g_matk += $matk;
            $g_spd += $spd; $g_eva += $eva; $g_mnd += $mnd; $g_hp += $hp;

            // 詳細データを格納
            $members_detail[] = [
                'name' => $p_name,
                'num'  => $p_num,
                'motto'=> $p_motto,
                'stats'=> "ATK:$atk / DEF:$def / MAG:$matk / SPD:$spd / HP:$hp"
            ];
        }

        // 🌟 ギルド名をシード値＋合計ステータス連動で完全自動決定
        $prefix_pool = ["爆炎の", "蒼空の", "悠久の", "漆黒の", "黄金の", "深淵の"];
        $seed = crc32($class_id);
        $prefix = $prefix_pool[abs($seed) % count($prefix_pool)];

        if ($member_count > 0) {
            $guild_stats = [
                '剣' => $g_atk,
                '杖' => $g_matk,
                '盾' => $g_def,
                '風' => $g_spd
            ];
            arsort($guild_stats);
            $suffix = key($guild_stats);
        } else {
            $suffix = "盾";
        }
        $guild_name = $prefix . $suffix;

        $guild_list[] = [
            'class_id' => $class_id,
            'name'     => $guild_name,
            'count'    => $member_count,
            'stats'    => ['atk' => $g_atk, 'def' => $g_def, 'matk' => $g_matk, 'spd' => $g_spd, 'eva' => $g_eva, 'mnd' => $g_mnd, 'hp' => $g_hp],
            'members'  => $members_detail
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚙️ 運営専用データダッシュボード</title>
    <style>
        body {
            background-color: #0b0f19; color: #e2e8f0; font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 0; padding: 40px 20px;
        }
        .container { max-width: 1100px; margin: 0 auto; }
        .header-area { border-bottom: 2px solid #1e293b; padding-bottom: 20px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .title { font-size: 26px; font-weight: bold; color: #38bdf8; letter-spacing: 1px; }
        .badge-dev { background: #ef4444; color: #fff; font-size: 11px; padding: 3px 8px; border-radius: 4px; font-weight: bold; vertical-align: middle; margin-left: 10px; }
        .system-status { background: #1e293b; border-radius: 8px; padding: 15px 25px; margin-bottom: 30px; display: inline-block; }
        .system-status span { font-size: 20px; color: #f59e0b; font-weight: bold; }
        
        .guild-grid { display: grid; grid-template-columns: 1fr; gap: 25px; }
        .guild-card { background: #111827; border: 1px solid #1f2937; border-radius: 10px; padding: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.2); }
        .guild-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #1f2937; padding-bottom: 12px; margin-bottom: 15px; }
        .guild-title { font-size: 20px; font-weight: bold; color: #f1f5f9; }
        .guild-count { background: #0ea5e9; color: #fff; padding: 2px 10px; border-radius: 12px; font-size: 13px; }
        
        /* 🔴 ギルド一括解散ボタン */
        .btn-disband-dashboard {
            background-color: #dc2626; color: #ffffff; text-decoration: none; font-size: 12px; font-weight: bold;
            padding: 5px 12px; border-radius: 4px; transition: background 0.2s; white-space: nowrap;
        }
        .btn-disband-dashboard:hover { background-color: #b91c1c; }

        .stats-row { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
        .stats-box { background: #1e293b; padding: 8px 15px; border-radius: 6px; min-width: 100px; box-sizing: border-box; }
        .stats-label { font-size: 11px; color: #94a3b8; display: block; }
        .stats-value { font-size: 16px; font-weight: bold; color: #38bdf8; }
        .stats-hp { color: #f43f5e; }

        details { background: #1f2937; border-radius: 6px; padding: 10px; }
        summary { cursor: pointer; font-size: 13px; color: #94a3b8; font-weight: bold; outline: none; user-select: none; }
        summary:hover { color: #38bdf8; }
        
        .member-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; }
        .member-table th { background: #111827; color: #94a3b8; text-align: left; padding: 8px; font-weight: normal; }
        .member-table td { border-bottom: 1px solid #374151; padding: 8px; color: #e2e8f0; }

        /* ❌ 個別追放ボタン */
        .btn-exile-dashboard {
            color: #f87171; text-decoration: none; font-size: 11px; font-weight: bold;
            border: 1px solid #ef4444; padding: 2px 8px; border-radius: 4px;
            background: rgba(239, 68, 68, 0.05); transition: all 0.2s;
        }
        .btn-exile-dashboard:hover { background: rgba(239, 68, 68, 0.2); color: #ffffff; }

        .btn-back { display: inline-block; background: #334155; color: #fff; text-decoration: none; padding: 8px 20px; border-radius: 4px; font-size: 14px; transition: background 0.2s; }
        .btn-back:hover { background: #475569; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-area">
        <div class="title">⚙️ 運営専用データダッシュボード<span class="badge-dev">INTERNAL USE ONLY</span></div>
        <a href="index.php" class="btn-back">ポータルに戻る</a>
    </div>

    <div class="system-status">
        🌐 現在の世界（システム全体）の総プレイヤー数: <span><?= $total_all_players ?></span> 名
    </div>

    <div class="guild-grid">
        <?php if (!empty($guild_list)): ?>
            <?php foreach ($guild_list as $g): ?>
                <div class="guild-card">
                    <div class="guild-header">
                        <div class="guild-title">🏰 ギルド：<?= h($g['name']) ?> <span style="font-size: 13px; color:#64748b;">[<?= h($g['class_id']) ?>]</span></div>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div class="guild-count">所属: <?= $g['count'] ?> 名</div>
                            <a href="delete.php?action=disband&class=<?= urlencode($g['class_id']) ?>&from=dashboard" 
                               class="btn-disband-dashboard"
                               onclick="return confirm('本当にこのギルド「<?= h($g['name']) ?>」を解散し、全データを物理削除しますか？')">
                                ギルド解散
                            </a>
                        </div>
                    </div>

                    <div class="stats-row">
                        <div class="stats-box"><span class="stats-label">合計 攻撃</span><span class="stats-value"><?= $g['stats']['atk'] ?></span></div>
                        <div class="stats-box"><span class="stats-label">合計 防御</span><span class="stats-value"><?= $g['stats']['def'] ?></span></div>
                        <div class="stats-box"><span class="stats-label">合計 魔法</span><span class="stats-value"><?= $g['stats']['matk'] ?></span></div>
                        <div class="stats-box"><span class="stats-label">合計 素早さ</span><span class="stats-value"><?= $g['stats']['spd'] ?></span></div>
                        <div class="stats-box"><span class="stats-label">合計 回避</span><span class="stats-value"><?= $g['stats']['eva'] ?></span></div>
                        <div class="stats-box"><span class="stats-label">合計 回復</span><span class="stats-value"><?= $g['stats']['mnd'] ?></span></div>
                        <div class="stats-box"><span class="stats-label" style="color:#f43f5e;">総合計 HP</span><span class="stats-value stats-hp"><?= $g['stats']['hp'] ?></span></div>
                    </div>

                    <details>
                        <summary>👤 このギルドの所属メンバー内訳を表示する（計 <?= $g['count'] ?> 名）</summary>
                        <?php if (!empty($g['members'])): ?>
                            <table class="member-table">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">出席番号</th>
                                        <th style="width: 150px;">名前</th>
                                        <th>各パラメーター実数値</th>
                                        <th>座右の銘</th>
                                        <th style="width: 80px; text-align: center;">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($g['members'] as $m): ?>
                                        <tr>
                                            <td><?= h($m['num']) ?>番</td>
                                            <td style="font-weight: bold; color: #38bdf8;"><?= h($m['name']) ?></td>
                                            <td style="font-family: monospace; color: #a3e635;"><?= h($m['stats']) ?></td>
                                            <td style="font-style: italic; color: #94a3b8;">「<?= h($m['motto']) ?>」</td>
                                            <td style="text-align: center;">
                                                <a href="delete.php?class=<?= urlencode($g['class_id']) ?>&num=<?= urlencode($m['num']) ?>&from=dashboard" 
                                                   class="btn-exile-dashboard"
                                                   onclick="return confirm('本当に <?= h($m['name']) ?> さんのデータを削除（追放）しますか？')">
                                                    追放
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p style="font-size: 12px; color: #94a3b8; margin: 10px 0 0 0;">メンバーデータがありません。</p>
                        <?php endif; ?>
                    </details>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="background: #111827; padding: 40px; text-align: center; border-radius: 8px; color: #94a3b8;">
                🏰 まだ世界にギルド（データファイル）が創設されていません。
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>