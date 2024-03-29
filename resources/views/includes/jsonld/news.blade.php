<!-- JSON-LD markup generated by Google Structured Data Markup Helper. -->
<script type="application/ld+json">
{
  "@context" : "https://schema.org",
  "@type" : "Article",
  "headline" : "{{ $project['article_title'] }}",
  "author" : {
    "@type" : "Organization",
    "name" : "The Fitzwilliam Museum"
  },
  "datePublished" : "{{ $project['publication_date'] }}",
  @if(isset($project['modified_on']))
  "dateModified" : "{{ $project['modified_on'] }}",
  @endif
  @if(isset($project['field_image']))
  "image" : "{{ $project['field_image']['data']['full_url'] }}",
  @else
  "image" : "https://content.fitz.ms/fitz-website/assets/gallery3_roof.jpg?key=directus-large-crop",
  @endif
  "articleBody" : "{{ $project['article_body'] }}",
  "url" : "{{ Request::url() }}",
  "mainEntityOfPage": {
         "@type": "WebPage",
         "@id": "{{ Request::url() }}"
      },
  "publisher" : {
    "@type" : "Organization",
    "name" : "The University of Cambridge",
    "logo":{
      "@type":"ImageObject",
      "url":"https://beta.fitz.ms/images/logos/FV.png"
    }
  }
}
</script>
