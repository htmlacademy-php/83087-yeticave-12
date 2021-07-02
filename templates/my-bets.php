<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
        <?php foreach ($lots as $lot) : ?>
            <?php
            $data = getDifferenceTime($lot['end_date'], true);
            ?>
            <tr class="rates__item <?php if (isset($lot['winner_id']) && $lot['winner_id'] == $userId && $lot['sum'] == currentRate($connection, $lot['id'])) {
                                        echo 'rates__item--win';
                                    } elseif ($data[0] <= 0) {
                                        echo 'rates__item--end';
                                    } ?>">
                <td class="rates__info">
                    <div class="rates__img">
                        <img src="<?= stripTags($lot['image_url']); ?>" width="54" height="40" alt="<?= stripTags($lot['name']); ?>">
                    </div>
                    <div>
                        <h3 class="rates__title"><a href="lot.php?id=<?= stripTags($lot['id']); ?>"><?= stripTags($lot['name']); ?></a></h3>
                        <?php if (isset($lot['winner_id']) && $lot['winner_id'] == $userId && $lot['sum'] == currentRate($connection, $lot['id'])) : ?>
                            <p><?= userContacts($connection, $lot['id']); ?></p>
                        <?php endif; ?>
                    </div>
                </td>
                <td class="rates__category">
                    <?= stripTags($lot['category']); ?>
                </td>
                <td class="rates__timer">
                    <?php if ($data[0] <= 0) : ?>
                        <?php if (isset($lot['winner_id']) && $lot['winner_id'] == $userId && $lot['sum'] == currentRate($connection, $lot['id'])) : ?>
                            <div class="timer timer--win">Ставка выиграла</div>
                        <?php else : ?>
                            <div class="timer timer--end">Торги окончены</div>
                        <?php endif; ?>
                    <?php elseif ($data[0] < 24) : ?>
                        <div class="timer timer--finishing">
                            <?php
                            echo $data[0] . ':' . $data[1] . ':' . $data[2];
                            ?>
                        </div>
                    <?php else : ?>
                        <div class="timer">
                            <?php
                            echo $data[0] . ':' . $data[1] . ':' . $data[2];
                            ?>
                        </div>
                    <?php endif; ?>
                </td>
                <td class="rates__price">
                    <?= formatPrice(stripTags($lot['sum'])); ?>
                </td>
                <td class="rates__time">
                    <?= lotRateDatePassed($lot['rate_date']); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>
