<?php

$mdFile = __DIR__ . '/resources/documents/siqueira/sample-loi.md';
$cssFile = __DIR__ . '/resources/tenants/siqueira/styles/standard_letter_A4.css';
$outputDir = __DIR__ . '/resources/output';

$htmlFile = $outputDir . '/sample-loi.html';
$pdfFile = $outputDir . '/sample-loi.pdf';

$tagMap = [
    'adr' => 'dw-recipient-address',
    'date' => 'dw-location-date',
    'subj' => 'dw-subject',
    'sub' => 'dw-subtitle',
    'sal' => 'dw-salutation',
    'body' => 'dw-body',
    'close' => 'dw-closing-salutation',
    'sign' => 'dw-sender-closing',
    'attach' => 'dw-references-attachments',
];

function remove_bom(string $text): string
{
    return preg_replace('/^\xEF\xBB\xBF/', '', $text) ?? $text;
}

function simple_markdown_to_html(string $text): string
{
    $text = trim($text);
    $lines = preg_split('/\R/', $text);
    $html = '';
    $paragraph = [];

    foreach ($lines as $line) {
        $trimmed = trim($line);

        if ($trimmed === '') {
            if ($paragraph !== []) {
                $html .= '<p>' . implode('<br>', $paragraph) . '</p>' . "\n";
                $paragraph = [];
            }
            continue;
        }

        if (str_starts_with($trimmed, '## ')) {
            if ($paragraph !== []) {
                $html .= '<p>' . implode('<br>', $paragraph) . '</p>' . "\n";
                $paragraph = [];
            }

            $html .= '<h2>' . htmlspecialchars(substr($trimmed, 3), ENT_QUOTES, 'UTF-8') . '</h2>' . "\n";
            continue;
        }

        $paragraph[] = htmlspecialchars($trimmed, ENT_QUOTES, 'UTF-8');
    }

    if ($paragraph !== []) {
        $html .= '<p>' . implode('<br>', $paragraph) . '</p>' . "\n";
    }

    return $html;
}

function render_blocks(string $markdown, array $tagMap): string
{
    $markdown = remove_bom($markdown);
    $markdown = preg_replace('/^---.*?---\s*/s', '', $markdown) ?? $markdown;

    preg_match_all('/:::\s*(\w+)\s*\R(.*?)\R:::/s', $markdown, $matches, PREG_SET_ORDER);

    $html = '';

    foreach ($matches as $match) {
        $tag = $match[1];
        $content = $match[2];
        $class = $tagMap[$tag] ?? 'dw-unknown';

        $html .= '<div class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '">' . "\n";
        $html .= simple_markdown_to_html($content);
        $html .= '</div>' . "\n";
    }

    return $html;
}

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0775, true);
}

if (!file_exists($mdFile)) {
    $message = 'Markdown file not found.';
    $commandOutput = '';
} else {
    $markdown = file_get_contents($mdFile);
    $css = file_exists($cssFile) ? file_get_contents($cssFile) : '';
    $bodyHtml = render_blocks((string)$markdown, $tagMap);

    $fullHtml = '<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Sample LOI</title>
    <style>
' . $css . '
    </style>
</head>
<body>
    <div class="dw-page">
' . $bodyHtml . '
    </div>
</body>
</html>';

    file_put_contents($htmlFile, $fullHtml);

    $cmd = '/usr/local/bin/wkhtmltopdf '
        . '--enable-local-file-access '
        . escapeshellarg('file://' . $htmlFile)
        . ' '
        . escapeshellarg($pdfFile)
        . ' 2>&1';

    $commandOutput = shell_exec($cmd);
    $message = file_exists($pdfFile) ? 'PDF created.' : 'PDF was not created.';
}

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>doc-writer render PDF</title>
</head>
<body>
    <h1>doc-writer render PDF</h1>

    <h2>Result</h2>
    <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>

    <?php if (file_exists($pdfFile)): ?>
        <p><a href="resources/output/sample-loi.pdf">Download sample-loi.pdf</a></p>
    <?php endif; ?>

    <h2>Command output</h2>
    <pre><?php echo htmlspecialchars((string)$commandOutput, ENT_QUOTES, 'UTF-8'); ?></pre>
</body>
</html>
