<?php
/**
 * =====================================================================
 *  SLIDER NAVIGÁCIA – PHP verzia (šípky + stredná lišta)
 *
 *  POUŽITIE:
 *  1) Súbor vlož do projektu a načítaj ho:
 *       require_once __DIR__ . '/slider-navigacia.php';
 *     (vo WordPresse napr. do functions.php alebo šablóny)
 *
 *  2) Slider (scrollovací kontajner) označ data-slider-track + id:
 *       <div id="tim" data-slider-track> ... karty ... </div>
 *
 *  3) Navigáciu vypíš kdekoľvek, koľkokrát chceš:
 *       <?php slider_nav('#tim'); ?>                 // tmavé pozadie (biele šípky)
 *       <?php slider_nav('#tim', true); ?>           // svetlé pozadie (zelené šípky)
 *       <?php slider_nav('#tim', false, 'moja-trieda'); ?>   // + vlastná CSS trieda
 *
 *     Ak chceš navigáciu v premennej namiesto okamžitého výpisu:
 *       $html = slider_nav_html('#tim');
 *
 *  4) WordPress shortcode (funguje automaticky, ak je WP načítané):
 *       [slider_nav target="#tim" light="1"]
 *
 *  CSS + JS sa vypíšu iba raz, aj keď navigáciu použiješ viackrát.
 * =====================================================================
 */

if (!function_exists('slider_nav_assets')) {

    /** CSS + JS – vypíše sa len pri prvom volaní. */
    function slider_nav_assets(): string
    {
        static $printed = false;
        if ($printed) {
            return '';
        }
        $printed = true;

        return <<<'ASSETS'
<style>
.sn{--sn-arrow:#fff;--sn-track:#12170d;--sn-thumb:#fff;--sn-line-w:70px;--sn-bar-w:88px;
  display:flex;align-items:center;justify-content:center;gap:24px;user-select:none;-webkit-user-select:none}
.sn--light{--sn-arrow:#3b4730;--sn-track:#dcdccc;--sn-thumb:#3b4730}
.sn *{box-sizing:border-box}
.sn__btn{display:flex;align-items:center;gap:6px;padding:6px;margin:0;background:none;border:0;color:var(--sn-arrow);cursor:pointer}
.sn__btn:disabled{cursor:default}
.sn__btn:focus-visible{outline:2px solid var(--sn-arrow);outline-offset:3px}
.sn__line{display:block;width:var(--sn-line-w);height:1.5px;background:var(--sn-arrow)}
.sn__btn svg{width:12px;height:12px;fill:none;stroke:var(--sn-arrow);stroke-width:1.5;stroke-linejoin:round}
.sn__bar{position:relative;width:var(--sn-bar-w);height:1.5px;background:var(--sn-track);cursor:pointer}
.sn__bar::before{content:"";position:absolute;inset:-10px 0}
.sn__thumb{position:absolute;left:0;top:0;height:100%;width:20%;background:var(--sn-thumb)}
</style>
<script>
(function(){
  function initSliderNav(nav){
    if(nav.dataset.snReady) return;
    var track = nav.dataset.target ? document.querySelector(nav.dataset.target) : null;
    if(!track){ var p = nav.parentElement; while(p && !track){ track = p.querySelector('[data-slider-track]'); p = p.parentElement; } }
    if(!track) return;
    nav.dataset.snReady = '1';
    var prev = nav.querySelector('.sn__prev'), next = nav.querySelector('.sn__next'),
        bar = nav.querySelector('.sn__bar'), thumb = nav.querySelector('.sn__thumb');
    function step(){
      var s = track.children[0]; if(!s) return track.clientWidth;
      var gap = parseFloat(getComputedStyle(track).columnGap) || 0;
      return s.getBoundingClientRect().width + gap;
    }
    function update(){
      var max = track.scrollWidth - track.clientWidth;
      var ratio = max > 0 ? track.scrollLeft / max : 0;
      var w = Math.max(track.clientWidth / track.scrollWidth, .12);
      thumb.style.width = (w*100) + '%';
      thumb.style.left = (ratio * (100 - w*100)) + '%';
      prev.disabled = track.scrollLeft <= 2;
      next.disabled = track.scrollLeft >= max - 2;
    }
    prev.addEventListener('click', function(){ track.scrollBy({left:-step(), behavior:'smooth'}); });
    next.addEventListener('click', function(){ track.scrollBy({left: step(), behavior:'smooth'}); });
    bar.addEventListener('click', function(e){
      var r = bar.getBoundingClientRect();
      track.scrollTo({left:((e.clientX - r.left)/r.width)*(track.scrollWidth - track.clientWidth), behavior:'smooth'});
    });
    track.addEventListener('scroll', update, {passive:true});
    window.addEventListener('resize', update);
    update();
  }
  window.initSliderNav = function(){ document.querySelectorAll('[data-slider-nav]').forEach(initSliderNav); };
  if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', window.initSliderNav);
  else window.initSliderNav();
})();
</script>
ASSETS;
    }
}

if (!function_exists('slider_nav_html')) {

    /**
     * Vráti HTML navigácie ako string.
     *
     * @param string $target     CSS selektor slidera, napr. '#tim' (prázdne = nájde najbližší [data-slider-track])
     * @param bool   $light      true = svetlé pozadie (zelené šípky)
     * @param string $extraClass voliteľná ďalšia CSS trieda
     */
    function slider_nav_html(string $target = '', bool $light = false, string $extraClass = ''): string
    {
        $esc = static fn(string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');

        $classes = trim('sn' . ($light ? ' sn--light' : '') . ($extraClass !== '' ? ' ' . $extraClass : ''));
        $targetAttr = $target !== '' ? ' data-target="' . $esc($target) . '"' : '';

        $html  = slider_nav_assets();
        $html .= '<div class="' . $esc($classes) . '" data-slider-nav' . $targetAttr . '>';
        $html .=   '<button class="sn__btn sn__prev" type="button" aria-label="Predchádzajúce">';
        $html .=     '<svg viewBox="0 0 12 12"><path d="M10 1.5v9L2 6z"/></svg><span class="sn__line"></span>';
        $html .=   '</button>';
        $html .=   '<div class="sn__bar" aria-hidden="true"><div class="sn__thumb"></div></div>';
        $html .=   '<button class="sn__btn sn__next" type="button" aria-label="Ďalšie">';
        $html .=     '<span class="sn__line"></span><svg viewBox="0 0 12 12"><path d="M2 1.5v9L10 6z"/></svg>';
        $html .=   '</button>';
        $html .= '</div>';

        return $html;
    }
}

if (!function_exists('slider_nav')) {

    /** Vypíše navigáciu priamo na stránku. */
    function slider_nav(string $target = '', bool $light = false, string $extraClass = ''): void
    {
        echo slider_nav_html($target, $light, $extraClass);
    }
}

/* WordPress shortcode: [slider_nav target="#tim" light="1" class="moja-trieda"] */
if (function_exists('add_shortcode')) {
    add_shortcode('slider_nav', static function ($atts) {
        $a = shortcode_atts(['target' => '', 'light' => '0', 'class' => ''], $atts, 'slider_nav');
        return slider_nav_html((string) $a['target'], (bool) (int) $a['light'], (string) $a['class']);
    });
}
