<?php

namespace App\Traits;

use App\Enums\PaymentStatusEnum;

trait PaymentsStatuses
{
	public function getStatusAttribute($value)
	{
		return PaymentStatusEnum::getKey(intval($value));
	}

	public function setStatusAttribute($value)
	{
		if (!is_integer($value))
			$value = PaymentStatusEnum::getValue(mb_ucfirst($value));

		$this->attributes['status'] = $value;
	}

	public function isStatusError()
	{
		return $this->status == PaymentStatusEnum::getKey(PaymentStatusEnum::Error);
	}

	public function isStatusSuccess()
	{
		return $this->status == PaymentStatusEnum::getKey(PaymentStatusEnum::Success);
	}

	public function isStatusWait()
	{
		return $this->status == PaymentStatusEnum::getKey(PaymentStatusEnum::Wait);
	}

	public function isStatusProcessing()
	{
		return $this->status == PaymentStatusEnum::getKey(PaymentStatusEnum::Processing);
	}

	public function isStatusCanceled()
	{
		return $this->status == PaymentStatusEnum::getKey(PaymentStatusEnum::Canceled);
	}

	public function statusProcessing()
	{
		$this->status = PaymentStatusEnum::Processing;
		$this->status_changed_at = now();
	}

	public function statusWait()
	{
		$this->status = PaymentStatusEnum::Wait;
		$this->status_changed_at = now();
	}

	public function statusSuccess()
	{
		$this->status = PaymentStatusEnum::Success;
		$this->status_changed_at = now();
	}

	public function statusError()
	{
		$this->status = PaymentStatusEnum::Error;
		$this->status_changed_at = now();
	}

	public function statusCanceled()
	{
		$this->status = PaymentStatusEnum::Canceled;
		$this->status_changed_at = now();
	}

	public function scopeWait($query)
	{
		return $query->where('status', PaymentStatusEnum::Wait);
	}

	public function scopeProcessed($query)
	{
		return $query->where('status', PaymentStatusEnum::Processing);
	}
}