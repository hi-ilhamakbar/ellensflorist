<?php
$faqs = [
    ['Do you do flowers and décor?', 'Yes! We do both 🌸 From bridal bouquets and ceremony flowers to table styling, backdrops, and floral installations.'],
    ['Can I share my Pinterest or moodboard?', 'Absolutely! We’d love to see your ideas and inspiration.'],
    ['Can I request specific flowers?', 'Of course! Just let us know your favorites. We’ll also suggest the best alternatives if they’re not in season.'],
    ['Can you work with my budget?', 'Yes! Let us know your budget and we’ll help create the best design based on your priorities.'],
    ['How far in advance should I book?', 'We recommend booking <strong>3–6 months ahead</strong>, especially for popular wedding dates.'],
    ['Do you work with wedding planners?', 'Yes! We love working together with wedding planners and other vendors to make everything run smoothly.'],
    ['Do you work with international couples?', 'Yes! We work with couples from all over the world planning their dream wedding in Bali.'],
    ['How do I secure my date?', 'Once you’re happy with the proposal, a <strong>50% deposit</strong> is required to secure your date and start the detailed design process.'],
    ['When do we get the moodboard?', 'Once the deposit is received, we’ll start working on your detailed moodboard and design.'],
    ['Do you handle setup?', 'Yes! Our team will take care of the floral and décor setup on your wedding day.'],
    ["What if my favorite flowers aren't available?", 'No worries! We’ll suggest beautiful alternatives that still match your color palette and overall design.'],
    ['How do I get started?', 'Just send us your <strong>wedding date, venue, and ideas</strong>—and let’s create something beautiful together! ✨'],
];

page_head('FAQs | Ellens Florist', 'FAQs | Ellens Florist', 'faq');
?>
<main id="main">
    <section class="page-hero"><p class="eyebrow">FAQs</p><h1>FAQs</h1></section>
    <section class="section"><div class="accordion">
        <?php foreach ($faqs as [$question, $answer]): ?>
            <details><summary><strong><?= e($question) ?></strong></summary><p><?= $answer ?></p></details>
        <?php endforeach; ?>
    </div></section>
</main>
<?php page_footer(); ?>
