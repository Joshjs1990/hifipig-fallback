<?php
/**
 * Top leaderboard ad (non-single pages)
 */
defined('ABSPATH') || exit;

// Prefer dynamic AdRotate output when configured.
$adrotate_group_id = (int) get_theme_mod('hifipig_adrotate_top_group_id', 0);
if ($adrotate_group_id > 0 && function_exists('shortcode_exists') && shortcode_exists('adrotate')) {
  $adrotate_shortcode = '[adrotate group="' . $adrotate_group_id . '"]';
  $adrotate_html = trim((string) do_shortcode($adrotate_shortcode));
  // Only use rendered output, not a raw fallback shortcode string.
  if ($adrotate_html !== '' && $adrotate_html !== $adrotate_shortcode) {
    ?>
    <div class="page-top-ad" aria-label="Sponsored">
      <?php echo $adrotate_html; ?>
    </div>
    <?php
    return;
  }
}

$ads = [
  [ 'href' => 'https://www.facebook.com/groups/137533209626187', 'src' => '/wp-content/uploads/2021/05/Audiophiles-north-america-facebook-group-banner-may-2021.jpg' ],
  [ 'href' => 'https://adm-d.com/', 'src' => '/wp-content/uploads/2025/05/ADMD_banner_728x90-May-2025.jpg' ],
  [ 'href' => 'https://www.sv-audio.com/', 'src' => '/wp-content/uploads/2024/07/Storgaard-Vestskov-banners-july-2024-leaderboard.png' ],
  [ 'href' => 'https://www.kiiaudio.com/kii-seven-lifestyle-listening/?utm_source=HP06&amp;utm_medium=HP06&amp;utm_campaign=HP06&amp;utm_id=HP06&amp;utm_term=HP06&amp;utm_content=HP06', 'src' => '/wp-content/uploads/2025/07/kii-leaderboard-july-2025-03-banner-ad-website-728X90-03A.png' ],
  [ 'href' => 'https://dmconnect.co/', 'src' => '/wp-content/uploads/2024/11/DM-connect-Nov2024-leaderboard.jpeg' ],
  [ 'href' => 'https://www.raidho.dk/', 'src' => '/wp-content/uploads/2023/06/Raidho-leaderboard-banner-june-2023.jpg' ],
  [ 'href' => 'https://reliablecorporation.com/en-eu/pages/uberlight-frame', 'src' => '/wp-content/uploads/2025/04/Reliable-corp-uberlight-frame-leaderboard-ad-april-2025.jpg' ],
  [ 'href' => 'https://innuos.com/stream-series/?utm_source=Hi-Fi_Pig_April_2025_Stream_Series&amp;utm_medium=Static_Banner_Stream_Series_728x90&amp;utm_campaign=Hi-Fi_Pig_April_2025_Stream_Series_Static_Banner_Stream_Series_728x90', 'src' => '/wp-content/uploads/2025/05/Innuos-may-2025-stream-series-728x90-hifi-pig.jpg' ],
  [ 'href' => '/jobs-in-hifi/', 'src' => '/wp-content/uploads/2025/06/Jobs-in-hifi-leader-june-2025.jpg' ],
  [ 'href' => 'https://432evo.be/?from=hp2025-728x90', 'src' => '/wp-content/uploads/2025/09/432EVO-sept-2025-banner-2025-728x90-1.png' ],
  [ 'href' => 'https://www.acoustic-signature.com/', 'src' => '/wp-content/uploads/2025/05/AS_hifipig_Banner_728x90px.jpg' ],
  [ 'href' => 'https://sineworld.com/index.html', 'src' => '/wp-content/uploads/2025/11/SINE-HKG-Leaderboard-nov-2025.png' ],
  [ 'href' => 'https://www.hegel.com/en/products/integrated/h150', 'src' => '/wp-content/uploads/2025/12/Hegel-Leaderboard-banner-dec-2025.jpg' ],
  [ 'href' => 'https://isoacoustics.com/home-audio-isolation-products/gaia-neo-series/?utm_source=hifi%20pig&amp;utm_campaign=clarity%20elevated&amp;utm_medium=web%20banner&amp;utm_content=gaia%20neo', 'src' => '/wp-content/uploads/2025/07/728x90-Clarity-Elevated-isoacoustics-july-2025.jpg' ],
  [ 'href' => 'https://www.bristolshow.co.uk/', 'src' => '/wp-content/uploads/2025/11/Bristol-Show-2026-Banner-728-x-90.jpg' ],
  [ 'href' => 'https://www.linsoul.com/', 'src' => '/wp-content/uploads/2025/09/Linsoul-sept-2025-leaderboard.png' ],
  [ 'href' => 'https://silentpound.com/', 'src' => '/wp-content/uploads/2025/04/silent-pound-assukuriaubaneri_Bloom728x90.png' ],
  [ 'href' => 'https://www.canton.de/en/products/series/reference/', 'src' => '/wp-content/uploads/2024/12/Canton-Webbanner-Reference1B-2025-728x90-EN.jpg' ],
  [ 'href' => 'https://shop.zmfheadphones.com/', 'src' => '/wp-content/uploads/2025/12/ZMF-leaderboard-dec-2025-ad-728x90_dec25.jpg' ],
  [ 'href' => 'https://titanaudio.co.uk/', 'src' => '/wp-content/uploads/2024/08/Titan-DGR-aug-2024-leaderboard-banner-1.jpg' ],
  [ 'href' => 'https://playtakumi.com/', 'src' => '/wp-content/uploads/2025/05/takumi-banners-may-2025-LEADERBOARD.jpg' ],
  [ 'href' => 'https://electrocompaniet.com/', 'src' => '/wp-content/uploads/2020/03/728X90-ECI80D.jpg' ],
  [ 'href' => 'https://bassocontinuo.biz/shop/', 'src' => '/wp-content/uploads/2025/12/bassocontinuo-banners-dec-2025-leaderboard.png' ],
  [ 'href' => 'https://www.electromod.co.uk/', 'src' => '/wp-content/uploads/2025/03/electromod-leaderboard-March-2025.jpg' ],
  [ 'href' => 'https://lin.mn/4hUIjg6', 'src' => '/wp-content/uploads/2025/04/Linn_MajikDSM2025_728x90.jpg' ],
  [ 'href' => 'http://www.metronome.audio', 'src' => '/wp-content/uploads/2025/09/Metronome-sept-2025-Leaderboard-V2.png' ],
  [ 'href' => 'https://www.michellaudio.com/michell-apollo-phonostage', 'src' => '/wp-content/uploads/2025/03/March-2025-michell-web-banner-728x90-1.jpg' ],
  [ 'href' => 'https://solidsteel.it/uk/', 'src' => '/wp-content/uploads/2025/03/solidsteel-leaderboard-March-2025-1.jpg' ],
  [ 'href' => 'https://www.thehifiasylum.com/hifipig', 'src' => '/wp-content/uploads/2023/07/HiFi-Asylum-leaderboard-July-2023.jpg' ],
  [ 'href' => 'https://hubs.la/Q02zr-DS0', 'src' => '/wp-content/uploads/2024/06/chord-elec-ultima-june-2024-leaderboard.jpg' ],
  [ 'href' => 'https://anagramaudio.co.uk/brands/fezz-audio', 'src' => '/wp-content/uploads/2025/03/Harmony-HiFi-March-2025-leaderboard-FEZZ.png' ],
  [ 'href' => 'http://telluriumq.com/', 'src' => '/wp-content/uploads/2021/05/TQ-Tellurium-728x90-leaderboard-May-2021.jpg' ],
  [ 'href' => 'https://www.russandrews.com/?utm_source=HiFiPig&amp;utm_medium=bannerad&amp;utm_campaign=online', 'src' => '/wp-content/uploads/2023/10/Russ-andrews-ads-october-2023-leaderboard.png' ],
  [ 'href' => 'https://www.henleyaudio.co.uk/brands/pro-ject-audio-systems/product/colourful-audio-system-2-turntables/#PJAACAS2A', 'src' => '/wp-content/uploads/2025/12/Henley-ads-december-2025-Leaderboard.png' ],
  [ 'href' => 'https://www.kickstarter.com/projects/final-audio-usa/1215498592?ref=e52vle&amp;token=4b4a3e68', 'src' => '/wp-content/uploads/2025/11/jackrabbit-NOV-2025-Final-leaderboard.png' ],
  [ 'href' => 'https://www.goldnote.it/', 'src' => '/wp-content/uploads/2025/01/goldnote-jan-2025-728x90_CD-5.jpg' ],
  [ 'href' => 'https://www.audioemotion.co.uk/', 'src' => '/wp-content/uploads/2023/08/audioemotion-august-2023-banners-leader.jpg' ],
  [ 'href' => 'https://audiogroupdenmark.com/ansuz-acoustics/', 'src' => '/wp-content/uploads/2025/06/AGD-2025-banner-LEADERBOARD-728x90_Sparkz-Sortz.jpg' ],
  [ 'href' => 'https://revivalaudio.fr/', 'src' => '/wp-content/uploads/2025/01/revival-JAN-2025-audio-leaderboard.jpeg' ],
  [ 'href' => 'https://www.javahifi.com/home', 'src' => '/wp-content/uploads/2024/07/Java-july-2024-leaderboard-2.png' ],
  [ 'href' => 'https://nobleaudio.com/products/noble-fokus-prestige-encore', 'src' => '/wp-content/uploads/2025/12/Noble-audio-dec-2025-Leaderboard-FoKus-Prestige-Encore-728x90-1.jpg' ],
  [ 'href' => 'https://epos-loudspeakers.com/', 'src' => '/wp-content/uploads/2025/11/Epos-Fink-leaderboard-Nov-2025.jpg' ],
  [ 'href' => 'https://www.fyneaudio.com', 'src' => '/wp-content/uploads/2019/12/728X90px-still-banner.jpg' ],
  [ 'href' => 'https://esprit-audio.fr/en/', 'src' => '/wp-content/uploads/2025/07/esprit-high-end-july-2025-leaderboard.jpg' ],
  [ 'href' => 'https://carbide.audio/', 'src' => '/wp-content/uploads/2024/09/CARBIDE-2024-new-carbide-ad-banner-728x90-v3.jpg' ],
  [ 'href' => 'https://www.audioanalogue.com/en/products/anniversary-designed-by-airtech', 'src' => '/wp-content/uploads/2025/05/Audio-analogue-may-2025-Banner728x90_Anniversary.png' ],
  [ 'href' => 'https://melco-audio.com/c1/', 'src' => '/wp-content/uploads/2024/03/Melco-ads-march-2024-c1-banner.jpg' ],
  [ 'href' => 'https://daudio.nl/', 'src' => '/wp-content/uploads/2025/09/Daudio-Sept-2025-leaderboard.jpg' ],
  [ 'href' => 'https://feliksaudio.pl/', 'src' => '/wp-content/uploads/2025/08/Feliks-audio-August-2025-EEcho-Vibe_banner_728x90_25-07-2025.png' ],
  [ 'href' => 'https://cyrusaudio.com/category/experience-the-40-series/', 'src' => '/wp-content/uploads/2025/01/cyrus-banner-jan-2025-banner-1.png' ],
  [ 'href' => 'https://www.boyeraudio.com/wadax', 'src' => '/wp-content/uploads/2024/07/BOYER-ADS-July-2024-bwadax_728x90.jpg' ],
  [ 'href' => 'https://www.connected-fidelity.com/float-30-pucks-and-accessories', 'src' => '/wp-content/uploads/2025/11/connected-fidelity-Leaderboard-1-NOV-2025-Pucks-accessories.png' ],
  [ 'href' => 'https://mezeaudio.com/products/poet?utm_source=hifipig&amp;utm_medium=banner&amp;utm_campaign=hifi_pig_collaboration_728x90px', 'src' => '/wp-content/uploads/2025/07/Meze-jul-25HiFi-Pig-advertising-_Poet_728x90px.jpg' ],
];

