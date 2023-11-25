<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Query\Builder;

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
        return $this->query()
            ->with('users')
            ->when(!empty($searchFields), function ($builder) use ($searchFields) {
                $builder->where(function ($builder) use ($searchFields){
                    foreach ($searchFields as $field => $value) {
                        $builder->orWhere($field, 'like', "%$value%");
                    }
                });

            })->paginate($perPage);
    }
}
