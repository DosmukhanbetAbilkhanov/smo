<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'accepted' => ':attribute өрісі қабылдануы тиіс.',
    'active_url' => ':attribute өрісі жарамды URL емес.',
    'after' => ':attribute өрісі :date кейінгі күн болуы тиіс.',
    'after_or_equal' => ':attribute өрісі :date кейінгі немесе тең күн болуы тиіс.',
    'alpha' => ':attribute өрісі тек әріптерден тұруы мүмкін.',
    'alpha_dash' => ':attribute өрісі тек әріптер, сандар, сызықшалар және асты сызықтардан тұруы мүмкін.',
    'alpha_num' => ':attribute өрісі тек әріптер мен сандардан тұруы мүмкін.',
    'array' => ':attribute өрісі массив болуы тиіс.',
    'before' => ':attribute өрісі :date дейінгі күн болуы тиіс.',
    'before_or_equal' => ':attribute өрісі :date дейінгі немесе тең күн болуы тиіс.',
    'between' => [
        'numeric' => ':attribute өрісі :min мен :max арасында болуы тиіс.',
        'file' => ':attribute файлының өлшемі :min мен :max килобайт арасында болуы тиіс.',
        'string' => ':attribute өрісі :min мен :max таңба арасында болуы тиіс.',
        'array' => ':attribute өрісі :min мен :max элемент арасында болуы тиіс.',
    ],
    'boolean' => ':attribute өрісі шын немесе жалған болуы тиіс.',
    'confirmed' => ':attribute растауы сәйкес емес.',
    'date' => ':attribute өрісі жарамды күн емес.',
    'date_equals' => ':attribute өрісі :date тең күн болуы тиіс.',
    'date_format' => ':attribute өрісі :format форматына сәйкес емес.',
    'different' => ':attribute және :other өрістері әр түрлі болуы тиіс.',
    'digits' => ':attribute өрісі :digits сан болуы тиіс.',
    'digits_between' => ':attribute өрісі :min мен :max сан арасында болуы тиіс.',
    'dimensions' => ':attribute өрісінің сурет өлшемдері жарамсыз.',
    'distinct' => ':attribute өрісінің қайталанатын мәні бар.',
    'email' => ':attribute өрісі жарамды электрондық пошта мекенжайы болуы тиіс.',
    'ends_with' => ':attribute өрісі келесі мәндердің бірімен аяқталуы тиіс: :values.',
    'exists' => 'Таңдалған :attribute мәні жарамсыз.',
    'file' => ':attribute өрісі файл болуы тиіс.',
    'filled' => ':attribute өрісі мәні болуы тиіс.',
    'gt' => [
        'numeric' => ':attribute өрісі :value үлкен болуы тиіс.',
        'file' => ':attribute файлының өлшемі :value килобайттан үлкен болуы тиіс.',
        'string' => ':attribute өрісі :value таңбадан көп болуы тиіс.',
        'array' => ':attribute өрісі :value элементтен көп болуы тиіс.',
    ],
    'gte' => [
        'numeric' => ':attribute өрісі :value үлкен немесе тең болуы тиіс.',
        'file' => ':attribute файлының өлшемі :value килобайттан үлкен немесе тең болуы тиіс.',
        'string' => ':attribute өрісі :value немесе одан көп таңба болуы тиіс.',
        'array' => ':attribute өрісі :value немесе одан көп элемент болуы тиіс.',
    ],
    'image' => ':attribute өрісі сурет болуы тиіс.',
    'in' => 'Таңдалған :attribute мәні жарамсыз.',
    'in_array' => ':attribute өрісі :other ішінде жоқ.',
    'integer' => ':attribute өрісі бүтін сан болуы тиіс.',
    'ip' => ':attribute өрісі жарамды IP мекенжайы болуы тиіс.',
    'ipv4' => ':attribute өрісі жарамды IPv4 мекенжайы болуы тиіс.',
    'ipv6' => ':attribute өрісі жарамды IPv6 мекенжайы болуы тиіс.',
    'json' => ':attribute өрісі жарамды JSON жолы болуы тиіс.',
    'lt' => [
        'numeric' => ':attribute өрісі :value кіші болуы тиіс.',
        'file' => ':attribute файлының өлшемі :value килобайттан кіші болуы тиіс.',
        'string' => ':attribute өрісі :value таңбадан аз болуы тиіс.',
        'array' => ':attribute өрісі :value элементтен аз болуы тиіс.',
    ],
    'lte' => [
        'numeric' => ':attribute өрісі :value кіші немесе тең болуы тиіс.',
        'file' => ':attribute файлының өлшемі :value килобайттан кіші немесе тең болуы тиіс.',
        'string' => ':attribute өрісі :value немесе одан аз таңба болуы тиіс.',
        'array' => ':attribute өрісі :value немесе одан аз элемент болуы тиіс.',
    ],
    'max' => [
        'numeric' => ':attribute өрісі :max үлкен болмауы тиіс.',
        'file' => ':attribute файлының өлшемі :max килобайттан үлкен болмауы тиіс.',
        'string' => ':attribute өрісі :max таңбадан көп болмауы тиіс.',
        'array' => ':attribute өрісі :max элементтен көп болмауы тиіс.',
    ],
    'mimes' => ':attribute өрісі келесі түрдегі файл болуы тиіс: :values.',
    'mimetypes' => ':attribute өрісі келесі түрдегі файл болуы тиіс: :values.',
    'min' => [
        'numeric' => ':attribute өрісі кем дегенде :min болуы тиіс.',
        'file' => ':attribute файлының өлшемі кем дегенде :min килобайт болуы тиіс.',
        'string' => ':attribute өрісі кем дегенде :min таңба болуы тиіс.',
        'array' => ':attribute өрісі кем дегенде :min элемент болуы тиіс.',
    ],
    'not_in' => 'Таңдалған :attribute мәні жарамсыз.',
    'not_regex' => ':attribute өрісінің форматы жарамсыз.',
    'numeric' => ':attribute өрісі сан болуы тиіс.',
    'present' => ':attribute өрісі болуы тиіс.',
    'regex' => ':attribute өрісінің форматы жарамсыз.',
    'required' => ':attribute өрісі міндетті.',
    'required_if' => ':attribute өрісі :other :value тең болғанда міндетті.',
    'required_unless' => ':attribute өрісі :other :values ішінде болмағанда міндетті.',
    'required_with' => ':attribute өрісі :values болғанда міндетті.',
    'required_with_all' => ':attribute өрісі барлық :values болғанда міндетті.',
    'required_without' => ':attribute өрісі :values жоқ болғанда міндетті.',
    'required_without_all' => ':attribute өрісі барлық :values жоқ болғанда міндетті.',
    'same' => ':attribute және :other өрістері сәйкес болуы тиіс.',
    'size' => [
        'numeric' => ':attribute өрісі :size болуы тиіс.',
        'file' => ':attribute файлының өлшемі :size килобайт болуы тиіс.',
        'string' => ':attribute өрісі :size таңба болуы тиіс.',
        'array' => ':attribute өрісі :size элемент болуы тиіс.',
    ],
    'starts_with' => ':attribute өрісі келесі мәндердің бірімен басталуы тиіс: :values.',
    'string' => ':attribute өрісі жол болуы тиіс.',
    'timezone' => ':attribute өрісі жарамды уақыт белдеуі болуы тиіс.',
    'unique' => 'Мұндай :attribute мәні бұрыннан бар.',
    'uploaded' => ':attribute жүктеу сәтсіз аяқталды.',
    'url' => ':attribute өрісінің форматы жарамсыз.',
    'uuid' => ':attribute өрісі жарамды UUID болуы тиіс.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'name' => 'атауы',
        'email' => 'email',
        'password' => 'құпия сөз',
        'phone' => 'телефон',
        'address' => 'мекенжай',
        'city' => 'қала',
        'price' => 'баға',
        'quantity' => 'саны',
        'description' => 'сипаттама',
    ],
];
