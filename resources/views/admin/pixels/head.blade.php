@if(!empty($activePixels))
  @foreach($activePixels as $px)
    @php $events = (array) ($px->events ?? []); @endphp

    {{-- meta --}}
    @if($px->provider === 'facebook' && !empty($px->pixel_id))
      <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
        n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $px->pixel_id }}');
        @if(in_array('pageview', $events)) fbq('track', 'PageView'); @endif
      </script>
      <noscript>
        <img height="1" width="1" style="display:none"
             src="https://www.facebook.com/tr?id={{ $px->pixel_id }}&ev=PageView&noscript=1"/>
      </noscript>
    @endif

    {{-- tiktok --}}
    @if($px->provider === 'tiktok' && !empty($px->pixel_id))
      <script>
        !function (w, d, t) {
          w.TiktokAnalyticsObject=t; var ttq=w[t]=w[t]||[];
          ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"];
          ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};
          for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);
          ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e};
          ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";
          ttq._i=ttq._i||{};ttq._i[e]=[];ttq._i[e]._u=i;ttq._t=ttq._t||{};ttq._t[e]=+new Date;
          ttq._o=ttq._o||{};ttq._o[e]=n||{};var o=d.createElement("script");o.type="text/javascript";o.async=!0;o.src=i+"?sdkid="+e+"&lib="+t;
          var a=d.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
          ttq.load('{{ $px->pixel_id }}');
          @if(in_array('pageview', $events)) ttq.page(); @endif
        }(window, document, 'ttq');
      </script>
    @endif

    {{-- google (GA4 / ADS) --}}
    @if($px->provider === 'google' && !empty($px->pixel_id))
      <script async src="https://www.googletagmanager.com/gtag/js?id={{ $px->pixel_id }}"></script>
      <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $px->pixel_id }}');
      </script>
    @endif

    {{-- snap --}}
    @if($px->provider === 'snap' && !empty($px->pixel_id))
      <script>
        (function(e,t,n){if(e.snaptr)return;var a=e.snaptr=function(){
        a.handleRequest?a.handleRequest.apply(a,arguments):a.queue.push(arguments)};
        a.queue=[];var s='script';r=t.createElement(s);r.async=!0;
        r.src=n;var u=t.getElementsByTagName(s)[0];u.parentNode.insertBefore(r,u);})(window,document,
        'https://sc-static.net/scevent.min.js');

        snaptr('init', '{{ $px->pixel_id }}');
        @if(in_array('pageview', $events)) snaptr('track', 'PAGE_VIEW'); @endif
      </script>
    @endif

  @endforeach
@endif
