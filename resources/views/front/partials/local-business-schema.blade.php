<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "@id": "{{ config('app.url') }}/#organization",
    "name": "Charlton Virtual Office",
    "url": "{{ config('app.url') }}",
    "telephone": "+442032474747",
    "email": "support@charltonvirtualoffice.com",
    "priceRange": "££",
    "image": "{{ asset('images/home-banner.jpg') }}",
    "address": {
        "@type": "PostalAddress",
        "streetAddress": "Unit 6, Block 3, Dockyard Industrial Estate, Church Street, Woolwich",
        "addressLocality": "London",
        "postalCode": "SE18 5PQ",
        "addressCountry": "GB"
    },
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": 51.4932,
        "longitude": 0.0536
    },
    "openingHoursSpecification": [
        {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
            "opens": "09:00",
            "closes": "18:00"
        }
    ],
    "areaServed": ["Woolwich", "Charlton", "Greenwich", "Plumstead", "Abbey Wood", "Thamesmead", "London"],
    "hasOfferCatalog": {
        "@type": "OfferCatalog",
        "name": "Virtual Office & Meeting Services",
        "itemListElement": [
            {
                "@type": "Offer",
                "itemOffered": { "@type": "Service", "name": "Virtual Business Address" }
            },
            {
                "@type": "Offer",
                "itemOffered": { "@type": "Service", "name": "Meeting Room Hire" }
            },
            {
                "@type": "Offer",
                "itemOffered": { "@type": "Service", "name": "Conference Room Hire" }
            },
            {
                "@type": "Offer",
                "itemOffered": { "@type": "Service", "name": "Mail Forwarding & Scanning" }
            },
            {
                "@type": "Offer",
                "itemOffered": { "@type": "Service", "name": "Registered Office Address" }
            }
        ]
    }
}
</script>