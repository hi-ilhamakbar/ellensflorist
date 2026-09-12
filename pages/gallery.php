<?php
page_head('Gallery | Ellens Florist', 'Browse Ellens Florist wedding flowers and elegant event decoration.', 'gallery');
$items = published_content('gallery');
if (!$items) {
    $items = [
        ['title' => 'Mega & Will Ceremony', 'image_path' => '/assets/images/mega-will-ceremony.webp'],
        ['title' => 'Mega & Will Reception', 'image_path' => '/assets/images/mega-will-reception.webp'],
        ['title' => 'Mega & Will Floral Details', 'image_path' => '/assets/images/mega-will-details.webp'],
        ['title' => 'Mega & Will Tablescape', 'image_path' => '/assets/images/mega-will-table.webp'],
        ['title' => 'Mega & Will Celebration', 'image_path' => '/assets/images/mega-will-dance.webp'],
        ['title' => 'Jisoo & Sabrina Bridal Flowers', 'image_path' => '/assets/images/jisoo-sabrina-ceremony.webp'],
        ['title' => 'Jisoo & Sabrina Wedding Moment', 'image_path' => '/assets/images/jisoo-sabrina-reception.webp'],
    ];
}
?>
<main id="main">
    <section class="page-hero"><p class="eyebrow">Our portfolio</p><h1>Moments in bloom.</h1><p>A glimpse of the atmosphere, feeling, and details we love to create.</p></section>
    <section class="section">
        <div class="gallery gallery-portfolio">
            <?php foreach ($items as $item): ?>
                <article><div class="image-block content-image" style="background-image:url('<?= e(real_image_path($item['image_path'])) ?>')" role="img" aria-label="<?= e($item['title']) ?>"></div></article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php page_footer(); ?>
