<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:site_name" content="ConectaTusFinanzas">
<meta property="og:title" content="ConectaTusFinanzas">
<meta property="og:description"
    content="Conecta tus finanzas - Toma el control de tus ingresos, gastos y presupuesto de forma sencilla.">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="https://conectivaits.com/images/logo/logo-completo-azul.png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@@ConectaTusFinanzas">
<meta name="twitter:creator" content="@@ConectaTusFinanzas">
<meta name="twitter:title" content="ConectaTusFinanzas">
<meta name="twitter:description"
    content="Conecta tus finanzas - Toma el control de tus ingresos, gastos y presupuesto de forma sencilla.">
<meta name="twitter:image" content="https://conectivaits.com/images/logo/logo-completo-azul.png">
<meta name="twitter:image:alt" content="ConectaTusFinanzas">

<!-- Additional SEO -->
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "ConectaTusFinanzas",
  "url": "{{ url('/') }}",
  "logo": "https://conectivaits.com/images/logo/logo-completo-azul.png",
  "applicationCategory": "FinanceApplication",
  "operatingSystem": "Any"
}
</script>

<link rel="icon" type="image/png" href="https://conectivaits.com/images/logo/favicon/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="https://conectivaits.com/images/logo/favicon/favicon.svg" />
<link rel="shortcut icon" href="https://conectivaits.com/images/logo/favicon/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="https://conectivaits.com/images/logo/favicon/apple-touch-icon.png" />
<meta name="apple-mobile-web-app-title" content="ConectaTusFinanzas" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">

<link rel="manifest" href="{{ asset('manifest.json') }}" />