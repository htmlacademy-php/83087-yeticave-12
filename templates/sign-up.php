<main>
    <nav class="nav">
        <ul class="nav__list container">
            <?php foreach ($categories as $category) : ?>
                <li class="nav__item">
                    <a href="/category.php?id=<?= stripTags($category['id']); ?>"><?= stripTags($category['name']); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <?php
    $classname = isset($errors) ? "form--invalid" : "";
    ?>
    <form class="form container <?= $classname; ?>" action="sign-up.php" method="post" autocomplete="off">
        <h2>Регистрация нового аккаунта</h2>
        <?php
        $classname = isset($errors['email']) ? "form__item--invalid" : "";
        ?>
        <div class="form__item <?= $classname; ?>">
            <label for="email">E-mail <sup>*</sup></label>
            <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= getPostVal('email'); ?>">
            <?php if (isset($errors['email'])) : ?>
                <span class="form__error"><?= $errors['email']; ?></span>
            <?php endif; ?>
        </div>
        <?php
        $classname = isset($errors['password']) ? "form__item--invalid" : "";
        ?>
        <div class="form__item <?= $classname; ?>">
            <label for="password">Пароль <sup>*</sup></label>
            <input id="password" type="password" name="password" placeholder="Введите пароль">
            <?php if (isset($errors['password'])) : ?>
                <span class="form__error"><?= $errors['password']; ?></span>
            <?php endif; ?>
        </div>
        <?php
        $classname = isset($errors['name']) ? "form__item--invalid" : "";
        ?>
        <div class="form__item <?= $classname; ?>">
            <label for="name">Имя <sup>*</sup></label>
            <input id="name" type="text" name="name" placeholder="Введите имя" value="<?= getPostVal('name'); ?>">
            <?php if (isset($errors['name'])) : ?>
                <span class="form__error"><?= $errors['name']; ?></span>
            <?php endif; ?>
        </div>
        <?php
        $classname = isset($errors['message']) ? "form__item--invalid" : "";
        ?>
        <div class="form__item <?= $classname; ?>">
            <label for="message">Контактные данные <sup>*</sup></label>
            <textarea id="message" name="message" placeholder="Напишите как с вами связаться"><?= getPostVal('message'); ?></textarea>
            <?php if (isset($errors['message'])) : ?>
                <span class="form__error"><?= $errors['message']; ?></span>
            <?php endif; ?>
        </div>
        <?php if (isset($errors)) : ?>
            <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
        <?php endif; ?>
        <button type="submit" class="button">Зарегистрироваться</button>
        <a class="text-link" href="login.php">Уже есть аккаунт</a>
    </form>
</main>
