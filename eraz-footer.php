<?php
/*
Plugin Name: Eraz Footer
Description: Tmavý footer na každej stránke. Odkazy (Regulamin, Polityka prywatności) sa spravujú vo Vzhľad → Menu.
Version: 1.0
*/
if (!defined('ABSPATH')) exit;

// NASTAVENIA: upravuj len tu
function eraz_footer_config() {
  return [
    'logo'      => 'SANCTYOUARY',
    'tagline'   => 'Body & Mind Institute',
    'name'      => 'Klinika Sanctyouary, Body & Mind Institute',
    'address'   => 'ul. Sucha 4, 42-470 Siewierz',
    'phone'     => '+48 500 777 908',
    'email'     => 'kontakt@sanctyouary.pl',
    'facebook'  => '#',
    'instagram' => '#',
    'copyright' => '© Sanctyouary',
    'credit'    => ['Design&Code', '#'],
  ];
}

add_action('after_setup_theme', function () {
  register_nav_menu('eraz_footer', 'Eraz footer');
});

function eraz_footer_fallback() {
  echo '<ul class="eraz-f-menu"><li><a href="#">Regulamin</a></li><li><a href="#">Polityka prywatności</a></li></ul>';
}

add_action('wp_footer', function () {
  $c   = eraz_footer_config();
  $tel = preg_replace('/[^0-9+]/', '', $c['phone']); ?>
  <footer class="eraz-footer">
    <div class="eraz-f-in">
      <div class="eraz-f-row">
        <div class="eraz-f-left">
          <?php echo esc_html($c['name']); ?><br>
          <?php echo esc_html($c['address']); ?>
        </div>
        <a class="eraz-f-logo" href="<?php echo esc_url(home_url('/')); ?>">
          <?php echo esc_html($c['logo']); ?><small><?php echo esc_html($c['tagline']); ?></small>
        </a>
        <div class="eraz-f-right">
          <a class="eraz-f-link" href="tel:<?php echo esc_attr($tel); ?>"><?php echo esc_html($c['phone']); ?></a>
          <a class="eraz-f-link" href="mailto:<?php echo esc_attr($c['email']); ?>"><?php echo esc_html($c['email']); ?></a>
        </div>
      </div>
      <div class="eraz-f-row eraz-f-bottom">
        <nav class="eraz-f-left">
          <?php wp_nav_menu(['theme_location' => 'eraz_footer', 'container' => false, 'menu_class' => 'eraz-f-menu', 'fallback_cb' => 'eraz_footer_fallback']); ?>
        </nav>
        <div class="eraz-f-social">
          <a href="<?php echo esc_url($c['facebook']); ?>" aria-label="Facebook"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>
          <a href="<?php echo esc_url($c['instagram']); ?>" aria-label="Instagram"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
        </div>
        <div class="eraz-f-right eraz-f-copy">
          <span><?php echo esc_html($c['copyright']); ?></span>
          <a href="<?php echo esc_url($c['credit'][1]); ?>"><?php echo esc_html($c['credit'][0]); ?></a>
        </div>
      </div>
    </div>
  </footer>
<?php }, 5);

add_action('wp_head', function () { ?>
<style>
.eraz-footer{--eraz-f-bg:#151f16;--eraz-f-text:#f1ead8;background:var(--eraz-f-bg);color:var(--eraz-f-text);padding:3.5rem 3rem 2.5rem;font-size:.65rem;letter-spacing:.08em;text-transform:uppercase;line-height:1.8}
.eraz-footer a{color:var(--eraz-f-text);text-decoration:none}
.eraz-f-in{max-width:1100px;margin:0 auto}
.eraz-f-row{display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:1rem}
.eraz-f-bottom{margin-top:5rem}
.eraz-f-right{text-align:right}
.eraz-f-link{display:block;text-decoration:underline!important}
.eraz-f-logo{text-align:center;font-family:Georgia,serif;font-size:1.5rem;letter-spacing:.1em;text-transform:none}
.eraz-f-logo small{display:block;font-size:.5rem;letter-spacing:.2em;text-transform:uppercase}
.eraz-f-menu{display:flex;gap:2rem;list-style:none;margin:0;padding:0}
.eraz-f-social{display:flex;gap:1.5rem;justify-content:center}
.eraz-f-copy{display:flex;gap:2rem;justify-content:flex-end}
@media(max-width:800px){
  .eraz-footer{padding:2.5rem 1.5rem}
  .eraz-f-row{grid-template-columns:1fr;text-align:center;justify-items:center}
  .eraz-f-right,.eraz-f-copy{text-align:center;justify-content:center}
  .eraz-f-menu{justify-content:center;flex-wrap:wrap;gap:1rem}
  .eraz-f-bottom{margin-top:2.5rem}
}
</style>
<?php });
