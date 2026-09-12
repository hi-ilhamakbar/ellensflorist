<?php
$fallback_posts = [
    'choosing-flowers-for-your-wedding-season' => [
        'title' => 'Choosing flowers for your wedding season',
        'excerpt' => 'A thoughtful guide to a floral palette that feels naturally beautiful, whatever the time of year.',
        'image_path' => '/assets/images/mega-will-ceremony.webp',
        'body' => [
            'The most memorable wedding flowers feel at home in their setting. Rather than chasing a single bloom, begin with the mood you want guests to feel: relaxed garden romance, polished city celebration, or a candlelit dinner that unfolds slowly into the evening.',
            'Seasonality gives a design its ease. It guides the flower varieties, colour depth, and movement in each arrangement. During your consultation, we look at your venue, ceremony time, dress details, and the experience you want to create before we build a palette around what is at its best.',
            'Use a few meaningful floral moments to carry the story throughout the day: a bouquet with a personal silhouette, ceremony flowers that frame the vows, and reception arrangements that invite conversation without getting in the way. Repeating a colour, texture, or flower variety makes every space feel considered and connected.',
        ],
    ],
    'how-to-make-reception-tables-memorable' => [
        'title' => 'How to make reception tables memorable',
        'excerpt' => 'The small floral and styling decisions that make a reception feel generous and unforgettable.',
        'image_path' => '/assets/images/mega-will-reception.webp',
        'body' => [
            'Reception tables are where guests settle in, share stories, and take in the atmosphere you have created. The most successful styling feels generous from every seat, while leaving room for the food, conversation, and small personal details that make the evening yours.',
            'Begin with proportion. Long tables often suit a rhythm of low arrangements, candles, and textural pieces, while round tables can hold one sculptural floral focal point. We balance height carefully so the room feels layered without blocking sightlines across the table.',
            'Consider the light as part of the flowers. Soft candlelight warms ivory petals and rich foliage, while a daylight celebration can hold a looser, airier palette. The final effect comes from the way florals, linen, glassware, and place settings speak to one another.',
        ],
    ],
    'a-considered-bridal-bouquet' => [
        'title' => 'Our guide to a considered bridal bouquet',
        'excerpt' => 'Find the shape, flower mix, and personality that feel entirely like you.',
        'image_path' => '/assets/images/jisoo-sabrina-ceremony.webp',
        'body' => [
            'A bridal bouquet is a small but defining part of your wedding look. It should feel like an extension of you: considered, comfortable to hold, and in harmony with the shape and detail of your dress.',
            'We start with silhouette. A compact rounded bouquet can feel polished and timeless, while a garden-inspired design has more movement and an effortless sense of romance. From there, we layer flower varieties, foliage, and ribbon to create something that photographs beautifully from every angle.',
            'Bring the details you love to your consultation, whether that is a fabric swatch, a saved image, or simply a colour that feels right. We will translate the feeling—not copy a formula—and design a bouquet that is unmistakably yours.',
        ],
    ],
];

$post = $fallback_posts[$post_slug] ?? null;
try {
    $query = db()->prepare('SELECT title, slug, excerpt, body, image_path FROM content WHERE type=? AND slug=? AND status="published" LIMIT 1');
    $query->execute(['post', $post_slug]);
    $stored_post = $query->fetch();
    if ($stored_post) {
        $post = $stored_post;
        $post['body'] = preg_split('/\R{2,}/', (string) ($stored_post['body'] ?: $stored_post['excerpt']));
    }
} catch (Throwable $e) {
    // The public fallback posts remain available until the CMS database is configured.
}

if (!$post) {
    http_response_code(404);
    require ROOT_PATH . '/pages/404.php';
    return;
}

page_head($post['title'] . ' | Ellens Florist', $post['excerpt'], 'blog/' . $post_slug);
$related = array_filter($fallback_posts, static fn ($slug) => $slug !== $post_slug, ARRAY_FILTER_USE_KEY);
?>
<main id="main">
    <article class="journal-article">
        <header class="page-hero"><p class="eyebrow">Ellens Florist journal</p><h1><?= e($post['title']) ?></h1><p><?= e($post['excerpt']) ?></p></header>
        <div class="post-hero-image content-image" style="background-image:url('<?= e(real_image_path($post['image_path'])) ?>')" role="img" aria-label="<?= e($post['title']) ?>"></div>
        <div class="journal-copy">
            <?php foreach ($post['body'] as $paragraph): ?><p><?= nl2br(e(trim($paragraph))) ?></p><?php endforeach; ?>
            <p><a class="button gold" href="/wedding-inquiry">Plan your wedding florals</a></p>
        </div>
    </article>
    <section class="section tinted related-posts">
        <div class="section-head"><p class="eyebrow">Continue reading</p><h2>More from the journal.</h2></div>
        <div class="post-list">
            <?php foreach ($related as $slug => $item): ?>
                <article class="post"><div class="image-block content-image" style="background-image:url('<?= e(real_image_path($item['image_path'])) ?>')"></div><div><h3><?= e($item['title']) ?></h3><p><?= e($item['excerpt']) ?></p><a href="/blog/<?= e($slug) ?>">Read more <span aria-hidden="true">→</span></a></div></article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php page_footer(); ?>
