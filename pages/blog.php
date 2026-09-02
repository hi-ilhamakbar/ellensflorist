<?php
page_head('Floral Journal | Ellens Florist', 'Wedding flower inspiration, floral design ideas, and stories from Ellens Florist.', 'blog');

$posts = published_content('post');
if (!$posts) {
    $posts = [
        ['slug' => 'choosing-flowers-for-your-wedding-season', 'title' => 'Choosing flowers for your wedding season', 'excerpt' => 'A thoughtful guide to a floral palette that feels naturally beautiful, whatever the time of year.', 'image_path' => '/assets/images/cassy-ceremony.webp'],
        ['slug' => 'how-to-make-reception-tables-memorable', 'title' => 'How to make reception tables memorable', 'excerpt' => 'The small floral and styling decisions that make a reception feel generous and unforgettable.', 'image_path' => '/assets/images/cassy-reception.webp'],
        ['slug' => 'a-considered-bridal-bouquet', 'title' => 'Our guide to a considered bridal bouquet', 'excerpt' => 'Find the shape, flower mix, and personality that feel entirely like you.', 'image_path' => '/assets/images/cassy-bouquet.webp'],
    ];
}
?>
<main id="main">
    <section class="page-hero"><p class="eyebrow">The floral journal</p><h1>Ideas, stories, and a little flower magic.</h1></section>
    <section class="section">
        <div class="post-list">
            <?php foreach ($posts as $post): ?>
                <article class="post">
                    <div class="image-block content-image" style="background-image:url('<?= e(real_image_path($post['image_path'])) ?>')"></div>
                    <div>
                        <p class="eyebrow">Ellens Florist journal</p>
                        <h2><?= e($post['title']) ?></h2>
                        <p><?= e($post['excerpt']) ?></p>
                        <a href="/blog/<?= e($post['slug']) ?>">Read more <span aria-hidden="true">→</span></a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php page_footer(); ?>
