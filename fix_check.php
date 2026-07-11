<?php
$dir = 'C:\Users\Isabel\Desktop\Drogueria_La_Formula\www';
$headerFile = $dir . '/constant/layout/header.php';

// Remove check.php from header.php
if (file_exists($headerFile)) {
    $headerContent = file_get_contents($headerFile);
    $headerContent = preg_replace('/include\s*["\']\.\/constant\/check\.php["\'];\r?\n?/', '', $headerContent);
    file_put_contents($headerFile, $headerContent);
}

// Prepend check.php to root files
$files = glob($dir . '/*.php');
foreach ($files as $file) {
    $basename = basename($file);
    // Skip login and files that don't need check
    if ($basename === 'login.php' || $basename === 'index.php') continue;

    $content = file_get_contents($file);
    // Only if it includes head.php or header.php
    if (strpos($content, 'head.php') !== false || strpos($content, 'header.php') !== false) {
        // If not already checking
        if (strpos($content, 'check.php') === false) {
            // we will prepend it. Wait, the file might start with <?php
            // The cleanest way is to just put it at the very top.
            // If it starts with <?php, insert after it.
            if (preg_match('/^\s*<\?php/i', $content)) {
                $content = preg_replace('/^\s*<\?php/i', "<?php\nrequire_once 'constant/check.php';", $content);
            } else {
                $content = "<?php require_once 'constant/check.php'; ?>\n" . $content;
            }
            file_put_contents($file, $content);
            echo "Fixed $basename\n";
        }
    }
}
echo "Done.";
