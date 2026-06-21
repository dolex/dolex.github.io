<?php

// ============================================================
// 1. ГЛОБАЛЬНЫЕ НАСТРОЙКИ (с комментариями)
// ============================================================

/**
 * Переименовать файлы в папке /big? (по старому формату broken-glass-)
 */
$enableRenameInBig = false;

/**
 * Переименовать файлы в папке /min?
 */
$enableRenameInMin = false;

/**
 * Включить выборочное переименование в папке /rename (по новому формату wallpaper-)
 */
$enableCustomRename = false;

/**
 * Включить сдвиг индексов (чтобы освободить место)
 */
$enableIndexShift = false;

/**
 * Добавить новые обои из папки /rename в data.json (основной режим)
 */
$enableAddExtensions = true;

/**
 * Конвертировать WebP в JPG (качество 100%)
 */
$enableConvertWebpToJpg = true;

/**
 * Конвертировать PNG в JPG (качество 100%)
 */
$enableConvertPngToJpg = true;

// --- Режимы работы с JSON ---

/**
 * Режим работы с JSON:
 * - 'merge'  – добавить новые записи к существующим (не удаляя старые)
 * - 'replace' – полностью перезаписать JSON новыми данными (если папка big не пуста – пересоздать из существующих файлов)
 */
$jsonMode = 'replace';

/**
 * Режим обновления JSON:
 * - 'after'  – обновить файл после обработки всех файлов
 * - 'during' – обновлять по мере обработки каждого файла (полезно при большом количестве)
 */
$jsonUpdateMode = 'after';

// --- Ограничения для изображений в папке min ---

/**
 * Максимальная высота для thumbnail (ширина 250px). Если исходная высота больше – обрезается по центру.
 */
$maxHeightThumbnail = 400;

/**
 * Максимальная высота для preview (ширина 500px). Если исходная высота больше – обрезается по центру.
 */
$maxHeightPreview = 850;

// --- Настройки переименования (для папки /rename) ---

/**
 * Путь к папке с исходными файлами (которые будут обработаны)
 */
$customRenameDirectory = __DIR__ . '/rename';

/**
 * Базовое имя для новых файлов (будет добавлен номер и расширение)
 */
$customRenameBaseName = 'wallpaper-';

/**
 * Начальный индекс для нумерации
 */
$customRenameStartIndex = 1;

// --- Настройки сдвига индексов ---

/**
 * Папка, в которой сдвигать индексы
 */
$shiftDirectory = __DIR__ . '/rename';

/**
 * Базовое имя файлов, которые нужно сдвинуть
 */
$shiftBaseName = 'wallpaper-';

/**
 * Начиная с какого индекса сдвигать (включительно)
 */
$shiftFromIndex = 1;

// --- Настройки для добавления обоев в JSON ---

/**
 * Папка, откуда брать исходные файлы для обработки (если не используется режим пересоздания из существующих)
 */
$addWallpapersSourceDir = __DIR__ . '/rename';

/**
 * Путь к файлу data.json (относительно корня проекта)
 */
$jsonFilePath = __DIR__ . '/../data.json';

/**
 * Ключ в JSON, содержащий массив обоев
 */
$wallpaperJsonKey = 'wallpaper';

/**
 * Префикс пути для thumbnail (будет добавлен в imgMinThumbnail)
 */
$minThumbnailPrefix = 'img/min/thumbnail/';

/**
 * Префикс пути для preview (будет добавлен в imgMinPreview)
 */
$minPreviewPrefix = 'img/min/preview/';

/**
 * Префикс пути для оригинала (будет добавлен в imgDownloadOriginal)
 */
$bigPathPrefix = 'img/big/';

// ============================================================
// 2. ФУНКЦИИ
// ============================================================

// --- Функции переименования (опущены для краткости, но должны быть ---
// renameFilesInDirectory, renameFilesFromIndex, shiftFileIndexes
// Они уже были даны ранее, здесь я их не дублирую, но в вашем файле они должны быть.

// --- Функции конвертации (WebP, PNG в JPG) ---
// convertWebpToJpg, convertPngToJpg – уже были даны

// --- Функция обрезки по высоте ---
function cropImageByHeight($image, int $maxHeight, string $mime)
{
    // ... (код был дан ранее)
}

