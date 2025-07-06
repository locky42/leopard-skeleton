<?php

/**
 * @var \App\Core\View $this
 */
$this->addStyle('/assets/css/global.css');
$this->addStyle('/assets/css/footer.css');
$this->addScript('/assets/js/global.js');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Default Title' ?></title>

    <!-- Стилі -->
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

    <!-- Скрипти -->
    <?php foreach ($this->getScripts() as $script): ?>
        <script src="<?= $script ?>"></script>
    <?php endforeach; ?>
</body>
</html>
