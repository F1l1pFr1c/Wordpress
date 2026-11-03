<?php
/*
Plugin Name: Eraz Navbar
Description: Priehľadný navbar na každej stránke. Položky menu sa spravujú vo Vzhľad → Menu.
Version: 1.0
*/
if (!defined('ABSPATH')) exit;

// NASTAVENIA: upravuj len tu
function eraz_navbar_config() {
  return [
    'logo'    => 'SANCTYOUARY',
    'tagline' => 'Body & Mind Institute',
    'phone'   => '+48 500 777 908',
    'btn1'    => ['Pakiety', '#'],
    'btn2'    => ['Rezerwuj', '#'],
  ];
}

add_action('after_setup_theme', function () {
  register_nav_menu('eraz_navbar', 'Eraz navbar');
});

function eraz_navbar_fallback() {
  echo '<ul class="eraz-menu"><li><a href="#">Oferta</a></li><li><a href="#">Wydarzenia</a></li><li><a href="#">O nas</a></li><li><a href="#">Kontakt</a></li></ul>';
}

add_action('wp_body_open', function () {
  $c = eraz_navbar_config(); ?>
  <header class="eraz-nav">
    <nav class="eraz-left">
      <?php wp_nav_menu(['theme_location' => 'eraz_navbar', 'container' => false, 'menu_class' => 'eraz-menu', 'fallback_cb' => 'eraz_navbar_fallback']); ?>
    </nav>
    <a class="eraz-logo" href="<?php echo esc_url(home_url('/')); ?>">
      <?php echo esc_html($c['logo']); ?><small><?php echo esc_html($c['tagline']); ?></small>
    </a>
    <div class="eraz-right">
      <span><?php echo esc_html($c['phone']); ?></span>
      <a class="eraz-btn eraz-outline" href="<?php echo esc_url($c['btn1'][1]); ?>"><?php echo esc_html($c['btn1'][0]); ?></a>
      <a class="eraz-btn eraz-solid" href="<?php echo esc_url($c['btn2'][1]); ?>"><?php echo esc_html($c['btn2'][0]); ?></a>
    </div>
  </header>
<?php });

add_action('wp_head', function () { ?>
<style>
.eraz-nav{position:absolute;top:0;left:0;right:0;z-index:999;display:grid;grid-template-columns:1fr auto 1fr;align-items:center;padding:1.2rem 3rem;background:transparent;color:#fff;font-size:.75rem;letter-spacing:.08em;text-transform:uppercase}
.admin-bar .eraz-nav{top:32px}
@media(max-width:782px){.admin-bar .eraz-nav{top:46px}}
.eraz-nav a{color:#fff;text-decoration:none}
.eraz-menu{display:flex;gap:2rem;list-style:none;margin:0;padding:0}
.eraz-menu li{position:relative}
.eraz-menu .sub-menu{display:none;position:absolute;top:100%;left:0;min-width:160px;padding:1rem;list-style:none;background:rgba(0,0,0,.7)}
.eraz-menu li:hover>.sub-menu{display:block}
.eraz-logo{text-align:center;font-family:Georgia,serif;font-size:1.4rem;letter-spacing:.1em}
.eraz-logo small{display:block;font-size:.5rem;letter-spacing:.2em}
.eraz-right{display:flex;gap:1rem;align-items:center;justify-content:flex-end}
.eraz-btn{padding:.7rem 1.4rem;border-radius:999px}
.eraz-outline{border:1px solid rgba(255,255,255,.4)}
.eraz-nav .eraz-solid{background:#f5efe3;color:#222}
@media(max-width:900px){.eraz-nav{padding:1rem;grid-template-columns:1fr auto}.eraz-left,.eraz-right span{display:none}}
</style>
<?php });
