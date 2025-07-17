<?php

namespace Moinframe\Loop\Models;

use Moinframe\Loop\Enums\CommentStatus;

/**
 * Comment model
 * @property int $id
 * @property string $author
 * @property string $url
 * @property string $page
 * @property string $comment
 * @property string $selector
 * @property string $lang
 * @property float $selectorOffsetX
 * @property float $selectorOffsetY
 * @property float $pagePositionX
 * @property float $pagePositionY
 * @property CommentStatus $status
 * @property int $timestamp
 * @property Reply[] $replies
 *
 */
class Comment
{

	public function __construct(
		public int $id = 0,
		public string $author = "",
		public string $url = "",
		public string $page = "",
		public string $comment = "",
		public string $selector = "",
		public float $selectorOffsetX = 0,
		public float $selectorOffsetY = 0,
		public float $pagePositionX = 0,
		public float $pagePositionY = 0,
		public CommentStatus $status = CommentStatus::OPEN,
		public int $timestamp = 0,
		public string $lang = "",
		/** @var Reply[] */
		public array $replies = []
	) {}

	/**
	 * Create a Comment instance from an array
	 *
	 * @param array{id?: int, author?: string, url?: string, page?: string, selector?: string, selectorOffsetX?: float, selectorOffsetY?: float, pagePositionX?: float, pagePositionY?: float, status?: CommentStatus, comment?: string, parentId?: int, timestamp?: int} $data
	 * @return self
	 * @throws \InvalidArgumentException If validation fails
	 */

	public static function fromArray($data): self
	{

		$errors = self::validateData($data);
		if (count($errors) > 0) {
			throw new \InvalidArgumentException(tt('moinframe.loop.comment.validation.failed', ['errors' => implode(', ', $errors)]));
		}

		$data = static::transformNumbers($data);

		// Convert reply arrays to Reply objects
		$replies = [];
		if (isset($data['replies']) && is_array($data['replies'])) {
			foreach ($data['replies'] as $replyData) {
				$replies[] = Reply::fromArray($replyData);
			}
		}

		return new self(
			id: $data['id'] ?? 0,
			author: strip_tags($data['author'] ?? ''),
			url: $data['url'] ?? '',
			page: $data['page'] ?? '',
			selector: $data['selector'] ?? '',
			selectorOffsetX: $data['selectorOffsetX'] ?? 0,
			selectorOffsetY: $data['selectorOffsetY'] ?? 0,
			pagePositionX: $data['pagePositionX'] ?? 0,
			pagePositionY: $data['pagePositionY'] ?? 0,
			status: isset($data['status']) ? CommentStatus::from($data['status']) : CommentStatus::OPEN,
			comment: strip_tags($data['comment'] ?? ''),
			timestamp: $data['timestamp'] ?? 0,
			lang: $data['lang'] ?? '',
			replies: $replies
		);
	}


	/**
	 * Transforms numeric fields in comment data
	 *
	 * @param array<mixed> $item
	 * @return array<mixed>
	 */
	protected static function transformNumbers(array $item): array
	{
		// Create a new array instead of modifying the input
		$result = $item;

		// Handle each numeric field explicitly
		if (isset($result['id'])) {
			$result['id'] = (int)$result['id'];
		}

		if (isset($result['selectorOffsetX'])) {
			$result['selectorOffsetX'] = (float)$result['selectorOffsetX'];
		}

		if (isset($result['selectorOffsetY'])) {
			$result['selectorOffsetY'] = (float)$result['selectorOffsetY'];
		}

		if (isset($result['pagePositionX'])) {
			$result['pagePositionX'] = (float)$result['pagePositionX'];
		}

		if (isset($result['pagePositionY'])) {
			$result['pagePositionY'] = (float)$result['pagePositionY'];
		}

		if (isset($result['timestamp'])) {
			$result['timestamp'] = (int)$result['timestamp'];
		}


		return $result;
	}

