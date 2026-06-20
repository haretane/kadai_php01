<?php
// ===================================================
// 1. INPUT RECEIPT & SECURITY SANITIZATION
// ===================================================
$class_param  = $_GET['class'] ?? '';
$num_param    = $_GET['num'] ?? '';
$action_param = $_GET['action'] ?? '';
$from_param   = $_GET['from'] ?? ''; // どこからアクセスされたかをキャッチ

$safe_class_id = preg_replace('/[^a-zA-Z0-9_\-]/', '', $class_param);
$safe_num      = preg_replace('/[^0-9]/', '', $num_param);

if (empty($safe_class_id)) {
    $target_file = "data.txt";
} else {
    $target_file = "data_" . $safe_class_id . ".txt";
}

// ===================================================
// NEW: ENTIRE GUILD DISBANDMENT PROCESS
// ===================================================
if ($action_param === 'disband') {
    if (!empty($safe_class_id) && file_exists($target_file)) {
        unlink($target_file);
    }
    
    // ダッシュボードからの解散ならダッシュボードへ戻る
    if ($from_param === 'dashboard') {
        header("Location: dashboard.php");
    } else {
        header("Location: guild_list.php");
    }
    exit;
}

// ===================================================
// 2. EXILE PROCESS (Physical Record Filtering)
// ===================================================
if (file_exists($target_file) && !empty($safe_num)) {
    $lines = file($target_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $remaining_lines = [];

    if ($lines !== false) {
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $row = explode(",", $line);
            $row = array_map('trim', $row);

            if (count($row) >= 3) {
                $current_num = $row[2]; // 出席番号はインデックス2に固定
                if ($current_num === $safe_num) {
                    continue; // 一致したらスキップ（追放）
                }
            }
            $remaining_lines[] = $line;
        }
    }

    if (!empty($remaining_lines)) {
        $final_data = implode("\n", $remaining_lines) . "\n";
        file_put_contents($target_file, $final_data, LOCK_EX);
    } else {
        unlink($target_file); // 誰もいなくなったらファイルごと削除してクリーンに
    }
}

// ===================================================
// 3. IMMEDIATE REDIRECT BACK TO GUILD VIEWER
// ===================================================
// ダッシュボードからの追放ならダッシュボードへ戻る
if ($from_param === 'dashboard') {
    header("Location: dashboard.php");
} else {
    header("Location: guild.php?class=" . urlencode($safe_class_id));
}
exit;