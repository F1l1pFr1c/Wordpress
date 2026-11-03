<?php
/**
 * =====================================================================
 *  STRÁNKOVANIE (PAGER) – PHP verzia
 *
 *  Vypíše: ‹ 1 2 … 8 ›   – každé číslo je odkaz (?page=N), takže funguje
 *  aj bez JavaScriptu a je dobré pre SEO.
 *
 *  POUŽITIE:
 *  1) Načítaj súbor:
 *       require_once __DIR__ . '/strankovanie.php';
 *
 *  2) Vypíš pager kdekoľvek (aj viackrát na stránke):
 *       <?php pager(8); ?>                      // 8 strán, aktuálna sa vezme z ?page=
 *       <?php pager(8, 3); ?>                   // 8 strán, aktuálna je 3
 *       <?php pager(8, null, 'strana'); ?>      // parameter v URL: ?strana=N
 *       <?php pager(8, null, 'page', '/oferta/'); ?>   // vlastná základná URL
 *       <?php pager(8, null, 'page', null, 'moja-trieda'); ?>  // + vlastná CSS trieda
 *
 *  3) Ak chceš HTML v premennej:  $html = pager_html(8);
 *
 *  4) Vo WordPresse: pager(8, max(1, get_query_var('paged')), 'paged');
 *     (pre pekné URL /page/2/ si odkazy uprav v $makeUrl nižšie)
 *
 *  Ostatné parametre v URL (filtre, hľadanie...) sa zachovajú.
 *  CSS sa vypíše iba raz. Tučnosť čísel: --pg-weight (predvolene 500).
 * =====================================================================
 */

if (!function_exists('pager_assets')) {

    function pager_assets(): string
    {
        static $printed = false;
        if ($printed) {
            return '';
        }
        $printed = true;

        return <<<'ASSETS'
<style>
.pg{--pg-ink:#1a2410;--pg-line:#e0d9c3;--pg-active-text:#fff;--pg-size:62px;--pg-weight:500;
  display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap;
  font-family:'Poppins',system-ui,sans-serif}
.pg *{box-sizing:border-box}
.pg__btn{width:var(--pg-size);height:var(--pg-size);display:grid;place-items:center;padding:0;margin:0;
  background:transparent;border:1px solid var(--pg-line);color:var(--pg-ink);text-decoration:none;
  font:var(--pg-weight) 14px/1 'Poppins',system-ui,sans-serif;cursor:pointer;transition:background .2s,color .2s}
.pg__btn:hover:not(.is-active):not(.is-disabled){background:rgba(26,36,16,.06)}
.pg__btn:focus-visible{outline:2px solid var(--pg-ink);outline-offset:3px}
.pg__btn.is-active{background:var(--pg-ink);border-color:var(--pg-ink);color:var(--pg-active-text);cursor:default}
.pg__btn.is-disabled{cursor:default}
.pg__btn svg{width:8px;height:8px;fill:currentColor}
.pg__dots{width:30px;text-align:center;font:var(--pg-weight) 14px 'Poppins',system-ui,sans-serif;color:var(--pg-ink)}
@media(max-width:520px){.pg{--pg-size:44px;gap:8px}}
</style>
ASSETS;
    }
}

