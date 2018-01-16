<?php

namespace Modules\Forum\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Models\Menu;
use Modules\Admin\Models\MenuItem;
use Modules\Category\Models\CategoryGroup;
use Netcore\Translator\Helpers\TransHelper;

class ForumDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->createCategoryGroup();
        $this->createMenuEntries();
    }

    /**
     * Create or update category group used by forum module.
     *
     * @return void
     */
    protected function createCategoryGroup(): void
    {
        $config = config('netcore.module-forum.used_category_group', []);

        CategoryGroup::updateOrCreate(
            array_only($config, 'key'), $config
        );
    }

    /**
     * Create administrator panel menu entries.
     *
     * @return void
     */
    protected function createMenuEntries(): void
    {
        $menuItems = [
            'leftAdminMenu' => [
                [
                    'name'            => 'Forum',
                    'icon'            => 'fa fa-bullhorn',
                    'type'            => 'url',
                    'value'           => '#',
                    'module'          => 'Forum',
                    'is_active'       => 1,
                    'active_resolver' => 'forum::admin.*',
                    'parameters'      => json_encode([]),
                    'children'        => [
                        [
                            'name'            => 'Reported posts',
                            'type'            => 'route',
                            'value'           => 'forum::admin.reports.index',
                            'module'          => 'Forum',
                            'is_active'       => 1,
                            'active_resolver' => 'forum::admin.reports.*',
                            'parameters'      => json_encode([]),
                        ],
                        [
                            'name'            => 'Forum management',
                            'type'            => 'route',
                            'value'           => 'forum::admin.management.index',
                            'module'          => 'Forum',
                            'is_active'       => 1,
                            'active_resolver' => 'forum::admin.management.*',
                            'parameters'      => json_encode([]),
                        ],
                    ],
                ],
            ],
        ];

        // Recursive iterator.
        $createMenu = function (array $items, Menu $menu, $parent = null) use (&$createMenu) {
            foreach ($items as $item) {
                $exceptKeys = ['name', 'value', 'parameters', 'children'];

                // Create menu item.
                $menuItem = $menu->items()->firstOrCreate(
                    array_except($item, $exceptKeys)
                );

                if ($parent && $parent instanceof MenuItem) {
                    $parent->appendNode($menuItem);
                }

                $translations = [];

                foreach (TransHelper::getAllLanguages() as $language) {
                    $translations[$language->iso_code] = [
                        'name'       => $item['name'],
                        'value'      => $item['value'],
                        'parameters' => $item['parameters'],
                    ];
                }

                $menuItem->updateTranslations($translations);

                if (isset($item['children'])) {
                    $createMenu($item['children'], $menu, $menuItem);
                }
            }
        };

        // Iterate over items recursively.
        foreach ($menuItems as $key => $items) {
            $menu = Menu::firstOrCreate([
                'key' => $key,
            ]);

            if (!$menu) {
                continue;
            }

            $createMenu($items, $menu);
        }
    }
}
