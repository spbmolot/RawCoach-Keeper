<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "RawPlan",
    "url": "{{ config('app.url') }}",
    "description": "Готовые планы питания для похудения",
    "inLanguage": "ru-RU",
    "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ config('app.url') }}/recipes?search={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
</script>
