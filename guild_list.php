<?php
// ===================================================
// 1. AUTO-SCAN ACTIVE GUILD FILES
// ===================================================
$guilds = [];
$files = glob("data_*.txt");

$prefix_pool = ["爆炎の", "蒼空の", "悠久の", "漆黒の", "黄金の", "深淵の"];

if ($files) {
    foreach ($files as $file) {
        $class_id = str_replace(["data_", ".txt"], "", basename($file));
        if (empty($class_id) || $class_id === 'default') continue;

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $member_count = count($lines);

        if ($member_count > 0) {
            $seed = crc32($class_id);
            $prefix = $prefix_pool[abs($seed) % count($prefix_pool)];
            
            $jobs = [];
            foreach ($lines as $line) {
                $row = explode(",", trim($line));
                $row = array_map('trim', $row);
                if (count($row) < 9) continue; // 正常な9列データ以外は無視
                
                $food = $row[4]; // foodは必ずインデックス4
                $jobs[] = $food;
            }
            
            $suffix = "盾"; 
            if (!empty($jobs)) {
                $counts = array_count_values($jobs);
                arsort($counts);
                $top = key($counts);
                if ($top === 'hamburg') $suffix = "剣";
                elseif ($top === 'curry') $suffix = "杖";
                elseif ($top === 'tanghulu') $suffix = "風";
            }
            $guild_name = $prefix . $suffix;
        } else {
            $guild_name = "始まりのギルド";
        }

        $guilds[] = [
            'class_id' => $class_id,
            'name' => $guild_name,
            'count' => $member_count
        ];
    }
}

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>📜 登録ギルド（クラス）一覧 📜</title>
    <style>
        body {
            background-color: #030712; color: #ffffff; font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 0; padding: 60px 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; box-sizing: border-box;
        }
        .list-card { width: 90%; max-width: 640px; background: rgba(10, 20, 50, 0.75); border: 2px solid #00bfff; border-radius: 12px; padding: 40px; box-shadow: 0 0 25px rgba(0, 191, 255, 0.2); backdrop-filter: blur(10px); }
        .title { font-size: 24px; font-weight: bold; color: #00bfff; text-align: center; margin-bottom: 30px; text-shadow: 0 0 8px rgba(0, 191, 255, 0.4); border-bottom: 2px solid rgba(0, 191, 255, 0.3); padding-bottom: 15px; }
        .guild-item { display: flex; justify-content: space-between; align-items: center; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 8px; padding: 15px 20px; margin-bottom: 15px; transition: all 0.2s; }
        .guild-item:hover { background: rgba(0, 191, 255, 0.08); border-color: rgba(0, 191, 255, 0.3); }
        .guild-info { text-decoration: none; color: #ffffff; flex-grow: 1; display: flex; align-items: center; gap: 12px; }
        .guild-name { font-size: 18px; font-weight: bold; color: #fffb00; text-shadow: 0 0 4px rgba(255, 251, 0, 0.2); }
        .class-tag { font-size: 13px; background: rgba(0, 191, 255, 0.2); color: #8cd9ff; padding: 2px 8px; border-radius: 4px; font-family: monospace; }
        .member-count { font-size: 14px; color: #a3e2ff; }
        .btn-disband { color: #ff007f; text-decoration: none; font-weight: bold; font-size: 13px; border: 1px solid rgba(255, 0, 127, 0.5); padding: 5px 12px; border-radius: 4px; background: rgba(255, 0, 127, 0.05); transition: all 0.2s; white-space: nowrap; }
        .btn-disband:hover { background: rgba(255, 0, 127, 0.25); box-shadow: 0 0 8px rgba(255, 0, 127, 0.5); }
        .empty-text { text-align: center; color: #64748b; padding: 30px 0; font-size: 15px; }
        .footer-area { text-align: center; margin-top: 30px; }
        .btn-home { display: inline-block; color: #00bfff; border: 1px solid #00bfff; padding: 10px 30px; border-radius: 4px; text-decoration: none; font-size: 14px; font-weight: bold; transition: all 0.3s; }
        .btn-home:hover { background: rgba(0, 191, 255, 0.15); }
    </style>
</head>
<body>
<div class="list-card">
    <div class="title">🏰 創設されたギルド一覧</div>
    <?php if (!empty($guilds)): ?>
        <?php foreach ($guilds as $g): ?>
            <div class="guild-item">
                <a href="guild.php?class=<?= h($g['class_id']) ?>" class="guild-info">
                    <span class="guild-name">ギルド：<?= h($g['name']) ?></span>
                    <span class="class-tag"><?= h($g['class_id']) ?></span>
                    <span class="member-count">(<?= $g['count'] ?>名)</span>
                </a>
                <a href="delete.php?action=disband&class=<?= h($g['class_id']) ?>" 
                   class="btn-disband"
                   onclick="return confirm('本当にこのギルドを解散（全データ物理削除）しますか？')">ギルドを解散</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-text">現在、世界に創設されたギルドはありません。<br>新米冒険者の登録を待っています。</div>
    <?php endif; ?>
    <div class="footer-area">
        <a href="index.php" class="btn-home">始まりの町に戻る</a>
    </div>
</div>
</body>
</html>