	/**
	 * Validates the comment data using Kirby validators
	 * @return array<string> Array of validation errors (empty if valid)
	 */
	/** @phpstan-ignore-next-line */
	public function validate(): array
	{
		$rules = [
			'author' => ['required', 'maxLength' => 255],
			'comment' => ['required', 'maxLength' => 5000],
			'page' => ['required', 'maxLength' => 255],
			'selector' => ['required', 'maxLength' => 1000],
			'url' => ['maxLength' => 2048],
			'selectorOffsetX' => ['min' => 0],
			'selectorOffsetY' => ['min' => 0],
			'pagePositionX' => ['min' => 0],
			'pagePositionY' => ['min' => 0],
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
			'page' => [
				t('moinframe.loop.page.required'),
				t('moinframe.loop.page.max.length')
			],
			'selector' => [
				t('moinframe.loop.selector.required'),
				t('moinframe.loop.selector.max.length')
			],
			'url' => t('moinframe.loop.url.max.length'),
			'selectorOffsetX' => t('moinframe.loop.selector.offset.x.min'),
			'selectorOffsetY' => t('moinframe.loop.selector.offset.y.min'),
			'pagePositionX' => t('moinframe.loop.page.position.x.min'),
			'pagePositionY' => t('moinframe.loop.page.position.y.min'),
			'timestamp' => t('moinframe.loop.timestamp.min')
		];

		$data = $this->toArray();

		// Custom URL validation if provided
		if (($data['url'] ?? '') !== '' && filter_var($data['url'], FILTER_VALIDATE_URL) === false) {
			return [t('moinframe.loop.url.format.invalid')];
		}

		/** @phpstan-ignore-next-line */
		$invalid = invalid($data, $rules, $messages) ?: [];

		// Convert validation errors to flat array of strings
		$errors = [];
		foreach ($invalid as $field => $fieldErrors) {
			if (is_array($fieldErrors)) {
				// Multiple validation rules failed for this field
				foreach ($fieldErrors as $error) {
					$errors[] = (string) $error;
				}
			} else {
				// Single validation rule failed
				$errors[] = (string) $fieldErrors;
			}
		}

		// Validate replies
		foreach ($this->replies as $index => $reply) {
			$replyErrors = $reply->validate();
			foreach ($replyErrors as $replyError) {
				$errors[] = tt('moinframe.loop.reply.index.error', ['index' => $index, 'error' => $replyError]);
			}
		}

		return $errors;
	}

	/**
	 * Checks if the comment is valid
	 * @return bool
	 */
	public function isValid(): bool
	{
		return count($this->validate()) === 0;
	}

	/**
	 * Validates data before creating Comment instance using Kirby validators
	 * @param array<mixed> $data
	 * @return array<string> Array of validation errors
	 */
	/** @phpstan-ignore-next-line */
	public static function validateData(array $data): array
	{
		$rules = [
			'author' => ['required', 'maxLength' => 255],
			'comment' => ['required', 'maxLength' => 5000],
			'page' => ['required', 'maxLength' => 255],
			'selector' => ['required', 'maxLength' => 1000],
			'url' => ['maxLength' => 2048],
			'selectorOffsetX' => ['min' => 0],
			'selectorOffsetY' => ['min' => 0],
			'pagePositionX' => ['min' => 0],
			'pagePositionY' => ['min' => 0],
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
			'page' => [
				t('moinframe.loop.page.required'),
				t('moinframe.loop.page.max.length')
			],
			'selector' => [
				t('moinframe.loop.selector.required'),
				t('moinframe.loop.selector.max.length')
			],
			'url' => t('moinframe.loop.url.max.length'),
			'selectorOffsetX' => t('moinframe.loop.selector.offset.x.min'),
			'selectorOffsetY' => t('moinframe.loop.selector.offset.y.min'),
			'pagePositionX' => t('moinframe.loop.page.position.x.min'),
			'pagePositionY' => t('moinframe.loop.page.position.y.min'),
			'timestamp' => t('moinframe.loop.timestamp.min')
		];

		// Custom URL validation if provided
		if (($data['url'] ?? '') !== '' && filter_var($data['url'], FILTER_VALIDATE_URL) === false) {
			return [t('moinframe.loop.url.format.invalid')];
		}

		/** @phpstan-ignore-next-line */
		$invalid = invalid($data, $rules, $messages) ?: [];

		// Convert validation errors to flat array of strings
		$errors = [];
		foreach ($invalid as $field => $fieldErrors) {
			if (is_array($fieldErrors)) {
				// Multiple validation rules failed for this field
				foreach ($fieldErrors as $error) {
					$errors[] = (string) $error;
				}
			} else {
				// Single validation rule failed
				$errors[] = (string) $fieldErrors;
			}
		}

		return $errors;
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
	 * Convert Comment instance to array
	 *
	 * @return array<mixed>
	 */
	public function toArray(): array
	{
		// Convert Reply objects to arrays
		$replies = [];
		foreach ($this->replies as $reply) {
			$replies[] = $reply->toArray();
		}

		return [
			'id' => $this->id ?? null,
			'author' => $this->resolveAuthor(),
			'url' => $this->url,
			'page' => $this->page,
			'comment' => $this->comment,
			'selector' => $this->selector,
			'selectorOffsetX' => $this->selectorOffsetX,
			'selectorOffsetY' => $this->selectorOffsetY,
			'pagePositionX' => $this->pagePositionX,
			'pagePositionY' => $this->pagePositionY,
			'status' => $this->status->value,
			'replies' => $replies,
			'timestamp' => $this->timestamp,
			'lang' => $this->lang
		];
	}
}
