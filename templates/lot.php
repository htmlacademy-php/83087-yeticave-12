<section class="lot-item container">
    <h2><?= $lots[0]['name']; ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="<?= strip_tags($lots[0]['image_url']); ?>" width="730" height="548" alt="<?= $lots[0]['name']; ?>">
            </div>
            <p class="lot-item__category">Категория: <span><?= strip_tags($lots[0]['category']); ?></span></p>
            <p class="lot-item__description"><?= strip_tags($lots[0]['description']); ?></p>
        </div>
        <div class="lot-item__right">
            <div class="lot-item__state">
                <?php
                $data = getDifferenceTime($lots[0]['end_date'], true);
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
                            <?= formatPrice(strip_tags($lots[0]['current_price'])); ?>
                        </span>
                    </div>
                    <?php if ((intval($lots[0]['current_price']) < LOT_PRICE_LIMIT) && ($data[0] > 0 && $data[1] > 0 && $data[2] > 0) && (intval($lots[0]['current_price']) + intval($lots[0]['price_step']) < LOT_PRICE_LIMIT)) : ?>
                        <div class="lot-item__min-cost">
                            Мин. ставка <span><?= formatPrice(strip_tags(lotMinRate($connection, $id))); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <?php
                $lastRateUserId = getWinnerId($connection, $id);

                if (intval($lots[0]['current_price']) === LOT_PRICE_LIMIT || intval($lots[0]['current_price']) + intval($lots[0]['price_step']) >= LOT_PRICE_LIMIT) : ?>
                    <p>Лот достиг максимальной цены.</p>
                <?php elseif ($data[0] < 0 || $data[1] < 0 || $data[2] < 0) : ?>
                    <p>Лот закончился.</p>
                <?php elseif (checkSession() && ($data[0] > 0 || $data[1] > 0 || $data[2] > 0) && $lots[0]['user_id'] != $userId && $lastRateUserId !== $userId) : ?>
                    <form class="lot-item__form" action="lot.php?id=<?= $id; ?>" method="post" autocomplete="off">
                        <?php
                        $classname = isset($errors['cost']) ? "form__item--invalid" : "";
                        ?>
                        <p class="lot-item__form-item form__item <?= $classname; ?>">
                            <label for="cost">Ваша ставка</label>
                            <input id="cost" type="text" name="cost" placeholder="<?= formatPrice(strip_tags(lotMinRate($connection, $id))); ?>" value="<?= getPostVal('cost'); ?>">
                            <?php if (isset($errors['cost'])) : ?>
                                <span class="form__error"><?= $errors['cost']; ?></span>
                            <?php endif; ?>
                        </p>
                        <button type="submit" class="button">Сделать ставку</button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="history">
                <h3>История ставок (<span><?= strip_tags($lotRateQty); ?></span>)</h3>
                <?php if ($lotRateQty >= 1) : ?>
                    <table class="history__list">
                        <?php
                        foreach ($lotRates as $lotRate) : ?>
                            <tr class="history__item">
                                <td class="history__name"><?= strip_tags($lotRate['name']); ?></td>
                                <td class="history__price"><?= formatPrice(strip_tags($lotRate['sum'])); ?></td>
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
