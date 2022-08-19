<!-- Main block layout - Must be have the name html.php -->
<?php 
    $bannerImage = get_field('banner_image');
    $bannerTitle = get_field('banner_title') ? '<p>'.get_field('banner_title').'</p>' : 'No Title';
    $bannerSubTitle = get_field('banner_subtitle') ? '<p>'.get_field('banner_subtitle').'</p>' : '';
?>
<div class="example-class" style="background-image:url('<?php echo $bannerImage['url']; ?>')">
    <div class="wrap">
        <h1><?php echo $bannerTitle; ?></h1>
        <?php echo $bannerSubTitle; ?>
    </div>
</div>