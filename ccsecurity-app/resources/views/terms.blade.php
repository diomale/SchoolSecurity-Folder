<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - Columban College Security System</title>
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Outfit', sans-serif; background: #0f172a; color: #e2e8f0; margin: 0; padding: 0; }
        .container { max-width: 800px; margin: 0 auto; padding: 40px 20px; }
        h1 { color: #60a5fa; font-size: 2rem; margin-bottom: 10px; }
        h2 { color: #93c5fd; font-size: 1.3rem; margin-top: 30px; }
        p, li { line-height: 1.8; color: #cbd5e1; }
        a { color: #60a5fa; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #60a5fa; }
        ul { padding-left: 20px; }
        li { margin-bottom: 8px; }
        .last-updated { color: #64748b; font-size: 0.9rem; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="back-link">&larr; Back to Home</a>
        <h1>Terms of Service</h1>
        <p class="last-updated">Last updated: {{ date('F d, Y') }}</p>

        <h2>1. Acceptance of Terms</h2>
        <p>By accessing and using the Columban College Security System, you agree to be bound by these Terms of Service. If you do not agree, please do not use the system.</p>

        <h2>2. User Accounts</h2>
        <ul>
            <li>You must provide accurate information when creating an account.</li>
            <li>You are responsible for maintaining the confidentiality of your password.</li>
            <li>You must not share your account credentials with others.</li>
            <li>You must notify us immediately of any unauthorized use of your account.</li>
        </ul>

        <h2>3. Acceptable Use</h2>
        <p>You agree to use the system only for its intended purpose: campus security and access management. You must not:</p>
        <ul>
            <li>Use the system for any unlawful purpose.</li>
            <li>Attempt to access unauthorized areas of the system.</li>
            <li>Interfere with or disrupt the system's functionality.</li>
            <li>Share your QR codes with unauthorized individuals.</li>
            <li>Provide false information during registration or check-in.</li>
        </ul>

        <h2>4. QR Code Policy</h2>
        <ul>
            <li>QR codes are issued for your personal use only.</li>
            <li>Sharing QR codes with others is strictly prohibited.</li>
            <li>QR codes have expiration dates and must be renewed as needed.</li>
            <li>Unauthorized use of QR codes may result in account suspension.</li>
        </ul>

        <h2>5. Privacy</h2>
        <p>Your use of the system is also governed by our <a href="{{ route('privacy') }}">Privacy Policy</a>. By using the system, you consent to the collection and use of your data as described therein.</p>

        <h2>6. Termination</h2>
        <p>We reserve the right to suspend or terminate your access to the system at any time, without prior notice, for conduct that we determine violates these terms or is harmful to other users or the system.</p>

        <h2>7. Limitation of Liability</h2>
        <p>The Columban College Security System is provided "as is" without warranties of any kind. We are not liable for any damages arising from the use of this system.</p>

        <h2>8. Changes to Terms</h2>
        <p>We may update these Terms of Service from time to time. Continued use of the system after changes constitutes acceptance of the new terms.</p>

        <h2>9. Contact</h2>
        <p>For questions about these Terms, please contact the Columban College Security System administrator.</p>
    </div>
</body>
</html>
