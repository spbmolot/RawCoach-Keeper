<?php

namespace App\Console\Commands;

use App\Models\Recipe;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadRecipeImages extends Command
{
    protected $signature = 'recipes:download-images {--force : Перезаписать существующие изображения}';

    protected $description = 'Скачать фото для рецептов из открытых источников';

    /**
     * Маппинг slug рецепта → URL фото (Unsplash direct links, бесплатные)
     */
    private function getImageUrls(): array
    {
        return [
            // === Завтраки ===
            // Овсянка с ягодами и орехами — миска овсянки с малиной и орехами сверху
            'ovsyanka-s-yagodami' => 'https://images.unsplash.com/photo-1517673400267-0251440c45dc?w=800&q=80',
            // Омлет с овощами — пышный омлет с помидорами и перцем на тарелке
            'omlet-s-ovoshchami' => 'https://images.unsplash.com/photo-1510693206972-df098062cb71?w=800&q=80',
            // Творог с фруктами — миска творога с нарезанными фруктами
            'tvorog-s-fruktami' => 'https://images.unsplash.com/photo-1464305795204-6f5bbfc7fb81?w=800&q=80',
            // Гречневая каша с молоком — каша в миске
            'grechnevaya-kasha' => 'https://images.unsplash.com/photo-1505253716362-afaea1d3d1af?w=800&q=80',
            // Яичница с авокадо на тосте — тост с авокадо и яйцом пашот
            'yaichnitsa-s-avokado' => 'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?w=800&q=80',
            // Смузи с бананом и шпинатом — зелёный смузи в стакане
            'smuzi-so-shpinatom' => 'https://images.unsplash.com/photo-1610970881699-44a5587cabec?w=800&q=80',
            // Сырники из творога — золотистые сырники на тарелке
            'syrniki' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=800&q=80',
            // Смузи-боул с бананом и шпинатом — смузи-боул с топпингами
            'smuzi-boul-s-bananom-i-shpinatom' => 'https://images.unsplash.com/photo-1590301157890-4810ed352733?w=800&q=80',
            // Сырники с ягодным соусом — оладьи/сырники с ягодами
            'syrniki-s-yagodnym-sousom' => 'https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=800&q=80',
            // Омлет с овощами и сыром — омлет с начинкой
            'omlet-s-ovoshchami-i-syrom' => 'https://images.unsplash.com/photo-1612240498936-65f5101365d2?w=800&q=80',
            // Гречневая каша с грибами — каша с грибами в миске
            'grechnevaya-kasha-s-gribami' => 'https://images.unsplash.com/photo-1536304929831-ee1ca9d44906?w=800&q=80',

            // === Обеды ===
            // Куриная грудка с киноа и овощами — тарелка с курицей, киноа и овощами
            'kurinaya-grudka-s-kinoa' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800&q=80',
            // Суп-пюре из брокколи — зелёный крем-суп в миске
            'sup-pyure-iz-brokkoli' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=800&q=80',
            // Салат с тунцом и яйцом — белковый салат с тунцом, яйцами, зеленью
            'salat-s-tuntsom' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&q=80',
            // Гречка с говядиной и грибами — мясо с гарниром на тарелке
            'grechka-s-govyadinoy' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&q=80',
            // Паста с курицей и шпинатом — паста пенне в сливочном соусе
            'pasta-s-kuritsey' => 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=800&q=80',
            // Рис с овощами и креветками — рис с креветками в азиатском стиле
            'ris-s-krevetkami' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=800&q=80',
            // Куриный бульон с лапшой — куриный суп с лапшой и овощами
            'kuriniy-bulyon' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=800&q=80',
            // Салат Цезарь с курицей — классический Цезарь с пармезаном
            'salat-tsezar' => 'https://images.unsplash.com/photo-1550304943-4f24f54ddde9?w=800&q=80',
            // Тыквенный крем-суп — оранжевый крем-суп из тыквы
            'tykvennyj-krem-sup' => 'https://images.unsplash.com/photo-1476718406336-bb5a9690ee2a?w=800&q=80',
            // Салат с тунцом и авокадо — салат с рыбой и авокадо
            'salat-s-tuntsom-i-avokado' => 'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=800&q=80',
            // Щи из квашеной капусты — суп с капустой в миске
            'shchi-iz-kvashenoj-kapusty' => 'https://images.unsplash.com/photo-1594756202469-9ff9799b2e4e?w=800&q=80',
            // Солянка мясная сборная — наваристый мясной суп
            'solyanka-myasnaya-sbornaya' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=800&q=80',
            // Плов с курицей — рис с курицей и специями
            'plov-s-kuritsej' => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=800&q=80',
            // Куриная грудка с брокколи — курица с брокколи на тарелке
            'kurinaya-grudka-s-brokkoli' => 'https://images.unsplash.com/photo-1532550907401-a500c9a57435?w=800&q=80',

            // === Ужины ===
            // Лосось на пару с овощами — филе лосося с овощами
            'losos-na-paru' => 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=800&q=80',
            // Куриные котлеты с овощным салатом — котлеты с салатом
            'kurinye-kotlety' => 'https://images.unsplash.com/photo-1632778149955-e80f8ceca2e8?w=800&q=80',
            // Запечённая треска с лимоном — рыба с лимоном и зеленью
            'zapechennaya-treska' => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=800&q=80',
            // Овощное рагу с индейкой — тушёные овощи с мясом
            'ovoshchnoe-ragu' => 'https://images.unsplash.com/photo-1585937421612-70a008356fbe?w=800&q=80',
            // Греческий салат с курицей — салат с фетой, оливками и курицей
            'grecheskiy-salat-s-kuritsey' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=800&q=80',
            // Тефтели из индейки в томатном соусе — мясные шарики в соусе
            'tefteli-iz-indeyki' => 'https://images.unsplash.com/photo-1529042410759-befb1204b468?w=800&q=80',
            // Кабачковые оладьи — оладьи из кабачков с зеленью
            'kabachkovye-oladi' => 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=800&q=80',
            // Запечённые овощи с сыром — овощи гриль с моцареллой
            'zapechennye-ovoshchi' => 'https://images.unsplash.com/photo-1606923829579-0cb981a83e2e?w=800&q=80',
            // Треска с овощами в духовке — запечённая рыба с овощами
            'treska-s-ovoshchami-v-duhovke' => 'https://images.unsplash.com/photo-1580476262798-bddd9f4b7369?w=800&q=80',
            // Тёплый салат с говядиной — тёплый мясной салат
            'tyoplyj-salat-s-govyadinoj' => 'https://images.unsplash.com/photo-1607532941433-304659e8198a?w=800&q=80',

            // === Перекусы ===
            // Греческий йогурт с мёдом — йогурт с мёдом и орехами в миске
            'grecheskiy-yogurt' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=800&q=80',
            // Яблоко с арахисовой пастой — нарезанное яблоко
            'yabloko-s-arahisovoy-pastoy' => 'https://images.unsplash.com/photo-1570913149827-d2ac84ab3f9a?w=800&q=80',
            // Морковные палочки с хумусом — морковь с хумусом
            'morkovnye-palochki-s-humusom' => 'https://images.unsplash.com/photo-1541519227354-08fa5d50c44d?w=800&q=80',
            // Банан с творогом — банан и творог
            'banan-s-tvorogom' => 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=800&q=80',
            // Горсть орехов и сухофруктов — микс орехов и сухофруктов
            'orehi-i-suhofrukty' => 'https://images.unsplash.com/photo-1508061253366-f7da158b6d46?w=800&q=80',
            // Творог с ягодами и мёдом — творог с ягодами
            'tvorog-s-yagodami-i-myodom' => 'https://images.unsplash.com/photo-1464305795204-6f5bbfc7fb81?w=800&q=80',
            // Овощные чипсы — хрустящие овощные чипсы
            'ovoshchnye-chipsy' => 'https://images.unsplash.com/photo-1621447504864-d8686e12698c?w=800&q=80',
            // Запечённое яблоко с корицей — печёное яблоко
            'zapechyonnoe-yabloko-s-koritsej' => 'https://images.unsplash.com/photo-1568571780765-9276ac8b75a2?w=800&q=80',
            // Смузи детокс — зелёный детокс-смузи
            'smuzi-detoks' => 'https://images.unsplash.com/photo-1610970881699-44a5587cabec?w=800&q=80',
        ];
    }

    public function handle(): int
    {
        $force = $this->option('force');
        $imageUrls = $this->getImageUrls();

        $recipes = Recipe::all();
        $this->info("Найдено рецептов: {$recipes->count()}");

        // Убедимся, что директория существует
        Storage::disk('public')->makeDirectory('recipes');

        $bar = $this->output->createProgressBar($recipes->count());
        $bar->start();

        $downloaded = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($recipes as $recipe) {
            $bar->advance();

            // Если уже есть фото и не force — пропускаем
            if ($recipe->main_image && !$force) {
                if (Storage::disk('public')->exists($recipe->main_image)) {
                    $skipped++;
                    continue;
                }
            }

            $url = $imageUrls[$recipe->slug] ?? null;

            if (!$url) {
                $this->newLine();
                $this->warn("  Нет URL для рецепта: {$recipe->title} ({$recipe->slug})");
                $errors++;
                continue;
            }

            try {
                $response = Http::timeout(30)
                    ->withOptions([
                        'allow_redirects' => true,
                        'verify' => false,
                    ])
                    ->get($url);

                if (!$response->successful()) {
                    $this->newLine();
                    $this->error("  Ошибка загрузки ({$response->status()}): {$recipe->title}");
                    $errors++;
                    continue;
                }

                $contentType = $response->header('Content-Type');
                $extension = 'jpg';
                if (str_contains($contentType, 'png')) {
                    $extension = 'png';
                } elseif (str_contains($contentType, 'webp')) {
                    $extension = 'webp';
                }

                $filename = "recipes/{$recipe->slug}.{$extension}";

                Storage::disk('public')->put($filename, $response->body());

                $recipe->update(['main_image' => $filename]);

                $downloaded++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("  Ошибка: {$recipe->title} — {$e->getMessage()}");
                $errors++;
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Готово!");
        $this->table(
            ['Статус', 'Количество'],
            [
                ['Скачано', $downloaded],
                ['Пропущено (уже есть)', $skipped],
                ['Ошибки', $errors],
            ]
        );

        return self::SUCCESS;
    }
}
