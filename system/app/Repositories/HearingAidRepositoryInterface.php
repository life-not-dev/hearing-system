<?php

namespace App\Repositories;

use App\Models\HearingAid;
use Illuminate\Support\Collection;

interface HearingAidRepositoryInterface
{
    public function all(): Collection;
    public function create(array $data): HearingAid;
    public function update(HearingAid $hearingAid, array $data): bool;
    public function delete(HearingAid $hearingAid): bool;
}
