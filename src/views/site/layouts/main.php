<?php

/**
 * @var Leopard\Core\View $this
 */
$this->addStyle('/assets/css/global.css');
$this->addStyle('/assets/css/footer.css');
$this->addStyle('/assets/css/header.css');
$this->addScript('/assets/js/global.js');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php if ($this->getSeo()): ?>
        <meta charset="<?= $this->getSeo()->getCharset() ?>">

        <?php foreach ($this->getSeo()->getMetaTags() as $metaName => $metaContent): ?>
            <meta name="<?= $metaName ?>" content="<?= $metaContent ?>">
        <?php endforeach; ?>

        <?php foreach ($this->getSeo()->getOpenGraphTags() as $ogProperty => $ogContent): ?>
            <meta property="og:<?= $ogProperty ?>" content="<?= $ogContent ?>">
        <?php endforeach; ?>

        <?php if ($this->getSeo()->getTwitterCards()): ?>
            <?php foreach ($this->getSeo()->getTwitterCards() as $tcName => $tcContent): ?>
                <meta name="twitter:<?= $tcName ?>" content="<?= $tcContent ?>">
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($this->getSeo()->getTitle()): ?>
            <title><?= $this->getSeo()->getTitle() ?></title>
        <?php endif; ?>

        <?php if ($this->getSeo()->getDescription()): ?>
            <meta name="description" content="<?= $this->getSeo()->getDescription() ?>">
        <?php endif; ?>

        <?php if ($this->getSeo()->getCanonicalUrl()): ?>
            <link rel="canonical" href="<?= $this->getSeo()->getCanonicalUrl() ?>">
        <?php endif; ?>

        <?php if ($this->getSeo()->getKeywords()): ?>
            <meta name="keywords" content="<?= implode(', ', $this->getSeo()->getKeywords()) ?>">
        <?php endif; ?>

        <?php if ($this->getSeo()->getRobots()): ?>
            <meta name="robots" content="<?= $this->getSeo()->getRobots() ?>">
        <?php endif; ?>
    <?php endif; ?>

    <?php foreach ($this->getStyles() as $style): ?>
        <link rel="stylesheet" href="<?= $style ?>">
    <?php endforeach; ?>
</head>
<body>
    <?= $this->renderBlock('header', ['title' => $title ?? 'Default Title']) ?>

    <main class="container">
        <?= $content ?>
    </main>

    <?= $this->renderBlock('footer') ?>

    <?php foreach ($this->getScripts() as $script): ?>
        <script src="<?= $script ?>"></script>
    <?php endforeach; ?>
</body>
</html>
