<?php
/**
 * index.php — Point d'entrée de l'application météo
 * Ce fichier contient uniquement le HTML. La logique est dans src/weather.php.
 */

require_once __DIR__ . '/src/weather.php';

// Charger les variables d'environnement depuis .env si présent
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

$cities = ['Rabat', 'Casablanca', 'Tanger', 'Agadir', 'Fès', 'Ouarzazate'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Météo au Maroc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

    <!-- Formulaire de sélection de ville -->
    <div class="form-wrapper">
        <form action="" method="post">
            <fieldset>
                <legend>🌤️ Météo au Maroc</legend>
                <span class="subtitle">Choisissez la bonne ville (Bonne chance avec les nuages !)</span>

                <br>
                <label for="city">Ville :</label>
                <select name="city" id="city">
                    <?php foreach ($cities as $city): ?>
                        <option value="<?= htmlspecialchars($city) ?>"
                            <?= ($selectedCity === $city) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($city) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <br>
                <button type="submit" name="sendww" class="btn-submit">
                    Voir la météo 🔍
                </button>
            </fieldset>
        </form>
    </div>

    <hr>

    <!-- Résultat de la météo -->
    <?php if ($weatherResult !== null): ?>
    <div class="result">
        <?php if (isset($weatherResult['error'])): ?>
            <p>❌ <?= htmlspecialchars($weatherResult['error']) ?></p>
        <?php else: ?>
            <p>
                🌡️ La température à <b><?= htmlspecialchars($weatherResult['city']) ?></b>
                est de <b><?= htmlspecialchars($weatherResult['temp']) ?>°C</b>
                et la météo est <b><?= htmlspecialchars($weatherResult['description']) ?></b>.
            </p>
            <p><?= htmlspecialchars($weatherResult['message']) ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</body>
</html>