// --- Функция ресайза и обрезки ---
function resizeAndCropImage(string $sourcePath, string $targetPath, int $targetWidth, int $maxHeight = null, int $quality = 85): bool
{
    // ... (код был дан ранее)
}

/**
 * Обновляет JSON файл с данными обоев.
 * Всегда возвращает bool (true при успехе, false при ошибке).
 */
function updateJsonFile(array $data, string $jsonFilePath, int $addedCounter, string $jsonMode): bool
{
    if ($addedCounter === 0) {
        // Нечего обновлять, но возвращаем true (успех)
        return true;
    }

    // Если режим replace, то оставляем только ключ 'wallpaper'
    if ($jsonMode === 'replace') {
        $data = ['wallpaper' => $data['wallpaper']];
    }

    $newJsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if (file_put_contents($jsonFilePath, $newJsonData) !== false) {
        echo "Файл '$jsonFilePath' успешно обновлен. Добавлено новых записей: $addedCounter.\n\n";
        return true;
    } else {
        echo "Ошибка: Не удалось записать обновленные данные в '$jsonFilePath'.\n\n";
        return false;
    }
}

/**
 * Сканирует папки big, min/thumbnail, min/preview и собирает записи для JSON.
 * Добавляет поле id (порядковый номер, начиная с 1).
 */
function scanExistingWallpapers(string $bigDir, string $thumbDir, string $previewDir, string $minThumbnailPrefix, string $minPreviewPrefix, string $bigPathPrefix): array
{
    $entries = [];
    $bigFiles = array_diff(scandir($bigDir), ['.', '..']);
    $id = 1;
    foreach ($bigFiles as $file) {
        if (is_file($bigDir . DIRECTORY_SEPARATOR . $file) && preg_match('/^wallpaper-\d+\.\w+$/', $file)) {
            $thumbPath = $thumbDir . DIRECTORY_SEPARATOR . $file;
            $previewPath = $previewDir . DIRECTORY_SEPARATOR . $file;
            if (file_exists($thumbPath) && file_exists($previewPath)) {
                $entries[] = [
                    'id' => $id,
                    'imgMinThumbnail' => $minThumbnailPrefix . $file,
                    'imgMinPreview' => $minPreviewPrefix . $file,
                    'imgDownloadOriginal' => $bigPathPrefix . $file,
                ];
                $id++;
            }
        }
    }
    return $entries;
}

/**
 * Основная функция: либо создаёт новые обои из папки /rename, либо (при $jsonMode === 'replace')
 * перезаписывает JSON на основе уже существующих файлов в папках big, min/thumbnail, min/preview.
 * Добавляет поле id в каждую запись.
 */
