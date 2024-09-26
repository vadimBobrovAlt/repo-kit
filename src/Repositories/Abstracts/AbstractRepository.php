<?php

namespace bobrovva\repo_kit\Repositories\Abstracts;

use bobrovva\repo_kit\Repositories\Interfaces\BaseRepositoryInterface;
use bobrovva\repo_kit\Repositories\Traits\AllowedRepositoryTrait;
use bobrovva\repo_kit\Repositories\Traits\FilterRepositoryTrait;
use bobrovva\repo_kit\Repositories\Traits\PaginateRepositoryTrait;
use bobrovva\repo_kit\Repositories\Traits\SelectFieldRepositoryTrait;
use bobrovva\repo_kit\Repositories\Traits\SoftDeleteTrait;
use bobrovva\repo_kit\Repositories\Traits\SortRepositoryTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

abstract class AbstractRepository implements BaseRepositoryInterface
{
    use AllowedRepositoryTrait;
    use PaginateRepositoryTrait;
    use SortRepositoryTrait;
    use FilterRepositoryTrait;
    use SelectFieldRepositoryTrait;
    use SoftDeleteTrait;

    protected string $modelClass;
    protected Model $model;

    public function __construct(?Model $model = null)
    {
        if (!$model) {
            $model = $this->createModel($this->modelClass);
        }

        $this->model = $model;
    }

    /**
     * Создает модель по указанному классу модели.
     *
     * @param string $modelClass Класс модели
     * @return Model Созданная модель
     */
    public function createModel(string $modelClass): Model
    {
        return app($modelClass);
    }

    /**
     * Получает запрос с возможными атрибутами (фильтрация, сортировка, выборка полей и т.д.).
     *
     * @param array $attributes Атрибуты запроса (фильтры, сортировки и т.д.)
     * @return Builder Конфигурируемый запрос
     */
    public function getQuery(array $attributes = []): Builder
    {
        $this->setAllowed();
        $query = $this->model->query();
        $query = $this->setSort($query, $attributes['sorts'] ?? []);
        $query = $this->setFilter($query, $attributes['filters'] ?? []);
        $query = $this->setSelectField($query, $attributes['select_fields'] ?? '');
        return $query;
    }

    /**
     * Получает список всех данных с фильтрацией.
     *
     * @return Collection Коллекция моделей
     */
    public function get(): Collection
    {
        return $this->getQuery()->get();
    }

    /**
     * Получает одну запись по ID или первую запись, если ID не указан.
     *
     * @param string|null $id Идентификатор записи (опционально)
     * @return Model|null Модель или null, если запись не найдена
     */
    public function one(?string $id = null): ?Model
    {
        if ($id) {
            return $this->getQuery()->find($id);
        }

        return $this->getQuery()->first();
    }

    /**
     * Получает одну запись по ID или фильтрам, иначе выбрасывает исключение, если запись не найдена.
     *
     * @param string|null $id Идентификатор записи (опционально)
     * @return Model Найденная модель
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Если запись не найдена
     */
    public function oneOrFail(?string $id = null): Model
    {
        if ($id) {
            return $this->getQuery()->findOrFail($id);
        }

        return $this->getQuery()->firstOrFail();
    }

    /**
     * Создает новую запись в базе данных.
     *
     * @param array $data Данные для создания записи
     * @return Model Созданная модель
     */
    public function create(array $data): Model
    {
        return $this->getQuery()->create($data);
    }

    /**
     * Обновляет существующую запись.
     *
     * @param string|Model $model Модель или идентификатор записи
     * @param array $data Данные для обновления
     * @return Model Обновленная модель
     */
    public function update(string|Model $model, array $data): ?Model
    {
        $model = $this->getModel($model);
        $model->update($data);
        $model->refresh();
        return $model;
    }

    /**
     * Удаляет запись.
     *
     * @param string|Model $model Модель или идентификатор записи
     * @return bool Успех операции удаления
     */
    public function delete(string|Model $model): bool
    {
        $model = $this->getModel($model);
        return $model->delete();
    }

    /**
     * Проверяет существование записей.
     *
     * @return bool Возвращает true, если записи существуют
     */
    public function exists(): bool
    {
        return $this->getQuery()->exists();
    }

    /**
     * Возвращает количество записей.
     *
     * @return int Количество записей
     */
    public function count(): int
    {
        return $this->getQuery()->count();
    }

    /**
     * Обновляет запись или создает новую, если запись не найдена.
     *
     * @param array $attributes Атрибуты для поиска записи
     * @param array $values Дополнительные значения для создания записи
     * @return Model Обновленная или созданная модель
     */
    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        return $this->getQuery()->updateOrCreate($attributes, $values);
    }

    /**
     * Находит первую запись с указанными атрибутами или создает новую запись, если она не найдена.
     *
     * @param array $attributes Атрибуты для поиска записи
     * @param array $values Дополнительные значения для создания записи
     * @return Model Найденная или созданная модель
     */
    public function firstOrCreate(array $attributes = [], array $values = []): Model
    {
        return $this->getQuery()->firstOrCreate($attributes, $values);
    }

    /**
     * Выполняет массовую вставку нескольких записей в базу данных.
     *
     * @param array $data Массив данных для вставки
     * @return bool Возвращает true, если вставка прошла успешно
     */
    public function bulkInsert(array $data): bool
    {
        return DB::table($this->model->getTable())->insert($data);
    }

    /**
     * Выполняет "сырой" SQL запрос и возвращает коллекцию результатов
     *
     * @param string $query SQL-запрос
     * @return Collection Коллекция результатов запроса
     */
    public function rawQuery(string $query): Collection
    {
        return collect(DB::select(DB::raw($query)));
    }

    /**
     * Восстанавливает удаленную запись по ID.
     *
     * @param string $id Идентификатор записи
     * @return bool Возвращает true, если запись успешно восстановлена
     */
    public function restore(string $id): bool
    {
        $model = $this->model->onlyTrashed()->find($id);
        if ($model) {
            return $model->restore();
        }
        return false;
    }

    /**
     * Принудительно удаляет запись по ID (без возможности восстановления).
     *
     * @param string $id Идентификатор записи
     * @return bool Возвращает true, если запись была успешно удалена
     */
    public function forceDelete(string $id): bool
    {
        $model = $this->model->onlyTrashed()->find($id);
        if ($model) {
            return $model->forceDelete();
        }
        return false;
    }

    /**
     * Выполняет массовое обновление записей с помощью фильтров и данных для обновления.
     *
     * @param array $filters Условия для поиска записей, которые нужно обновить
     * @param array $data Массив данных для обновления записей
     * @return bool Возвращает true, если обновление прошло успешно
     */
    public function bulkUpdate(array $filters, array $data): bool
    {
        return $this->getQuery()->where($filters)->update($data);
    }

    private function getModel(string|Model $model): Model
    {
        if (is_string($model)) {
            $model = $this->getQuery()->findOrFail($model);
        }

        return $model;
    }
}
