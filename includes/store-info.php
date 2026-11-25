<?php
declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}

/**
 * PLACEHOLDER store details.
 *
 * Every value below is a sample taken from the design mockup. Replace them with
 * the real shop details before going live. In a later phase these will come from
 * the database (admin "Website Settings").
 *
 * - whatsapp_number: digits only, WITH country code, e.g. '919XXXXXXXXX'. Empty = WhatsApp not set up yet.
 * - phone_tel:       number for tap-to-call links, e.g. '+919XXXXXXXXX'. Empty = shown as plain text.
 * - maps_url:        full https:// Google Maps link. Empty = link to the Store Location page instead.
 * - social:          full https:// profile URLs. Empty = icon hidden in production.
 *
 * @return array<string, mixed>
 */
function store_info(): array
{
    static $info = null;

    if ($info !== null) {
        return $info;
    }

    $info = [
        'is_placeholder'  => true,   // set to false once real details are entered
        'address_lines'   => ['123, Station Road', 'Serampore, Hooghly', 'West Bengal - 712201'],
        'phone_display'   => '+91 98765 43210',
        'phone_tel'       => '',
        'whatsapp_number' => '',
        'email'           => '',
        'maps_url'        => '',
        'availability'    => 'Available 9:00 AM - 8:00 PM',
        'hours'           => [
            ['label' => 'Mon - Sat', 'time' => '9:00 AM - 8:00 PM'],
            ['label' => 'Sunday',    'time' => '10:00 AM - 4:00 PM'],
        ],
        'social'          => [
            'facebook'  => '',
            'instagram' => '',
            'youtube'   => '',
        ],
    ];

    return $info;
}

/** Accept only https:// URLs (blocks javascript: and other schemes). */
function store_clean_url(string $url): string
{
    $url = trim($url);
    return str_starts_with($url, 'https://') ? $url : '';
}

/** Valid WhatsApp number (digits only, 11 to 15 digits incl. country code) or ''. */
function store_whatsapp_number(): string
{
    $digits = preg_replace('/\D+/', '', (string) (store_info()['whatsapp_number'] ?? '')) ?? '';
    return preg_match('/^\d{11,15}$/', $digits) ? $digits : '';
}

/**
 * WhatsApp chat link. Falls back to the Contact page when no number is configured.
 */
function store_whatsapp_url(string $message = ''): string
{
    $number = store_whatsapp_number();
    if ($number === '') {
        return url('pages/contact.php');
    }

    $link = 'https://wa.me/' . $number;
    if ($message !== '') {
        $link .= '?text=' . rawurlencode($message);
    }
    return $link;
}

/** Google Maps link, or the Store Location page when no link is configured. */
function store_maps_url(): string
{
    $maps = store_clean_url((string) (store_info()['maps_url'] ?? ''));
    return $maps !== '' ? $maps : url('pages/store-location.php');
}

/**
 * Social links for the footer: each item has label, icon name and url ('' = not set).
 *
 * @return array<string, array{label:string, icon:string, url:string}>
 */
function store_social_links(): array
{
    $social = store_info()['social'] ?? [];

    return [
        'facebook'  => ['label' => 'Facebook',  'icon' => 'facebook',  'url' => store_clean_url((string) ($social['facebook'] ?? ''))],
        'instagram' => ['label' => 'Instagram', 'icon' => 'instagram', 'url' => store_clean_url((string) ($social['instagram'] ?? ''))],
        'whatsapp'  => ['label' => 'WhatsApp',  'icon' => 'whatsapp',  'url' => store_whatsapp_number() !== '' ? store_whatsapp_url() : ''],
        'youtube'   => ['label' => 'YouTube',   'icon' => 'youtube',   'url' => store_clean_url((string) ($social['youtube'] ?? ''))],
    ];
}

/** Development-only reminder that the store details are still placeholders. */
function render_placeholder_notice(): void
{
    if (APP_ENV === 'production' || empty(store_info()['is_placeholder'])) {
        return;
    }

    echo '<p class="dev-notice" role="note"><strong>Preview note:</strong> the address, phone number, hours and social links '
       . 'on this site are placeholders. Edit <code>includes/store-info.php</code> before going live.</p>';
}