<?php

$outputDir = __DIR__ . '/resources/output';

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0775, true);
}

$pdfFile = $outputDir . '/test.pdf';
$htmlFile = $outputDir . '/test.html';

$html = '<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>doc-writer PDF test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1 { font-size: 24px; }
        p { font-size: 14px; }
    </style>
</head>
<body>
    <h1>doc-writer PDF test</h1>
    <p>If this PDF is generated, wkhtmltopdf works from PHP.</p>
</body>
</html>';

file_put_contents($htmlFile, $html);

$htmlUrl = 'file://' . $htmlFile;

$cmd = '/usr/local/bin/wkhtmltopdf '
    . '--enable-local-file-access '
    . escapeshellarg($htmlUrl)
    . ' '
    . escapeshellarg($pdfFile)
    . ' 2>&1';

$output = shell_exec($cmd);

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>doc-writer PDF test</title>
</head>
<body>
    <h1>doc-writer PDF test</h1>

    <h2>Command output</h2>
    <pre><?php echo htmlspecialchars((string)$output, ENT_QUOTES, 'UTF-8'); ?></pre>

    <h2>Result</h2>
    <?php if (file_exists($pdfFile)): ?>
        <p>PDF created.</p>
        <p><a href="resources/output/test.pdf">Download test.pdf</a></p>
    <?php else: ?>
        <p>PDF was not created.</p>
    <?php endif; ?>
</body>
</html>
