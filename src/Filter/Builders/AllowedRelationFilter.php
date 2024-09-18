<?php

namespace bobrovva\repo_kit\Filter\Builders;

/**
 * Класс AllowedRelationFilter
 *
 * Наследует `AllowedFilter` и предоставляет функциональность для фильтров, которые относятся к связям между моделями.
 * Использует префикс `'relation'` для фильтров, связанных с отношениями.
 */
class AllowedRelationFilter extends AllowedFilter
{
    /**
     * Префикс, используемый для фильтров отношений.
     */
    public const prefix = 'relation';
}