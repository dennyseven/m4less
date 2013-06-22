<?php if ($reviews) { ?>
<?php foreach ($reviews as $review) { ?>
<div class="review-content"><span class="review-author"><?php echo $review['author']; ?> on <?php echo $review['date_added']; ?></span>   <img src="catalog/view/theme/geccoshop/image/stars-<?php echo $review['rating'] . 'a.png'; ?>" alt="<?php echo $review['reviews']; ?>" /><br />

<div class="review-text"><?php echo $review['text']; ?></div>
</div>
<?php } ?>
<div class="pagination"><?php echo $pagination; ?></div>
<?php } else { ?>
<div class="review-content"><?php echo $text_no_reviews; ?></div>
<?php } ?>
