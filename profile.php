<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Viewer</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="pet-styles.css" class="">

    <!-- jQuery (если еще не подключен) -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Обработчик изменения значения в выпадающем списке
            $('#sort').change(function() {
                // Получаем выбранное значение сортировки
                var sortMethod = $(this).val();

                // Получаем playerName из URL
                var playerName = '<?php echo isset($_GET['playerName']) ? $_GET['playerName'] : ''; ?>';

                // Отправляем AJAX-запрос на сервер с параметрами сортировки и playerName
                $.ajax({
                    url: 'ajax_sort_profile.php',
                    method: 'GET',
                    data: { sort: sortMethod, playerName: playerName },
                    success: function(response) {
                        // Обновляем контент на странице с полученными данными
                        $('#pet-container').html(response);
                    },
                    error: function(error) {
                        console.log('Error:', error);
                    }
                });
            });
        });
    </script>
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

                    // Выводим контейнер для питомцев с выпадающим списком сортировки
                    echo "<div>";
                    echo "<label for='sort'>Сортировка:</label>";
                    echo "<select id='sort' name='sort'>";
                    echo "<option value='rarity'>По редкости</option>";
                    echo "<option value='level'>По уровню</option>";
                    echo "</select>";
                    echo "</div>";

                    // Выводим контейнер для питомцев
                    echo "<div id='pet-container'>";
                    // Помещаем сюда код для вывода питомцев (замените на ваш код)
                    echo "</div>";

                    // Закрываем теги для контейнеров
                    echo "</div>";
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
