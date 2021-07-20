<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
    <ul class="promo__list">
        <?php foreach ($categories as $category) : ?>
            <li class="promo__item promo__item--<?= strip_tags($category['code']); ?>">
                <a class="promo__link" href="/category.php?id=<?= strip_tags($category['id']); ?>&page=1">
                    <?= strip_tags($category['name']); ?>
                </a>
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
                    <img src="<?= strip_tags($lot['image_url']); ?>" width="350" height="260" alt="<?= strip_tags($lot['name']); ?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?= strip_tags($lot['category']); ?></span>
                    <h3 class="lot__title">
                        <a class="text-link" href="/lot.php?id=<?= strip_tags($lot['id']); ?>">
                            <?= strip_tags($lot['name']); ?>
                        </a>
                    </h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">
                                <?php rateQty($connection, $lot['id']); ?>
                            </span>
                            <span class="lot__cost">
                                <?= formatPrice(strip_tags($lot['current_price'])); ?>
                            </span>
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
