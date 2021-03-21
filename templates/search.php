<main>
    <nav class="nav">
        <ul class="nav__list container">
            <?php
            foreach ($categories as $category) : ?>
                <li class="nav__item">
                    <a href="/pages/all-lots.html"><?= stripTags($category['name']); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <div class="container">
        <section class="lots">
            <h2>Результаты поиска по запросу «<span><?= stripTags($_GET['search']); ?></span>»</h2>
            <?php if ($lots) : ?>
                <ul class="lots__list">
                    <?php foreach ($lots as $lot) : ?>
                        <li class="lots__item lot">
                            <div class="lot__image">
                                <img src="<?= stripTags($lot['image_url']); ?>" width="350" height="260" alt="<?= stripTags($lot['name']); ?>">
                            </div>
                            <div class="lot__info">
                                <span class="lot__category"><?= stripTags($lot['category']); ?></span>
                                <h3 class="lot__title"><a class="text-link" href="/lot.php?id=<?= $lot['id']; ?>"><?= stripTags($lot['name']); ?></a></h3>
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
            <?php else : ?>
                <p><?= $errors; ?></p>
            <?php endif; ?>
        </section>
        <ul class="pagination-list">
            <!-- <?php foreach ($pages as $page) : ?>
                <li class="pagination-item"><a href="#">2</a></li>
            <?php endforeach; ?> -->
            <li class="pagination-item pagination-item-prev"><a>Назад</a></li>
            <li class="pagination-item pagination-item-active"><a>1</a></li>
            <li class="pagination-item"><a href="#">2</a></li>
            <li class="pagination-item"><a href="#">3</a></li>
            <li class="pagination-item"><a href="#">4</a></li>
            <li class="pagination-item pagination-item-next"><a href="#">Вперед</a></li>
        </ul>
    </div>
</main>
