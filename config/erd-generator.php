<?php

return [

    /*
     * Ruta al directorio de Graphviz. Laravel ERD asumirá que el ejecutable 'dot'
     * está dentro de esta carpeta, sin necesidad de envolverlo en comillas extra.
     */
    'graphviz'         => 'C:\\Program Files\\Graphviz\\bin\\',

    /*
     * El generador intentará leer la estructura de columnas desde la base de datos
     * si esto está habilitado.
     */
    'use_db_schema'    => true,

    /*
     * Si quieres que aparezcan los tipos de columna (VARCHAR, INT, etc.) en el diagrama.
     * Requiere 'use_db_schema' => true.
     */
    'use_column_types' => true,

    /*
     * Directorios donde buscar tus modelos. Se escanearán recursivamente.
     */
    'directories' => [
        base_path('app' . DIRECTORY_SEPARATOR . 'Models'),
    ],

    /*
     * Ignorar modelos completos o relaciones específicas:
     * - Para ignorar un modelo: App\Models\SomeModel::class
     * - Para ignorar relaciones de un modelo: App\Models\Post::class => ['comments', 'author']
     */
    'ignore' => [
        // App\Models\SomeModel::class,
        // App\Models\Post::class => ['comments', 'author'],
    ],

    /*
     * Si quieres incluir solo ciertos modelos, enuméralos aquí.
     * Cuando esto no esté vacío, 'ignore' se omitirá.
     */
    'whitelist' => [
        // App\Models\User::class,
        // App\Models\Order::class,
    ],

    /*
     * Si quieres definir directorios adicionales, ponlos aquí.
     * 'recursive' controla si se exploran subcarpetas.
     */
    'recursive' => true,

    /*
     * Estilos de tabla en el diagrama.
     */
    'table' => [
        'header_background_color' => '#d3d3d3',
        'header_font_color'       => '#333333',
        'row_background_color'    => '#ffffff',
        'row_font_color'          => '#333333',
    ],

    /*
     * Atributos del grafo Graphviz. Consulta:
     * http://www.graphviz.org/doc/info/attrs.html#d:label
     */
    'graph' => [
        'style'      => 'filled',
        'bgcolor'    => '#F7F7F7',
        'fontsize'   => 12,
        'labelloc'   => 't',
        'concentrate'=> true,
        'splines'    => 'polyline',
        'overlap'    => false,
        'nodesep'    => 1,
        'rankdir'    => 'LR',
        'pad'        => 0.5,
        'ranksep'    => 2,
        'esep'       => true,
        'fontname'   => 'Helvetica Neue',
    ],

    /*
     * Estilo por defecto de nodos (entidades).
     */
    'node' => [
        'margin'   => 0,
        'shape'    => 'rectangle',
        'fontname' => 'Helvetica Neue',
    ],

    /*
     * Estilo por defecto de aristas (relaciones).
     */
    'edge' => [
        'color'    => '#003049',
        'penwidth' => 1.8,
        'fontname' => 'Helvetica Neue',
    ],

    /*
     * Estilo específico para cada tipo de relación.
     */
    'relations' => [
        'HasOne' => [
            'dir'       => 'both',
            'color'     => '#D62828',
            'arrowhead' => 'tee',
            'arrowtail' => 'none',
        ],
        'BelongsTo' => [
            'dir'       => 'both',
            'color'     => '#F77F00',
            'arrowhead' => 'tee',
            'arrowtail' => 'crow',
        ],
        'HasMany' => [
            'dir'       => 'both',
            'color'     => '#FCBF49',
            'arrowhead' => 'crow',
            'arrowtail' => 'none',
        ],
    ],

];
