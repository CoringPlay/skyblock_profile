<?php
// ajax_sort_profile.php

// Функция для сравнения питомцев по редкости
function comparePetsByRarity($a, $b) {
    $rarityOrder = [
        'mythic' => 1,
        'legendary' => 2,
        'epic' => 3,
        'rare' => 4,
        'uncommon' => 5,
        'common' => 6,
    ];

    $rarityA = isset($a['rarity']) ? $a['rarity'] : 'common';
    $rarityB = isset($b['rarity']) ? $b['rarity'] : 'common';

    return $rarityOrder[$rarityA] - $rarityOrder[$rarityB];
}

// Функция для сравнения питомцев по уровню
function comparePetsByLevel($a, $b) {
    $levelA = isset($a['level']['level']) ? $a['level']['level'] : 0;
    $levelB = isset($b['level']['level']) ? $b['level']['level'] : 0;

    return $levelB - $levelA; // Изменение порядка для сортировки от большего к меньшему
}

// Получаем выбранный метод сортировки
$sortMethod = isset($_GET['sort']) ? $_GET['sort'] : 'rarity';

// Получаем playerName из GET-параметра
$playerName = isset($_GET['playerName']) ? $_GET['playerName'] : '';

if (!empty($playerName)) {
    // Ваш код для получения данных о питомцах (замените на ваш реальный код)
    $json = file_get_contents("https://sky.shiiyu.moe/api/v2/profile/{$playerName}");

    if ($json !== false) {
        $data = json_decode($json, true);

        // Ваш код для обработки и сортировки питомцев
        if ($data && isset($data['profiles'])) {
            // Выбираем первый профиль (вы можете настроить выбор профиля по вашим требованиям)
            $profile = reset($data['profiles']);
            
            // Проверяем, есть ли объект "raw" и внутри него массив "pets"
            if (isset($profile['raw']['pets']) && is_array($profile['raw']['pets'])) {
                // Получаем массив питомцев
                $pets = $profile['raw']['pets'];

                // Сортируем питомцев в соответствии с выбранным методом
                if ($sortMethod === 'rarity') {
                    usort($pets, 'comparePetsByRarity');
                } elseif ($sortMethod === 'level') {
                    usort($pets, 'comparePetsByLevel');
                }

                // Выводим HTML-код отсортированных питомцев
                echo "<div class='pet-container'>";
                foreach ($pets as $pet) {
                    // Ваш код для вывода информации о питомце (замените на ваш реальный код)
                    $name = isset($pet['name']) ? $pet['name'] : 'Нет данных';
                    $level = isset($pet['level']['level']) ? $pet['level']['level'] : 'Нет данных';
                    $soulbound = isset($pet['soulbound']) ? ($pet['soulbound'] ? 'Да' : 'Нет') : 'Нет данных';
                    $rarity = isset($pet['rarity']) ? $pet['rarity'] : 'Нет данных';
                    $texturePath = isset($pet['texture_path']) ? "https://mc-heads.net" . $pet['texture_path'] : 'Нет данных';
                    $lore = isset($pet['lore']) ? $pet['lore'] : 'Нет данных';

                    // Определение класса для фона в зависимости от редкости
                    $rarityClass = "piece-common-bg"; // По умолчанию для случая, если редкость не соответствует ожидаемым значениям

                    switch ($rarity) {
                        case 'mythic':
                            $rarityClass = "piece-mythic-bg";
                            break;
                        case 'legendary':
                            $rarityClass = "piece-legendary-bg";
                            break;
                        case 'epic':
                            $rarityClass = "piece-epic-bg";
                            break;
                        case 'rare':
                            $rarityClass = "piece-rare-bg";
                            break;
                        case 'uncommon':
                            $rarityClass = "piece-uncommon-bg";
                            break;
                        case 'common':
                            $rarityClass = "piece-common-bg";
                            break;
                    }

                    echo "<div class='pet-item {$rarityClass}'>";
                    echo "<div class='pet-image'><img src='{$texturePath}' alt='{$name}'></div>";
                    echo "<strong>Имя:</strong> {$name}, ";
                    echo "<strong>Уровень:</strong> {$level}, ";
                    echo "<strong>Soulbound:</strong> {$soulbound}, ";
                    echo "<strong>Редкость:</strong> {$rarity}, ";
                    echo "<strong>Описание:</strong> {$lore}";
                    echo "</div>";
                }
                echo "</div>";
            } else {
                echo "<p>Нет информации о питомцах.</p>";
            }
        } else {
            echo "<p>Ошибка при получении данных профиля.</p>";
        }
    } else {
        echo "<p>Ошибка при получении данных профиля. Возможно, игрок не существует или профиль скрыт.</p>";
    }
} else {
    echo "<p>Ошибка: Не указан игрок (playerName).</p>";
}
?>
