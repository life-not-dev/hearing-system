<?php

namespace App\Repositories;

use App\Models\HearingAid;
use Illuminate\Support\Collection;

class HearingAidRepository implements HearingAidRepositoryInterface
{
    public function all(): Collection
    {
        return HearingAid::orderBy('id')->get();
    }

    public function create(array $data): HearingAid
    {
        return HearingAid::create($data);
    }

    public function update(HearingAid $hearingAid, array $data): bool
    {
        return $hearingAid->update($data);
    }

    public function delete(HearingAid $hearingAid): bool
    {
        return $hearingAid->delete();
    }
}
