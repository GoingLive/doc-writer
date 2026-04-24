<?php

$mdFile = __DIR__ . '/resources/documents/siqueira/sample-loi.md';

if (!file_exists($mdFile)) {
    $content = 'Markdown file not found: ' . $mdFile;
} else {
    $content = file_get_contents($mdFile);
}

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>doc-writer Markdown preview</title>
</head>
<body>
    <h1>doc-writer Markdown preview</h1>

    <pre><?php echo htmlspecialchars((string)$content, ENT_QUOTES, 'UTF-8'); ?></pre>
</body>
</html>