if (!function_exists('pager_html')) {

    /**
     * Vráti HTML stránkovania ako string.
     *
     * @param int         $total      počet strán
     * @param int|null    $current    aktuálna strana (null = z $_GET[$param], inak 1)
     * @param string      $param      názov parametra v URL
     * @param string|null $baseUrl    základná URL (null = aktuálna adresa)
     * @param string      $extraClass voliteľná ďalšia CSS trieda
     */
    function pager_html(
        int $total,
        ?int $current = null,
        string $param = 'page',
        ?string $baseUrl = null,
        string $extraClass = ''
    ): string {
        if ($total < 1) {
            return '';
        }

        $esc = static fn(string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');

        // aktuálna strana
        if ($current === null) {
            $current = isset($_GET[$param]) ? (int) $_GET[$param] : 1;
        }
        $current = max(1, min($total, $current));

        // základná URL + zachovanie ostatných parametrov
        $base = $baseUrl ?? (string) strtok($_SERVER['REQUEST_URI'] ?? '', '?');
        $query = [];
        if ($baseUrl === null) {
            parse_str((string) ($_SERVER['QUERY_STRING'] ?? ''), $query);
        }
        unset($query[$param]);

        $makeUrl = static function (int $page) use ($base, $query, $param): string {
            $q = $query;
            if ($page > 1) {
                $q[$param] = $page;            // strana 1 = čistá URL bez parametra
            }
            return $base . ($q ? '?' . http_build_query($q) : '');
        };

        // ktoré čísla zobraziť: 1 … 4 5 6 … 8
        $set = [1, $total, $current - 1, $current, $current + 1];
        if ($current <= 2)          { $set[] = 2; $set[] = 3; }
        if ($current >= $total - 1) { $set[] = $total - 1; $set[] = $total - 2; }
        $list = array_values(array_unique(array_filter($set, static fn($n) => $n >= 1 && $n <= $total)));
        sort($list);

        $items = [];
        foreach ($list as $i => $n) {
            if ($i > 0 && $n - $list[$i - 1] > 1) {
                $items[] = '…';
            }
            $items[] = $n;
        }

        $arrowL = '<svg viewBox="0 0 8 8"><path d="M7 0v8L1 4z"/></svg>';
        $arrowR = '<svg viewBox="0 0 8 8"><path d="M1 0v8l6-4z"/></svg>';

        $arrow = static function (string $svg, string $label, ?int $page, callable $makeUrl, callable $esc): string {
            if ($page === null) {   // neaktívna šípka – ostáva viditeľná, len sa nedá kliknúť
                return '<span class="pg__btn is-disabled" aria-disabled="true" aria-label="' . $esc($label) . '">' . $svg . '</span>';
            }
            return '<a class="pg__btn" href="' . $esc($makeUrl($page)) . '" aria-label="' . $esc($label) . '">' . $svg . '</a>';
        };

        $classes = trim('pg' . ($extraClass !== '' ? ' ' . $extraClass : ''));

        $html  = pager_assets();
        $html .= '<nav class="' . $esc($classes) . '" aria-label="Stránkovanie">';
        $html .= $arrow($arrowL, 'Predchádzajúca strana', $current > 1 ? $current - 1 : null, $makeUrl, $esc);

        foreach ($items as $item) {
            if ($item === '…') {
                $html .= '<span class="pg__dots">…</span>';
            } elseif ($item === $current) {
                $html .= '<span class="pg__btn is-active" aria-current="page" aria-label="Strana ' . $item . '">' . $item . '</span>';
            } else {
                $html .= '<a class="pg__btn" href="' . $esc($makeUrl($item)) . '" aria-label="Strana ' . $item . '">' . $item . '</a>';
            }
        }

        $html .= $arrow($arrowR, 'Ďalšia strana', $current < $total ? $current + 1 : null, $makeUrl, $esc);
        $html .= '</nav>';

        return $html;
    }
}

if (!function_exists('pager')) {

    /** Vypíše stránkovanie priamo na stránku. */
    function pager(
        int $total,
        ?int $current = null,
        string $param = 'page',
        ?string $baseUrl = null,
        string $extraClass = ''
    ): void {
        echo pager_html($total, $current, $param, $baseUrl, $extraClass);
    }
}

/* WordPress shortcode: [pager total="8" param="page"] */
if (function_exists('add_shortcode')) {
    add_shortcode('pager', static function ($atts) {
        $a = shortcode_atts(['total' => '1', 'current' => '', 'param' => 'page', 'class' => ''], $atts, 'pager');
        return pager_html(
            (int) $a['total'],
            $a['current'] !== '' ? (int) $a['current'] : null,
            (string) $a['param'],
            null,
            (string) $a['class']
        );
    });
}
