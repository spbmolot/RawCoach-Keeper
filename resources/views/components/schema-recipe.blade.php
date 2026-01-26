@props(['recipe'])

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Recipe",
    "name": "{{ $recipe->title }}",
    "description": "{{ Str::limit($recipe->description, 200) }}",
    "image": "{{ $recipe->image_url ?? asset('images/recipe-placeholder.jpg') }}",
    "author": {
        "@type": "Organization",
        "name": "RawPlan"
    },
    "datePublished": "{{ $recipe->created_at->toIso8601String() }}",
    "prepTime": "PT{{ $recipe->prep_time ?? 10 }}M",
    "cookTime": "PT{{ $recipe->cook_time ?? 20 }}M",
    "totalTime": "PT{{ ($recipe->prep_time ?? 10) + ($recipe->cook_time ?? 20) }}M",
    "recipeYield": "{{ $recipe->servings ?? 2 }} порции",
    "recipeCategory": "{{ $recipe->meal_type ?? 'Основное блюдо' }}",
    "recipeCuisine": "Русская",
    "nutrition": {
        "@type": "NutritionInformation",
        "calories": "{{ $recipe->calories ?? 0 }} ккал",
        "proteinContent": "{{ $recipe->protein ?? 0 }} г",
        "fatContent": "{{ $recipe->fat ?? 0 }} г",
        "carbohydrateContent": "{{ $recipe->carbs ?? 0 }} г"
    },
    @if($recipe->ingredients && count($recipe->ingredients) > 0)
    "recipeIngredient": [
        @foreach($recipe->ingredients as $index => $ingredient)
        "{{ $ingredient->name }} - {{ $ingredient->pivot->amount ?? '' }} {{ $ingredient->pivot->unit ?? '' }}"@if($index < count($recipe->ingredients) - 1),@endif
        @endforeach
    ],
    @endif
    @if($recipe->instructions)
    "recipeInstructions": [
        @php $steps = is_array($recipe->instructions) ? $recipe->instructions : json_decode($recipe->instructions, true); @endphp
        @if($steps)
        @foreach($steps as $index => $step)
        {
            "@type": "HowToStep",
            "text": "{{ is_array($step) ? ($step['text'] ?? $step['description'] ?? '') : $step }}"
        }@if($index < count($steps) - 1),@endif
        @endforeach
        @endif
    ],
    @endif
    "keywords": "рецепт, {{ $recipe->meal_type ?? 'блюдо' }}, похудение, КБЖУ, здоровое питание"
}
</script>
