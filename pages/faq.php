<?php
$faqs=[
['Do you do flowers and décor?','Yes. We do both—from bridal bouquets and ceremony flowers to table styling, backdrops, and floral installations.'],
['Can I share my Pinterest or moodboard?','Absolutely. We would love to see your ideas and inspiration.'],
['Can I request specific flowers?','Of course. Let us know your favourites, and we will suggest beautiful alternatives when a variety is out of season.'],
['Can you work with my budget?','Yes. Share your budget and priorities, and we will create the best design within them.'],
['How far in advance should I book?','We recommend booking 3–6 months ahead, especially for popular wedding dates.'],
['Do you work with wedding planners?','Yes. We love working with wedding planners and trusted vendors to make every detail run smoothly.'],
['Do you work with international couples?','Yes. We work with couples from around the world planning their dream wedding in Bali.'],
['How do I secure my date?','Once you are happy with the proposal, a 50% deposit secures your date and begins the detailed design process.'],
['When do we get the moodboard?','Once the deposit is received, we start creating your detailed moodboard and design.'],
['Do you handle setup?','Yes. Our team takes care of the floral and décor setup on your wedding day.'],
['What if my favourite flowers are not available?','No worries. We will suggest beautiful alternatives that still match your colour palette and overall design.'],
['How do I get started?','Send us your wedding date, venue, and ideas—and let’s create something beautiful together.']
]; page_head('Frequently Asked Questions | Ellens Florist','Answers to common questions about wedding flowers and décor with Ellens Florist.','faq'); ?>
<main id="main"><section class="page-hero"><p class="eyebrow">Helpful answers</p><h1>Frequently asked questions.</h1><p>Everything you need to know before we begin creating your celebration.</p></section><section class="section"><div class="accordion"><?php foreach($faqs as [$question,$answer]):?><details><summary><?=e($question)?></summary><p><?=e($answer)?></p></details><?php endforeach?></div></section></main><?php page_footer(); ?>
