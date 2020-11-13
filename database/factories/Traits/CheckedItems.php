<?php

namespace Database\Factories\Traits;

use App\Enums\StatusEnum;
use App\User;

trait CheckedItems
{
    public function private()
    {
        return $this->afterMaking(function ($item) {
            $item->{$item->getStatusColumn()} = StatusEnum::Private;
        });
    }

    public function accepted()
    {
        return $this->afterMaking(function ($item) {
            $item->{$item->getStatusColumn()} = StatusEnum::Accepted;
        });
    }

    public function rejected()
    {
        return $this->afterMaking(function ($item) {
            $item->{$item->getStatusColumn()} = StatusEnum::Rejected;
        });
    }

    public function sent_for_review()
    {
        return $this->afterMaking(function ($item) {
            $item->{$item->getStatusColumn()} = StatusEnum::OnReview;
        });
    }

    public function review_starts()
    {
        return $this->afterMaking(function ($item) {
            $item->{$item->getStatusColumn()} = StatusEnum::ReviewStarts;
        });
    }
}