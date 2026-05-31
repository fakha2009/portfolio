<?php

declare(strict_types=1);

cv_partial('admin/partials/flash-stack', ['flashes' => $flashes ?? []]);
$sectionFile = cv_root('templates/admin/sections/' . $section . '.php');
if (is_file($sectionFile)) {
    require $sectionFile;
} else {
    echo '<div class="admin-card"><p>Section template not found.</p></div>';
}
