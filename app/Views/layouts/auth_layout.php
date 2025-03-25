<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Web Linux Interface' ?></title>
    <link rel="stylesheet" href="<?= base_url('styles/output.css') ?>">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <?= $this->renderSection('content') ?>

    <div class="text-center fixed bottom-4 w-full text-sm text-gray-500">
        <p>© <?= date('Y') ?> Web Linux Interface. All rights reserved.</p>
    </div>
</body>

</html>
