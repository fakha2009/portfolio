<?php

declare(strict_types=1);
?>
<section class="section section--tight page-hero">
    <div class="container">
        <span class="eyebrow">404</span>
        <h1>Page not found.</h1>
        <p>The route does not exist or has been moved. Use the main portfolio navigation to continue.</p>
        <div class="hero-actions">
            <a class="btn btn--primary" href="<?= cv_url('') ?>"><?= cv_e(cv_t('actions.home')) ?></a>
            <a class="btn btn--ghost" href="<?= cv_url('projects') ?>"><?= cv_e(cv_t('nav.projects')) ?></a>
        </div>
    </div>
</section>
