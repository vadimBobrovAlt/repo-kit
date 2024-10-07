<?php

namespace bobrovva\repo_kit\Repositories\Traits;

use bobrovva\repo_kit\Filter\FilterBuilder;
use Illuminate\Database\Eloquent\Builder;

trait FilterRepositoryTrait
{
    /**
     * Разрешенные фильтры для запроса.
     *
     * @var array
     */
    protected array $allowedFilters = [];

    /**
     * Системные фильтры, которые будут применяться ко всем запросам.
     *
     * @var array
     */
    protected array $systemFilters = [];

    /**
     * Фильтры запроса, действуют только в 1 запросе.
     *
     * @var array
     */
    protected array $queryFilters = [];

    /**
     * Устанавливает разрешенные фильтры для запроса.
     *
     * @return array Разрешенные фильтры
     */
    protected function setAllowedFilters(): array
    {
        return $this->allowedFilters;
    }

    /**
     * Устанавливает системные фильтры, которые будут применяться ко всем запросам.
     *
     * @return array Системные фильтры
     */
    protected function setSystemFilters(): array
    {
        return $this->systemFilters;
    }

    /**
     * Устанавливает фильтры для запроса.
     *
     * @param array $filters Фильтры для установки
     * @return self Возвращает текущий экземпляр класса
     */
    public function setQueryFilters(array $filters): self
    {
        $this->queryFilters = $filters;
        return $this;
    }

    /**
     * Применяет фильтры к запросу.
     *
     * Фильтры будут объединены с системными фильтрами и фильтрами из запроса.
     *
     * @param Builder $query Построитель запроса Eloquent
     * @param array|null $filters Фильтры для применения
     * @return Builder Построитель запроса Eloquent с примененными фильтрами
     */
    protected function setFilter(Builder $query, ?array $filters = []): Builder
    {
        $allowedFilters = array_reduce($this->setAllowedFilters(), fn($carry, $item) => array_merge($carry, $item), []);

        $query = FilterBuilder::build(
            $query,
            array_merge($filters, request()->get('filters', []), $this->queryFilters, $this->setSystemFilters()),
            $allowedFilters
        );

        $this->queryFilters = [];

        return $query;
    }
}