# Ellen’s Florist deployment

1. Create a MySQL database and user in cPanel, then import `database/schema.sql` with phpMyAdmin.
2. Copy `.env.example` to `.env`; set the application URL, a long `APP_KEY`, database values, and SMTP values. Never commit `.env`.
3. Run `composer install --no-dev --optimize-autoloader`, or upload its resulting `vendor` folder. This installs PHPMailer for SSL SMTP.
4. Point the domain at this directory and enable Apache `mod_rewrite` and PHP 8.1+.
5. Run `scripts/create_admin.php` once in a private session, then permanently delete it. Change the supplied starter passwords immediately.
6. Make `storage/uploads` writable by PHP (normally 0755).

Before launch: replace placeholders with licensed compressed WebP images, configure a real business address, verify both SMTP recipients, test forms/keyboard navigation/mobile/HTTPS, and enable cPanel backups.

Security included: prepared statements, CSRF, output escaping, validation, secure sessions, rate limits, password hashing, file type/size checks, CAPTCHA, and secure SMTP. Maintain HTTPS, patch PHP/dependencies, restrict `.env`, rotate credentials, and use a least-privilege database user.
