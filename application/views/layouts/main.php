<!DOCTYPE html>

<html class="no-js">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>NLRC | <?= $template['title'] ?></title>
    <meta name="description" content="<?php $this->load->view('seo/description');?>" />
    <meta name="keywords" content="<?php $this->load->view('seo/keywords');?>" />
    <meta property="og:title" content="Project Name | <?= $template['title']; ?>" />
    <meta name="og:description" content="<?php $this->load->view('seo/description');?>" />
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="shortcut icon" href="<?php echo base_url('assets/img/nlrc-logo.png'); ?>">


    <!-- BOOTSTRAP 5.0.2-->
    <!-- <link rel="stylesheet" href="<?php //echo base_url('plugins/bootstrap-5.0.2/css/bootstrap.min.css')?>"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">

    <!-- FONTAWESOME 6.2.0 -->
    <!-- <link rel="stylesheet" href="<?php //echo base_url('plugins/fontawesome-6.2.0/css/all.min.css') ?>" media="screen"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <!-- FONTS -->
    <link rel="stylesheet" href="<?php echo base_url('assets/fonts/font-face.css?ver=' . filemtime(FCPATH . "assets/fonts/font-face.css")); ?>" media="screen">

    <!-- CUSTOM STYLES -->
    <link rel="stylesheet" href="<?php echo versionAsset('assets/css/styles.css') ?>" media="screen">
    <link rel="stylesheet" href="<?php echo versionAsset('assets/css/main.css') ?>" media="screen">
    <link rel="stylesheet" href="<?php echo versionAsset('assets/css/mobile.css') ?>" media="screen">



    <!-- MODULE STYLES -->
    <?= $template['prepend_metadata']?>



    <script>
        var base_url = "<?=base_url()?>";
        var captchaToken = "<?=recaptcha_site_key() ?>";
    </script>

</head>



<body>
    <div id="main-wrapper">
        <?php 
            if (isset($template['partials']['header'])) {
                echo $template['partials']['header'];
            } 
        ?>
        <?= $template['body']; ?>
    </div>

    <!-- FOOTER -->
    <?php 
        if (isset($template['partials']['footer'])) {
            echo $template['partials']['footer'];
        } 

    ?>

    <!-- JQUERY -->

    <!-- <script src="<?php //echo base_url('plugins/jquery-3.6.0/js/jquery-3.6.0.min.js') ?>"></script> -->
    <script src="https://code.jquery.com/ui/1.14.0/jquery-ui.min.js" integrity="sha256-Fb0zP4jE3JHqu+IBB9YktLcSjI1Zc6J2b6gTjB0LpoM=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js" integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- TETHER -->

    <!-- <script src="<?php //echo base_url('plugins/tether.min.js') ?>"></script> -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.7/js/tether.min.js" integrity="sha512-X7kCKQJMwapt5FCOl2+ilyuHJp+6ISxFTVrx+nkrhgplZozodT9taV2GuGHxBgKKpOJZ4je77OuPooJg9FJLvw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- BOOTSRAP JS -->

    <!-- <script src="<?php //echo base_url('plugins/bootstrap-5.0.2/js/popper.min.js')?>"></script>

    <script src="<?php //echo base_url('plugins/bootstrap-5.0.2/js/bootstrap.min.js')?>"></script> -->

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>

    

    <!-- CUSTOM JS -->

    <script src="<?php echo versionAsset('assets/js/main.js'); ?>"></script>
    <script src="https://www.google.com/recaptcha/api.js?render=<?= recaptcha_site_key() ?>"></script>
    

    <?= $template['metadata']?>

</body>

</html>