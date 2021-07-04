<?php
$classname = isset($errors) ? "form--invalid" : "";
?>
<form class="form form--add-lot container <?= $classname; ?>" action="add.php" method="POST" enctype="multipart/form-data">
    <h2>Добавление лота</h2>
    <div class="form__container-two">
        <?php
        $classname = isset($errors['lot-name']) ? "form__item--invalid" : "";
        ?>
        <div class="form__item <?= $classname; ?>">
            <label for="lot-name">Наименование <sup>*</sup></label>
            <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота" value="<?= getPostVal('lot-name'); ?>" required="">
            <?php if (isset($errors['lot-name'])) : ?>
                <span class="form__error"><?= $errors['lot-name']; ?></span>
            <?php endif; ?>
        </div>
        <?php
        $classname = isset($errors['category']) ? "form__item--invalid" : "";
        ?>
        <div class="form__item <?= $classname; ?>">
            <label for="category">Категория <sup>*</sup></label>
            <select id="category" name="category" required="">
                <option value="">Выберите категорию</option>
                <?php foreach ($categories as $category) : ?>
                    <option value="<?= $category['id']; ?>" <?php if ($category['id'] == getPostVal('category')) : ?>selected<?php endif; ?>><?= stripTags($category['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['category'])) : ?>
                <span class="form__error"><?= $errors['category']; ?></span>
            <?php endif; ?>
        </div>
    </div>
    <?php
    $classname = isset($errors['message']) ? "form__item--invalid" : "";
    ?>
    <div class="form__item form__item--wide <?= $classname; ?>">
        <label for="message">Описание <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите описание лота" required=""><?= getPostVal('message'); ?></textarea>
        <?php if (isset($errors['message'])) : ?>
            <span class="form__error"><?= $errors['message']; ?></span>
        <?php endif; ?>
    </div>
    <?php
    $classname = isset($errors['file']) ? "form__item--invalid" : "";
    ?>
    <div class="form__item form__item--file <?= $classname; ?>">
        <label>Изображение <sup>*</sup></label>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" id="lot-img" value="<?= getPostVal('file'); ?>" name="file" required="">
            <label for="lot-img">
                Добавить
            </label>
        </div>
        <?php if (isset($errors['file'])) : ?>
            <span class="form__error"><?= $errors['file']; ?></span>
        <?php endif; ?>
    </div>
    <div class="form__container-three">
        <?php
        $classname = isset($errors['lot-rate']) ? "form__item--invalid" : "";
        ?>
        <div class="form__item form__item--small <?= $classname; ?>">
            <label for="lot-rate">Начальная цена <sup>*</sup></label>
            <input id="lot-rate" type="text" name="lot-rate" placeholder="0" value="<?= getPostVal('lot-rate'); ?>" required="">
            <?php if (isset($errors['lot-rate'])) : ?>
                <span class="form__error"><?= $errors['lot-rate']; ?></span>
            <?php endif; ?>
        </div>
        <?php
        $classname = isset($errors['lot-step']) ? "form__item--invalid" : "";
        ?>
        <div class="form__item form__item--small <?= $classname; ?>">
            <label for="lot-step">Шаг ставки <sup>*</sup></label>
            <input id="lot-step" type="text" name="lot-step" placeholder="0" value="<?= getPostVal('lot-step'); ?>" required="">
            <?php if (isset($errors['lot-step'])) : ?>
                <span class="form__error"><?= $errors['lot-step']; ?></span>
            <?php endif; ?>
        </div>
        <?php
        $classname = isset($errors['lot-date']) ? "form__item--invalid" : "";
        ?>
        <div class="form__item <?= $classname; ?>">
            <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
            <input class="form__input-date" id="lot-date" type="text" name="lot-date" placeholder="Введите дату в формате ГГГГ-ММ-ДД" value="<?= getPostVal('lot-date'); ?>" required="">
            <?php if (isset($errors['lot-date'])) : ?>
                <span class="form__error"><?= $errors['lot-date']; ?></span>
            <?php endif; ?>
        </div>
    </div>
    <?php if (isset($errors)) : ?>
        <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <?php endif; ?>
    <button type="submit" class="button">Добавить лот</button>
</form>
