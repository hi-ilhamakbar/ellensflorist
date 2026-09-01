<?php
page_head('Gallery | Ellens Florist', 'Browse Ellens Florist wedding flowers and elegant event decoration.', 'gallery');
$items = published_content('gallery');
if (!$items) {
    $items = [
        ['title' => 'Ivory Garden Reception', 'image_path' => '/assets/images/temporary-wedding-reception.png'],
        ['title' => 'Blush Ceremony Moment', 'image_path' => '/assets/images/ceremony-arch.png'],
        ['title' => 'Golden Hour Tablescape', 'image_path' => '/assets/images/reception-tablescape.png'],
        ['title' => 'Modern Garden Romance', 'image_path' => '/assets/images/bridal-bouquet.png'],
        ['title' => 'Dinner Under Flowers', 'image_path' => '/assets/images/hero-wedding-florals.png'],
        ['title' => 'Cassy & Brian Welcome', 'image_path' => '/assets/images/cassy-ceremony.webp'],
        ['title' => 'Cassy & Brian Reception', 'image_path' => '/assets/images/cassy-reception.webp'],
        ['title' => 'Cassy & Brian Floral Details', 'image_path' => '/assets/images/cassy-details.webp'],
        ['title' => 'Cassy & Brian Bridal Bouquet', 'image_path' => '/assets/images/cassy-bouquet.webp'],
        ['title' => 'Cassy & Brian Table Styling', 'image_path' => '/assets/images/cassy-table.webp'],
    ];
}
?>
<main id="main">
    <section class="page-hero"><p class="eyebrow">Our portfolio</p><h1>Moments in bloom.</h1><p>A glimpse of the atmosphere, feeling, and details we love to create.</p></section>
    <section class="section">
        <div class="gallery gallery-portfolio">
            <?php foreach ($items as $item): ?>
                <article><div class="image-block content-image" style="background-image:url('<?= e($item['image_path']) ?>')" role="img" aria-label="<?= e($item['title']) ?>"></div></article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php page_footer(); ?>