function addFilesAsWallpapersToJson(
    string $sourceDirectoryPath,
    string $jsonFilePath,
    string $wallpaperJsonKey,
    string $minThumbnailPrefix,
    string $minPreviewPrefix,
    string $bigPathPrefix,
    string $baseName,
    int $startIndex,
    bool $convertWebpToJpg = false,
    bool $convertPngToJpg = false,
    int $maxHeightThumbnail = 400,
    int $maxHeightPreview = 850,
    string $jsonMode = 'merge',
    string $jsonUpdateMode = 'after'
): void {
    if (!file_exists($jsonFilePath) || !is_readable($jsonFilePath) || !is_writable($jsonFilePath)) {
        echo "Ошибка: Файл data.json не найден, не доступен для чтения или записи: $jsonFilePath\n";
        return;
    }

    $bigDir = __DIR__ . '/big';
    $thumbDir = __DIR__ . '/min/thumbnail';
    $previewDir = __DIR__ . '/min/preview';

    // === Режим REPLACE: если папка big не пуста, пересоздаём JSON из существующих файлов ===
    if ($jsonMode === 'replace' && is_dir($bigDir) && !empty(array_diff(scandir($bigDir), ['.', '..']))) {
        echo "Режим REPLACE: обнаружены существующие файлы в папке big. Пересоздаём JSON без повторной обработки.\n";

        $entries = scanExistingWallpapers($bigDir, $thumbDir, $previewDir, $minThumbnailPrefix, $minPreviewPrefix, $bigPathPrefix);
        if (empty($entries)) {
            echo "Не найдено полных наборов файлов (big + thumbnail + preview). Проверьте папки.\n";
            return;
        }

        $data = [$wallpaperJsonKey => $entries];
        updateJsonFile($data, $jsonFilePath, count($entries), 'replace');
        return;
    }

    // === Обычный режим: создание из папки /rename ===
    if (!is_dir($sourceDirectoryPath) || !is_readable($sourceDirectoryPath)) {
        echo "Ошибка: Директория для сканирования не найдена или недоступна: $sourceDirectoryPath\n";
        return;
    }

    $files = array_diff(scandir($sourceDirectoryPath), ['.', '..']);
    if (empty($files)) {
        echo "Информация: Директория '$sourceDirectoryPath' пуста. Нечего добавлять.\n";
        return;
    }

    sort($files, SORT_NATURAL);

    $jsonData = file_get_contents($jsonFilePath);
    $data = json_decode($jsonData, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Ошибка: Не удалось декодировать JSON из файла '$jsonFilePath'. Ошибка: " . json_last_error_msg() . "\n";
        return;
    }
    if (!isset($data[$wallpaperJsonKey]) || !is_array($data[$wallpaperJsonKey])) {
        echo "Ошибка: Ключ '$wallpaperJsonKey' не найден или не является массивом в файле '$jsonFilePath'.\n";
        return;
    }

    if ($jsonMode === 'replace') {
        $data[$wallpaperJsonKey] = [];
        echo "Режим REPLACE: JSON будет полностью перезаписан новыми данными.\n";
    }

    // Создаём папки, если их нет
    foreach ([$bigDir, $thumbDir, $previewDir] as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            echo "Информация: Создана директория $dir\n";
        }
    }

    $addedCounter = 0;
    $counter = $startIndex;
    $id = 1; // для id в JSON
    echo "Начинаем добавление записей из '$sourceDirectoryPath' в '$jsonFilePath'\n";

    $existingMinThumbnailPaths = array_column($data[$wallpaperJsonKey], 'imgMinThumbnail');

    foreach ($files as $file) {
        $filePath = $sourceDirectoryPath . DIRECTORY_SEPARATOR . $file;
        if (!is_file($filePath))
            continue;

        $imageInfo = @getimagesize($filePath);
        if ($imageInfo === false) {
            echo "  - Пропускаем: Файл '$file' не является изображением.\n";
            continue;
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $sourceFileForProcessing = $filePath;
        $originalExtension = strtolower($extension);

        // Конвертация WebP
        if ($convertWebpToJpg && $originalExtension === 'webp') {
            $jpgFileName = pathinfo($file, PATHINFO_FILENAME) . '.jpg';
            $jpgFilePath = $sourceDirectoryPath . DIRECTORY_SEPARATOR . $jpgFileName;
            if (convertWebpToJpg($filePath, $jpgFilePath, 100)) {
                $sourceFileForProcessing = $jpgFilePath;
                $extension = 'jpg';
                echo "  - WebP конвертирован в JPG: $file -> $jpgFileName\n";
            } else {
                echo "  - Ошибка: Не удалось конвертировать WebP файл '$file'\n";
                continue;
            }
        }
        // Конвертация PNG
        elseif ($convertPngToJpg && $originalExtension === 'png') {
            $jpgFileName = pathinfo($file, PATHINFO_FILENAME) . '.jpg';
            $jpgFilePath = $sourceDirectoryPath . DIRECTORY_SEPARATOR . $jpgFileName;
            if (convertPngToJpg($filePath, $jpgFilePath, 100)) {
                $sourceFileForProcessing = $jpgFilePath;
                $extension = 'jpg';
                echo "  - PNG конвертирован в JPG: $file -> $jpgFileName\n";
            } else {
                echo "  - Ошибка: Не удалось конвертировать PNG файл '$file'\n";
                continue;
            }
        }

        $newFileName = $baseName . $counter . '.' . $extension;

        // Копируем оригинал в big
        $bigFilePath = $bigDir . DIRECTORY_SEPARATOR . $newFileName;
        if (file_exists($bigFilePath)) {
            echo "  - Пропускаем: Файл '$newFileName' уже существует в папке big.\n";
            $counter++;
            continue;
        }
        if (!copy($sourceFileForProcessing, $bigFilePath)) {
            echo "  - Ошибка: Не удалось скопировать файл в папку big как '$newFileName'\n";
            $counter++;
            continue;
        }
        echo "  - Оригинал скопирован в big как '$newFileName'\n";

        // Создаём превью
        $thumbPath = $thumbDir . DIRECTORY_SEPARATOR . $newFileName;
        $previewPath = $previewDir . DIRECTORY_SEPARATOR . $newFileName;
        $resize250 = resizeAndCropImage($sourceFileForProcessing, $thumbPath, 250, $maxHeightThumbnail);
        $resize500 = resizeAndCropImage($sourceFileForProcessing, $previewPath, 500, $maxHeightPreview);

        // Удаляем временный JPG
        if (($convertWebpToJpg && $originalExtension === 'webp') || ($convertPngToJpg && $originalExtension === 'png')) {
            if ($sourceFileForProcessing !== $filePath && file_exists($sourceFileForProcessing)) {
                unlink($sourceFileForProcessing);
                echo "  - Временный JPG файл удален: " . basename($sourceFileForProcessing) . "\n";
            }
        }

        if ($resize250 && $resize500) {
            $entry = [
                'id' => $id, // добавляем поле id
                'imgMinThumbnail' => $minThumbnailPrefix . $newFileName,
                'imgMinPreview' => $minPreviewPrefix . $newFileName,
                'imgDownloadOriginal' => $bigPathPrefix . $newFileName,
            ];

            if ($jsonMode === 'merge' && in_array($entry['imgMinThumbnail'], $existingMinThumbnailPaths)) {
                echo "  - Пропускаем: Запись для файла '$newFileName' уже существует.\n";
                $counter++;
                $id++;
                continue;
            }

            $data[$wallpaperJsonKey][] = $entry;
            if ($jsonMode === 'merge') {
                $existingMinThumbnailPaths[] = $entry['imgMinThumbnail'];
            }
            echo "  - Добавлена запись для файла '$newFileName' (id: $id)\n";
            $addedCounter++;
            $id++;

            if ($jsonUpdateMode === 'during' && $addedCounter > 0) {
                updateJsonFile($data, $jsonFilePath, $addedCounter, $jsonMode);
                $addedCounter = 0;
            }
        } else {
            echo "  - Ошибка: Не удалось создать превью для файла '$file'\n";
        }
        $counter++;
    }

    if ($jsonUpdateMode === 'after' || $jsonMode === 'replace') {
        if ($addedCounter > 0) {
            updateJsonFile($data, $jsonFilePath, $addedCounter, $jsonMode);
        } else {
            echo "Новых уникальных файлов для добавления в '$sourceDirectoryPath' не найдено.\n\n";
        }
    }
}

