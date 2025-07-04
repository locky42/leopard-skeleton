<!-- filepath: /home/user/Projects/leopard-skeleton/views/site/layouts/main.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Site Title' ?></title>
</head>
<body>
    <?= $this->renderBlock('header', ['title' => $title ?? 'Site Title']) ?>

    <main>
        <?= $content ?>
    </main>

    <?= $this->renderBlock('footer') ?>
</body>
</html>