if (!$ads) {
  return;
}

// Ensure each ad slot on a page gets a different pick.
static $hifipig_ad_used = [];
$available = $ads;
if (!empty($hifipig_ad_used)) {
  $used_keys = array_flip($hifipig_ad_used);
  $available = array_diff_key($ads, $used_keys);
}

if (empty($available)) {
  // Fallback if slots exceed available ads.
  $hifipig_ad_used = [];
  $available = $ads;
}

$keys = array_keys($available);
$pick = $keys[array_rand($keys)];
$hifipig_ad_used[] = $pick;
$ad = $available[$pick];

$href = isset($ad['href']) ? (string) $ad['href'] : '';
$src = isset($ad['src']) ? (string) $ad['src'] : '';

// Normalize root-relative URLs to the current site's domain/path.
if (strpos($href, '/') === 0 && strpos($href, '//') !== 0) {
  $href = home_url($href);
}
if (strpos($src, '/wp-content/uploads/') === 0 || strpos($src, '/uploads/') === 0) {
  $uploads = wp_get_upload_dir();
  $upload_baseurl = isset($uploads['baseurl']) ? (string) $uploads['baseurl'] : '';
  if ($upload_baseurl !== '') {
    if (strpos($src, '/wp-content/uploads/') === 0) {
      $relative_upload_path = substr($src, strlen('/wp-content/uploads/'));
    } else {
      $relative_upload_path = substr($src, strlen('/uploads/'));
    }
    $src = trailingslashit($upload_baseurl) . ltrim($relative_upload_path, '/');
  }
}

if ($href === '' || $src === '') {
  return;
}
?>
<div class="page-top-ad" aria-label="Sponsored">
  <div class="g g-1">
    <div class="g-dyn a-1 c-1">
      <a href="<?php echo esc_url($href); ?>" target="_blank" rel="noopener">
        <img
          src="<?php echo esc_url($src); ?>"
          width="728"
          height="90"
          style="width:100%;height:auto;"
          alt=""
          loading="lazy"
          decoding="async"
          fetchpriority="low"
        >
      </a>
    </div>
  </div>
</div>
