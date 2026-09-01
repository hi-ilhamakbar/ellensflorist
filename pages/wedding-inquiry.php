<?php
require ROOT_PATH . '/app/mailer.php';
$sent = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (!rate_limit('inquiry', 4)) {
        $error = 'Please wait before submitting another inquiry.';
    } elseif ((int) ($_POST['captcha'] ?? -1) !== ($_SESSION['captcha_answer'] ?? -2)) {
        $error = 'The security answer was incorrect. Please try again.';
    } elseif (empty($_POST['bride_name']) || !filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) || empty($_POST['phone'])) {
        $error = 'Please provide the bride’s name, a valid email address, and phone number.';
    } else {
        try {
            $file = null;
            if (!empty($_FILES['inspiration']['name'])) {
                $image = @getimagesize($_FILES['inspiration']['tmp_name']);
                $ok = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
                $type = $image[2] ?? null;
                if (!is_uploaded_file($_FILES['inspiration']['tmp_name']) || !isset($ok[$type]) || $_FILES['inspiration']['size'] > 5 * 1024 * 1024) {
                    throw new RuntimeException('Upload a JPG, PNG, or WebP image under 5 MB.');
                }
                $name = bin2hex(random_bytes(16)) . '.' . $ok[$type];
                $dir = ROOT_PATH . '/storage/uploads';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                move_uploaded_file($_FILES['inspiration']['tmp_name'], $dir . '/' . $name);
                $file = 'storage/uploads/' . $name;
            }
            $budget = trim($_POST['currency']) . ' ' . trim($_POST['budget']);
            $q = db()->prepare('INSERT INTO inquiries (bride_name,groom_name,email,phone,wedding_date,venue,guests,decoration_style,flower_preference,budget,contact_method,notes,inspiration_path) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $q->execute([
                trim($_POST['bride_name']), trim($_POST['groom_name']), trim($_POST['email']), trim($_POST['phone']),
                $_POST['wedding_date'] ?: null, trim($_POST['venue']), $_POST['guests'] ?: null, trim($_POST['style']),
                trim($_POST['flowers']), $budget, $_POST['contact_method'], trim($_POST['notes']), $file,
            ]);
            notify_submission('Wedding inquiry received - Ellens Florist', [
                'Bride' => $_POST['bride_name'], 'Groom' => $_POST['groom_name'], 'Email' => $_POST['email'],
                'Phone' => $_POST['phone'], 'Wedding date' => $_POST['wedding_date'], 'Venue' => $_POST['venue'],
                'Budget' => $budget, 'Notes' => $_POST['notes'],
            ], $_POST['email']);
            $sent = true;
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $error = 'We could not save your inquiry. Please check the database configuration, then try again.';
        }
    }
}
[$a, $op, $b] = captcha();
page_head('Wedding Inquiry | Ellens Florist', 'Tell us about your celebration and begin planning your bespoke wedding flowers.', 'wedding-inquiry');
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/css/intlTelInput.css">
<main id="main">
    <section class="page-hero">
        <p class="eyebrow">Begin your floral story</p>
        <h1>Tell us about the day you’re dreaming of.</h1>
        <p>Share the details below and our team will be in touch during customer service hours (09:00–20:00 WITA).</p>
    </section>
    <section class="section tinted">
        <div class="form-wrap">
            <?php if ($sent): ?>
                <div class="alert"><strong>Thank you—your inquiry is with us.</strong> We’ll be in touch soon with the next steps.</div>
            <?php else: ?>
                <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf" value="<?= csrf() ?>">
                    <div class="form-grid">
                        <p class="full"><strong>Your celebration</strong></p>
                        <p><label for="bride_name">Bride’s name *</label><input required id="bride_name" name="bride_name" autocomplete="name"></p>
                        <p><label for="groom_name">Groom’s name</label><input id="groom_name" name="groom_name"></p>
                        <p><label for="email">Email *</label><input required type="email" id="email" name="email" autocomplete="email"></p>
                        <p><label for="phone">Phone number *</label><input required id="phone" name="phone" inputmode="tel" autocomplete="tel"></p>
                        <p><label for="wedding_date">Wedding date</label><input type="date" id="wedding_date" name="wedding_date" min="<?= date('Y-m-d') ?>"></p>
                        <p><label for="venue">Wedding venue</label><input id="venue" name="venue"></p>
                        <p><label for="guests">Estimated guests</label><input type="number" min="1" id="guests" name="guests"></p>
                        <p><label for="style">Decoration style</label><select id="style" name="style"><option value="">Select a style</option><option>Classic romance</option><option>Modern minimal</option><option>Garden-inspired</option><option>Editorial luxury</option><option>Not sure yet</option></select></p>
                        <p><label for="flowers">Flower preferences</label><input id="flowers" name="flowers" placeholder="e.g. roses, orchids, peonies"></p>
                        <p><label for="budget">Estimated budget</label><span style="display:flex;gap:6px"><select name="currency" style="width:94px"><option value="IDR" selected>IDR</option><option value="USD">USD</option><option value="EUR">EUR</option><option value="GBP">GBP</option></select><input id="budget" name="budget" placeholder="Your comfortable range" inputmode="numeric" autocomplete="off"></span></p>
                        <p><label for="contact_method">Preferred contact method</label><select id="contact_method" name="contact_method"><option>WhatsApp</option><option>Email</option><option>Phone call</option></select></p>
                        <p><label for="inspiration">Inspiration photos (optional)</label><input type="file" accept="image/jpeg,image/png,image/webp" id="inspiration" name="inspiration"><small class="help">JPG, PNG or WebP · 5 MB maximum</small></p>
                        <p class="full"><label for="notes">Anything else we should know?</label><textarea id="notes" name="notes" placeholder="Tell us about the feeling you want to create."></textarea></p>
                        <p><label for="captcha">Security: <?= e("$a $op $b") ?> = ? *</label><input required type="number" id="captcha" name="captcha" inputmode="numeric"></p>
                        <p class="full"><button class="button gold" type="submit">Send my inquiry</button></p>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </section>
</main>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/intlTelInputWithUtils.min.js"></script>
<script>
window.addEventListener('load', function () {
    var phone = document.querySelector('#phone');
    if (phone) window.intlTelInput(phone, { initialCountry: 'id', separateDialCode: true });
    var budget = document.querySelector('#budget');
    var currency = document.querySelector('[name="currency"]');
    if (!budget || !currency) return;
    function formatBudget() {
        var digits = budget.value.replace(/\D/g, '');
        if (!digits) { budget.value = ''; return; }
        var locale = { IDR: 'id-ID', USD: 'en-US', EUR: 'de-DE', GBP: 'en-GB' }[currency.value] || 'en-US';
        budget.value = new Intl.NumberFormat(locale, { maximumFractionDigits: 0 }).format(Number(digits));
    }
    budget.addEventListener('input', formatBudget);
    currency.addEventListener('change', formatBudget);
});
</script>
<?php page_footer(); ?>
