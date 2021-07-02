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
                            <span class="lot__category"><?= $lot['category']; ?></span>
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

    <?php if ($totalPages > 1) : ?>
        <ul class="pagination-list">
            <?php if ($currentSearchPage > 1) : ?>
                <li class="pagination-item pagination-item-prev"><a href="/search.php?search=<?= $searchedWord; ?>&page=<?= $currentSearchPage - 1 ?>">Назад</a></li>
            <?php else : ?>
                <li class="pagination-item pagination-item-prev">Назад</li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i = $i + 1) : ?>
                <li class="pagination-item <?= $i === $currentSearchPage ? 'pagination-item-active' : '' ?>">
                    <a href="/search.php?search=<?= $searchedWord; ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <?php if ($totalPages > $currentSearchPage) : ?>
                <li class="pagination-item pagination-item-next"><a href="/search.php?search=<?= $searchedWord; ?>&page=<?= $currentSearchPage + 1 ?>">Вперед</a></li>
            <?php else : ?>
                <li class="pagination-item pagination-item-next">Вперед</li>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
</div>
