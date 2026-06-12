<?php
/* ═══════════════════════════════════════════════════════════
   Jena Brands Ltd · contact.php
   Handles POST from the contact form, returns JSON.
   ═══════════════════════════════════════════════════════════ */

header('Content-Type: application/json; charset=utf-8');

/* Only accept POST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

/* ── Helpers ─────────────────────────────────────────────── */
function clean(string $value): string {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

/* ── Collect & sanitise inputs ────────────────────────────── */
$name    = clean($_POST['name']    ?? '');
$email   = trim($_POST['email']    ?? '');
$phone   = clean($_POST['phone']   ?? '');
$org     = clean($_POST['org']     ?? '');
$service = clean($_POST['service'] ?? '');
$message = clean($_POST['message'] ?? '');

/* ── Validate ─────────────────────────────────────────────── */
$errors = [];

if (strlen($name) < 2) {
    $errors[] = 'Please enter your full name.';
}

$emailFiltered = filter_var($email, FILTER_VALIDATE_EMAIL);
if (!$emailFiltered) {
    $errors[] = 'Please enter a valid email address.';
} else {
    $email = $emailFiltered;
}

if (strlen($message) < 10) {
    $errors[] = 'Please enter a message (at least 10 characters).';
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

/* ── Build email ──────────────────────────────────────────── */
$to      = 'info@jenabrandsltd.co.ke';
$subject = 'Enquiry from ' . $name . ($service ? ' – ' . $service : '');

$body  = "New enquiry received via the Jena Brands Ltd website.\n";
$body .= str_repeat('─', 52) . "\n\n";
$body .= "Name:          $name\n";
$body .= "Email:         $email\n";
if ($phone)   $body .= "Phone:         $phone\n";
if ($org)     $body .= "Organisation:  $org\n";
if ($service) $body .= "Service:       $service\n";
$body .= "\nMessage:\n$message\n\n";
$body .= str_repeat('─', 52) . "\n";
$body .= "Sent from jenabrands.co.ke · " . date('Y-m-d H:i:s T') . "\n";

$headers  = "From: Jena Brands Website <noreply@jenabrandsltd.co.ke>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

/* ── Send ─────────────────────────────────────────────────── */
$sent = mail($to, $subject, $body, $headers);

if ($sent) {
    echo json_encode([
        'success' => true,
        'message' => 'Thank you, ' . htmlspecialchars($name) . '. We\'ll be in touch within 24 hours.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'We couldn\'t send your message right now. Please email us directly at info@jenabrandsltd.co.ke.'
    ]);
}