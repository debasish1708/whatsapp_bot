<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute musí být přijat.',
    'accepted_if' => ':attribute musí být přijat, když je :other rovno :value.',
    'active_url' => ':attribute není platná URL adresa.',
    'after' => ':attribute musí být datum po :date.',
    'after_or_equal' => ':attribute musí být datum po nebo rovno :date.',
    'alpha' => ':attribute může obsahovat pouze písmena.',
    'alpha_dash' => ':attribute může obsahovat pouze písmena, číslice a pomlčky.',
    'alpha_num' => ':attribute může obsahovat pouze písmena a číslice.',
    'array' => ':attribute musí být pole.',
    'before' => ':attribute musí být datum před :date.',
    'before_or_equal' => ':attribute musí být datum před nebo rovno :date.',
    'between' => [
        'array' => ':attribute musí mít mezi :min a :max položkami.',
        'file' => ':attribute musí být mezi :min a :max kilobajty.',
        'numeric' => ':attribute musí být mezi :min a :max.',
        'string' => ':attribute musí být mezi :min a :max znaky.',
    ],
    'boolean' => ':attribute musí být true nebo false.',
    'confirmed' => 'Potvrzení :attribute se neshoduje.',
    'current_password' => 'Heslo je nesprávné.',
    'date' => ':attribute není platné datum.',
    'date_equals' => ':attribute musí být rovno :date.',
    'date_format' => ':attribute se neshoduje s formátem :format.',
    'declined' => ':attribute musí být odmítnut.',
    'declined_if' => ':attribute musí být odmítnut, pokud je :other rovno :value.',
    'different' => ':attribute a :other se musí lišit.',
    'digits' => ':attribute musí mít :digits číslic.',
    'digits_between' => ':attribute musí mít mezi :min a :max číslicemi.',
    'dimensions' => ':attribute má neplatné rozměry obrázku.',
    'distinct' => ':attribute má duplicitní hodnotu.',
    'email' => ':attribute musí být platná e-mailová adresa.',
    'ends_with' => ':attribute musí končit jednou z následujících hodnot: :values.',
    'enum' => 'Vybraná hodnota :attribute je neplatná.',
    'exists' => 'Vybraná hodnota :attribute je neplatná.',
    'file' => ':attribute musí být soubor.',
    'filled' => ':attribute musí mít hodnotu.',
    'gt' => [
        'array' => ':attribute musí mít více než :value položek.',
        'file' => ':attribute musí být větší než :value kilobajtů.',
        'numeric' => ':attribute musí být větší než :value.',
        'string' => ':attribute musí být delší než :value znaků.',
    ],
    'gte' => [
        'array' => ':attribute musí mít :value položek nebo více.',
        'file' => ':attribute musí být větší nebo rovno :value kilobajtům.',
        'numeric' => ':attribute musí být větší nebo rovno :value.',
        'string' => ':attribute musí být dlouhé alespoň :value znaků.',
    ],
    'image' => ':attribute musí být obrázek.',
    'in' => 'Vybraná hodnota pro :attribute je neplatná.',
    'in_array' => ':attribute neexistuje v :other.',
    'integer' => ':attribute musí být celé číslo.',
    'ip' => ':attribute musí být platná IP adresa.',
    'ipv4' => ':attribute musí být platná IPv4 adresa.',
    'ipv6' => ':attribute musí být platná IPv6 adresa.',
    'json' => ':attribute musí být platný JSON řetězec.',
    'lt' => [
        'array' => ':attribute musí mít méně než :value položek.',
        'file' => ':attribute musí být menší než :value kilobajtů.',
        'numeric' => ':attribute musí být menší než :value.',
        'string' => ':attribute musí být kratší než :value znaků.',
    ],
    'lte' => [
        'array' => ':attribute nesmí mít více než :value položek.',
        'file' => ':attribute musí být menší nebo rovno :value kilobajtům.',
        'numeric' => ':attribute musí být menší nebo rovno :value.',
        'string' => ':attribute musí být maximálně :value znaků.',
    ],
    'mac_address' => ':attribute musí být platná MAC adresa.',
    'max' => [
        'array' => ':attribute nesmí mít více než :max položek.',
        'file' => ':attribute nesmí být větší než :max kilobajtů.',
        'numeric' => ':attribute nesmí být větší než :max.',
        'string' => ':attribute nesmí být delší než :max znaků.',
    ],
    'mimes' => ':attribute musí být soubor typu: :values.',
    'mimetypes' => ':attribute musí být soubor typu: :values.',
    'min' => [
        'array' => ':attribute musí mít alespoň :min položek.',
        'file' => ':attribute musí být alespoň :min kilobajtů.',
        'numeric' => ':attribute musí být alespoň :min.',
        'string' => ':attribute musí být alespoň :min znaků.',
    ],
    'multiple_of' => ':attribute musí být násobkem :value.',
    'not_in' => 'Vybraná hodnota :attribute je neplatná.',
    'not_regex' => 'Formát :attribute je neplatný.',
    'numeric' => ':attribute musí být číslo.',
    'password' => 'Heslo je nesprávné.',
    'present' => ':attribute musí být přítomno.',
    'prohibited' => ':attribute je zakázáno.',
    'prohibited_if' => ':attribute je zakázáno, pokud je :other rovno :value.',
    'prohibited_unless' => ':attribute je zakázáno, pokud není :other v :values.',
    'prohibits' => ':attribute brání přítomnosti :other.',
    'regex' => 'Formát :attribute je neplatný.',
    'required' => 'Pole :attribute je povinné.',
    'required_array_keys' => ':attribute musí obsahovat záznamy pro: :values.',
    'required_if' => 'Pole :attribute je povinné, pokud je :other rovno :value.',
    'required_unless' => 'Pole :attribute je povinné, pokud není :other v :values.',
    'required_with' => 'Pole :attribute je povinné, pokud je přítomno :values.',
    'required_with_all' => 'Pole :attribute je povinné, pokud jsou přítomny :values.',
    'required_without' => 'Pole :attribute je povinné, pokud není přítomno :values.',
    'required_without_all' => 'Pole :attribute je povinné, pokud nejsou přítomny žádné z :values.',
    'same' => ':attribute a :other se musí shodovat.',
    'size' => [
        'array' => ':attribute musí obsahovat :size položek.',
        'file' => ':attribute musí být :size kilobajtů velikosti.',
        'numeric' => ':attribute musí být :size.',
        'string' => ':attribute musí být :size znaků dlouhý.',
    ],
    'starts_with' => ':attribute musí začínat jednou z následujících hodnot: :values.',
    'string' => ':attribute musí být řetězec.',
    'timezone' => ':attribute musí být platné časové pásmo.',
    'unique' => ':attribute je již zabrané.',
    'uploaded' => 'Nahrání :attribute selhalo.',
    'url' => ':attribute musí být platná URL.',
    'uuid' => ':attribute musí být platné UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'vlastní zpráva',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];