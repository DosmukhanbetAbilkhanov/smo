<?php

return [
    'units' => [
        [
            'name' => [
                'ru' => 'Штука',
                'kz' => 'Дана',
            ],
        ],
        [
            'name' => [
                'ru' => 'Килограмм',
                'kz' => 'Килограмм',
            ],
        ],
        [
            'name' => [
                'ru' => 'Тонна',
                'kz' => 'Тонна',
            ],
        ],
        [
            'name' => [
                'ru' => 'Метр',
                'kz' => 'Метр',
            ],
        ],
        [
            'name' => [
                'ru' => 'Метр квадратный',
                'kz' => 'Шаршы метр',
            ],
        ],
    ],

    'categories' => [
        [
            'name' => ['ru' => 'Сухие смеси и грунтовки', 'kz' => 'Құрғақ қоспалар мен праймерлер'],
            'children' => [
                ['name' => ['ru' => 'Штукатурки', 'kz' => 'Сылақтар']],
                ['name' => ['ru' => 'Шпаклевки', 'kz' => 'Шпаклевкалар']],
                ['name' => ['ru' => 'Цемент', 'kz' => 'Цемент']],
            ],
        ],
        [
            'name' => ['ru' => 'Кирпич и блоки', 'kz' => 'Кірпіш және блоктар'],
            'children' => [
                ['name' => ['ru' => 'Кирпич', 'kz' => 'Кірпіш']],
                ['name' => ['ru' => 'Газоблок', 'kz' => 'Газ блок']],
                ['name' => ['ru' => 'Пеноблок', 'kz' => 'Пеноблок']],
            ],
        ],
        [
            'name' => ['ru' => 'Пиломатериалы', 'kz' => 'Ағаш материалдары'],
            'children' => [
                ['name' => ['ru' => 'Доска', 'kz' => 'Тақтай']],
                ['name' => ['ru' => 'Брус', 'kz' => 'Брус']],
                ['name' => ['ru' => 'Фанера', 'kz' => 'Фанера']],
            ],
        ],
        [
            'name' => ['ru' => 'Сантехника', 'kz' => 'Сантехника'],
            'children' => [
                ['name' => ['ru' => 'Смесители', 'kz' => 'Араластырғыштар']],
                ['name' => ['ru' => 'Трубы', 'kz' => 'Құбырлар']],
                ['name' => ['ru' => 'Фитинги', 'kz' => 'Фитингтер']],
            ],
        ],
        [
            'name' => ['ru' => 'Электрика', 'kz' => 'Электрика'],
            'children' => [
                ['name' => ['ru' => 'Кабели', 'kz' => 'Кабельдер']],
                ['name' => ['ru' => 'Розетки', 'kz' => 'Розеткалар']],
                ['name' => ['ru' => 'Автоматы', 'kz' => 'Автоматтар']],
            ],
        ],
        [
            'name' => ['ru' => 'Крепёж', 'kz' => 'Бекіткіштер'],
            'children' => [
                ['name' => ['ru' => 'Саморезы', 'kz' => 'Өздігінен бұранда']],
                ['name' => ['ru' => 'Гвозди', 'kz' => 'Шегелер']],
                ['name' => ['ru' => 'Болты', 'kz' => 'Болттар']],
            ],
        ],
        [
            'name' => ['ru' => 'Отделочные материалы', 'kz' => 'Бояу және әрлеу материалдары'],
            'children' => [
                ['name' => ['ru' => 'Лакокрасочные материалы', 'kz' => 'Бояулар']],
                ['name' => ['ru' => 'Обои', 'kz' => 'Түсқағаз']],
                ['name' => ['ru' => 'Напольные покрытия', 'kz' => 'Еден жабындары']],
            ],
        ],
        [
            'name' => ['ru' => 'Инструменты', 'kz' => 'Құралдар'],
            'children' => [
                ['name' => ['ru' => 'Ручной инструмент', 'kz' => 'Қол құралдары']],
                ['name' => ['ru' => 'Электроинструмент', 'kz' => 'Электр құралдары']],
                ['name' => ['ru' => 'Расходные материалы', 'kz' => 'Шығын материалдары']],
            ],
        ],
        [
            'name' => ['ru' => 'Металлопрокат', 'kz' => 'Металл өнімдері'],
            'children' => [
                ['name' => ['ru' => 'Арматура', 'kz' => 'Арматура']],
                ['name' => ['ru' => 'Уголок', 'kz' => 'Бұрыш']],
                ['name' => ['ru' => 'Труба проф.', 'kz' => 'Профильді құбыр']],
            ],
        ],
        [
            'name' => ['ru' => 'Окна и двери', 'kz' => 'Терезелер мен есіктер'],
            'children' => [
                ['name' => ['ru' => 'Двери', 'kz' => 'Есіктер']],
                ['name' => ['ru' => 'Окна ПВХ', 'kz' => 'ПВХ терезелер']],
                ['name' => ['ru' => 'Фурнитура', 'kz' => 'Фурнитура']],
            ],
        ],
    ],

    'user_types' => [
        [
            'name' => [
                'en' => 'Admin',
                'ru' => 'Админ',
                'kz' => 'Админ',
            ],
        ],
        [
            'name' => [
                'en' => 'Retail Customer',
                'ru' => 'Покупатель частное лицо',
                'kz' => 'Жеке сатып алушы',
            ],
        ],
        [
            'name' => [
                'en' => 'Company Customer',
                'ru' => 'Покупатель компания',
                'kz' => 'Компания сатып алушы',
            ],
        ],
        [
            'name' => [
                'en' => 'Seller',
                'ru' => 'Продавец',
                'kz' => 'Сатушы',
            ],
        ],
    ],
];

// Tables
// cities: name
// user_types: name_ru, name_kz
// user:  type_id, city_id, name, email, email_verified_at, phone, phone_verified_at, password, active (boolean)
// units: name_ru, name_kz
// companies: city_id, name, user_id
// shops: company_id, name, address, min_order_amount
// units: name_ru, name_kz
// categories: name_ru, name_kz, parent_id, icon (nullable), slug (nullable)
// category_specs: category_id, spec_id, is_required(boolean)
// nomenclatures: name_ru, name_kz, unit_id, category_id, description_kz (nullable), description_ru (nullable), SKU (nullable), GTIN (nullable), NTIN (nullable), brandname (nullable)
// products: name_ru, name_kz, nomenclature_id, shop_id, price, quantity
// specs: name_ru, name_kz
// product_specs: product_id, spec_name, spec_value

// transactions
// cart
// cart_items
// orders
// order_items
