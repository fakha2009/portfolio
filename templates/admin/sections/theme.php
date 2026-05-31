<?php

declare(strict_types=1);

$practiceItems = $practice_block['items'] ?? [[], [], []];
$pricingDefaults = cv_block_defaults('pricing', $locale);
$pricingPlans = $pricing_block['plans'] ?? ($pricingDefaults['plans'] ?? [[], [], []]);
$pricingPlans = array_replace([[], [], []], array_slice((array) $pricingPlans, 0, 3));
?>
<form class="admin-card admin-form" method="post" action="<?= cv_admin_url('theme') . '?locale=' . urlencode((string) $locale) ?>" data-ajax-save>
    <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>">
    <div class="admin-card-head">
        <h2>Theme and content blocks</h2>
        <div class="locale-tabs">
            <a class="<?= $locale === 'ru' ? 'is-active' : '' ?>" href="<?= cv_admin_url('theme') ?>?locale=ru">RU</a>
            <a class="<?= $locale === 'en' ? 'is-active' : '' ?>" href="<?= cv_admin_url('theme') ?>?locale=en">EN</a>
        </div>
    </div>

    <div class="admin-subsection">
        <h3>Skills intro</h3>
        <div class="admin-form-grid">
            <label><span>Label</span><input type="text" name="skills_label" value="<?= cv_e((string) ($skills_intro_block['label'] ?? '')) ?>"></label>
            <label class="full"><span>Title</span><input type="text" name="skills_title" value="<?= cv_e((string) ($skills_intro_block['title'] ?? '')) ?>"></label>
            <label class="full"><span>Text</span><textarea name="skills_text" rows="3"><?= cv_e((string) ($skills_intro_block['text'] ?? '')) ?></textarea></label>
        </div>
    </div>

    <div class="admin-subsection">
        <h3>Services intro</h3>
        <div class="admin-form-grid">
            <label><span>Label</span><input type="text" name="services_label" value="<?= cv_e((string) ($services_intro_block['label'] ?? '')) ?>"></label>
            <label class="full"><span>Title</span><input type="text" name="services_title" value="<?= cv_e((string) ($services_intro_block['title'] ?? '')) ?>"></label>
            <label class="full"><span>Text</span><textarea name="services_text" rows="3"><?= cv_e((string) ($services_intro_block['text'] ?? '')) ?></textarea></label>
        </div>
    </div>

    <div class="admin-subsection">
        <h3>Portfolio intro</h3>
        <div class="admin-form-grid">
            <label><span>Label</span><input type="text" name="portfolio_label" value="<?= cv_e((string) ($portfolio_intro_block['label'] ?? '')) ?>"></label>
            <label class="full"><span>Title</span><input type="text" name="portfolio_title" value="<?= cv_e((string) ($portfolio_intro_block['title'] ?? '')) ?>"></label>
            <label class="full"><span>Text</span><textarea name="portfolio_text" rows="3"><?= cv_e((string) ($portfolio_intro_block['text'] ?? '')) ?></textarea></label>
        </div>
    </div>

    <div class="admin-subsection">
        <h3>Practice / experience</h3>
        <div class="admin-form-grid">
            <label class="full"><span>Section title</span><input type="text" name="practice_title" value="<?= cv_e((string) ($practice_block['title'] ?? '')) ?>"></label>
            <label class="full"><span>Intro text</span><textarea name="practice_intro" rows="3"><?= cv_e((string) ($practice_block['intro'] ?? '')) ?></textarea></label>
            <?php foreach ($practiceItems as $index => $item): ?>
                <label><span>Item <?= $index + 1 ?> title</span><input type="text" name="practice_item_<?= $index + 1 ?>_title" value="<?= cv_e((string) ($item['title'] ?? '')) ?>"></label>
                <label><span>Item <?= $index + 1 ?> text</span><textarea name="practice_item_<?= $index + 1 ?>_text" rows="3"><?= cv_e((string) ($item['text'] ?? '')) ?></textarea></label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="admin-subsection">
        <h3>Pricing / tariffs</h3>
        <div class="admin-form-grid">
            <label><span>Label</span><input type="text" name="pricing_label" value="<?= cv_e((string) ($pricing_block['label'] ?? '')) ?>"></label>
            <label class="full"><span>Title</span><input type="text" name="pricing_title" value="<?= cv_e((string) ($pricing_block['title'] ?? '')) ?>"></label>
            <label class="full"><span>Text</span><textarea name="pricing_text" rows="3"><?= cv_e((string) ($pricing_block['text'] ?? '')) ?></textarea></label>
            <?php foreach ($pricingPlans as $index => $plan): ?>
                <?php $planNumber = $index + 1; ?>
                <label><span>Plan <?= $planNumber ?> name</span><input type="text" name="pricing_plan_<?= $planNumber ?>_name" value="<?= cv_e((string) ($plan['name'] ?? '')) ?>"></label>
                <label><span>Plan <?= $planNumber ?> price</span><input type="text" name="pricing_plan_<?= $planNumber ?>_price" value="<?= cv_e((string) ($plan['price'] ?? '')) ?>"></label>
                <label class="full"><span>Plan <?= $planNumber ?> description</span><textarea name="pricing_plan_<?= $planNumber ?>_description" rows="3"><?= cv_e((string) ($plan['description'] ?? '')) ?></textarea></label>
                <label class="full"><span>Plan <?= $planNumber ?> features</span><textarea name="pricing_plan_<?= $planNumber ?>_features" rows="2"><?= cv_e((string) ($plan['features'] ?? '')) ?></textarea></label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="admin-subsection">
        <h3>Process block</h3>
        <div class="admin-form-grid">
            <label class="full"><span>Process title</span><input type="text" name="process_title" value="<?= cv_e((string) ($process_block['title'] ?? '')) ?>"></label>
            <?php foreach (($process_block['steps'] ?? [[], [], [], []]) as $index => $step): ?>
                <label><span>Step <?= $index + 1 ?> title</span><input type="text" name="process_step_<?= $index + 1 ?>_title" value="<?= cv_e((string) ($step['title'] ?? '')) ?>"></label>
                <label><span>Step <?= $index + 1 ?> text</span><input type="text" name="process_step_<?= $index + 1 ?>_text" value="<?= cv_e((string) ($step['text'] ?? '')) ?>"></label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="admin-subsection">
        <h3>Testimonials intro</h3>
        <div class="admin-form-grid">
            <label><span>Label</span><input type="text" name="testimonials_label" value="<?= cv_e((string) ($testimonials_intro_block['label'] ?? '')) ?>"></label>
            <label class="full"><span>Title</span><input type="text" name="testimonials_title" value="<?= cv_e((string) ($testimonials_intro_block['title'] ?? '')) ?>"></label>
            <label class="full"><span>Text</span><textarea name="testimonials_text" rows="3"><?= cv_e((string) ($testimonials_intro_block['text'] ?? '')) ?></textarea></label>
        </div>
    </div>

    <div class="admin-subsection">
        <h3>FAQ intro</h3>
        <div class="admin-form-grid">
            <label class="full"><span>FAQ title</span><input type="text" name="faq_title" value="<?= cv_e((string) ($faq_intro_block['title'] ?? '')) ?>"></label>
            <label class="full"><span>FAQ text</span><textarea name="faq_text" rows="3"><?= cv_e((string) ($faq_intro_block['text'] ?? '')) ?></textarea></label>
        </div>
    </div>

    <div class="admin-subsection">
        <h3>Contact block</h3>
        <div class="admin-form-grid">
            <label class="full"><span>Contact title</span><input type="text" name="contact_title" value="<?= cv_e((string) ($contact_block['title'] ?? '')) ?>"></label>
            <label class="full"><span>Contact text</span><textarea name="contact_text" rows="3"><?= cv_e((string) ($contact_block['text'] ?? '')) ?></textarea></label>
            <label><span>Success title</span><input type="text" name="success_title" value="<?= cv_e((string) ($contact_block['success_title'] ?? '')) ?>"></label>
            <label><span>Success text</span><input type="text" name="success_text" value="<?= cv_e((string) ($contact_block['success_text'] ?? '')) ?>"></label>
        </div>
    </div>
    <button class="admin-button admin-button-primary" type="submit"><?= cv_e(cv_t('actions.save')) ?></button>
</form>
