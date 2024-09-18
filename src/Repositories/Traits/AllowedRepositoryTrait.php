<?php

namespace bobrovva\repo_kit\Repositories\Traits;

/**
 * Трейт AllowedRepositoryTrait
 *
 * Предназначен для работы с разрешенными полями (allowed) в запросах.
 * Трейт предоставляет функционал для задания разрешенных полей по умолчанию и через запрос.
 */
trait AllowedRepositoryTrait
{
    /**
     * Разрешенные поля для выборки.
     *
     * @var array
     */
    private array $allowed = [];

    /**
     * Устанавливает разрешенные поля по умолчанию.
     *
     * @return array Разрешенные поля по умолчанию.
     */
    protected function setDefaultAllowed(): array
    {
        return $this->allowed;
    }

    /**
     * Устанавливает разрешенные поля, переданные в запросе.
     *
     * @param array $allowed Массив разрешенных полей.
     * @return self Возвращает текущий экземпляр для цепочечных вызовов.
     */
    public function setQueryAllowed(array $allowed): self
    {
        $this->allowed = $allowed;
        return $this;
    }

    /**
     * Проверяет и устанавливает разрешенные поля.
     *
     * Если разрешенные поля не заданы, используются поля по умолчанию,
     * а также добавляется поле 'id' и заполняемые поля из модели.
     *
     * @return void
     */
    protected function setAllowed(): void
    {
        if (empty($this->allowed)) {
            $this->allowed = $this->setDefaultAllowed();
        }

        if (empty($this->allowed)) {
            $this->allowed = array_merge(['id'], $this->model->getFillable());
        }
    }
}