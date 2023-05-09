<?php

namespace Up\Calendar\Services;

class FlashMessage
{
	public static function setError(string $message): void
	{
		$_SESSION['errors'][] = $message;
	}

	public static function setArray(array $messages): void
	{
		foreach($messages as $message)
		{
			self::setError($message);
		}
	}

	public static function showMessages(): string
	{
		return implode("<br>", $_SESSION['errors']);
	}

	public static function unset(): void
	{
		unset($_SESSION['errors']);
	}

	public static function hasError(): bool
	{
		if (isset($_SESSION['errors']))
		{
			return true;
		}
		return false;
	}
}