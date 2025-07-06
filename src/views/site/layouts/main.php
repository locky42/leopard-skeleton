<!-- filepath: /home/user/Projects/leopard-skeleton/views/site/layouts/main.php -->
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

    <main>
        <?= $content ?>
    </main>

    <?= $this->renderBlock('footer') ?>

    <!-- Скрипти -->
    <?php foreach ($this->getScripts() as $script): ?>
        <script src="<?= $script ?>"></script>
    <?php endforeach; ?>
</body>
</html>