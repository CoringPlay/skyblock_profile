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
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .pet-details {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            z-index: 1000;
        }

        .pet-details strong {
            color: #333;
        }

        .close-btn {
            cursor: pointer;
            position: absolute;
            top: 5px;
            right: 5px;
            padding: 5px;
            background-color: #ddd;
        }
    </style>
</head>
<body>
<div class="overlay"></div>
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
<script>
    // JavaScript-код для отображения и закрытия всплывающего окна при клике на питомца
    document.addEventListener('DOMContentLoaded', function () {
        const overlay = document.querySelector('.overlay');
        const petDetails = document.querySelectorAll('.pet-details');
        const closeBtns = document.querySelectorAll('.close-btn');

        // Функция для открытия всплывающего окна
        function openPetDetails(index) {
            overlay.style.display = 'block';
            petDetails[index].style.display = 'block';
        }

        // Функция для закрытия всплывающего окна
        function closePetDetails(index) {
            overlay.style.display = 'none';
            petDetails[index].style.display = 'none';
        }

        // Обработчики событий для каждой кнопки "Подробнее"
        const petItems = document.querySelectorAll('.pet-item');
        petItems.forEach(function (petItem, index) {
            petItem.addEventListener('click', function () {
                openPetDetails(index);
            });
        });

        // Обработчики событий для каждой кнопки "Закрыть"
        closeBtns.forEach(function (closeBtn, index) {
            closeBtn.addEventListener('click', function (event) {
                event.stopPropagation(); // Предотвращаем всплывание события к родительским элементам
                closePetDetails(index);
            });
        });

        // Обработчик события для закрытия всплывающего окна при клике за его пределами
        overlay.addEventListener('click', function () {
            overlay.style.display = 'none';
            petDetails.forEach(function (petDetail) {
                petDetail.style.display = 'none';
            });
        });
    });
</script>
</body>
</html>
