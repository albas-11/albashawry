<script>
    window.trackStoreEvent = function(eventKey, params = {}) {
        // Safety
        if (!eventKey) return;

        // Build a normalized params object
        const payload = params || {};

        @if (!empty($activePixels))
            @foreach ($activePixels as $px)
                @php
                    $events = (array) ($px->events ?? []);
                    $provider = $px->provider ?? '';
                    $enabledEventsJson = json_encode(array_values($events));
                @endphp

                    (function() {
                        const enabled = {!! $enabledEventsJson !!};
                        if (!Array.isArray(enabled) || !enabled.includes(eventKey)) return;

                        const provider = @json($provider);

                        //facebook
                        if (provider === 'facebook') {
                            if (typeof fbq === 'function') {
                                const map = {
                                    pageview: 'PageView',
                                    view_content: 'ViewContent',
                                    add_to_cart: 'AddToCart',
                                    initiate_checkout: 'InitiateCheckout',
                                    purchase: 'Purchase'
                                };

                                const metaEvent = map[eventKey] || eventKey;
                                fbq('track', metaEvent, payload);
                            }
                            return;
                        }

                        //tiktok
                        if (provider === 'tiktok') {
                            if (typeof ttq === 'object' && typeof ttq.track === 'function') {
                                const map = {
                                    pageview: 'PageView',
                                    view_content: 'ViewContent',
                                    add_to_cart: 'AddToCart',
                                    initiate_checkout: 'InitiateCheckout',
                                    purchase: 'CompletePayment'
                                };

                                const ttEvent = map[eventKey] || eventKey;
                                ttq.track(ttEvent, payload);
                            }
                            return;
                        }

                        //google
                        if (provider === 'google') {
                            if (typeof gtag === 'function') {
                                gtag('event', eventKey, payload);
                            }
                            return;
                        }

                        //snap
                        if (provider === 'snap') {
                            if (typeof snaptr === 'function') {
                                const map = {
                                    pageview: 'PAGE_VIEW',
                                    view_content: 'VIEW_CONTENT',
                                    add_to_cart: 'ADD_CART',
                                    initiate_checkout: 'START_CHECKOUT',
                                    purchase: 'PURCHASE'
                                };

                                const snapEvent = map[eventKey] || eventKey;
                                snaptr('track', snapEvent, payload);
                            }
                            return;
                        }

                    })();
            @endforeach
        @endif
    };
</script>
