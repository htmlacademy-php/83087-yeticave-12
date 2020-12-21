<main class="container">
    <section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">
            <?php
            $indexСategories = 0;
            $numСategories = count($categories);
            while ($indexСategories < $numСategories) : ?>
                <li class="promo__item promo__item--boards">
                    <a class="promo__link" href="pages/all-lots.html"><?= strip_tags($categories[$indexСategories]); ?></a>
                </li>
                <?php $indexСategories++; ?>
            <?php endwhile; ?>
        </ul>
    </section>
    <section class="lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <ul class="lots__list">
            <?php foreach ($announcement as $key => $value) : ?>
                <li class="lots__item lot">
                    <div class="lot__image">
                        <img src="<?= strip_tags($value['image_url']); ?>" width="350" height="260" alt="">
                    </div>
                    <div class="lot__info">
                        <span class="lot__category"><?= strip_tags($value['category']); ?></span>
                        <h3 class="lot__title"><a class="text-link" href="pages/lot.html"><?= strip_tags($value['name']); ?></a></h3>
                        <div class="lot__state">
                            <div class="lot__rate">
                                <span class="lot__amount">Стартовая цена</span>
                                <span class="lot__cost"><?= formatPrice(strip_tags($value['price'])); ?></span>
                            </div>
                            <?php
                            $data = getDifferenceTime($value['lot_date']);
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
