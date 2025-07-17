<?php

namespace Moinframe\Loop\Models;

class Reply
{
	public function __construct(
		public int $id = 0,
		public string $author = '',
		public string $comment = '',
		public int $parentId = 0,
		public int $timestamp = 0
	) {}

	/**
	 * Create a Reply instance from an array
	 *
	 * @param array{id?: int, author?: string, comment?: string, parentId?: int, timestamp?: int} $data
	 * @return self
	 * @throws \InvalidArgumentException If validation fails
	 */

	public static function fromArray($data): self
	{
		$errors = self::validateData($data);
		if (count($errors) > 0) {
			throw new \InvalidArgumentException(tt('moinframe.loop.reply.validation.failed', ['errors' => implode(', ', $errors)]));
		}

		$data = static::transformNumbers($data);

		return new self(
			id: $data['id'] ?? 0,
			author: strip_tags($data['author'] ?? ''),
			comment: strip_tags($data['comment'] ?? ''),
			parentId: $data['parentId'] ?? 0,
			timestamp: $data['timestamp'] ?? 0
		);
	}

	/**
	 * Transforms numeric fields in reply data
	 *
	 * @param array<mixed> $item
	 * @return array<mixed>
	 */
	protected static function transformNumbers(array $item): array
	{
		$result = $item;

		if (isset($result['id'])) {
			$result['id'] = (int)$result['id'];
		}

		if (isset($result['parentId'])) {
			$result['parentId'] = (int)$result['parentId'];
		}

		if (isset($result['timestamp'])) {
			$result['timestamp'] = (int)$result['timestamp'];
		}

		return $result;
	}

	/**
	 * Validates the reply data using Kirby validators
	 * @return array<string> Array of validation errors (empty if valid)
	 */
	public function validate(): array
	{
		$rules = [
			'author' => ['required', 'maxLength' => 255],
			'comment' => ['required', 'maxLength' => 5000],
			'parentId' => ['required', 'min' => 1],
			'timestamp' => ['min' => 0]
		];

		$messages = [
			'author' => [
				t('moinframe.loop.author.required'),
				t('moinframe.loop.author.max.length')
			],
			'comment' => [
				t('moinframe.loop.comment.required'),
				t('moinframe.loop.comment.max.length')
			],
			'parentId' => [
				t('moinframe.loop.parent.id.required'),
				t('moinframe.loop.parent.id.required')
			],
			'timestamp' => t('moinframe.loop.timestamp.min')
		];

		$data = $this->toArray();
		/** @phpstan-ignore-next-line */
		$invalid = invalid($data, $rules, $messages) ?: [];
		return array_map('strval', array_values($invalid));
	}

	/**
	 * Checks if the reply is valid
	 * @return bool
	 */
	public function isValid(): bool
	{
		return count($this->validate()) === 0;
	}

	/**
	 * Validates data before creating Reply instance using Kirby validators
	 * @param array<mixed> $data
	 * @return array<string> Array of validation errors
	 */
	public static function validateData(array $data): array
	{
		$rules = [
			'author' => ['required', 'maxLength' => 255],
			'comment' => ['required', 'maxLength' => 5000],
			'parentId' => ['required', 'min' => 1],
			'timestamp' => ['min' => 0]
		];

		$messages = [
			'author' => [
				t('moinframe.loop.author.required'),
				t('moinframe.loop.author.max.length')
			],
			'comment' => [
				t('moinframe.loop.comment.required'),
				t('moinframe.loop.comment.max.length')
			],
			'parentId' => [
				t('moinframe.loop.parent.id.required'),
				t('moinframe.loop.parent.id.required')
			],
			'timestamp' => t('moinframe.loop.timestamp.min')
		];

		/** @phpstan-ignore-next-line */
		$invalid = invalid($data, $rules, $messages) ?: [];
		return array_map('strval', array_values($invalid));
	}

	/**
	 * Resolves the author string to a display name
	 * If author starts with 'user://', attempts to resolve Kirby user
	 * Returns user's name or email prefix, otherwise returns the stored string
	 * @return string Resolved author display name
	 */
	public function resolveAuthor(): string
	{
		// Check if author is a Kirby user reference
		if (str_starts_with($this->author, 'user://')) {
			$userId = substr($this->author, 7); // Remove 'user://' prefix

			try {
				$user = kirby()->user($userId);
				if ($user !== null && $user->exists()) {
					// Return user's name if available
					if ($user->name()->isNotEmpty()) {
						return $user->name()->value();
					}

					// Fallback to email prefix (everything before @)
					$email = $user->email();
					if ($email !== null && str_contains($email, '@')) {
						return explode('@', $email)[0];
					}

					// Final fallback to email
					return $email !== null ? $email : $this->author;
				}
			} catch (\Exception) {
				// If user resolution fails, fall back to original string
			}
		}

		// Return the original author string if not a user reference or resolution failed
		return $this->author;
	}

	/**
	 * Convert Reply instance to array
	 *
	 * @return array<mixed>
	 */
	public function toArray(): array
	{
		return [
			'id' => $this->id,
			'author' => $this->resolveAuthor(),
			'comment' => $this->comment,
			'parentId' => $this->parentId,
			'timestamp' => $this->timestamp
		];
	}
}
