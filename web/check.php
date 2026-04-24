<?php

function run_check(string $cmd): string
{
    if (!function_exists('shell_exec')) {
        return 'shell_exec is not available';
    }

    $result = shell_exec($cmd . ' 2>&1');

    if ($result === null || trim($result) === '') {
        return 'no output';
    }

    return trim($result);
}

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>doc-writer server check</title>
</head>
<body>
    <h1>doc-writer server check</h1>

    <h2>PHP</h2>
    <pre><?php echo htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8'); ?></pre>

    <h2>wkhtmltopdf path</h2>
    <pre><?php echo htmlspecialchars(run_check('which wkhtmltopdf'), ENT_QUOTES, 'UTF-8'); ?></pre>

    <h2>wkhtmltopdf version</h2>
    <pre><?php echo htmlspecialchars(run_check('wkhtmltopdf --version'), ENT_QUOTES, 'UTF-8'); ?></pre>
</body>
</html>
