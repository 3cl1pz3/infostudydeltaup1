<?php
declare(strict_types=1);

/**
 * @return array<string, array{progress_percent: int, updated_at: string|null}>
 */
function getUserReadingProgressMap(int $userId): array
{
    try {
        $stmt = getDb()->prepare(
            'SELECT section_slug, progress_percent, updated_at FROM reading_progress WHERE user_id = :uid ORDER BY section_slug ASC'
        );
        $stmt->execute(['uid' => $userId]);
        $out = [];
        foreach ($stmt->fetchAll() as $row) {
            $slug = (string)$row['section_slug'];
            $out[$slug] = [
                'progress_percent' => (int)$row['progress_percent'],
                'updated_at' => $row['updated_at'] !== null ? (string)$row['updated_at'] : null,
            ];
        }

        return $out;
    } catch (Throwable $e) {
        return [];
    }
}

function upsertReadingProgress(int $userId, string $sectionSlug, int $percent): void
{
    if (!getSectionConfig($sectionSlug)) {
        return;
    }
    $percent = max(0, min(100, $percent));

    $sql = 'INSERT INTO reading_progress (user_id, section_slug, progress_percent)
            VALUES (:user_id, :section_slug, :progress_percent)
            ON DUPLICATE KEY UPDATE
              progress_percent = GREATEST(progress_percent, VALUES(progress_percent)),
              updated_at = CURRENT_TIMESTAMP';

    $stmt = getDb()->prepare($sql);
    $stmt->execute([
        'user_id' => $userId,
        'section_slug' => $sectionSlug,
        'progress_percent' => $percent,
    ]);
}
