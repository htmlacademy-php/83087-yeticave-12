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
    <section class="lot-item container">
        <h2><?= $lots[0]['name']; ?></h2>
        <div class="lot-item__content">
            <div class="lot-item__left">
                <div class="lot-item__image">
                    <img src="<?= stripTags($lots[0]['image_url']); ?>" width="730" height="548" alt="<?= $lots[0]['name']; ?>">
                </div>
                <p class="lot-item__category">Категория: <span><?= stripTags($lots[0]['category']); ?></span></p>
                <p class="lot-item__description"><?= stripTags($lots[0]['description']); ?></p>
            </div>
            <div class="lot-item__right">

                <div class="lot-item__state">
                    <?php
                    $data = getDifferenceTime($lots[0]['end_date']);
                    ?>
                    <div class="lot-item__timer timer <?php echo ($data[0] <= 0) ? 'timer--finishing' : ''; ?>">
                        <?php
                        echo $data[0] . ':' . $data[1];
                        ?>
                    </div>
                    <div class="lot-item__cost-state">
                        <div class="lot-item__rate">
                            <span class="lot-item__amount">Текущая цена</span>
                            <span class="lot-item__cost">
                                <?php
                                $currentRate = currentRate($connection, $id);

                                if (!empty($currentRate)) {
                                    echo formatPrice(stripTags($currentRate));
                                } else {
                                    echo formatPrice(stripTags($lots[0]['price']));
                                }
                                ?>
                            </span>
                        </div>
                        <div class="lot-item__min-cost">
                            Мин. ставка <span><?= formatPrice(stripTags(lotMinRate($connection, $id))); ?></span>
                        </div>
                    </div>
                    <?php
                    $lastRateUserId = winnerUserId($connection, $id);
                    ?>
                    <?php if (checkSession() && ($data[0] > 0 && $data[1] > 0) && $lots[0]['user_id'] != $userId && $lastRateUserId['user_id'] !== $userId) : ?>
                        <form class="lot-item__form" action="lot.php?id=<?= $id; ?>" method="post" autocomplete="off">
                            <?php
                            $classname = isset($errors['cost']) ? "form__item--invalid" : "";
                            ?>
                            <p class="lot-item__form-item form__item <?= $classname; ?>">
                                <label for="cost">Ваша ставка</label>
                                <input id="cost" type="text" name="cost" placeholder="12 000" value="<?= getPostVal('cost'); ?>">
                                <?php if (isset($errors['cost'])) : ?>
                                    <span class="form__error"><?= $errors['cost']; ?></span>
                                <?php endif; ?>
                            </p>
                            <button type="submit" class="button">Сделать ставку</button>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="history">
                    <h3>История ставок (<span><?= stripTags($lotRateQty); ?></span>)</h3>
                    <?php if ($lotRateQty >= 1) : ?>
                        <table class="history__list">
                            <?php
                            foreach ($lotRates as $lotRate) : ?>
                                <tr class="history__item">
                                    <td class="history__name"><?= stripTags($lotRate['name']); ?></td>
                                    <td class="history__price"><?= formatPrice(stripTags($lotRate['sum'])); ?></td>
                                    <td class="history__time">
                                        <?php
                                        lotRateDatePassed($lotRate['rate_date']);
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else : ?>
                        <p>Ставок нет</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>
