<?php

/**
 * Функция для форматирования суммы и добавления к ней знака рубля
 * @param int $numberRate сумма лота
 * @return string Отформатированная сумма вместе со знаком рубля
 */
function formatPrice($numberRate)
{
    $numberRate = ceil($numberRate);

    if ($numberRate >= 1000) {
        $numberRate = number_format($numberRate, 0, ".", " ");
    }

    return $numberRate . ' ₽';
}

function stripTags($str)
{
    $text = strip_tags($str);

    return $text;
}

/**
 * Функция возврата оставшегося времени лота в формате ЧЧ:ММ
 * @param string $lotDate дата вида - ГГГГ-ММ-ДД
 */
function getDifferenceTime($lotDate)
{
    $currentDate = time();
    $lotDateUnix = strtotime($lotDate);

    $dateDiff = $lotDateUnix - $currentDate;
    $allMinutes = floor($dateDiff / 60);
    $hours = floor($allMinutes / 60);
    $minute = $allMinutes - $hours * 60;

    $res = [
        $hours, $minute
    ];

    return $res;
}

function getCategoryMenu($categories)
{
    foreach ($categories as $category) : ?>
        <li class="nav__item">
            <a href="/pages/all-lots.html"><?= stripTags($category['name']); ?></a>
        </li>
    <?php endforeach;
}

function getLots($lots)
{
    foreach ($lots as $lot) : ?>
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
<?php endforeach;
}
