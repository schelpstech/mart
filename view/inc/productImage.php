<?php
// Callers supply HTML-escaped card values. Resolve only files inside the product image directory.
$visualName = trim(html_entity_decode($productName ?? 'Product', ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?: 'Product';
$visualFilename = html_entity_decode($productImage ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
$visualRoot = realpath(__DIR__ . '/../assets/images/product/main');
$visualPath = $visualFilename !== '' && $visualRoot ? realpath($visualRoot . DIRECTORY_SEPARATOR . $visualFilename) : false;
$visualPrefix = $visualRoot . DIRECTORY_SEPARATOR;
$visualAvailable = $visualPath && is_file($visualPath) && strncasecmp($visualPath, $visualPrefix, strlen($visualPrefix)) === 0;
$visualUrl = $visualAvailable ? 'assets/images/product/main/' . implode('/', array_map('rawurlencode', explode('/', str_replace('\\', '/', $visualFilename)))) : '';
$visualEscape = static function ($value) { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
?>
<a href="viewproduct.php?id=<?= (int)$productId; ?>" class="image qs-product-visual<?= $visualAvailable ? '' : ' is-placeholder'; ?>" aria-label="<?= $visualEscape($visualName); ?>">
    <span class="qs-product-placeholder" aria-hidden="true">
        <span class="qs-product-symbol tone-<?= (int)$productId % 3; ?>"><?= $visualEscape(mb_strtoupper(mb_substr($visualName, 0, 1, 'UTF-8'), 'UTF-8')); ?><small>✦</small></span>
        <span class="qs-product-image-caption">Image unavailable</span>
    </span>
    <?php if ($visualAvailable): ?>
        <img class="main-image" src="<?= $visualEscape($visualUrl); ?>" alt="<?= $visualEscape($visualName); ?>" data-product-image decoding="async" />
        <img class="hover-image" src="<?= $visualEscape($visualUrl); ?>" alt="" aria-hidden="true" decoding="async" />
    <?php endif; ?>
</a>
