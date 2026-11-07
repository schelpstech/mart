<?php
$categoryVisualName = (string)($cat['category_name'] ?? 'Category');
$categoryVisualIcon = ltrim(str_replace('\\', '/', (string)($cat['icon'] ?? '')), '/');
$categoryVisualRoot = realpath(__DIR__ . '/../assets/images');
$categoryVisualPath = $categoryVisualIcon !== '' && $categoryVisualRoot ? realpath($categoryVisualRoot . DIRECTORY_SEPARATOR . $categoryVisualIcon) : false;
$categoryVisualPrefix = $categoryVisualRoot . DIRECTORY_SEPARATOR;
$categoryVisualAvailable = $categoryVisualPath && is_file($categoryVisualPath) && strncasecmp($categoryVisualPath, $categoryVisualPrefix, strlen($categoryVisualPrefix)) === 0;
$categoryVisualEscape = static function ($value) { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
?>
<span class="qs-category-visual<?= $categoryVisualAvailable ? '' : ' is-placeholder'; ?>" aria-hidden="true">
    <span class="qs-category-symbol tone-<?= (int)$cat['categoryTbl_id'] % 3; ?>"><?= $categoryVisualEscape(mb_strtoupper(mb_substr(trim($categoryVisualName), 0, 1, 'UTF-8'), 'UTF-8')); ?><small>✦</small></span>
    <?php if ($categoryVisualAvailable): ?><img src="assets/images/<?= $categoryVisualEscape(implode('/', array_map('rawurlencode', explode('/', $categoryVisualIcon)))); ?>" alt="" data-category-image decoding="async"><?php endif; ?>
</span>
