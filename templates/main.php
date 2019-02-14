<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
    <ul class="promo__list">
        <?php foreach ($categories as $index): ?>
            <li class="promo__item promo__item--boards">
                <a class="promo__link" href="pages/all-lots.html"><?=$index['name']; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
    <ul class="lots__list">
        <?php foreach ($lots as $lot): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?=$lot['image']; ?>" width="350" height="260" alt="">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?=$lot['category_id']; ?></span>
                    <h3 class="lot__title"><a class="text-link" href="pages/lot.html"><?=screening_txt($lot['name']); ?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount"><?=formatPrice($lot['price']); ?></span>
                            <span class="lot__cost"><?=formatPrice($lot['starting_price']); ?></b></span>
                        </div>
                        <div class="lot__timer timer">
                            <?=lot_expire($lot['date_expire']); ?>
                        </div>
                    </div>
                </div>
            </li>
        <?php  endforeach; ?>
    </ul>
</section>