// ============================================================
// 3. ВЫПОЛНЕНИЕ СКРИПТА
// ============================================================

if ($enableRenameInBig) {
    renameFilesInDirectory(__DIR__ . '/big');
}
if ($enableRenameInMin) {
    renameFilesInDirectory(__DIR__ . '/min');
}
if ($enableCustomRename) {
    if (!is_dir($customRenameDirectory)) {
        mkdir($customRenameDirectory, 0777, true);
        echo "Информация: Создана директория $customRenameDirectory\n";
    }
    renameFilesFromIndex($customRenameDirectory, $customRenameBaseName, $customRenameStartIndex);
}
if ($enableIndexShift) {
    shiftFileIndexes($shiftDirectory, $shiftBaseName, $shiftFromIndex);
}
if ($enableAddExtensions) {
    if (!is_dir($addWallpapersSourceDir)) {
        mkdir($addWallpapersSourceDir, 0777, true);
        echo "Информация: Создана директория $addWallpapersSourceDir\n";
    }
    addFilesAsWallpapersToJson(
        $addWallpapersSourceDir,
        $jsonFilePath,
        $wallpaperJsonKey,
        $minThumbnailPrefix,
        $minPreviewPrefix,
        $bigPathPrefix,
        $customRenameBaseName,
        $customRenameStartIndex,
        $enableConvertWebpToJpg,
        $enableConvertPngToJpg,
        $maxHeightThumbnail,
        $maxHeightPreview,
        $jsonMode,
        $jsonUpdateMode
    );
}