<?php
declare(strict_types=1);

function sectionConfigs(): array
{
    return [
        'osnovy' => [
            'page_title' => 'Основы информатики',
            'display_title' => 'Основы информатики',
            'default_file' => __DIR__ . '/../content/default/osnovy.html',
        ],
        'algoritmy' => [
            'page_title' => 'Алгоритмы',
            'display_title' => 'Алгоритмы и структуры данных',
            'default_file' => __DIR__ . '/../content/default/algoritmy.html',
        ],
        'programmirovanie' => [
            'page_title' => 'Программирование',
            'display_title' => 'Языки программирования',
            'default_file' => __DIR__ . '/../content/default/programmirovanie.html',
        ],
        'seti' => [
            'page_title' => 'Компьютерные сети',
            'display_title' => 'Компьютерные сети',
            'default_file' => __DIR__ . '/../content/default/seti.html',
        ],
        'apparat' => [
            'page_title' => 'Аппаратное обеспечение',
            'display_title' => 'Аппаратное обеспечение',
            'default_file' => __DIR__ . '/../content/default/apparat.html',
        ],
        'bezopasnost' => [
            'page_title' => 'Информационная безопасность',
            'display_title' => 'Информационная безопасность',
            'default_file' => __DIR__ . '/../content/default/bezopasnost.html',
        ],
    ];
}

function getSectionConfig(string $slug): ?array
{
    $sections = sectionConfigs();
    return $sections[$slug] ?? null;
}

function getDefaultSectionContent(string $slug): string
{
    $config = getSectionConfig($slug);
    if (!$config) {
        return '';
    }

    $filePath = $config['default_file'];
    if (!is_file($filePath)) {
        return '';
    }

    $content = file_get_contents($filePath);
    return is_string($content) ? $content : '';
}

function getSectionContent(string $slug): string
{
    $defaultContent = getDefaultSectionContent($slug);

    try {
        $stmt = getDb()->prepare('SELECT content_html FROM sections WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();

        if (!$row) {
            return $defaultContent;
        }

        $stored = trim((string)$row['content_html']);
        return $stored !== '' ? $stored : $defaultContent;
    } catch (Throwable $e) {
        return $defaultContent;
    }
}

function upsertSectionContent(string $slug, string $title, string $contentHtml): void
{
    $sql = 'INSERT INTO sections (slug, title, content_html) VALUES (:slug, :title, :content_html)
            ON DUPLICATE KEY UPDATE title = VALUES(title), content_html = VALUES(content_html), updated_at = CURRENT_TIMESTAMP';

    $stmt = getDb()->prepare($sql);
    $stmt->execute([
        'slug' => $slug,
        'title' => $title,
        'content_html' => $contentHtml,
    ]);
}
