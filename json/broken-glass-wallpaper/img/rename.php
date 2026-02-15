<?php

/**
 * Переименовывает все файлы в указанной директории в формат 'gradient-X.ext'.
 *
 * @param string $directoryPath Путь к директории с файлами.
 * @return void
 */
function renameFilesInDirectory(string $directoryPath): void
{
    // Проверяем, существует ли директория и доступна ли для чтения
    if (!is_dir($directoryPath) || !is_readable($directoryPath)) {
        echo "Ошибка: Директория не найдена или недоступна для чтения: $directoryPath\n";
        return;
    }

    // Получаем список файлов, исключая системные папки '.' и '..'
    $files = array_diff(scandir($directoryPath), ['.', '..']);

    if (empty($files)) {
        echo "Информация: Директория пуста: $directoryPath\n";
        return;
    }

    // Найдем максимальный номер среди уже переименованных файлов, чтобы продолжить нумерацию
    $maxNumber = -1;
    foreach ($files as $file) {
        if (is_file($directoryPath . DIRECTORY_SEPARATOR . $file) && preg_match('/^gradient-(\d+)/', $file, $matches)) {
            if ((int) $matches[1] > $maxNumber) {
                $maxNumber = (int) $matches[1];
            }
        }
    }
    $counter = $maxNumber + 1;

    // Сортируем файлы в "естественном" порядке (например, 'img2.jpg' будет перед 'img10.jpg')
    sort($files, SORT_NATURAL);

    $renamedCounter = 0;
    echo "Начинаем переименование в папке: $directoryPath\n";

    foreach ($files as $file) {
        $oldFilePath = $directoryPath . DIRECTORY_SEPARATOR . $file;

        // Убедимся, что это действительно файл, а не поддиректория
        if (is_file($oldFilePath)) {
            // Если имя файла уже соответствует формату 'gradient-ЧИСЛО', пропускаем его.
            if (preg_match('/^broken-glass-\d+/', $file)) {
                echo "  - Пропускаем: Файл '$file' уже в нужном формате.\n";
                continue;
            }

            // Получаем расширение файла
            $extension = pathinfo($oldFilePath, PATHINFO_EXTENSION);

            // Формируем новое имя файла. Если у файла нет расширения, оно не будет добавлено.
            $newFileName = 'broken-glass-' . $counter . ($extension ? '.' . $extension : '');
            $newFilePath = $directoryPath . DIRECTORY_SEPARATOR . $newFileName;

            // Переименовываем файл
            if (rename($oldFilePath, $newFilePath)) {
                echo "  - Файл '$file' успешно переименован в '$newFileName'\n";
                $counter++;
                $renamedCounter++;
            } else {
                echo "  - Ошибка: Не удалось переименовать файл '$file'\n";
            }
        }
    }

    echo "Переименование в папке $directoryPath завершено. Всего переименовано файлов: $renamedCounter.\n\n";
}

// --- НАЧАЛО ВЫПОЛНЕНИЯ СКРИПТА ---

// Определяем пути к директориям относительно текущего файла.
// Это делает скрипт более переносимым.
$bigDirectory = __DIR__ . '/big';
$minDirectory = __DIR__ . '/min';

// Выполняем переименование для обеих директорий
renameFilesInDirectory($bigDirectory);
renameFilesInDirectory($minDirectory);
