<?php

$mdFile = __DIR__ . '/resources/documents/siqueira/sample-loi.md';

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

if (!file_exists($mdFile)) {
    $rendered = '<p>Markdown file not found.</p>';
} else {
    $markdown = file_get_contents($mdFile);
    $rendered = render_blocks((string)$markdown, $tagMap);
}

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>doc-writer render preview</title>
    <link rel="stylesheet" href="resources/tenants/siqueira/styles/standard_letter_A4.css">
</head>
<body>
    <div class="dw-page">
        <?php echo $rendered; ?>
    </div>
</body>
</html>
