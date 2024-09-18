<?php

namespace App\Repositories\Base;

use bobrovva\repo_kit\Filter\Builders\AllowedFilter;
use bobrovva\repo_kit\Filter\Builders\AllowedRelationFilter;
use bobrovva\repo_kit\Repositories\Abstracts\AbstractRepository;
use bobrovva\repo_kit\SelectField\Builders\AllowedField;
use bobrovva\repo_kit\SelectField\Builders\AllowedRelationField;
use Illuminate\Database\Eloquent\Model;

class BaseRepository extends AbstractRepository
{
    protected string $modelClass = Model::class;

    // Устанавливаем дефолтные поля
    protected function setDefaultAllowed(): array
    {
        return [
            'id',
            'name',
        ];
    }

    // Устанавливаем количество элементов при пагинации (по дефолту 25)
    protected function setDefaultPerPage(): int
    {
        return 30;
    }

    // Устанавливаем поля по которым разрешена сортировка (необязательно, если не указанно то берется из setDefaultAllowed)
    protected function setAllowedSorts(): array
    {
        return [
            'id',
            'name'
        ];
    }

    // Устанавливаем дефолтное поле для сортировки (по дефолту id)
    protected function setDefaultSorts(): array
    {
        return [
            'name'
        ];
    }

    // Устанавливаем поля по которым разрешена фильтровать
    protected function setAllowedFilters(): array
    {
        return [
            AllowedFilter::whereIn('id', 'uuid'),
            AllowedFilter::where('name'),
            AllowedFilter::whereLike('owner_id'),
            AllowedRelationFilter::where('owner_id', 'relation_table.owner_id'),
        ];
    }

    // Устанавливаем системные фильтры (необязательно)
    protected function setSystemFilters(): array
    {
        return [
            'owner_id' => 'shop_id',
            'is_active' => 'true'
        ];
    }

    // Необязательно по дефолту будут браться поля из setDefaultAllowed
    protected function setAllowedFields(): array
    {
        return [
            AllowedField::embed(
                'model_table',
                'id',
                'name',
                'slug',
                AllowedRelationField::embed('model_table', 'relation', [
                    'id',
                    'model'
                ], 'relation_table', 'relation_table_id', 'model_table_id')
            )
        ];
    }

    // Устанавливаем дефолтную выборку для репозитория (необязательно)
    protected function setDefaultFields(): array
    {
        return [
            'id',
            'name',
        ];
    }

    // Пример запроса
    public function queryExample()
    {
        $this
            ->setQueryAllowed(['id','name'])
            ->setQuerySorts(['name'])
            ->setQueryFilters([
                'id' => [1,2,3,4],
                'name' => 'test'
            ])
            ->withTrashed()
            ->get();

        $this->onlyTrashed()->get();
    }
}
