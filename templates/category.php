<main>
    <nav class="nav">
        <ul class="nav__list container">
            <?php
            foreach ($categories as $category) : ?>
                <li class="nav__item">
                    <a href="/category.php?code=<?= stripTags($category['code']); ?>"><?= stripTags($category['name']); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <div class="container">
        <section class="lots">
            <h2>Все лоты в категории <span>«<?= $categoryName; ?>»</span></h2>
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
        <?php
        if ($pagesTotal > 1) : ?>
            <ul class="pagination-list">
                <?php
                if ($currentCategoryPage > 1) : ?>
                    <li class="pagination-item pagination-item-prev"><a href="/category.php?code=<?= $trurl; ?>&page=<?= $currentCategoryPage - 1 ?>">Назад</a></li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $pagesTotal; $i = $i + 1) : ?>
                    <li class="pagination-item <?= $i === $currentCategoryPage ? 'pagination-item-active' : '' ?>">
                        <a href="/category.php?code=<?= $trurl; ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <?php
                if ($pagesTotal > $currentCategoryPage) : ?>
                    <li class="pagination-item pagination-item-next"><a href="/category.php?code=<?= $trurl; ?>&page=<?= $currentCategoryPage + 1 ?>">Вперед</a></li>
                <?php endif;
                ?>
            </ul>
        <?php endif;
        ?>
    </div>
</main>
