<?php

namespace bobrovva\repo_kit\Repositories\Traits;

use bobrovva\repo_kit\Sort\SortBuilder;
use Illuminate\Database\Eloquent\Builder;

trait SortRepositoryTrait
{
    /**
     * Сортировка по умолчанию.
     * @var array
     */
    private array $defaultSort = ['id'];

    /**
     * Сортировки, переданные в запросе.
     * @var array
     */
    private array $querySorts = [];

    /**
     * Разрешенные параметры сортировки.
     * @var array
     */
    private array $allowedSorts = [];

    /**
     * Устанавливает сортировки по умолчанию.
     *
     * @return array Сортировки по умолчанию.
     */
    protected function setDefaultSorts(): array
    {
        return $this->defaultSort;
    }

    /**
     * Устанавливает разрешенные параметры сортировки.
     *
     * @return array Разрешенные параметры сортировки.
     */
    protected function setAllowedSorts(): array
    {
        return $this->allowedSorts;
    }

    /**
     * Устанавливает сортировки, переданные в запросе.
     *
     * @param array $sorts Массив параметров сортировки.
     * @return self Возвращает текущий экземпляр для цепочечных вызовов.
     */
    public function setQuerySorts(array $sorts): self
    {
        $this->querySorts = $sorts;
        return $this;
    }

    /**
     * Применяет сортировку к запросу.
     *
     * @param Builder $query Экземпляр запроса.
     * @param array|null $sorts Массив параметров сортировки, переданный в запросе.
     * @return Builder Возвращает модифицированный запрос с примененной сортировкой.
     */
    protected function setSort(Builder $query, ?array $sorts = []): Builder
    {
        $allowedSorts = $this->setAllowedSorts();
        if (empty($allowedSorts)) {
            $allowedSorts = $this->allowedSorts;
        }

        $defaultSorts = $this->querySorts;
        if (empty($defaultSorts)) {
            $defaultSorts = $this->setDefaultSorts();
        }

        $query = SortBuilder::build($query, array_merge($defaultSorts, $sorts), $allowedSorts);

        $this->querySorts = [];

        return $query;
    }
}
