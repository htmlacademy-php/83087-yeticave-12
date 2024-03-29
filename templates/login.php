<?php
$classname = isset($errors) ? "form--invalid" : "";
?>
<form class="form container <?= $classname; ?>" action="login.php" method="post">
    <?php if (!empty($errors)) : ?>
        <span class="form__error form__error--bottom">Вы ввели неверный email/пароль</span>
    <?php endif; ?>
    <h2>Вход</h2>
    <?php
    $classname = isset($errors['email']) ? "form__item--invalid" : "";
    ?>
    <div class="form__item <?= $classname; ?>">
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= getPostVal('email'); ?>" required="">
        <?php if (isset($errors['email'])) : ?>
            <span class="form__error"><?= $errors['email']; ?></span>
        <?php endif; ?>
    </div>
    <?php
    $classname = isset($errors['password']) ? "form__item--invalid" : "";
    ?>
    <div class="form__item form__item--last <?= $classname; ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" required="">
        <?php if (isset($errors['password'])) : ?>
            <span class="form__error"><?= $errors['password']; ?></span>
        <?php endif; ?>
    </div>
    <button type="submit" class="button">Войти</button>
</form>
