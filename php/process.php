<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = htmlspecialchars(trim($_POST['name']));
    $email   = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone   = htmlspecialchars(trim($_POST['phone']));
    $service = htmlspecialchars(trim($_POST['service']));
    $message = htmlspecialchars(trim($_POST['message']));

    $errors = [];
    if (empty($name)) {
        $errors[] = "Full Name is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email address is required.";
    }
    if (empty($message)) {
        $errors[] = "Please enter your message.";
    }

    if (!empty($errors)) {
        $errorString = implode(' ', $errors);
        header("Location: index.html#contact?error=" . urlencode($errorString));
        exit;
    }

    $to = "info@jenabrandsltd.co.ke";
    $subject = "New Contact Inquiry from Jena Brands Website";
    $emailBody  = "Name: " . $name . "\n";
    $emailBody .= "Email: " . $email . "\n";
    $emailBody .= "Phone: " . $phone . "\n";
    $emailBody .= "Service Interest: " . $service . "\n";
    $emailBody .= "Message:\n" . $message . "\n";
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";

    if (mail($to, $subject, $emailBody, $headers)) {
        header("Location: index.html#contact?success=1");
    } else {
        header("Location: index.html#contact?error=mailfailed");
    }
    exit;
}
?>