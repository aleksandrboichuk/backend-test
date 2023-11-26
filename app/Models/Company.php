<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\HigherOrderWhenProxy;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'address',
    ];

    /**
     * Relation Company-Users through company positions
     *
     * @return HasManyThrough
     */
    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            CompanyPositions::class,
            'company_id',
            'user_id',
            'company_id',
            'user_id'
        );
    }

    /**
     * Returns all companies with pagination and allows to use search by fields
     *
     * @param int $perPage
     * @param array $searchFields
     * @return LengthAwarePaginator
     */
    public function getAll(int $perPage = 10, array $searchFields = []): LengthAwarePaginator
    {
        return $this->getSearchByFieldsBuilder($searchFields)
            ->with('users')
            ->paginate($perPage);
    }

    /**
     * Returns query builder with query by search fields array
     *
     * @param array $searchFields
     * @return Builder|HigherOrderWhenProxy
     */
    public function getSearchByFieldsBuilder(array $searchFields): Builder|HigherOrderWhenProxy
    {
        return $this->query()
            ->when(!empty($searchFields), function ($builder) use ($searchFields) {
                $builder->where(function ($builder) use ($searchFields){
                    foreach ($searchFields as $field => $value) {
                        $builder->orWhere($field, 'like', "%$value%");
                    }
                });
            });
    }
}
