<?php if (session()->has('errors')) : ?>
    <div class="error-div" role="alert">
        <?php if (is_array(session('errors'))) : ?>
            <ul>
                <?php foreach (session('errors') as $error) : ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
            </ul>
        <?php else : ?>
            <ul>
                <li><?= esc(session('errors')) ?></li>
            </ul>
        <?php endif ?>
    </div>
<?php endif; ?>

<?php if (session()->has('success')) : ?>
    <div class="success-div" role="alert">
        <p><?= esc(session('success')) ?></p>
    </div>
<?php endif; ?>

<!-- Hidden to be access through javascript -->
<div class="hidden success-div" role="alert" id="success-div">

</div>
<div class="hidden error-div" role="alert" id="error-div">

</div>