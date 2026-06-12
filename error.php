<?php

declare(strict_types=1);

/**
 * UC 26: Global Error Page
 *
 * Apache directs uncaught HTTP errors to this standalone page through the
 * ErrorDocument rules in .htaccess.
 */

/**
 * Escape a value before printing it in HTML.
 */
function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** @var array<int, array{title: string, message: string}> $errors */
$errors = [
    400 => [
        'title' => 'Bad Request',
        'message' => 'The request could not be understood. Please check it and try again.',
    ],
    401 => [
        'title' => 'Unauthorized',
        'message' => 'You need to sign in or provide valid credentials to view this page.',
    ],
    403 => [
        'title' => 'Forbidden',
        'message' => 'You do not have permission to access this page or resource.',
    ],
    404 => [
        'title' => 'Page Not Found',
        'message' => 'The page you requested could not be found.',
    ],
    500 => [
        'title' => 'Internal Server Error',
        'message' => 'Something went wrong on our side. Please try again later.',
    ],
];

$requestedCode = filter_input(INPUT_GET, 'code', FILTER_VALIDATE_INT);
$code = is_int($requestedCode) && isset($errors[$requestedCode])
    ? $requestedCode
    : 500;

http_response_code($code);

$error = $errors[$code];

// Change this path if the project folder is renamed from "Web-projekt".
$projectBasePath = '/Web-projekt/';
$bootstrapUrl = $projectBasePath . 'assets/css/bootstrap.min.css';
$homeUrl = $projectBasePath . 'index.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= escapeHtml((string) $code); ?> - <?= escapeHtml($error['title']); ?></title>
    <link rel="stylesheet" href="<?= escapeHtml($bootstrapUrl); ?>">
    <style>
        html,
        body {
            min-height: 100%;
        }

        body {
            margin: 0;
            background: #f4f6f8;
            color: #212529;
            font-family: Arial, Helvetica, sans-serif;
        }

        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            padding: 2rem 1rem;
        }

        .error-card {
            width: 100%;
            max-width: 42rem;
            box-sizing: border-box;
            padding: 2.5rem;
            border: 1px solid #dee2e6;
            border-radius: 0.75rem;
            background: #fff;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .error-code {
            margin: 0 0 0.5rem;
            color: #6c757d;
            font-size: clamp(3rem, 10vw, 5rem);
            font-weight: 700;
            line-height: 1;
        }

        .error-title {
            margin: 0 0 1rem;
            font-size: clamp(1.5rem, 5vw, 2rem);
        }

        .error-message {
            margin: 0 auto 1.75rem;
            max-width: 32rem;
            color: #5c636a;
            line-height: 1.6;
        }

        .home-link {
            display: inline-block;
            padding: 0.7rem 1.2rem;
            border: 1px solid #0d6efd;
            border-radius: 0.375rem;
            background: #0d6efd;
            color: #fff;
            font-weight: 600;
            text-decoration: none;
        }

        .home-link:hover,
        .home-link:focus {
            background: #0b5ed7;
            border-color: #0a58ca;
            color: #fff;
        }
    </style>
</head>
<body>
    <main class="error-page container">
        <section class="error-card" aria-labelledby="error-title">
            <p class="error-code"><?= escapeHtml((string) $code); ?></p>
            <h1 class="error-title" id="error-title"><?= escapeHtml($error['title']); ?></h1>
            <p class="error-message"><?= escapeHtml($error['message']); ?></p>
            <a class="home-link btn btn-primary" href="<?= escapeHtml($homeUrl); ?>">
                Back to homepage
            </a>
        </section>
    </main>
</body>
</html>
