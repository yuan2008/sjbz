<?php
// 设置CSV文件路径
$csvPath = 'fulitu_images.csv';

// 获取URL参数中的keyword
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// 检查CSV文件是否存在
if (!file_exists($csvPath)) {
    die("错误：找不到CSV文件 {$csvPath}");
}

// 读取CSV文件内容
$lines = array_filter(file($csvPath, FILE_IGNORE_NEW_LINES), function($line) {
    return trim($line) !== '';
});

// 移除标题行
array_shift($lines);

// 解析CSV行并提取URL
$entries = [];

foreach ($lines as $line) {
    // 使用str_getcsv函数来正确解析CSV行
    $fields = str_getcsv($line);
    
    // 从第一个字段获取名称，从第二个字段开始获取URL
    $name = trim($fields[0]);
    
    // 如果提供了关键词且当前条目的名称不包含关键词，则跳过
    if (!empty($keyword) && stripos($name, $keyword) === false) {
        continue;
    }
    
    // 从第二个字段开始是URL
    for ($i = 1; $i < count($fields); $i++) {
        $url = trim($fields[$i]);
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $entries[] = [
                'name' => $name,
                'url' => $url
            ];
        }
    }
}

// 如果没有找到有效的条目，则显示错误
if (empty($entries)) {
    if (!empty($keyword)) {
        die("错误：没有找到包含关键词 '{$keyword}' 的条目");
    } else {
        die("错误：CSV文件中没有找到有效的URL");
    }
}

// 随机选择一个条目
$randomIndex = array_rand($entries);
$selectedEntry = $entries[$randomIndex];

// 记录日志（可选）
if (!empty($keyword)) {
    error_log("根据关键词 '{$keyword}' 随机选择了: {$selectedEntry['name']}, 链接: {$selectedEntry['url']}");
} else {
    error_log("随机选择了: {$selectedEntry['name']}, 链接: {$selectedEntry['url']}");
}

// 执行重定向
header("Location: " . $selectedEntry['url']);
exit();
?>
