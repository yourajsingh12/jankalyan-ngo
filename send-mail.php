<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method not allowed.');
}

require_csrf();

$fields = ['name', 'email', 'phone', 'subject', 'message'];
$form = [];
foreach ($fields as $field) {
    $form[$field] = is_string($_POST[$field] ?? null) ? trim($_POST[$field]) : '';
}

if (
    mb_strlen($form['name']) < 2
    || mb_strlen($form['name']) > 160
    || mb_strlen($form['email']) > 190
    || filter_var($form['email'], FILTER_VALIDATE_EMAIL) === false
    || preg_match('/^[6-9][0-9]{9}$/', $form['phone']) !== 1
    || $form['subject'] === ''
    || mb_strlen($form['subject']) > 255
    || mb_strlen($form['message']) < 10
    || mb_strlen($form['message']) > 5000
) {
    redirect('contact.php?status=invalid');
}

try {
    q(
        'INSERT INTO contact_messages (name, email, phone, subject, message, ip_address)
         VALUES (?, ?, ?, ?, ?, ?)',
        [
            $form['name'],
            $form['email'],
            $form['phone'],
            $form['subject'],
            $form['message'],
            filter_var($_SERVER['REMOTE_ADDR'] ?? '', FILTER_VALIDATE_IP) ?: null,
        ]
    );
} catch (PDOException $exception) {
    error_log('Contact message submission failed: ' . $exception->getMessage());
    redirect('contact.php?status=error');
}

redirect('contact.php?status=success');
