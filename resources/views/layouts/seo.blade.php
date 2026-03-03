<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:site_name" content="SmartBud">
<meta property="og:title" content="SmartBud">
<meta property="og:description"
  content="SmartBud - Toma el control de tus ingresos, gastos y presupuesto de forma sencilla.">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ asset('/logo.png') }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@@SmartBud">
<meta name="twitter:creator" content="@@SmartBud">
<meta name="twitter:title" content="SmartBud">
<meta name="twitter:description"
  content="SmartBud - Toma el control de tus ingresos, gastos y presupuesto de forma sencilla.">
<meta name="twitter:image" content="{{ asset('/logo.png') }}">
<meta name="twitter:image:alt" content="SmartBud">

<!-- Additional SEO -->
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "SmartBud",
  "url": "{{ url('/') }}",
  "logo": "{{ asset('/logo.png') }}",
  "applicationCategory": "FinanceApplication",
  "operatingSystem": "Any"
}
</script>

<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
<meta name="apple-mobile-web-app-title" content="SmartBud" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">

<link rel="manifest" href="{{ asset('manifest.json') }}" />