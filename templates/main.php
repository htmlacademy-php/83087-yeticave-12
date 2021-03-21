<main class="container">
    <section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">
            <?php
            foreach ($categories as $category) : ?>
                <li class="promo__item promo__item--<?= stripTags($category['code']); ?>">
                    <a class="promo__link" href="/category.php?code=<?= stripTags($category['code']); ?>&page=1"><?= stripTags($category['name']); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class=" lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <ul class="lots__list">
            <?php foreach ($lots as $lot) : ?>
                <li class="lots__item lot">
                    <div class="lot__image">
                        <img src="<?= stripTags($lot['image_url']); ?>" width="350" height="260" alt="<?= stripTags($lot['name']); ?>">
                    </div>
                    <div class="lot__info">
                        <span class="lot__category"><?= stripTags($lot['category']); ?></span>
                        <h3 class="lot__title"><a class="text-link" href="/lot.php?id=<?= stripTags($lot['id']); ?>"><?= stripTags($lot['name']); ?></a></h3>
                        <div class="lot__state">
                            <div class="lot__rate">
                                <span class="lot__amount">Стартовая цена</span>
                                <span class="lot__cost"><?= formatPrice(stripTags($lot['price'])); ?></span>
                            </div>
                            <?php
                            $data = getDifferenceTime($lot['end_date']);
                            ?>
                            <div class="lot__timer timer <?php echo ($data[0] <= 0) ? 'timer--finishing' : ''; ?>">
                                <?php
                                echo $data[0] . ':' . $data[1];
                                ?>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</main>
