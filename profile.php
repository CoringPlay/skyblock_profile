<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Viewer</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="pet-styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap"></noscript>
    <style>
        .pet-details {
            padding: 10px;
            border: 1px solid #ddd;
            margin-top: 10px;
            background-color: #f9f9f9;
        }

        .pet-details strong {
            color: #333;
        }
    </style>
</head>
<body>
<?php
if (isset($_GET['playerName'])) {
    $playerName = $_GET['playerName'];
    $apiUrl = "https://sky.shiiyu.moe/api/v2/profile/{$playerName}";

    // Получаем JSON-данные
    $json = file_get_contents($apiUrl);

    // Преобразуем JSON в массив
    $data = json_decode($json, true);

    if ($data) {
        // Отображаем профиль
        echo "<h1>Профиль игрока: {$playerName}</h1>";

        // Выводим массив profiles
        echo "<h2>Выберите профиль:</h2>";
        echo "<ul>";
        foreach ($data['profiles'] as $profile) {
            // Добавляем ссылку с параметром профиля в URL
            echo "<li><a href='profile.php?playerName={$playerName}&profile={$profile['profile_id']}'>{$profile['cute_name']}</a></li>";
        }
        echo "</ul>";

        // Проверяем, есть ли выбранный профиль в URL
        if (isset($_GET['profile'])) {
            $selectedProfileId = $_GET['profile'];

            // Выводим данные выбранного профиля
            foreach ($data['profiles'] as $profile) {
                if ($profile['profile_id'] == $selectedProfileId) {
                    echo "<h2>Выбранный профиль: {$profile['cute_name']}</h2>";

                    // Проверяем, есть ли объект "raw" и внутри него массив "pets"
                    if (isset($profile['raw']['pets']) && is_array($profile['raw']['pets'])) {
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

                        // Сортировка массива питомцев по редкости
                        usort($profile['raw']['pets'], 'comparePetsByRarity');

                        // Выводим питомцев
                        echo "<h3>Информация о питомцах:</h3>";
                        echo "<div class='pet-container'>";
                        foreach ($profile['raw']['pets'] as $pet) {
                            // Проверяем, есть ли нужные ключи в данных о питомце
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
                            echo "<strong>{$name}</strong><br>";
                            echo "<div class='pet-image'><img src='{$texturePath}' alt='{$name}'></div>";
                            echo "<div class='pet-details'>";
                            echo "{$lore}<br>";
                            echo "</div>";
                            echo "</div>";
                        }
                        echo "</div>";
                    } else {
                        echo "<p>Нет информации о питомцах.</p>";
                    }
                    // Выводите другие данные по вашему желанию
                }
            }
        }
    } else {
        echo "<p>Ошибка при получении данных профиля.</p>";
    }
} else {
    echo "<p>Введите никнейм в форму.</p>";
}
?>
</body>
</html>
